<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Widgets\QuickAccessWidget;
use App\Filament\Admin\Widgets\UserInfoWidget;
use App\Filament\Admin\Widgets\VentasMesPasadoChart;
use App\Filament\Admin\Widgets\VentasUltimos10DiasChart;
use App\Filament\Admin\Resources\Pedidos\Widgets\PedidosTotalWidget;
use App\Filament\Admin\Resources\Pedidos\Widgets\PedidosVentasMesCantidadWidget;
use App\Filament\Admin\Resources\Pedidos\Widgets\PedidosVentasMesPasadoCantidadWidget;
use App\Filament\Admin\Resources\Pedidos\Widgets\PedidosVentasMesImporteWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'Escritorio';

    protected static ?string $title = 'Escritorio';

    protected static ?int $navigationSort = 1;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home';

    public function getWidgets(): array
    {
        return [
            UserInfoWidget::class,
            QuickAccessWidget::class,
            VentasUltimos10DiasChart::class,
            VentasMesPasadoChart::class,
            PedidosTotalWidget::class,
            PedidosVentasMesCantidadWidget::class,
            PedidosVentasMesPasadoCantidadWidget::class,
            PedidosVentasMesImporteWidget::class,
        ];
    }

    public function getColumns(): int|array
    {
        return [
            'default' => 1,
            'md' => 2,
            'lg' => 4,
        ];
    }
}
