<?php

namespace App\Filament\Clientes\Resources\Pedidos\Pages;

use App\Filament\Clientes\Resources\Pedidos\PedidosResource;
use Filament\Resources\Pages\ListRecords;

class ListPedidos extends ListRecords
{
    protected static string $resource = PedidosResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
