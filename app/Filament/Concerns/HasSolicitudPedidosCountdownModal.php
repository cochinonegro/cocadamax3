<?php

namespace App\Filament\Concerns;

use App\Filament\Clientes\Resources\Pedidos\PedidosResource;
use Filament\Actions\Action;
use Livewire\Attributes\On;

trait HasSolicitudPedidosCountdownModal
{
    public bool $solicitudPedidosModalHabilitado = false;

    #[On('solicitud-enviada')]
    public function abrirModalSolicitudPedidos(): void
    {
        $this->solicitudPedidosModalHabilitado = false;
        $this->mountAction('solicitudSolicitada');
    }

    public function habilitarBotonPedidosModal(): void
    {
        $this->solicitudPedidosModalHabilitado = true;
    }

    public function solicitudSolicitadaAction(): Action
    {
        return Action::make('solicitudSolicitada')
            ->modalHeading('Solicitud enviada, ESPERA LA CONFIRMACIÓN')
            ->modalContent(fn () => view('filament.clientes.modals.solicitud-pedidos-countdown'))
            ->modalSubmitAction(
                fn (Action $action): Action|bool => $this->solicitudPedidosModalHabilitado
                    ? $action
                        ->label('DESCARGAR')
                        ->color('success')
                    : false,
            )
            ->modalCancelAction(false)
            ->closeModalByClickingAway(false)
            ->closeModalByEscaping(false)
            ->modalWidth('md')
            ->action(function (): void {
                if (! $this->solicitudPedidosModalHabilitado) {
                    return;
                }

                $this->redirect(PedidosResource::getUrl(), navigate: true);
            });
    }
}
