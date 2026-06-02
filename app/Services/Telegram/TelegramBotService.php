<?php

namespace App\Services\Telegram;

use App\Exceptions\TelegramException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramBotService
{
    public function isConfigured(): bool
    {
        return $this->hasValidTokenFormat()
            && filled($this->adminChatId());
    }

    public function hasValidTokenFormat(): bool
    {
        $token = $this->botToken();

        return filled($token) && (bool) preg_match('/^\d+:[A-Za-z0-9_-]{20,}$/', $token);
    }

    public function botToken(): string
    {
        return trim((string) config('telegram.bot_token'));
    }

    public function adminChatId(): string
    {
        return trim((string) config('telegram.admin_chat_id'));
    }

    /**
     * @return array<string, mixed>
     */
    public function getMe(): array
    {
        return $this->request('getMe');
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getUpdates(): array
    {
        $result = $this->request('getUpdates', [
            'limit' => 10,
            'allowed_updates' => json_encode(['message', 'callback_query']),
        ]);

        return is_array($result) ? $result : [];
    }

    /**
     * @return array<string, mixed>
     */
    public function sendMessage(string $text, ?array $replyMarkup = null): array
    {
        $payload = [
            'chat_id' => $this->adminChatId(),
            'text' => $text,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => true,
        ];

        if ($replyMarkup !== null) {
            $payload['reply_markup'] = json_encode($replyMarkup, JSON_UNESCAPED_UNICODE);
        }

        $result = $this->request('sendMessage', $payload);

        if (! is_array($result)) {
            throw new TelegramException('Telegram no devolvió un mensaje válido.');
        }

        return $result;
    }

    public function editMessageText(string $chatId, int $messageId, string $text, ?array $replyMarkup = null): void
    {
        $payload = [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'text' => $text,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => true,
        ];

        if ($replyMarkup !== null) {
            $payload['reply_markup'] = json_encode($replyMarkup, JSON_UNESCAPED_UNICODE);
        }

        $this->request('editMessageText', $payload);
    }

    public function answerCallbackQuery(string $callbackQueryId, string $text, bool $showAlert = false): void
    {
        $this->request('answerCallbackQuery', [
            'callback_query_id' => $callbackQueryId,
            'text' => $text,
            'show_alert' => $showAlert || mb_strlen($text) > 80,
        ]);
    }

    public function setWebhook(string $url, ?string $secretToken = null): ?array
    {
        $payload = [
            'url' => $url,
            'allowed_updates' => json_encode(['callback_query', 'message']),
        ];

        if (filled($secretToken)) {
            $payload['secret_token'] = $secretToken;
        }

        return $this->request('setWebhook', $payload);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>|null
     */
    private function request(string $method, array $payload = []): ?array
    {
        $response = $this->post($method, $payload);

        if ($response === null) {
            throw new TelegramException('TELEGRAM_BOT_TOKEN no está configurado.');
        }

        $body = $response->json();

        if (! $response->successful() || ! ($body['ok'] ?? false)) {
            $description = (string) ($body['description'] ?? $response->body());

            Log::error('Telegram API error', [
                'method' => $method,
                'status' => $response->status(),
                'description' => $description,
                'chat_id' => $payload['chat_id'] ?? null,
            ]);

            throw new TelegramException($this->humanizeError($description));
        }

        $result = $body['result'] ?? null;

        return is_array($result) ? $result : null;
    }

    private function post(string $method, array $payload = []): ?Response
    {
        $token = $this->botToken();

        if (blank($token)) {
            return null;
        }

        return Http::timeout(15)
            ->post("https://api.telegram.org/bot{$token}/{$method}", $payload);
    }

    private function humanizeError(string $description): string
    {
        return match (true) {
            str_contains($description, 'chat not found') => 'Chat de Telegram no encontrado. TELEGRAM_ADMIN_CHAT_ID debe ser el Id de @userinfobot (no tu teléfono). Pulsa /start en tu bot.',
            str_contains($description, 'bot was blocked') => 'Has bloqueado el bot. Desbloquéalo en Telegram y pulsa /start.',
            str_contains($description, 'Unauthorized') => 'Token del bot inválido o revocado. En @BotFather: tu bot → API Token → Revoke → copia el token nuevo en Forge, luego en el servidor ejecuta: php artisan config:clear && php artisan config:cache && php artisan telegram:test',
            default => 'Telegram: '.$description,
        };
    }
}
