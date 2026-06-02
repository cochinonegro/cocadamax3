<?php

namespace App\Filament\Admin\Resources\Pedidos\Pages;

use App\Filament\Admin\Resources\Pedidos\Widgets\PedidosTotalWidget;
use App\Filament\Admin\Resources\Pedidos\Widgets\PedidosVentasMesCantidadWidget;
use App\Filament\Admin\Resources\Pedidos\Widgets\PedidosVentasMesPasadoCantidadWidget;
use App\Filament\Admin\Resources\Pedidos\Widgets\PedidosVentasMesImporteWidget;
use App\Filament\Admin\Resources\Pedidos\PedidosResource;
use App\Filament\Concerns\HasPedidosOffAction;
use Filament\Resources\Pages\ListRecords;

class ListPedidos extends ListRecords
{
    use HasPedidosOffAction;

    protected static string $resource = PedidosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->makePedidosOffAction(),
        ];
    }

    public function getHeaderWidgets(): array
    {
        return [
            PedidosTotalWidget::class,
            PedidosVentasMesCantidadWidget::class,
            PedidosVentasMesPasadoCantidadWidget::class,
            PedidosVentasMesImporteWidget::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int | array
    {
        return 4;
    }

    public function getSubheading(): ?string
    {
        return 'Vista igual que la de los clientes. Aquí puedes quitar un programa para que deje de aparecer en Pedidos.';
    }
}
