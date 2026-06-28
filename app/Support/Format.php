<?php

namespace App\Support;

/**
 * Display/normalisation helpers for Indonesian identity fields.
 *
 * Storage stays canonical (NIK = 16 raw digits, phone = "+62 8xx xxxx xxxx"),
 * while the UI shows the grouped, human-friendly form. Every method is
 * idempotent and null-safe so it is safe to run on already-formatted values.
 */
class Format
{
    /** Strip everything that is not a digit. */
    public static function digits(?string $value): string
    {
        return preg_replace('/\D/', '', (string) $value) ?? '';
    }

    /** National significant number for an ID mobile (drops +62 / 62 / leading 0). */
    public static function phoneNational(?string $value): string
    {
        $digits = self::digits($value);

        if (str_starts_with($digits, '62')) {
            return substr($digits, 2);
        }

        if (str_starts_with($digits, '0')) {
            return substr($digits, 1);
        }

        return $digits;
    }

    /**
     * Pretty Indonesian mobile: 081212350164 / +6281212350164 → "+62 812 1235 0164"
     * (country code, then the first 3 digits, then groups of 4).
     */
    public static function phoneId(?string $value): ?string
    {
        $national = self::phoneNational($value);

        if ($national === '') {
            return null;
        }

        $head = substr($national, 0, 3);
        $tail = substr($national, 3);
        $groups = $tail === '' ? [] : str_split($tail, 4);

        return rtrim('+62 '.$head.($groups === [] ? '' : ' '.implode(' ', $groups)));
    }

    /** Canonical NIK for storage: 16 raw digits (or null). */
    public static function nik(?string $value): ?string
    {
        $digits = substr(self::digits($value), 0, 16);

        return $digits === '' ? null : $digits;
    }

    /** Pretty NIK: 1234567890123456 → "1234-5678-9012-3456". */
    public static function nikMasked(?string $value): ?string
    {
        $digits = self::nik($value);

        return $digits === null ? null : implode('-', str_split($digits, 4));
    }
}
