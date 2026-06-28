<?php

namespace App\Support;

/**
 * Cryptographically-strong one-time codes.
 *
 * Uses an unambiguous uppercase alphanumeric alphabet (no 0/O, 1/I/L) so codes
 * like "3H9J4D" are easy to read and type, and random_int() (CSPRNG) so they
 * are not predictable.
 */
class Otp
{
    private const ALPHABET = '23456789ABCDEFGHJKMNPQRSTUVWXYZ';

    public static function code(int $length = 6): string
    {
        $max = strlen(self::ALPHABET) - 1;
        $code = '';

        for ($i = 0; $i < $length; $i++) {
            $code .= self::ALPHABET[random_int(0, $max)];
        }

        return $code;
    }
}
