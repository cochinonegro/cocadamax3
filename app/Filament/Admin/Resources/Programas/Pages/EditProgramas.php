<?php

namespace App\Filament\Admin\Resources\Programas\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Admin\Resources\Programas\ProgramasResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProgramas extends EditRecord
{
    protected static string $resource = ProgramasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
