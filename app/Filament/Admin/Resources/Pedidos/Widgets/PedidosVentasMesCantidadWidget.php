<?php

namespace App\Filament\Admin\Resources\Pedidos\Widgets;

use App\Models\Descarga;
use App\Support\DisplayTimezone;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PedidosVentasMesCantidadWidget extends StatsOverviewWidget
{
    protected static bool $isDiscovered = false;

    protected ?string $pollingInterval = '10s';

    protected ?string $heading = 'VENTAS DEL MES';

    protected int|string|array $columnSpan = 1;

    protected function getStats(): array
    {
        $inicio = now(DisplayTimezone::name())->startOfMonth();
        $fin = $inicio->copy()->endOfMonth();

        $cantidad = Descarga::query()
            ->whereBetween('downloaded_at', [$inicio, $fin])
            ->count();

        return [
            Stat::make('Total', (string) $cantidad)
                ->description('Cantidad de pedidos en el mes actual')
                ->color('success'),
        ];
    }
}

