<?php

namespace App\Support;

use App\Models\Descarga;
use Carbon\CarbonInterface;

class DescargaVentas
{
    public static function totalSemana(): float
    {
        $now = now(DisplayTimezone::name());

        return self::totalBetween(
            $now->copy()->startOfWeek(),
            $now->copy()->endOfWeek(),
        );
    }

    public static function totalMes(): float
    {
        $now = now(DisplayTimezone::name());

        return self::totalBetween(
            $now->copy()->startOfMonth(),
            $now->copy()->endOfMonth(),
        );
    }

    public static function totalBetween(CarbonInterface $from, CarbonInterface $to): float
    {
        return (float) Descarga::query()
            ->where('pagado', true)
            ->whereNotNull('precio')
            ->whereBetween('downloaded_at', [$from, $to])
            ->sum('precio');
    }

    public static function formatEuro(float $amount): string
    {
        return number_format($amount, 2, ',', '.').' €';
    }
}
