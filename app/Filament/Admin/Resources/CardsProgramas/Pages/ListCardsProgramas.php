<?php

namespace App\Filament\Admin\Resources\CardsProgramas\Pages;

use App\Filament\Admin\Resources\CardsProgramas\CardsProgramasResource;
use App\Models\Programas;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Livewire\WithPagination;

class ListCardsProgramas extends Page
{
    use WithPagination;

    protected static string $resource = CardsProgramasResource::class;

    protected string $view = 'filament.admin.resources.cards-programas.list';

    protected static ?string $title = 'Cards Programas';

    public ?string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<int, Programas>
     */
    public function getProgramasProperty()
    {
        return Programas::query()
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
}
