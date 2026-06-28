<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageRouteTest extends TestCase
{
    use RefreshDatabase;

    private function putSamplePng(string $path): void
    {
        $im = imagecreatetruecolor(1200, 800);
        imagefill($im, 0, 0, imagecolorallocate($im, 100, 150, 200));
        ob_start();
        imagepng($im);
        $binary = (string) ob_get_clean();
        imagedestroy($im);

        Storage::disk('public')->put($path, $binary);
    }

    public function test_resizes_a_local_image_to_webp(): void
    {
        Storage::fake('public');
        $this->putSamplePng('covers/sample.png');

        $this->get('/img/covers/sample.png?w=400')
            ->assertOk()
            ->assertHeader('Content-Type', 'image/webp');
    }

    public function test_rejects_a_width_not_in_the_allow_list(): void
    {
        Storage::fake('public');
        $this->putSamplePng('covers/sample.png');

        $this->get('/img/covers/sample.png?w=999')->assertNotFound();
    }

    public function test_rejects_a_missing_file(): void
    {
        Storage::fake('public');

        $this->get('/img/covers/missing.png?w=400')->assertNotFound();
    }
}
