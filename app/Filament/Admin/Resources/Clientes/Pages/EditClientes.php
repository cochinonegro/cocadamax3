<?php

namespace App\Filament\Admin\Resources\Clientes\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Admin\Resources\Clientes\ClientesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditClientes extends EditRecord
{
    protected static string $resource = ClientesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
