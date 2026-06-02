<?php

namespace App\Filament\Admin\Resources\Pedidos\Widgets;

use App\Models\Descarga;
use App\Support\DisplayTimezone;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PedidosVentasMesPasadoCantidadWidget extends StatsOverviewWidget
{
    protected static bool $isDiscovered = false;

    protected ?string $pollingInterval = '10s';

    protected ?string $heading = 'TOTAL VENTAS MES PASADO';

    protected int|string|array $columnSpan = 1;

    protected function getStats(): array
    {
        $mesPasadoInicio = now(DisplayTimezone::name())->subMonth()->startOfMonth();
        $mesPasadoFin = $mesPasadoInicio->copy()->endOfMonth();

        $cantidad = Descarga::query()
            ->whereBetween('downloaded_at', [$mesPasadoInicio, $mesPasadoFin])
            ->count();

        return [
            Stat::make('Total', (string) $cantidad)
                ->description('Cantidad de pedidos en el mes pasado')
                ->color('success'),
        ];
    }
}

