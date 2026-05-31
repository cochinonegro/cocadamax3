<?php

namespace App\Filament\Clientes\Resources\Programas\Pages;

use App\Filament\Clientes\Pages\Tienda\TiendaElegirOs;
use App\Filament\Clientes\Resources\Programas\ProgramasResource;
use App\Filament\Concerns\HasProgramasOsTabs;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListProgramas extends ListRecords
{
    use HasProgramasOsTabs;

    protected static string $resource = ProgramasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('ver_tienda')
                ->label('VER TIENDA')
                ->icon('heroicon-o-building-storefront')
                ->color('primary')
                ->url(TiendaElegirOs::getUrl()),
        ];
    }
}
