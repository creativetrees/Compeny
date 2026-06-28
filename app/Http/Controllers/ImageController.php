<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * On-the-fly responsive image resizer (native GD — no third-party dependency).
 *
 * Serves WebP variants of images on the public disk at a small allow-list of
 * widths, cached to disk after the first request. Hardened against:
 *   - path traversal     → reject "..", only the public disk is reachable
 *   - resize-DoS         → width must be one of a fixed allow-list
 *   - oversized sources  → very large source images are skipped (served as-is)
 *   - non-images         → MIME must be image/*
 */
class ImageController extends Controller
{
    /** Allowed output widths (px). Anything else is a 404 — no arbitrary resizing. */
    private const WIDTHS = [400, 800, 1200, 1600];

    /** Skip resizing sources larger than this (anti resize-bomb), in megapixels. */
    private const MAX_SOURCE_MEGAPIXELS = 40;

    public function show(Request $request, string $path): Response
    {
        $width = (int) $request->query('w', 800);
        abort_unless(in_array($width, self::WIDTHS, true), 404);

        // No traversal; the public disk is the only reachable root.
        abort_if(str_contains($path, '..') || str_starts_with($path, '/'), 404);

        $disk = Storage::disk('public');
        abort_unless($disk->exists($path), 404);
        abort_unless(Str::startsWith((string) $disk->mimeType($path), 'image/'), 404);

        $cacheKey = 'responsive/'.$width.'/'.$path.'.webp';

        if (! $disk->exists($cacheKey)) {
            $binary = $this->resizeToWebp($disk->path($path), $width);

            // Unsupported format / no WebP support → fall back to the original.
            if ($binary === null) {
                return redirect($disk->url($path));
            }

            $disk->put($cacheKey, $binary);
        }

        return response($disk->get($cacheKey), 200, [
            'Content-Type' => 'image/webp',
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }

    private function resizeToWebp(string $source, int $width): ?string
    {
        if (! function_exists('imagecreatefromstring') || ! function_exists('imagewebp')) {
            return null;
        }

        $info = @getimagesize($source);
        if ($info === false) {
            return null;
        }

        [$srcW, $srcH] = $info;
        if (($srcW * $srcH) > self::MAX_SOURCE_MEGAPIXELS * 1_000_000) {
            return null; // refuse to resize an enormous source
        }

        $data = @file_get_contents($source);
        $src = $data ? @imagecreatefromstring($data) : false;
        if (! $src) {
            return null;
        }

        $targetW = min($width, $srcW);
        $targetH = (int) round($srcH * ($targetW / $srcW));

        $dst = imagecreatetruecolor($targetW, $targetH);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $targetW, $targetH, $srcW, $srcH);

        ob_start();
        imagewebp($dst, null, 82);
        $out = ob_get_clean();

        imagedestroy($src);
        imagedestroy($dst);

        return $out ?: null;
    }
}
