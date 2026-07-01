<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use App\Models\Testimonial;
use App\Support\Html;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RichContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_rich_html_strips_dangerous_html(): void
    {
        $this->assertStringNotContainsString('script', rich_html('<script>evil()</script>Hello'));
        $this->assertStringContainsString('Hello', rich_html('<script>evil()</script>Hello'));
        $this->assertStringNotContainsString('javascript', rich_html('<a href="javascript:alert(1)">x</a>'));
    }

    public function test_rich_html_unwraps_single_paragraph_but_keeps_multi(): void
    {
        $this->assertSame('Short line', rich_html('<p>Short line</p>'));
        $this->assertStringContainsString('<p>a</p>', rich_html('<p>a</p><p>b</p>'));
    }

    public function test_rich_html_escapes_plain_legacy_text(): void
    {
        $this->assertStringContainsString('<br', rich_html("line1\nline2"));
        $this->assertStringNotContainsString('<script', rich_html('<script>x</script>'));
        $this->assertSame('', rich_html(null));
        $this->assertSame('', rich_html(''));
    }

    public function test_model_sanitizes_rich_html_on_save(): void
    {
        $testimonial = Testimonial::factory()->create([
            'quote' => '<script>steal()</script><p>Amazing partner</p>',
        ]);

        $stored = $testimonial->fresh()->quote;

        $this->assertStringNotContainsString('script', $stored);
        $this->assertStringNotContainsString('steal', $stored);
        $this->assertStringContainsString('Amazing partner', $stored);
    }

    public function test_sanitizer_rejects_protocol_relative_urls(): void
    {
        $out = Html::clean('<a href="//evil.com">x</a>');
        $this->assertStringNotContainsString('//evil.com', $out);

        $ok = Html::clean('<a href="/about">a</a><a href="https://creativetreesgroup.com">b</a>');
        $this->assertStringContainsString('href="/about"', $ok);
        $this->assertStringContainsString('href="https://creativetreesgroup.com"', $ok);
    }

    public function test_site_setting_sanitizes_rich_columns_on_save(): void
    {
        $setting = SiteSetting::query()->firstOrCreate(['id' => 1]);
        $setting->update(['footer_tagline' => '<script>steal()</script><p>Built to compound</p>']);

        $stored = $setting->fresh()->footer_tagline;
        $this->assertStringNotContainsString('script', $stored);
        $this->assertStringContainsString('Built to compound', $stored);
    }
}
