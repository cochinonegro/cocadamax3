<?php

namespace App\Filament\Clientes\Resources\Programas\Pages;

use App\Filament\Clientes\Resources\Programas\ProgramasResource;
use App\Models\Programas;
use App\Services\ProgramaSolicitudService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Validation\ValidationException;

class ViewProgramas extends ViewRecord
{
    protected static string $resource = ProgramasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('solicitar')
                ->label('Solicitar')
                ->icon('heroicon-o-paper-airplane')
                ->color('warning')
                ->visible(fn (): bool => $this->solicitarStatus() === 'disponible')
                ->action(fn () => $this->submitSolicitud()),

            Action::make('solicitud_pendiente')
                ->label('Solicitud pendiente')
                ->icon('heroicon-o-clock')
                ->color('gray')
                ->disabled()
                ->visible(fn (): bool => $this->solicitarStatus() === 'pendiente'),

            Action::make('en_pedidos')
                ->label('Disponible en Pedidos')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->disabled()
                ->visible(fn (): bool => $this->solicitarStatus() === 'en_pedidos'),

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

    protected function solicitarStatus(): string
    {
        $user = auth()->user();

        if (! $user) {
            return 'disponible';
        }

        /** @var Programas $record */
        $record = $this->getRecord();

        return app(ProgramaSolicitudService::class)->statusFor($user, $record);
    }

    protected function submitSolicitud(): void
    {
        $user = auth()->user();

        if (! $user) {
            return;
        }

        /** @var Programas $record */
        $record = $this->getRecord();

        try {
            app(ProgramaSolicitudService::class)->submit($user, $record);

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
}
