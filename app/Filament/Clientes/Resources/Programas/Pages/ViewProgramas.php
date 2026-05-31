<?php

namespace App\Filament\Clientes\Resources\Programas\Pages;

use App\Filament\Clientes\Resources\Programas\ProgramasResource;
use App\Models\Programas;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewProgramas extends ViewRecord
{
    protected static string $resource = ProgramasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Volver al listado')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(ProgramasResource::getUrl()),
        ];
    }

    public function getTitle(): string | Htmlable
    {
        /** @var Programas $record */
        $record = $this->getRecord();

        return $record->progname;
    }
}
