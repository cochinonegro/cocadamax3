<?php

namespace App\Support;

use App\Models\Descarga;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

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
        return (float) self::baseQuery()
            ->whereBetween('downloaded_at', [$from, $to])
            ->sum('precio');
    }

    /**
     * Importes por fecha (Y-m-d) en un rango.
     *
     * @return array<string, float>
     */
    public static function montosPorFecha(CarbonInterface $from, CarbonInterface $to): array
    {
        $rows = self::baseQuery()
            ->whereBetween('downloaded_at', [$from, $to])
            ->selectRaw('DATE(downloaded_at) as dia, SUM(precio) as total')
            ->groupByRaw('DATE(downloaded_at)')
            ->pluck('total', 'dia');

        $montos = [];

        foreach ($rows as $fecha => $total) {
            $montos[(string) $fecha] = (float) $total;
        }

        return $montos;
    }

    /**
     * Importes por día del mes (1–31).
     *
     * @return array<int, float>
     */
    public static function montosPorDiaDelMes(CarbonInterface $inicioMes): array
    {
        $from = $inicioMes->copy()->startOfMonth();
        $to = $inicioMes->copy()->endOfMonth();

        $porDia = [];

        foreach (self::montosPorFecha($from, $to) as $fecha => $total) {
            $porDia[Carbon::parse($fecha)->day] = $total;
        }

        return $porDia;
    }

    public static function formatEuro(float $amount): string
    {
        return number_format($amount, 2, ',', '.').' €';
    }

    private static function baseQuery()
    {
        return Descarga::query()
            ->whereNotNull('precio');
    }
}
