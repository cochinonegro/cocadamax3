<?php

namespace App\Filament\Clientes\Resources\Programas\Pages;

use App\Filament\Clientes\Resources\Programas\ProgramasResource;
use App\Filament\Concerns\HasSolicitudPedidosCountdownModal;
use App\Filament\Support\ProgramaSolicitudTableColumn;
use App\Models\Programas;
use App\Support\ProgramaSolicitudSubmitter;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewProgramas extends ViewRecord
{
    use HasSolicitudPedidosCountdownModal;

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

    public function getTitle(): string|Htmlable
    {
        /** @var Programas $record */
        $record = $this->getRecord();

        return $record->progname;
    }

    public function solicitarPrograma(): void
    {
        if ($this->solicitarStatus() !== 'disponible') {
            return;
        }

        /** @var Programas $record */
        $record = $this->getRecord();

        if (! ProgramaSolicitudSubmitter::submit($record, notifyOnSuccess: false)) {
            return;
        }

        $this->abrirModalSolicitudPedidos();
    }

    public function solicitarStatus(): string
    {
        /** @var Programas $record */
        $record = $this->getRecord();

        return ProgramaSolicitudTableColumn::status($record);
    }
}
