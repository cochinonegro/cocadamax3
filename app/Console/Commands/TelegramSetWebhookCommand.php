<?php

namespace App\Console\Commands;

use App\Services\Telegram\TelegramBotService;
use Illuminate\Console\Command;

class TelegramSetWebhookCommand extends Command
{
    protected $signature = 'telegram:set-webhook {--url= : URL pública del webhook}';

    protected $description = 'Registra el webhook de Telegram para aceptar/rechazar solicitudes';

    public function handle(TelegramBotService $telegram): int
    {
        if (! $telegram->isConfigured()) {
            $this->components->error('Configura TELEGRAM_BOT_TOKEN y TELEGRAM_ADMIN_CHAT_ID en .env');

            return self::FAILURE;
        }

        $url = $this->option('url') ?: rtrim((string) config('app.url'), '/').'/telegram/webhook';

        $result = $telegram->setWebhook($url, config('telegram.webhook_secret'));

        $this->components->info("Webhook registrado: {$url}");
        $this->line(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return self::SUCCESS;
    }
}
