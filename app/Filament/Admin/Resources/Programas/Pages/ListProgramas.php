<?php

namespace App\Filament\Admin\Resources\Programas\Pages;

use App\Filament\Admin\Resources\Programas\ProgramasResource;
use App\Filament\Concerns\HasInstallOffAction;
use App\Filament\Concerns\HasPedidosOffAction;
use App\Filament\Concerns\HasProgramasOsTabs;
use App\Filament\Concerns\PersistsTableColumnsForAuthenticatedUser;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProgramas extends ListRecords
{
    use HasInstallOffAction;
    use HasPedidosOffAction;
    use HasProgramasOsTabs;
    use PersistsTableColumnsForAuthenticatedUser;

    protected static string $resource = ProgramasResource::class;

    protected function shouldIncludeProgramasTodosTab(): bool
    {
        return true;
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->makeInstallOffAction(),
            $this->makePedidosOffAction(),
        ];
    }
}
