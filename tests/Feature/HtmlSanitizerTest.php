<?php

namespace Tests\Feature;

use App\Support\Html;
use Tests\TestCase;

class HtmlSanitizerTest extends TestCase
{
    public function test_it_removes_script_tags_with_their_contents(): void
    {
        $out = Html::clean('<script>fetch("//evil")</script>Hello');

        $this->assertStringNotContainsString('script', $out);
        $this->assertStringNotContainsString('evil', $out);
        $this->assertStringContainsString('Hello', $out);
    }

    public function test_it_removes_iframes_and_unwraps_unknown_tags(): void
    {
        $this->assertStringNotContainsString('iframe', (string) Html::clean('<iframe src="//evil"></iframe>ok'));
        $this->assertStringNotContainsString('marquee', (string) Html::clean('<marquee>hi</marquee>'));
        $this->assertStringContainsString('hi', (string) Html::clean('<marquee>hi</marquee>'));
    }

    public function test_it_strips_event_handlers_but_keeps_formatting(): void
    {
        $out = (string) Html::clean('<p onclick="evil()">Hi <strong>there</strong></p>');

        $this->assertStringNotContainsString('onclick', $out);
        $this->assertStringContainsString('<strong>there</strong>', $out);
    }

    public function test_it_strips_javascript_hrefs_but_keeps_safe_links(): void
    {
        $this->assertStringNotContainsString('javascript', (string) Html::clean('<a href="javascript:alert(1)">x</a>'));

        $safe = (string) Html::clean('<a href="https://creativetreesgroup.com">link</a>');
        $this->assertStringContainsString('href="https://creativetreesgroup.com"', $safe);
        $this->assertStringContainsString('rel="noopener nofollow"', $safe);
    }

    public function test_plain_text_passes_through_untouched(): void
    {
        $this->assertSame('Just plain copy — no tags here.', Html::clean('Just plain copy — no tags here.'));
        $this->assertNull(Html::clean(null));
        $this->assertSame('', Html::clean(''));
    }

    public function test_plain_text_with_stray_angle_brackets_is_preserved_not_swallowed(): void
    {
        // Regression: public Lead messages (plain text run through the sanitizer)
        // must not have "< $5k ... > 3%" silently deleted as a bogus tag.
        $this->assertSame(
            'Budget is &lt; $5k, need conversions &gt; 3%.',
            Html::clean('Budget is < $5k, need conversions > 3%.')
        );
        $this->assertSame('love you &lt;3', Html::clean('love you <3'));
        $this->assertSame('5 &lt; 10 and 10 &gt; 5', Html::clean('5 < 10 and 10 > 5'));
    }

    public function test_it_keeps_safe_images_but_strips_handlers_and_bad_src(): void
    {
        $ok = (string) Html::clean('<p><img src="/storage/site/richtext/pic.png" alt="x" onerror="alert(1)"></p>');
        $this->assertStringContainsString('src="/storage/site/richtext/pic.png"', $ok);
        $this->assertStringContainsString('alt="x"', $ok);
        $this->assertStringNotContainsString('onerror', $ok);

        // An <img> with a non-http(s)/relative src is dropped entirely.
        $this->assertStringNotContainsString('<img', (string) Html::clean('<p><img src="javascript:alert(1)"></p>'));
    }

    public function test_it_keeps_tables_and_text_align_but_drops_other_styles(): void
    {
        $table = (string) Html::clean('<table><tbody><tr><td colspan="2">x</td></tr></tbody></table>');
        $this->assertStringContainsString('<table>', $table);
        $this->assertStringContainsString('colspan="2"', $table);

        $aligned = (string) Html::clean('<p style="color:red;text-align:right">x</p>');
        $this->assertStringContainsString('text-align: right', $aligned);
        $this->assertStringNotContainsString('color', $aligned);
    }

    public function test_is_safe_url_blocks_dangerous_and_backslash_normalised_links(): void
    {
        $this->assertTrue(Html::isSafeUrl('/work'));
        $this->assertTrue(Html::isSafeUrl('https://example.com'));
        $this->assertTrue(Html::isSafeUrl('mailto:hi@example.com'));
        $this->assertFalse(Html::isSafeUrl('javascript:alert(1)'));
        $this->assertFalse(Html::isSafeUrl('/\\evil.com'));
        $this->assertFalse(Html::isSafeUrl(null));
    }

    public function test_clean_deep_sanitizes_nested_string_leaves(): void
    {
        $out = Html::cleanDeep([
            'a' => '<script>x</script>ok',
            'nested' => ['b' => 'plain', 'c' => '<strong>bold</strong>'],
        ]);

        $this->assertStringNotContainsString('script', $out['a']);
        $this->assertSame('plain', $out['nested']['b']);
        $this->assertStringContainsString('<strong>bold</strong>', $out['nested']['c']);
    }
}
