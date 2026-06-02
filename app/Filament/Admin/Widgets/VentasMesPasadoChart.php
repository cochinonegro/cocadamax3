<?php

namespace App\Filament\Admin\Widgets;

use App\Support\DescargaVentas;
use App\Support\DisplayTimezone;
use Filament\Widgets\ChartWidget;

class VentasMesPasadoChart extends ChartWidget
{
    protected static bool $isDiscovered = false;

    protected ?string $heading = 'Pedidos / ventas (€) — mes actual / mes pasado';

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $mesActual = now(DisplayTimezone::name())->startOfMonth();
        $mesPasado = $mesActual->copy()->subMonth()->startOfMonth();
        $diasEnGrafico = max($mesActual->daysInMonth, $mesPasado->daysInMonth);

        $montosActual = DescargaVentas::montosPorDiaDelMes($mesActual);
        $montosPasado = DescargaVentas::montosPorDiaDelMes($mesPasado);

        $etiquetas = [];
        $serieActual = [];
        $seriePasado = [];

        for ($dia = 1; $dia <= $diasEnGrafico; $dia++) {
            $etiquetas[] = (string) $dia;
            $serieActual[] = round($montosActual[$dia] ?? 0, 2);
            $seriePasado[] = round($montosPasado[$dia] ?? 0, 2);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Mes actual ('.$mesActual->locale('es')->translatedFormat('M Y').')',
                    'data' => $serieActual,
                    'backgroundColor' => '#f59e0b',
                    'borderColor' => '#d97706',
                    'borderWidth' => 1,
                ],
                [
                    'label' => 'Mes pasado ('.$mesPasado->locale('es')->translatedFormat('M Y').')',
                    'data' => $seriePasado,
                    'backgroundColor' => '#38bdf8',
                    'borderColor' => '#0ea5e9',
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
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
            ],
            'scales' => [
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Día del mes',
                    ],
                ],
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
