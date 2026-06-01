<?php

namespace App\Console\Commands;

use App\Exceptions\TelegramException;
use App\Services\Telegram\TelegramBotService;
use Illuminate\Console\Command;

class TelegramTestCommand extends Command
{
    protected $signature = 'telegram:test {--discover : Mostrar chats recientes que escribieron al bot}';

    protected $description = 'Prueba el bot de Telegram (token, chat_id y envío de mensaje)';

    public function handle(TelegramBotService $telegram): int
    {
        if (! $telegram->isConfigured()) {
            $this->components->error('Telegram no está bien configurado en .env');
            $this->line('  Token presente: '.($telegram->botToken() !== '' ? 'sí' : 'no'));
            $this->line('  Formato token válido: '.($telegram->hasValidTokenFormat() ? 'sí' : 'no'));
            $this->line('  chat_id presente: '.($telegram->adminChatId() !== '' ? 'sí' : 'no'));
            $this->line('Tras cambiar Forge → SSH: php artisan config:clear && php artisan config:cache');

            return self::FAILURE;
        }

        $maskedToken = substr($telegram->botToken(), 0, 6).'…'.substr($telegram->botToken(), -4);
        $this->components->info('Configuración cargada:');
        $this->line("  token: {$maskedToken}");
        $this->line('  chat_id: '.$telegram->adminChatId());
        $this->newLine();

        if ($this->option('discover')) {
            return $this->discoverChats($telegram);
        }

        try {
            $me = $telegram->getMe();
            $username = $me['username'] ?? '?';
            $this->components->info("Bot OK: @{$username}");
        } catch (TelegramException $exception) {
            $this->components->error('Token inválido: '.$exception->getMessage());

            return self::FAILURE;
        }

        $this->line('Envía /start a tu bot en Telegram si aún no lo has hecho.');
        $this->newLine();

        try {
            $telegram->sendMessage(
                '✅ <b>Prueba CocadaMax</b>'."\n\nSi ves este mensaje, Telegram está bien configurado.",
                [
                    'inline_keyboard' => [
                        [
                            ['text' => '✅ Aceptar (prueba)', 'callback_data' => 'ps:0:a'],
                            ['text' => '❌ Rechazar (prueba)', 'callback_data' => 'ps:0:r'],
                        ],
                    ],
                ],
            );

            $this->components->success('Mensaje enviado correctamente a chat_id: '.$telegram->adminChatId());
            $this->line('Registra el webhook: php artisan telegram:set-webhook');

            return self::SUCCESS;
        } catch (TelegramException $exception) {
            $this->components->error($exception->getMessage());
            $this->newLine();
            $this->warn('El chat_id NO es tu teléfono (+34…). Obtén el ID así:');
            $this->line('  1. Abre tu bot en Telegram y pulsa /start');
            $this->line('  2. En el servidor: php artisan telegram:test --discover');
            $this->line('  3. Copia el id que aparece en TELEGRAM_ADMIN_CHAT_ID');
            $this->newLine();
            $this->line('O escribe a @userinfobot en Telegram y usa el número "Id".');

            return self::FAILURE;
        }
    }

    private function discoverChats(TelegramBotService $telegram): int
    {
        try {
            $telegram->getMe();
        } catch (TelegramException $exception) {
            $this->components->error('Token inválido: '.$exception->getMessage());

            return self::FAILURE;
        }

        $updates = $telegram->getUpdates();

        if ($updates === []) {
            $this->components->warn('No hay mensajes recientes en getUpdates.');
            $this->line('Si ya registraste el webhook, Telegram no guarda mensajes aquí.');
            $this->line('Usa @userinfobot en Telegram: el número "Id" es tu TELEGRAM_ADMIN_CHAT_ID.');
            $this->line('O temporalmente: php artisan telegram:delete-webhook y vuelve a /start al bot.');

            return self::FAILURE;
        }

        $this->components->info('Chats detectados (usa el id en TELEGRAM_ADMIN_CHAT_ID):');
        $this->newLine();

        $shown = [];

        foreach ($updates as $update) {
            $message = $update['message'] ?? $update['callback_query']['message'] ?? null;
            $from = $update['message']['from'] ?? $update['callback_query']['from'] ?? null;
            $chat = is_array($message) ? ($message['chat'] ?? null) : null;

            if (! is_array($chat)) {
                continue;
            }

            $chatId = (string) ($chat['id'] ?? '');

            if ($chatId === '' || isset($shown[$chatId])) {
                continue;
            }

            $shown[$chatId] = true;

            $name = trim(($from['first_name'] ?? '').' '.($from['last_name'] ?? ''));
            $username = filled($from['username'] ?? null) ? '@'.$from['username'] : 'sin @usuario';

            $this->line("  TELEGRAM_ADMIN_CHAT_ID={$chatId}");
            $this->line("    {$name} ({$username})");
            $this->newLine();
        }

        return self::SUCCESS;
    }
}
