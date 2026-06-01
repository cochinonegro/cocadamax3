<?php

namespace App\Filament\Support;

use Illuminate\Support\HtmlString;

class ClienteFormatting
{
    public static function phoneDigits(?string $phone): string
    {
        if (blank($phone)) {
            return '';
        }

        return preg_replace('/\D+/', '', (string) $phone) ?? '';
    }

    public static function formatPhone(?string $phone): string
    {
        $digits = self::phoneDigits($phone);

        if ($digits === '') {
            return $phone !== null && $phone !== '' ? trim((string) $phone) : '—';
        }

        return trim(implode(' ', str_split($digits, 3)));
    }

    public static function phoneHtml(?string $phone): HtmlString
    {
        $formatted = e(self::formatPhone($phone));

        if ($formatted === '—') {
            return new HtmlString('<span class="text-zinc-500">—</span>');
        }

        return new HtmlString(
            '<span class="font-bold text-amber-400 dark:text-amber-300">'.$formatted.'</span>',
        );
    }
}
