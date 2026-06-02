<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Venta;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class VentasMesPasadoChart extends ChartWidget
{
    protected static bool $isDiscovered = false;

    protected ?string $heading = 'Ventas mes actual / mes pasado';

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $mesActual = now()->startOfMonth();
        $mesPasado = now()->subMonth()->startOfMonth();
        $diasEnGrafico = max($mesActual->daysInMonth, $mesPasado->daysInMonth);

        $conteosActual = $this->conteosPorDiaDelMes($mesActual);
        $conteosPasado = $this->conteosPorDiaDelMes($mesPasado);

        $etiquetas = [];
        $serieActual = [];
        $seriePasado = [];

        for ($dia = 1; $dia <= $diasEnGrafico; $dia++) {
            $etiquetas[] = (string) $dia;
            $serieActual[] = $conteosActual[$dia] ?? 0;
            $seriePasado[] = $conteosPasado[$dia] ?? 0;
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

    /**
     * @return array<int, int>
     */
    private function conteosPorDiaDelMes(Carbon $inicioMes): array
    {
        $fin = $inicioMes->copy()->endOfMonth();

        $conteos = Venta::query()
            ->whereBetween('fecha_venta', [$inicioMes->toDateString(), $fin->toDateString()])
            ->selectRaw('DATE(fecha_venta) as dia, COUNT(*) as total')
            ->groupByRaw('DATE(fecha_venta)')
            ->pluck('total', 'dia');

        $porDia = [];

        foreach ($conteos as $fecha => $total) {
            $porDia[Carbon::parse($fecha)->day] = (int) $total;
        }

        return $porDia;
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
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
        ];
    }
}
