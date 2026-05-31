<?php

namespace App\Filament\Clientes\Resources\Programas\Pages;

use App\Filament\Clientes\Resources\Programas\ProgramasResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewProgramas extends ViewRecord
{
    protected static string $resource = ProgramasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('descargar')
                ->label('Descargar')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->url(fn () => route('invitado.descarga', $this->getRecord()))
                ->openUrlInNewTab(),
        ];
    }
}
