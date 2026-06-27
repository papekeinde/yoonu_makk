<?php

namespace App\Support;

final class PhoneNumber
{
    public static function normalize(?string $input, string $defaultCountryCode = '221'): ?string
    {
        if ($input === null) {
            return null;
        }

        $raw = trim($input);
        if ($raw === '') {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $raw) ?? '';
        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '00') && strlen($digits) > 2) {
            $digits = substr($digits, 2);
        }

        // Formats déjà internationalisés usuels.
        if ((str_starts_with($digits, '221') && strlen($digits) > 3)
            || (str_starts_with($digits, '1') && strlen($digits) > 1)) {
            return '+' . $digits;
        }

        // Numéro local sans indicatif : défaut Sénégal.
        if (strlen($digits) <= 10) {
            return '+' . $defaultCountryCode . $digits;
        }

        // Fallback : on force le format international avec '+'.
        return '+' . $digits;
    }

    public static function candidates(string $input): array
    {
        $raw = trim($input);
        $digits = preg_replace('/\D+/', '', $raw) ?? '';

        if ($raw === '' && $digits === '') {
            return [];
        }

        $normalized = self::normalize($input);

        $candidates = array_filter([
            $raw,
            $digits,
            $normalized,
            $digits !== '' ? '+' . $digits : null,
        ]);

        if ($digits !== '') {
            $candidates[] = '221' . $digits;
            $candidates[] = '+221' . $digits;
            $candidates[] = '1' . $digits;
            $candidates[] = '+1' . $digits;
        }

        if (str_starts_with($digits, '221') && strlen($digits) > 3) {
            $local = substr($digits, 3);
            $candidates[] = $local;
            $candidates[] = '221' . $local;
            $candidates[] = '+221' . $local;
        }

        if (str_starts_with($digits, '1') && strlen($digits) > 1) {
            $local = substr($digits, 1);
            $candidates[] = $local;
            $candidates[] = '1' . $local;
            $candidates[] = '+1' . $local;
        }

        return array_values(array_unique(array_filter($candidates)));
    }
}
