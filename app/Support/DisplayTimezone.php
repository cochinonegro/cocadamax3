<?php

namespace App\Support;

use Carbon\CarbonInterface;

final class DisplayTimezone
{
    public const DEFAULT = 'Europe/Madrid';

    public static function name(): string
    {
        return (string) config('app.timezone', self::DEFAULT);
    }

    public static function format(?CarbonInterface $dateTime, string $format): ?string
    {
        if ($dateTime === null) {
            return null;
        }

        return $dateTime->copy()->timezone(self::name())->format($format);
    }

    public static function formatDate(?CarbonInterface $dateTime): string
    {
        return self::format($dateTime, 'd/m/Y') ?? '—';
    }

    public static function formatTime(?CarbonInterface $dateTime): ?string
    {
        return self::format($dateTime, 'H:i');
    }

    public static function formatDateTime(?CarbonInterface $dateTime): ?string
    {
        if ($dateTime === null) {
            return null;
        }

        $formatted = self::format($dateTime, 'd/m/Y H:i');

        return $formatted ?? '—';
    }
}
