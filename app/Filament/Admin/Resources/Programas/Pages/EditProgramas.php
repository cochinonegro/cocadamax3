<?php

namespace App\Filament\Admin\Resources\Programas\Pages;

use App\Filament\Admin\Resources\Programas\ProgramasResource;
use App\Filament\Admin\Resources\Programas\Schemas\ProgramasForm;
use App\Filament\Concerns\PersistsProgramaWizardProgress;
use App\Filament\Support\ProgramaImageUpload;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProgramas extends EditRecord
{
    use PersistsProgramaWizardProgress;

    protected static string $resource = ProgramasResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->prepareProgramaPersistenceData($data);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data = ProgramasForm::hydrateOsCheckboxes($data);

        return ProgramaImageUpload::normalizeFormImagePaths($data);
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

            DeleteAction::make(),
        ];
    }
}
