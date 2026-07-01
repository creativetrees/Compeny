<?php

namespace Tests\Feature;

use App\Models\Testimonial;
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
}
