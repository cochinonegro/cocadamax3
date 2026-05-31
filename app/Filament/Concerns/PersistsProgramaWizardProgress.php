<?php

namespace App\Filament\Concerns;

use App\Filament\Admin\Resources\Programas\Schemas\ProgramasForm;
use App\Filament\Support\ProgramaImageUpload;
use App\Filament\Support\ProgramaCategories;
use App\Filament\Support\ProgramasTableColumns;
use App\Models\Programas;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;

trait PersistsProgramaWizardProgress
{
    use NormalizesProgramaVisibility;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function prepareProgramaPersistenceData(array $data): array
    {
        $data = $this->normalizeProgramaVisibility($data);
        $data = $this->normalizeInstallationSteps($data);
        $data = ProgramaImageUpload::normalizeFormImagePaths($data);

        if (isset($data['gallery_images']) && is_array($data['gallery_images'])) {
            $data['gallery_images'] = array_values(array_filter($data['gallery_images']));
        }

        if (blank($data['working'] ?? null) && ! ProgramaCategories::hasSubcategories($data['category'] ?? null)) {
            $data['working'] = null;
        }

        if (filled($data['url'] ?? null)) {
            $data['url'] = ProgramasTableColumns::downloadUrl($data['url']);
        }

        return ProgramasForm::stripVirtualOsFields($data);
    }

    public function persistProgramaWizardStep(): void
    {
        $this->form->callBeforeStateDehydrated();

        /** @var array<string, mixed> $state */
        $state = $this->form->getState(shouldCallHooksBefore: false);

        if ($this instanceof CreateRecord && ! $this->record?->exists) {
            $data = $this->prepareProgramaPersistenceData($state);
            $record = Programas::query()->create($data);

            Notification::make()
                ->title('Paso guardado')
                ->body('El programa quedó registrado. Puedes continuar más tarde desde editar.')
                ->success()
                ->duration(3500)
                ->send();

            $url = static::getResource()::getUrl('edit', ['record' => $record]);
            $url .= '?paso='.$this->nextWizardStepKey();

            $this->redirect($url, navigate: true);

            return;
        }

        if ($this instanceof EditRecord && $this->record instanceof Programas) {
            $data = $this->prepareProgramaPersistenceData($state);
            $this->record->update($data);
            $this->form->model($this->record)->saveRelationships();
            $this->record->refresh();
            $this->fillForm();

            Notification::make()
                ->title('Paso guardado')
                ->success()
                ->duration(2500)
                ->send();
        }
    }

    public function persistProgramaWizardDraft(): void
    {
        if (blank($this->form->getRawState()['progname'] ?? null)) {
            Notification::make()
                ->title('Indica al menos el nombre del programa')
                ->warning()
                ->send();

            return;
        }

        $this->persistProgramaWizardStep();
    }

    protected function nextWizardStepKey(): string
    {
        return match (request()->query('paso')) {
            'descripcion' => 'galeria',
            'galeria' => 'instalacion',
            default => 'descripcion',
        };
    }
}
