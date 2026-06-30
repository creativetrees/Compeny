<?php

use App\Models\SiteSetting;

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
