<?php

namespace App\Filament\Admin\Resources\Pedidos\Widgets;

use App\Support\DescargaVentas;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PedidosVentasMesImporteWidget extends StatsOverviewWidget
{
    protected static bool $isDiscovered = false;

    protected ?string $pollingInterval = '10s';

    protected ?string $heading = 'VENTAS DEL MES (€)';

    protected int|string|array $columnSpan = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Total', DescargaVentas::formatEuro(DescargaVentas::totalMes()))
                ->description('Importe total (€/precio) del mes actual')
                ->color('success'),
        ];
    }
}

