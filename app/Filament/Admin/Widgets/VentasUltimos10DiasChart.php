<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Venta;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class VentasUltimos10DiasChart extends ChartWidget
{
    protected static bool $isDiscovered = false;

    protected ?string $heading = 'Ventas de los últimos 10 días';

    protected static ?int $sort = 0;

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $inicio = now()->subDays(9)->startOfDay();

        $conteos = Venta::query()
            ->whereDate('fecha_venta', '>=', $inicio)
            ->selectRaw('DATE(fecha_venta) as dia, COUNT(*) as total')
            ->groupByRaw('DATE(fecha_venta)')
            ->pluck('total', 'dia');

        $etiquetas = [];
        $datos = [];

        for ($i = 0; $i < 10; $i++) {
            $fecha = Carbon::today()->subDays(9 - $i);
            $clave = $fecha->toDateString();
            $etiquetas[] = $fecha->format('d/m');
            $datos[] = (int) ($conteos[$clave] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Ventas',
                    'data' => $datos,
                    'backgroundColor' => '#f59e0b',
                    'borderColor' => '#d97706',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $etiquetas,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
