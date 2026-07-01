<?php

use App\Models\SiteSetting;
use App\Support\Html;

if (! function_exists('content')) {
    /**
     * Resolve an editable copy string by dotted key (e.g. "services.hero_eyebrow")
     * from the Site Settings "page_content" store — managed per page in the admin
     * Site Settings tabs. Falls back to the original hardcoded $default when unset
     * or blank, so every call site keeps working before anything is customised.
     */
    function content(string $key, string $default = ''): string
    {
        $value = data_get(SiteSetting::current()->page_content ?? [], $key);

        return is_string($value) && $value !== '' ? $value : $default;
    }
}

if (! function_exists('content_rich')) {
    /**
     * Like content(), but for a single-paragraph RichEditor value rendered inline
     * inside an existing block element: strips one wrapping <p>…</p> so the HTML
     * does not nest inside the container (which would drop the container styling).
     */
    function content_rich(string $key, string $default = ''): string
    {
        $html = trim(content($key, $default));

        if (preg_match('#^<p>(.*)</p>$#is', $html, $m) && stripos($m[1], '<p') === false) {
            return $m[1];
        }

        return $html;
    }
}

if (! function_exists('content_title')) {
    /**
     * Plain-text title for animated/scramble headlines and <title>-style headings,
     * where a RichEditor value must render as clean text (no raw tags, no escaped
     * entities). Converts block boundaries (</p>, <br>, </div>, </h1-6>, </li>) to
     * line breaks, strips inline tags, decodes entities, then trims. Line breaks are
     * preserved so a multi-paragraph value still renders as a multi-line heading;
     * inline formatting (bold/italic/links) is intentionally dropped.
     */
    function content_title(string $key, string $default = ''): string
    {
        $text = preg_replace(
            ['#</(?:p|div|h[1-6]|li)>#i', '#<br\s*/?>#i'],
            "\n",
            content($key, $default)
        );

        return trim(html_entity_decode(strip_tags($text), ENT_QUOTES));
    }
}

if (! function_exists('rich_html')) {
    /**
     * Safely render a RichEditor-backed model field with {!! !!}. HTML values are
     * sanitized (defence-in-depth on top of the model's save-time sanitizer); plain
     * legacy text is escaped and line-broken so nothing is lost before a re-save.
     */
    function rich_html(?string $value): string
    {
        if ($value === null || trim($value) === '') {
            return '';
        }

        if (! str_contains($value, '<')) {
            return nl2br(e($value));   // plain legacy text — escape + keep line breaks
        }

        $html = (string) Html::clean($value);

        // Unwrap a single wrapping <p> so short values render inline (no nested <p>);
        // multi-paragraph values keep their structure for a block container.
        if (preg_match('#^\s*<p>(.*)</p>\s*$#is', $html, $m) && stripos($m[1], '<p') === false) {
            return $m[1];
        }

        return $html;
    }
}
