<?php

namespace App\Filament\Admin\Resources\Descargas\Widgets;

use App\Support\DescargaVentas;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DescargasVentasSemanaWidget extends StatsOverviewWidget
{
    protected static bool $isDiscovered = false;

    protected ?string $pollingInterval = '10s';

    protected ?string $heading = 'VENTAS ESTA SEMANA';

    protected int|string|array $columnSpan = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Total', DescargaVentas::formatEuro(DescargaVentas::totalSemana()))
                ->description('Suma de precios registrados en Descargas')
                ->color('success'),
        ];
    }
}
