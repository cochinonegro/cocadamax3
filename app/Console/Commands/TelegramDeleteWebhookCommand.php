<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TelegramDeleteWebhookCommand extends Command
{
    protected $signature = 'telegram:delete-webhook';

    protected $description = 'Elimina el webhook (útil para diagnosticar con telegram:test --discover)';

    public function handle(): int
    {
        $token = config('telegram.bot_token');

        if (blank($token)) {
            $this->components->error('TELEGRAM_BOT_TOKEN no configurado.');

            return self::FAILURE;
        }

        $response = Http::post("https://api.telegram.org/bot{$token}/deleteWebhook");

        $this->line($response->body());

        return $response->successful() ? self::SUCCESS : self::FAILURE;
    }
}
