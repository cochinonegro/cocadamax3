<?php

namespace App\Filament\Admin\Resources\Programas\Pages;

use App\Filament\Admin\Resources\Programas\ProgramasResource;
use App\Filament\Concerns\NormalizesProgramaVisibility;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProgramas extends CreateRecord
{
    use NormalizesProgramaVisibility;

    protected static string $resource = ProgramasResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->normalizeProgramaVisibility($data);
    }
}
