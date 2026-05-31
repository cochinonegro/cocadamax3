<?php

namespace App\Filament\Admin\Resources\Programas\Pages;

use App\Filament\Admin\Resources\Programas\ProgramasResource;
use App\Filament\Concerns\NormalizesProgramaVisibility;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateProgramas extends CreateRecord
{
    use NormalizesProgramaVisibility;

    protected static string $resource = ProgramasResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->label('CREAR')
                ->action(fn (): void => $this->create())
                ->keyBindings(['mod+s']),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data = $this->normalizeProgramaVisibility($data);
        $data = $this->normalizeInstallationSteps($data);

        if (isset($data['gallery_images']) && is_array($data['gallery_images'])) {
            $data['gallery_images'] = array_values(array_filter($data['gallery_images']));
        }

        return $data;
    }
}
