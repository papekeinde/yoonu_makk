<?php

namespace App\Support;

class MailConfiguration
{
    public static function isConfigured(): bool
    {
        $mailer = (string) config('mail.default', 'log');

        if ($mailer !== 'smtp') {
            return true;
        }

        $host = trim((string) config('mail.mailers.smtp.host', ''));
        $username = trim((string) config('mail.mailers.smtp.username', ''));
        $password = trim((string) config('mail.mailers.smtp.password', ''));

        if ($host === '' || $username === '' || $password === '') {
            return false;
        }

        if (
            str_contains($username, 'REMPLACE_PAR_') ||
            str_contains($password, 'REMPLACE_PAR_') ||
            str_contains($password, '<YOUR_') ||
            str_contains($password, 'YOUR_API_TOKEN')
        ) {
            return false;
        }

        return true;
    }
}
