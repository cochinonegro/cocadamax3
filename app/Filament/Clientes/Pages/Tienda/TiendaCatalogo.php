<?php

namespace App\Filament\Clientes\Pages\Tienda;

use App\Filament\Clientes\Resources\Programas\ProgramasResource;
use App\Filament\Support\ProgramaCategories;
use App\Filament\Support\TiendaProgramas;
use App\Models\Programas;
use Filament\Pages\Page;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class TiendaCatalogo extends Page
{
    use WithPagination;

    protected static ?string $slug = 'tienda/catalogo';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'Catálogo';

    protected string $view = 'filament.clientes.tienda.catalogo';

    #[Url(as: 'os')]
    public ?string $os = null;

    #[Url(as: 'category')]
    public ?string $category = null;

    #[Url(as: 'working')]
    public ?string $working = null;

    public function mount(): void
    {
        if (! TiendaProgramas::isValidOs($this->os)) {
            $this->redirect(TiendaElegirOs::getUrl());

            return;
        }

        if (! TiendaProgramas::isValidCategory($this->category)) {
            $this->redirect(TiendaElegirCategoria::getUrl(['os' => $this->os]));

            return;
        }

        if (ProgramaCategories::hasSubcategories($this->category) && ! TiendaProgramas::isValidWorking($this->category, $this->working)) {
            $this->redirect(TiendaElegirSubcategoria::getUrl([
                'os' => $this->os,
                'category' => $this->category,
            ]));
        }
    }

    public function getHeading(): string
    {
        return 'Catálogo de productos';
    }

    public function getSubheading(): ?string
    {
        return null;
    }

    public function catalogBadge(): string
    {
        $parts = [
            TiendaProgramas::osLabel((string) $this->os),
            ProgramaCategories::label($this->category),
        ];

        if (filled($this->working)) {
            $parts[] = ProgramaCategories::subcategoryLabel($this->category, $this->working);
        }

        return implode(' · ', $parts);
    }

    /** @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<int, Programas> */
    public function getProgramasProperty()
    {
        return TiendaProgramas::query($this->os, $this->category, $this->working)
            ->latest('id')
            ->paginate(20);
    }

    public function verMasUrl(Programas $programa): string
    {
        return ProgramasResource::getUrl('view', ['record' => $programa]);
    }
}
