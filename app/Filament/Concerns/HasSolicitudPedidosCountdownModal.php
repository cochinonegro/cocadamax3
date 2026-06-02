<?php

namespace App\Filament\Concerns;

use App\Enums\ProgramaSolicitudStatus;
use App\Filament\Clientes\Resources\Pedidos\PedidosResource;
use App\Models\ProgramaSolicitud;
use App\Models\Programas;
use App\Services\ProgramaSolicitudService;
use App\Support\PrecioAcordadoPedido;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Livewire\Attributes\On;

trait HasSolicitudPedidosCountdownModal
{
    public bool $solicitudPedidosModalHabilitado = false;

    public ?int $solicitudProgramasId = null;

    #[On('solicitud-enviada')]
    public function abrirModalSolicitudPedidos(?int $programasId = null): void
    {
        $this->solicitudProgramasId = $programasId;
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
                        ->label('CONTINUAR')
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

                $solicitud = $this->resolveSolicitudModal();

                if ($solicitud?->status === ProgramaSolicitudStatus::Accepted) {
                    $this->replaceMountedAction('solicitudAceptada');

                    return;
                }

                if ($solicitud?->status === ProgramaSolicitudStatus::Rejected) {
                    Notification::make()
                        ->title('Solicitud rechazada')
                        ->body('El administrador no ha aceptado esta solicitud. Si tienes dudas, contacta con soporte.')
                        ->danger()
                        ->send();

                    return;
                }

                Notification::make()
                    ->title('Esperando confirmación')
                    ->body('El administrador aún no ha aceptado tu solicitud en Telegram. Vuelve a pulsar CONTINUAR cuando te hayan confirmado.')
                    ->warning()
                    ->send();
            });
    }

    public function solicitudAceptadaAction(): Action
    {
        return Action::make('solicitudAceptada')
            ->modalHeading('')
            ->modalContent(fn () => view('filament.clientes.modals.solicitud-aceptada-precio', [
                'precioFormateado' => $this->resolvePrecioAceptadoFormateado(),
            ]))
            ->modalSubmitActionLabel('Ir a Pedidos')
            ->modalCancelAction(false)
            ->closeModalByClickingAway(false)
            ->closeModalByEscaping(false)
            ->modalWidth('md')
            ->action(function (): void {
                $this->redirect(PedidosResource::getUrl(), navigate: true);
            });
    }

    protected function resolveSolicitudModal(): ?ProgramaSolicitud
    {
        $user = auth()->user();
        $programa = $this->resolveSolicitudPrograma();

        if ($user === null || $programa === null) {
            return null;
        }

        return app(ProgramaSolicitudService::class)->latestForUserAndPrograma($user, $programa);
    }

    protected function resolveSolicitudPrograma(): ?Programas
    {
        if ($this->solicitudProgramasId === null) {
            return null;
        }

        return Programas::query()->find($this->solicitudProgramasId);
    }

    protected function resolvePrecioAceptadoFormateado(): ?string
    {
        $solicitud = $this->resolveSolicitudModal();

        if ($solicitud === null || blank($solicitud->precio_acordado)) {
            return null;
        }

        return PrecioAcordadoPedido::format((float) $solicitud->precio_acordado);
    }
}
