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
