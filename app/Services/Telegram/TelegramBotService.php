<?php

namespace App\Services\Telegram;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class TelegramBotService
{
    public function isConfigured(): bool
    {
        return filled(config('telegram.bot_token'))
            && filled(config('telegram.admin_chat_id'));
    }

    public function sendMessage(string $text, ?array $replyMarkup = null): ?array
    {
        $payload = [
            'chat_id' => config('telegram.admin_chat_id'),
            'text' => $text,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => true,
        ];

        if ($replyMarkup !== null) {
            $payload['reply_markup'] = json_encode($replyMarkup, JSON_UNESCAPED_UNICODE);
        }

        $response = $this->post('sendMessage', $payload);

        return $response?->json('result');
    }

    public function editMessageText(string $chatId, int $messageId, string $text): void
    {
        $this->post('editMessageText', [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'text' => $text,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => true,
        ]);
    }

    public function answerCallbackQuery(string $callbackQueryId, string $text): void
    {
        $this->post('answerCallbackQuery', [
            'callback_query_id' => $callbackQueryId,
            'text' => $text,
            'show_alert' => mb_strlen($text) > 80,
        ]);
    }

    public function setWebhook(string $url, ?string $secretToken = null): ?array
    {
        $payload = [
            'url' => $url,
            'allowed_updates' => json_encode(['callback_query']),
        ];

        if (filled($secretToken)) {
            $payload['secret_token'] = $secretToken;
        }

        return $this->post('setWebhook', $payload)?->json('result');
    }

    private function post(string $method, array $payload): ?Response
    {
        $token = config('telegram.bot_token');

        if (blank($token)) {
            return null;
        }

        return Http::timeout(15)
            ->post("https://api.telegram.org/bot{$token}/{$method}", $payload);
    }
}
