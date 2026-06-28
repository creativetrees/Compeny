<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Require multi-factor authentication
    |--------------------------------------------------------------------------
    |
    | When true, every admin must complete a second factor (email OTP, with the
    | authenticator app as a fallback) to reach /admin. Disabled in the test
    | suite so feature tests can exercise the panel without the MFA challenge.
    |
    | NOTE: panel access itself is gated by User::canAccessPanel() → the guarded
    | `is_admin` flag (default-deny). The previous email-domain allow-list was
    | removed because it was never wired into the gate.
    |
    */

    'mfa_required' => (bool) env('PANEL_MFA_REQUIRED', true),

];
