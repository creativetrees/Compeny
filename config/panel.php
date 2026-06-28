<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Require multi-factor authentication
    |--------------------------------------------------------------------------
    |
    | When true, every admin must complete a second factor (authenticator app,
    | or email OTP) to reach /admin. Disabled in the test suite so feature tests
    | can exercise the panel without the MFA challenge.
    |
    | DEFAULT is now FALSE (optional): forcing MFA before the admin has enrolled
    | a factor traps them on the set-up screen, and email OTP delivery depends on
    | the host's mail relay (which may reject transactional mail as spam). Enrol
    | the authenticator app first, then flip PANEL_MFA_REQUIRED=true to enforce.
    |
    | NOTE: panel access itself is gated by User::canAccessPanel() → the guarded
    | `is_admin` flag (default-deny). MFA is an additional factor, not the gate.
    |
    */

    'mfa_required' => (bool) env('PANEL_MFA_REQUIRED', false),

];
