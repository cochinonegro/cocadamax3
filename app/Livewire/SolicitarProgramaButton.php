<?php

namespace App\Livewire;

use App\Models\Programas;
use App\Services\ProgramaSolicitudService;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class SolicitarProgramaButton extends Component
{
    public int $programaId;

    public string $variant = 'table';

    public function mount(int $programaId, string $variant = 'table'): void
    {
        $this->programaId = $programaId;
        $this->variant = $variant;
    }

    public function solicitar(ProgramaSolicitudService $solicitudes): void
    {
        $user = auth()->user();

        if (! $user) {
            Notification::make()
                ->title('Debes iniciar sesión')
                ->danger()
                ->send();

            return;
        }

        $programa = Programas::query()->active()->find($this->programaId);

        if (! $programa) {
            Notification::make()
                ->title('Programa no disponible')
                ->danger()
                ->send();

            return;
        }

        try {
            $solicitudes->submit($user, $programa);

            Notification::make()
                ->title('Solicitud enviada')
                ->body('Recibirás el programa en Pedidos cuando sea aceptada.')
                ->success()
                ->send();
        } catch (ValidationException $exception) {
            $message = collect($exception->errors())->flatten()->first()
                ?? 'No se pudo enviar la solicitud.';

            Notification::make()
                ->title($message)
                ->warning()
                ->send();
        }
    }

    public function statusLabel(ProgramaSolicitudService $solicitudes): string
    {
        $user = auth()->user();
        $programa = Programas::query()->find($this->programaId);

        if (! $user || ! $programa) {
            return 'disponible';
        }

        return $solicitudes->statusFor($user, $programa);
    }

    public function render(ProgramaSolicitudService $solicitudes)
    {
        return view('livewire.solicitar-programa-button', [
            'status' => $this->statusLabel($solicitudes),
        ]);
    }
}
