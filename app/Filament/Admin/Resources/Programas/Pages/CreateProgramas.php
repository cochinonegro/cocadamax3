<?php

namespace App\Filament\Admin\Resources\Programas\Pages;

use App\Filament\Admin\Resources\Programas\Schemas\ProgramasForm;
use App\Filament\Admin\Resources\Programas\ProgramasResource;
use App\Filament\Concerns\PersistsProgramaWizardProgress;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateProgramas extends CreateRecord
{
    use PersistsProgramaWizardProgress;

    protected static string $resource = ProgramasResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save_draft')
                ->label('Guardar borrador')
                ->color('gray')
                ->action(function (): void {
                    $this->persistProgramaWizardDraft();
                }),

            Action::make('create')
                ->label('CREAR')
                ->action(function (): void {
                    $this->create();
                })
                ->keyBindings(['mod+s']),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return ProgramasForm::stripVirtualOsFields(
            $this->prepareProgramaPersistenceData($data)
        );
    }
}
