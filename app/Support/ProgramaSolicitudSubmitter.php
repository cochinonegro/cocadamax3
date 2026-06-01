<?php

namespace App\Support;

use App\Models\Programas;
use App\Models\User;
use App\Services\ProgramaSolicitudService;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;

class ProgramaSolicitudSubmitter
{
    public static function submit(Programas $programa, ?User $user = null, bool $notifyOnSuccess = true): bool
    {
        $user ??= auth()->user();

        if (! $user) {
            Notification::make()
                ->title('Debes iniciar sesión')
                ->danger()
                ->send();

            return false;
        }

        $programa = Programas::query()->active()->find($programa->getKey());

        if (! $programa) {
            Notification::make()
                ->title('Programa no disponible')
                ->danger()
                ->send();

            return false;
        }

        try {
            app(ProgramaSolicitudService::class)->submit($user, $programa);

            if ($notifyOnSuccess) {
                Notification::make()
                    ->title('Solicitud enviada')
                    ->body('Recibirás el programa en Pedidos cuando sea aceptada.')
                    ->success()
                    ->send();
            }

            return true;
        } catch (ValidationException $exception) {
            $message = collect($exception->errors())->flatten()->first()
                ?? 'No se pudo enviar la solicitud.';

            Notification::make()
                ->title($message)
                ->warning()
                ->send();

            return false;
        }
    }
}
