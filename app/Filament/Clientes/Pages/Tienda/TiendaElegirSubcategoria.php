<?php

namespace App\Filament\Clientes\Pages\Tienda;

use App\Filament\Support\ProgramaCategories;
use App\Filament\Support\TiendaProgramas;
use Filament\Pages\Page;
use Livewire\Attributes\Url;

class TiendaElegirSubcategoria extends Page
{
    protected static ?string $slug = 'tienda/subcategorias';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'Elige subcategoría';

    protected string $view = 'filament.clientes.tienda.elegir-subcategoria';

    #[Url(as: 'os')]
    public ?string $os = null;

    #[Url(as: 'category')]
    public ?string $category = null;

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

        if (! ProgramaCategories::hasSubcategories($this->category)) {
            $this->redirect(TiendaCatalogo::getUrl([
                'os' => $this->os,
                'category' => $this->category,
            ]));
        }
    }

    public function getHeading(): string
    {
        return ProgramaCategories::label($this->category);
    }

    public function getSubheading(): ?string
    {
        return TiendaProgramas::osLabel((string) $this->os).' · Elige una subcategoría';
    }

    /** @return array<string, string> */
    public function getSubcategoriasProperty(): array
    {
        return ProgramaCategories::subcategoryOptions($this->category) ?? [];
    }

    public function elegirSubcategoria(string $working): void
    {
        if (! TiendaProgramas::isValidWorking($this->category, $working)) {
            return;
        }

        $this->redirect(TiendaCatalogo::getUrl([
            'os' => $this->os,
            'category' => $this->category,
            'working' => $working,
        ]));
    }
}
