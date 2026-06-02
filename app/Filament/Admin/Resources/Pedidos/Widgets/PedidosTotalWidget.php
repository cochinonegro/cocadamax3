<?php

namespace App\Filament\Admin\Resources\Pedidos\Widgets;

use App\Models\Descarga;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PedidosTotalWidget extends StatsOverviewWidget
{
    protected static bool $isDiscovered = false;

    protected ?string $pollingInterval = '10s';

    protected ?string $heading = 'TOTAL PEDIDOS';

    protected int|string|array $columnSpan = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Total', (string) Descarga::query()->count())
                ->description('Cantidad de pedidos (descargas) en total')
                ->color('success'),
        ];
    }
}

