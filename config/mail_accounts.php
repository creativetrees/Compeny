<?php

return [

    /*
    |--------------------------------------------------------------------------
    | CMS mail-account passwords
    |--------------------------------------------------------------------------
    |
    | SMTP passwords for the role-based mail accounts configured in
    | Site Settings → Email addresses. Secrets live ONLY here (read from .env),
    | NEVER in the database — so a compromised admin panel can neither read nor
    | change them. The non-secret parts (mailer, host, port, encryption,
    | username) are managed in the CMS; this file supplies only the password,
    | keyed by the account's "role".
    |
    | Set each on the server, e.g. in .env:
    |     MAIL_SUPPORT_PASSWORD="…"
    |     MAIL_NO_REPLY_PASSWORD="…"
    |
    */

    'passwords' => [
        'general' => env('MAIL_GENERAL_PASSWORD'),
        'support' => env('MAIL_SUPPORT_PASSWORD'),
        'sales' => env('MAIL_SALES_PASSWORD'),
        'no_reply' => env('MAIL_NO_REPLY_PASSWORD'),
        'developer' => env('MAIL_DEVELOPER_PASSWORD'),
        'info' => env('MAIL_INFO_PASSWORD'),
        'billing' => env('MAIL_BILLING_PASSWORD'),
        'careers' => env('MAIL_CAREERS_PASSWORD'),
        'press' => env('MAIL_PRESS_PASSWORD'),
        'other' => env('MAIL_OTHER_PASSWORD'),
    ],

];
