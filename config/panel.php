<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Access
    |--------------------------------------------------------------------------
    |
    | Only users whose email address belongs to one of these domains may
    | access the Filament panel at /admin. Provide a comma-separated list
    | via PANEL_ALLOWED_EMAIL_DOMAINS. An empty list denies all access
    | (the access gate fails closed).
    |
    */

    'allowed_email_domains' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('PANEL_ALLOWED_EMAIL_DOMAINS', 'creativetrees.group'))
    ))),

];
