<?php

namespace App\Filament\Clientes\Pages\Tienda;

use App\Filament\Clientes\Pages\Tienda\TiendaElegirCategoria;
use Filament\Pages\Page;

class TiendaElegirOs extends Page
{
    protected static ?string $slug = 'tienda';

    protected static ?string $title = 'Ver tienda';

    protected static ?string $navigationLabel = 'Ver tienda';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?int $navigationSort = 0;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-building-storefront';

    protected string $view = 'filament.clientes.tienda.elegir-os';

    public function getHeading(): string
    {
        return '¿Qué sistema operativo usas?';
    }

    public function elegirOs(string $os): void
    {
        $this->redirect(TiendaElegirCategoria::getUrl(['os' => $os]));
    }
}
