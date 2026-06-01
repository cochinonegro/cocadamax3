<?php

namespace App\Services;

use App\Enums\ProgramaSolicitudStatus;
use App\Exceptions\TelegramException;
use App\Filament\Support\ProgramasTableColumns;
use App\Models\Programas;
use App\Models\ProgramaSolicitud;
use App\Models\User;
use App\Services\Telegram\TelegramBotService;
use App\Support\PedidosVisibility;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProgramaSolicitudService
{
    public function __construct(
        private readonly TelegramBotService $telegram,
    ) {}

    public function submit(User $user, Programas $programa): ProgramaSolicitud
    {
        if (! $this->telegram->isConfigured()) {
            throw ValidationException::withMessages([
                'solicitar' => 'Telegram no está configurado en el servidor (token o chat_id).',
            ]);
        }

        if (! $this->telegram->hasValidTokenFormat()) {
            throw ValidationException::withMessages([
                'solicitar' => 'TELEGRAM_BOT_TOKEN tiene un formato incorrecto. Genera uno nuevo en @BotFather.',
            ]);
        }

        if ($programa->isVisibleInPedidos()) {
            throw ValidationException::withMessages([
                'solicitar' => 'Este programa ya está disponible en Pedidos.',
            ]);
        }

        if ($this->hasPendingSolicitud($user, $programa)) {
            throw ValidationException::withMessages([
                'solicitar' => 'Ya tienes una solicitud pendiente para este programa.',
            ]);
        }

        try {
            return DB::transaction(function () use ($user, $programa): ProgramaSolicitud {
                $solicitud = ProgramaSolicitud::query()->create([
                    'user_id' => $user->id,
                    'programas_id' => $programa->id,
                    'status' => ProgramaSolicitudStatus::Pending,
                    'telegram_chat_id' => (string) config('telegram.admin_chat_id'),
                ]);

                $message = $this->telegram->sendMessage(
                    $this->buildTelegramMessage($solicitud),
                    $this->inlineKeyboard($solicitud),
                );

                $solicitud->update([
                    'telegram_message_id' => $message['message_id'] ?? null,
                ]);

                return $solicitud->fresh(['user', 'programa']);
            });
        } catch (TelegramException $exception) {
            throw ValidationException::withMessages([
                'solicitar' => $exception->getMessage(),
            ]);
        }
    }

    public function accept(ProgramaSolicitud $solicitud): void
    {
        if (! $solicitud->isPending()) {
            return;
        }

        $programa = $solicitud->programa;
        $minutes = (int) config('telegram.pedidos_minutes', 30);

        DB::transaction(function () use ($solicitud, $programa, $minutes): void {
            PedidosVisibility::enableForMinutes($programa, $minutes);

            $solicitud->update([
                'status' => ProgramaSolicitudStatus::Accepted,
                'responded_at' => now(),
            ]);
        });

        $solicitud->refresh();
        $programa = $solicitud->programa;

        $this->updateTelegramAfterResponse(
            $solicitud,
            "✅ <b>ACEPTADA</b>\n\nEl programa «{$programa->progname}» está visible en Pedidos durante {$minutes} minutos.",
        );
    }

    public function reject(ProgramaSolicitud $solicitud): void
    {
        if (! $solicitud->isPending()) {
            return;
        }

        $programa = $solicitud->programa;

        $solicitud->update([
            'status' => ProgramaSolicitudStatus::Rejected,
            'responded_at' => now(),
        ]);

        $this->updateTelegramAfterResponse(
            $solicitud,
            "❌ <b>RECHAZADA</b>\n\nEl programa «{$programa->progname}» no se activará en Pedidos.",
        );
    }

    public function handleCallback(string $data, string $callbackQueryId): void
    {
        if (! preg_match('/^ps:(\d+):(a|r)$/', $data, $matches)) {
            $this->telegram->answerCallbackQuery($callbackQueryId, 'Acción no reconocida.');

            return;
        }

        $solicitud = ProgramaSolicitud::query()
            ->with(['user', 'programa'])
            ->find((int) $matches[1]);

        if (! $solicitud) {
            $this->telegram->answerCallbackQuery($callbackQueryId, 'Solicitud no encontrada.');

            return;
        }

        if (! $solicitud->isPending()) {
            $this->telegram->answerCallbackQuery($callbackQueryId, 'Esta solicitud ya fue gestionada.');

            return;
        }

        if ($matches[2] === 'a') {
            $this->accept($solicitud);
            $this->telegram->answerCallbackQuery($callbackQueryId, 'Aceptada: visible en Pedidos.');

            return;
        }

        $this->reject($solicitud);
        $this->telegram->answerCallbackQuery($callbackQueryId, 'Rechazada.');
    }

    public function statusFor(User $user, Programas $programa): string
    {
        if ($programa->isVisibleInPedidos()) {
            return 'en_pedidos';
        }

        if ($this->hasPendingSolicitud($user, $programa)) {
            return 'pendiente';
        }

        $last = ProgramaSolicitud::query()
            ->where('user_id', $user->id)
            ->where('programas_id', $programa->id)
            ->latest('id')
            ->first();

        if ($last?->status === ProgramaSolicitudStatus::Rejected) {
            return 'rechazada';
        }

        return 'disponible';
    }

    public function hasPendingSolicitud(User $user, Programas $programa): bool
    {
        return ProgramaSolicitud::query()
            ->where('user_id', $user->id)
            ->where('programas_id', $programa->id)
            ->where('status', ProgramaSolicitudStatus::Pending)
            ->exists();
    }

    public function adminSolicitarStatus(Programas $programa): string
    {
        if ($programa->isPedidosTimerActive()) {
            return 'en_pedidos';
        }

        if ($this->hasPendingSolicitudesForProgram($programa)) {
            return 'pendiente';
        }

        return 'inactivo';
    }

    public function adminCycleSolicitarState(Programas $programa): string
    {
        match ($this->adminSolicitarStatus($programa)) {
            'inactivo' => PedidosVisibility::enableForMinutes(
                $programa,
                (int) config('telegram.pedidos_minutes', 30),
            ),
            'en_pedidos' => PedidosVisibility::disableFor($programa),
            'pendiente' => $this->acceptAllPendingForProgram($programa),
            default => null,
        };

        $programa->refresh();

        return $this->adminSolicitarStatus($programa);
    }

    public function hasPendingSolicitudesForProgram(Programas $programa): bool
    {
        return ProgramaSolicitud::query()
            ->where('programas_id', $programa->id)
            ->where('status', ProgramaSolicitudStatus::Pending)
            ->exists();
    }

    private function acceptAllPendingForProgram(Programas $programa): void
    {
        $pending = ProgramaSolicitud::query()
            ->where('programas_id', $programa->id)
            ->where('status', ProgramaSolicitudStatus::Pending)
            ->orderBy('id')
            ->get();

        if ($pending->isEmpty()) {
            PedidosVisibility::enableForMinutes(
                $programa,
                (int) config('telegram.pedidos_minutes', 30),
            );

            return;
        }

        foreach ($pending as $solicitud) {
            if ($solicitud->isPending()) {
                $this->accept($solicitud);
            }
        }
    }

    private function buildTelegramMessage(ProgramaSolicitud $solicitud): string
    {
        $user = $solicitud->user;
        $programa = $solicitud->programa;
        $os = ProgramasTableColumns::osRequiredLabel($programa->os_required);

        $lines = [
            '📥 <b>Nueva solicitud de programa</b>',
            '',
            '<b>Cliente:</b> '.e($user->name),
            '<b>Correo:</b> '.e($user->email ?? '—'),
            '<b>Teléfono:</b> '.e($user->phone ?? '—'),
            '',
            '<b>Programa:</b> '.e($programa->progname),
            '<b>ID:</b> '.$programa->id,
            '<b>Sistema:</b> '.e($os),
            '<b>Tamaño:</b> '.e($programa->size ?? '—'),
            '',
            '<i>Solicitud #'.$solicitud->id.'</i>',
        ];

        return implode("\n", $lines);
    }

    /**
     * @return array<string, mixed>
     */
    private function inlineKeyboard(ProgramaSolicitud $solicitud): array
    {
        $id = $solicitud->id;

        return [
            'inline_keyboard' => [
                [
                    ['text' => '✅ Aceptar', 'callback_data' => "ps:{$id}:a"],
                    ['text' => '❌ Rechazar', 'callback_data' => "ps:{$id}:r"],
                ],
            ],
        ];
    }

    private function updateTelegramAfterResponse(ProgramaSolicitud $solicitud, string $footer): void
    {
        if (blank($solicitud->telegram_chat_id) || blank($solicitud->telegram_message_id)) {
            return;
        }

        $solicitud->loadMissing(['user', 'programa']);

        $text = $this->buildTelegramMessage($solicitud->fresh(['user', 'programa']))
            ."\n\n".$footer;

        $this->telegram->editMessageText(
            (string) $solicitud->telegram_chat_id,
            (int) $solicitud->telegram_message_id,
            $text,
        );
    }
}
