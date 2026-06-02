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
            "✅ <b>ACEPTADA</b>\n\nEl programa «{$programa->progname}» está visible en Pedidos durante {$minutes} minutos."
            .$this->formatPrecioAcordadoFooter($solicitud),
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
        if (preg_match('/^ps:(\d+):([ar])$/', $data, $matches)) {
            $this->handleAcceptRejectCallback($matches, $callbackQueryId);

            return;
        }

        if (preg_match('/^ps:(\d+):(\d+)$/', $data, $matches)) {
            $this->handlePrecioPresetCallback($matches, $callbackQueryId);

            return;
        }

        if (preg_match('/^ps:(\d+):o$/', $data, $matches)) {
            $this->handlePrecioOtroCallback($matches, $callbackQueryId);

            return;
        }

        $this->telegram->answerCallbackQuery($callbackQueryId, 'Acción no reconocida.');
    }

    /**
     * @param  array<int, string>  $matches
     */
    private function handleAcceptRejectCallback(array $matches, string $callbackQueryId): void
    {
        $solicitud = $this->findPendingSolicitud((int) $matches[1]);

        if (! $solicitud) {
            $this->telegram->answerCallbackQuery($callbackQueryId, 'Solicitud no encontrada o ya gestionada.');

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

    /**
     * @param  array<int, string>  $matches
     */
    private function handlePrecioPresetCallback(array $matches, string $callbackQueryId): void
    {
        $solicitud = $this->findPendingSolicitud((int) $matches[1]);

        if (! $solicitud) {
            $this->telegram->answerCallbackQuery($callbackQueryId, 'Solicitud no encontrada o ya gestionada.');

            return;
        }

        $precio = (float) $matches[2];

        if (! in_array($precio, [20.0, 25.0, 30.0], true)) {
            $this->telegram->answerCallbackQuery($callbackQueryId, 'Precio no válido.');

            return;
        }

        $solicitud->update(['precio_acordado' => $precio]);
        $this->refreshTelegramMessage($solicitud->fresh(['user', 'programa']));
        $this->telegram->answerCallbackQuery(
            $callbackQueryId,
            'Precio: '.$this->formatPrecio($precio),
        );
    }

    /**
     * @param  array<int, string>  $matches
     */
    private function handlePrecioOtroCallback(array $matches, string $callbackQueryId): void
    {
        $solicitud = $this->findPendingSolicitud((int) $matches[1]);

        if (! $solicitud) {
            $this->telegram->answerCallbackQuery($callbackQueryId, 'Solicitud no encontrada o ya gestionada.');

            return;
        }

        $this->telegram->answerCallbackQuery(
            $callbackQueryId,
            'Responde al mensaje de la solicitud con el monto (ej: 15 o 22.50).',
            true,
        );
    }

    private function findPendingSolicitud(int $id): ?ProgramaSolicitud
    {
        $solicitud = ProgramaSolicitud::query()
            ->with(['user', 'programa'])
            ->find($id);

        if (! $solicitud?->isPending()) {
            return null;
        }

        return $solicitud;
    }

    public function handleTelegramMessage(array $message): void
    {
        $chatId = (string) ($message['chat']['id'] ?? '');

        if ($chatId !== $this->telegram->adminChatId()) {
            return;
        }

        $text = trim((string) ($message['text'] ?? ''));
        $replyToMessageId = $message['reply_to_message']['message_id'] ?? null;

        if ($text === '' || $replyToMessageId === null) {
            return;
        }

        if (! preg_match('/^\d+(?:[.,]\d{1,2})?$/', str_replace(['€', ' '], '', $text))) {
            return;
        }

        $precio = (float) str_replace(',', '.', preg_replace('/[^\d.,]/', '', $text));

        $solicitud = ProgramaSolicitud::query()
            ->with(['user', 'programa'])
            ->where('telegram_message_id', $replyToMessageId)
            ->where('status', ProgramaSolicitudStatus::Pending)
            ->first();

        if (! $solicitud) {
            return;
        }

        $solicitud->update(['precio_acordado' => $precio]);
        $this->refreshTelegramMessage($solicitud->fresh(['user', 'programa']));

        $this->telegram->sendMessage(
            '✏️ Precio manual guardado: '.$this->formatPrecio($precio),
        );
    }

    public function latestForUserAndPrograma(User $user, Programas $programa): ?ProgramaSolicitud
    {
        return ProgramaSolicitud::query()
            ->where('user_id', $user->id)
            ->where('programas_id', $programa->id)
            ->latest('id')
            ->first();
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

        if (filled($solicitud->precio_acordado)) {
            $lines[] = '';
            $lines[] = '💰 <b>Precio acordado:</b> '.$this->formatPrecio((float) $solicitud->precio_acordado);
        }

        if ($solicitud->isPending()) {
            $lines[] = '';
            $lines[] = '<i>Elige un precio (20 / 25 / 30 € u Otro), luego Aceptar o Rechazar.</i>';
        }

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
                    ['text' => '20 €', 'callback_data' => "ps:{$id}:20"],
                    ['text' => '25 €', 'callback_data' => "ps:{$id}:25"],
                    ['text' => '30 €', 'callback_data' => "ps:{$id}:30"],
                ],
                [
                    ['text' => 'Otro: precio manual', 'callback_data' => "ps:{$id}:o"],
                ],
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

    private function refreshTelegramMessage(ProgramaSolicitud $solicitud): void
    {
        if (blank($solicitud->telegram_chat_id) || blank($solicitud->telegram_message_id)) {
            return;
        }

        $this->telegram->editMessageText(
            (string) $solicitud->telegram_chat_id,
            (int) $solicitud->telegram_message_id,
            $this->buildTelegramMessage($solicitud),
            $this->inlineKeyboard($solicitud),
        );
    }

    private function formatPrecioAcordadoFooter(ProgramaSolicitud $solicitud): string
    {
        if (blank($solicitud->precio_acordado)) {
            return '';
        }

        return "\n\n💰 <b>Precio acordado:</b> ".$this->formatPrecio((float) $solicitud->precio_acordado);
    }

    private function formatPrecio(float $precio): string
    {
        return number_format($precio, 2, ',', '.').' €';
    }
}
