<?php

namespace App\Filament\Admin\Resources\Programas\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Admin\Resources\Programas\ProgramasResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProgramas extends ListRecords
{
    protected static string $resource = ProgramasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
