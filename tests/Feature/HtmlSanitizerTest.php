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
