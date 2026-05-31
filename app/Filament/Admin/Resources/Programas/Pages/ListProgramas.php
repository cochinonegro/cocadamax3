<?php

namespace App\Filament\Admin\Resources\Programas\Pages;

use App\Filament\Admin\Resources\Programas\ProgramasResource;
use App\Filament\Concerns\HasInstallOffAction;
use App\Filament\Concerns\HasPedidosOffAction;
use App\Filament\Concerns\HasProgramasOsTabs;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProgramas extends ListRecords
{
    use HasInstallOffAction;
    use HasPedidosOffAction;
    use HasProgramasOsTabs;

    protected static string $resource = ProgramasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->makeInstallOffAction(),
            $this->makePedidosOffAction(),
        ];
    }
}
