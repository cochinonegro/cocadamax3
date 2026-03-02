<?php

namespace App\Filament\Admin\Resources\Clientes\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Admin\Resources\Clientes\ClientesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClientes extends ListRecords
{
    protected static string $resource = ClientesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
