<?php

namespace App\Filament\Admin\Resources\Programas\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Admin\Resources\Programas\ProgramasResource;
use App\Filament\Concerns\NormalizesProgramaVisibility;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProgramas extends EditRecord
{
    use NormalizesProgramaVisibility;

    protected static string $resource = ProgramasResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->normalizeProgramaVisibility($data);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
