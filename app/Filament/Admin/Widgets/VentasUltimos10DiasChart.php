<?php

namespace App\Filament\Admin\Widgets;

use App\Support\DescargaVentas;
use App\Support\DisplayTimezone;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class VentasUltimos10DiasChart extends ChartWidget
{
    protected static bool $isDiscovered = false;

    protected ?string $heading = 'Pedidos / ventas (€) — últimos 10 días';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = [
        'default' => 1,
        'md' => 1,
    ];

    protected ?string $maxHeight = '190px';

    protected function getData(): array
    {
        $hoy = now(DisplayTimezone::name())->startOfDay();
        $inicio = $hoy->copy()->subDays(9);

        $montos = DescargaVentas::montosPorFecha(
            $inicio,
            $hoy->copy()->endOfDay(),
        );

        $etiquetas = [];
        $datos = [];

        for ($i = 0; $i < 10; $i++) {
            $fecha = $hoy->copy()->subDays(9 - $i);
            $clave = $fecha->toDateString();
            $etiquetas[] = $fecha->format('d/m');
            $datos[] = round($montos[$clave] ?? 0, 2);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Importe (€)',
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

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => '€',
                    ],
                ],
            ],
        ];
    }
}
