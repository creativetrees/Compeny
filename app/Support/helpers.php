<?php

use App\Models\SiteContent;

if (! function_exists('content')) {
    /**
     * Resolve an editable copy string (managed in the admin "Site Content"
     * resource) by key, falling back to the original hardcoded $default.
     */
    function content(string $key, string $default = ''): string
    {
        return SiteContent::value($key, $default);
    }
}
