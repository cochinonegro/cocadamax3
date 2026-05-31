<?php

namespace App\Filament\Clientes\Resources\Programas\Pages;

use App\Filament\Clientes\Resources\Programas\ProgramasResource;
use Filament\Resources\Pages\ViewRecord;

class ViewProgramas extends ViewRecord
{
    protected static string $resource = ProgramasResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
