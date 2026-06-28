<?php

namespace App\Support;

/**
 * Cryptographically-strong numeric one-time codes.
 *
 * Numeric (not alphanumeric) because the segmented OTP input — Filament's
 * OneTimeCodeInput, the 6-box paste-able field — is digit-only by design.
 * Per the security review, 6 digits behind an attempt-capped, expiring,
 * single-use verify step is more than sufficient; entropy is not the load-
 * bearing control, the cap is. random_int() is a CSPRNG.
 */
class Otp
{
    public static function code(int $length = 6): string
    {
        return str_pad((string) random_int(0, (10 ** $length) - 1), $length, '0', STR_PAD_LEFT);
    }
}
