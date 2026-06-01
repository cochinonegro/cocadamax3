<?php

namespace App\Filament\Admin\Resources\Pedidos\Pages;

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

    public function getSubheading(): ?string
    {
        return 'Vista igual que la de los clientes. Aquí puedes quitar un programa para que deje de aparecer en Pedidos.';
    }
}
