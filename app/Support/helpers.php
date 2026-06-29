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
