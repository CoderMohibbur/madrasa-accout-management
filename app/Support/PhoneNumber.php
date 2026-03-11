<?php

namespace App\Support;

class PhoneNumber
{
    public static function normalize(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim($value);

        if ($trimmed === '') {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $trimmed);

        if (! is_string($digits) || $digits === '') {
            return null;
        }

        if (str_starts_with($trimmed, '+')) {
            return '+'.$digits;
        }

        if (str_starts_with($digits, '008801') && strlen($digits) === 15) {
            return '+'.substr($digits, 2);
        }

        if (str_starts_with($digits, '8801') && strlen($digits) === 13) {
            return '+'.$digits;
        }

        if (str_starts_with($digits, '01') && strlen($digits) === 11) {
            return '+88'.$digits;
        }

        if (str_starts_with($digits, '00')) {
            return '+'.substr($digits, 2);
        }

        return $digits;
    }

    public static function mask(?string $value): ?string
    {
        $normalized = self::normalize($value);

        if ($normalized === null) {
            return null;
        }

        if (strlen($normalized) <= 7) {
            return $normalized;
        }

        $prefix = str_starts_with($normalized, '+')
            ? substr($normalized, 0, 4)
            : substr($normalized, 0, 2);

        return $prefix
            .str_repeat('*', max(strlen($normalized) - strlen($prefix) - 4, 0))
            .substr($normalized, -4);
    }
}
