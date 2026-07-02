<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMXPath;

class Html
{
    /** Tags allowed to survive sanitization (matches what the RichEditor produces). */
    private const ALLOWED_TAGS = [
        'p', 'br', 'strong', 'b', 'em', 'i', 'u', 's', 'sub', 'sup',
        'ul', 'ol', 'li', 'a', 'h2', 'h3', 'h4', 'blockquote', 'code', 'pre', 'span',
        'img', 'table', 'thead', 'tbody', 'tfoot', 'tr', 'th', 'td',
    ];

    /** Block-level tags the editor may tag with a text-align style. */
    private const ALIGNABLE_TAGS = ['p', 'h2', 'h3', 'h4', 'blockquote', 'li', 'td', 'th'];

    /** Tags removed together with their contents (everything else unknown is unwrapped). */
    private const STRIP_WITH_CONTENTS = ['script', 'style', 'iframe', 'object', 'embed', 'form'];

    /**
     * Sanitize admin-authored rich HTML (RichEditor output) so it is safe to render
     * on the public site with {!! !!}. Keeps a small formatting allowlist (including
     * images, tables, and text-align — all produced by the default editor toolbar),
     * removes script/style/iframe (with contents), unwraps any other unknown tag,
     * strips every attribute except a safe <a href>, a validated <img src>, table
     * spans, and a canonicalised text-align, and drops on* handlers / javascript:
     * URLs. Plain text (no "<") passes through untouched, and stray "<"/">" inside
     * otherwise-plain text (public Lead messages, seeders, tinker) are preserved
     * rather than silently swallowed by the DOM parser.
     */
    public static function clean(?string $html): ?string
    {
        if ($html === null || trim($html) === '' || ! str_contains($html, '<')) {
            return $html;
        }

        // Escape stray "<" that don't begin a real tag or comment. RichEditor output
        // already entity-escapes "<" in text nodes, so real rich HTML is unaffected;
        // this only rescues plain-text writes ("Budget is < $5k, ROI > 3%") that
        // DOMDocument would otherwise parse as a bogus tag and delete. A bare ">"
        // with no matching "<" is treated as literal text by the parser already.
        $html = preg_replace('/<(?![a-zA-Z!\/])/', '&lt;', $html);

        $dom = new DOMDocument;
        $previous = libxml_use_internal_errors(true);
        $dom->loadHTML(
            '<?xml encoding="UTF-8"?><body>'.$html.'</body>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $xpath = new DOMXPath($dom);

        foreach (iterator_to_array($xpath->query('//*')) as $node) {
            if (! $node instanceof DOMElement) {
                continue;
            }

            $tag = strtolower($node->nodeName);

            if ($tag === 'body') {
                continue;
            }

            if (! in_array($tag, self::ALLOWED_TAGS, true)) {
                if (in_array($tag, self::STRIP_WITH_CONTENTS, true)) {
                    $node->parentNode?->removeChild($node);
                } else {
                    $node->parentNode?->replaceChild($dom->createTextNode($node->textContent), $node);
                }

                continue;
            }

            self::filterAttributes($node, $tag);

            // An <img> whose src didn't survive validation is a broken/empty tag — drop it.
            if ($tag === 'img' && ! $node->hasAttribute('src')) {
                $node->parentNode?->removeChild($node);

                continue;
            }

            if ($tag === 'a' && $node->hasAttribute('href')) {
                $node->setAttribute('rel', 'noopener nofollow');
            }
        }

        $body = $dom->getElementsByTagName('body')->item(0);

        if (! $body) {
            return '';
        }

        $out = '';
        foreach ($body->childNodes as $child) {
            $out .= $dom->saveHTML($child);
        }

        return $out;
    }

    /** Strip every attribute on an allowed element except the small per-tag safelist. */
    private static function filterAttributes(DOMElement $node, string $tag): void
    {
        foreach (iterator_to_array($node->attributes) as $attribute) {
            $name = strtolower($attribute->nodeName);
            $value = trim($attribute->nodeValue ?? '');

            // Keep only a canonical text-align on block tags; drop every other inline style.
            if ($name === 'style') {
                if (in_array($tag, self::ALIGNABLE_TAGS, true)
                    && preg_match('/text-align:\s*(left|right|center|justify)/i', $value, $m)) {
                    $node->setAttribute('style', 'text-align: '.strtolower($m[1]));
                } else {
                    $node->removeAttribute($attribute->nodeName);
                }

                continue;
            }

            $keep = match ($tag) {
                'a' => $name === 'href' && self::isSafeHref($value),
                'img' => ($name === 'src' && self::isSafeImageSrc($value)) || $name === 'alt',
                'td', 'th' => in_array($name, ['colspan', 'rowspan'], true) && ctype_digit($value) && $value !== '0',
                default => false,
            };

            if (! $keep) {
                $node->removeAttribute($attribute->nodeName);
            }
        }
    }

    /** A rich-text <a href> value that is safe to emit (relative, anchor, or http/mailto). */
    public static function isSafeHref(string $value): bool
    {
        // Reject backslashes: browsers normalise "/\evil.com" → "//evil.com" → offsite.
        if (str_contains($value, '\\')) {
            return false;
        }

        return preg_match('#^(https?://|mailto:|/(?!/)|\#)#i', $value) === 1;
    }

    /** An <img src> value that is safe to emit (same-origin relative or http/https only). */
    private static function isSafeImageSrc(string $value): bool
    {
        if (str_contains($value, '\\')) {
            return false;
        }

        return preg_match('#^(https?://|/(?!/))#i', $value) === 1;
    }

    /**
     * A navigation URL (footer/header links) that is safe to place in an href:
     * relative path, anchor, http(s), mailto, or tel — never javascript:/data: or a
     * backslash-normalised offsite link. Used to validate admin input and guard render.
     */
    public static function isSafeUrl(?string $value): bool
    {
        $value = trim((string) $value);

        if ($value === '' || str_contains($value, '\\')) {
            return false;
        }

        return preg_match('#^(https?://|mailto:|tel:|/(?!/)|\#)#i', $value) === 1;
    }

    /** Recursively sanitize every string leaf of a nested array (e.g. page_content). */
    public static function cleanDeep(array $data): array
    {
        array_walk_recursive($data, function (&$value): void {
            if (is_string($value)) {
                $value = static::clean($value);
            }
        });

        return $data;
    }
}
