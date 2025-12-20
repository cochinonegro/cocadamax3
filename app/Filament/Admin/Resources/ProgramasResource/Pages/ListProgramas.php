<?php

namespace App\Filament\Admin\Resources\ProgramasResource\Pages;

use App\Filament\Admin\Resources\ProgramasResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProgramas extends ListRecords
{
    protected static string $resource = ProgramasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
