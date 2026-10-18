<?php

namespace App\Filament\Clientes\Pages\Tienda;

use App\Filament\Support\ProgramaCategories;
use App\Filament\Support\TiendaProgramas;
use Filament\Pages\Page;
use Livewire\Attributes\Url;

class TiendaElegirCategoria extends Page
{
    protected static ?string $slug = 'tienda/categorias';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'Búsqueda de programa';

    protected string $view = 'filament.clientes.tienda.elegir-categoria';

    #[Url(as: 'os')]
    public ?string $os = null;

    public function mount(): void
    {
        if (! TiendaProgramas::isValidOs($this->os)) {
            $this->redirect(TiendaElegirOs::getUrl());
        }
    }

    public function getHeading(): string
    {
        // El título visible está en la sección «BÚSQUEDA DE PROGRAMA» de la vista.
        return '';
    }

    public function getSubheading(): ?string
    {
        return null;
    }

    /** @return array<string, string> */
    public function getCategoriasProperty(): array
    {
        return ProgramaCategories::options();
    }

    public function elegirCategoria(string $category): void
    {
        if (! TiendaProgramas::isValidCategory($category)) {
            return;
        }

        if (ProgramaCategories::hasSubcategories($category)) {
            $this->redirect(TiendaElegirSubcategoria::getUrl([
                'os' => $this->os,
                'category' => $category,
            ]));

            return;
        }

        $this->redirect(TiendaCatalogo::getUrl([
            'os' => $this->os,
            'category' => $category,
        ]));
    }

    public function elegirCategoriaConSub(string $category, string $working): void
    {
        if (! TiendaProgramas::isValidCategory($category)) {
            return;
        }

        if (! TiendaProgramas::isValidWorking($category, $working)) {
            return;
        }

        $this->redirect(TiendaCatalogo::getUrl([
            'os' => $this->os,
            'category' => $category,
            'working' => $working,
        ]));
    }
}
