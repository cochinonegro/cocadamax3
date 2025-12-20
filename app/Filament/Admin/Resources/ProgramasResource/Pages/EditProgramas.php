<?php

namespace App\Filament\Admin\Resources\ProgramasResource\Pages;

use App\Filament\Admin\Resources\ProgramasResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProgramas extends EditRecord
{
    protected static string $resource = ProgramasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
