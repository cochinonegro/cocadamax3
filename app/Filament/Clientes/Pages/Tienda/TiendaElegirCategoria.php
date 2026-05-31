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

    protected static ?string $title = 'Elige categoría';

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
        return '¿Qué categoría te interesa?';
    }

    public function getSubheading(): ?string
    {
        return 'Sistema: '.TiendaProgramas::osLabel((string) $this->os);
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
}
