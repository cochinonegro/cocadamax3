<?php

namespace App\Filament\Admin\Resources\CardsProgramas\Pages;

use App\Filament\Admin\Resources\CardsProgramas\CardsProgramasResource;
use App\Filament\Concerns\HasInstallOffAction;
use App\Models\Programas;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Livewire\WithPagination;

class ListCardsProgramas extends Page
{
    use HasInstallOffAction;
    use WithPagination;

    protected static string $resource = CardsProgramasResource::class;

    protected string $view = 'filament.admin.resources.cards-programas.list';

    protected static ?string $title = 'Cards Programas';

    protected function getHeaderActions(): array
    {
        return [
            $this->makeInstallOffAction(),
        ];
    }

    public ?string $search = '';

    public string $osFilter = 'windows';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function setOsFilter(string $os): void
    {
        if (! in_array($os, ['windows', 'mac'], true)) {
            return;
        }

        $this->osFilter = $os;
        $this->resetPage();
    }

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<int, Programas>
     */
    public function getProgramasProperty()
    {
        return Programas::query()
            ->when($this->osFilter === 'windows', fn ($query) => $query->whereIn('os_required', ['windows', 'win-mac']))
            ->when($this->osFilter === 'mac', fn ($query) => $query->whereIn('os_required', ['mac', 'win-mac']))
            ->when(filled($this->search), function ($query) {
                $term = '%'.$this->search.'%';

                $query->where(function ($q) use ($term) {
                    $q->where('progname', 'like', $term)
                        ->orWhere('company', 'like', $term)
                        ->orWhere('category', 'like', $term)
                        ->orWhere('id', 'like', $term);
                });
            })
            ->latest('id')
            ->paginate(24);
    }

    public function notifyLinkCopied(): void
    {
        Notification::make()
            ->title('Enlace copiado')
            ->body('El link de descarga se copió al portapapeles.')
            ->success()
            ->send();
    }

    public function notifyNoLink(): void
    {
        Notification::make()
            ->title('Sin enlace externo')
            ->body('Este programa no tiene link de descarga configurado.')
            ->warning()
            ->send();
    }

    public function deletePrograma(int $id): void
    {
        $programa = Programas::query()->findOrFail($id);
        $nombre = $programa->progname;
        $programa->delete();

        Notification::make()
            ->title('Programa eliminado')
            ->body("«{$nombre}» se eliminó correctamente.")
            ->success()
            ->send();
    }
}
