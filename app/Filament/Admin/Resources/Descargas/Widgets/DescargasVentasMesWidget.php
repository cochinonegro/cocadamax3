<?php

namespace App\Filament\Admin\Resources\Descargas\Widgets;

use App\Support\DescargaVentas;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DescargasVentasMesWidget extends StatsOverviewWidget
{
    protected static bool $isDiscovered = false;

    protected ?string $pollingInterval = '10s';

    protected ?string $heading = 'VENTAS MES';

    protected int|string|array $columnSpan = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Total', DescargaVentas::formatEuro(DescargaVentas::totalMes()))
                ->description('Suma de precios registrados en Descargas')
                ->color('success'),
        ];
    }
}
