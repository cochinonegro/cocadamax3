<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Venta;
use Filament\Widgets\ChartWidget;

class VentasMesPasadoChart extends ChartWidget
{
    protected static bool $isDiscovered = false;

    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 1;

    public function getHeading(): ?string
    {
        $mes = now()->subMonth()->locale('es')->translatedFormat('F Y');

        return "Ventas de {$mes}";
    }

    protected function getData(): array
    {
        $inicio = now()->subMonth()->startOfMonth();
        $fin = now()->subMonth()->endOfMonth();

        $conteos = Venta::query()
            ->whereBetween('fecha_venta', [$inicio->toDateString(), $fin->toDateString()])
            ->selectRaw('DATE(fecha_venta) as dia, COUNT(*) as total')
            ->groupByRaw('DATE(fecha_venta)')
            ->pluck('total', 'dia');

        $etiquetas = [];
        $datos = [];

        for ($dia = 1; $dia <= $inicio->daysInMonth; $dia++) {
            $fecha = $inicio->copy()->day($dia);
            $clave = $fecha->toDateString();
            $etiquetas[] = (string) $dia;
            $datos[] = (int) ($conteos[$clave] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Ventas',
                    'data' => $datos,
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
}
