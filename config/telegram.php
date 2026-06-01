<?php

if (! function_exists('telegram_env')) {
    /**
     * Normaliza variables .env (espacios, comillas, saltos de línea de Forge).
     */
    function telegram_env(string $key): ?string
    {
        $value = env($key);

        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        if ($value === '') {
            return null;
        }

        return trim($value, "\"'");
    }
}

return [

    /*
    |--------------------------------------------------------------------------
    | Bot de Telegram — solicitudes de programas
    |--------------------------------------------------------------------------
    |
    | 1. Crea un bot con @BotFather → /newbot y copia el token.
    | 2. Si el token falla: @BotFather → tu bot → API Token → Revoke y genera uno nuevo.
    | 3. TELEGRAM_ADMIN_CHAT_ID = Id de @userinfobot (NO tu teléfono +34…).
    | 4. En el servidor: php artisan config:clear && php artisan config:cache
    | 5. php artisan telegram:test
    |
    */

    'bot_token' => telegram_env('TELEGRAM_BOT_TOKEN'),

    'admin_chat_id' => telegram_env('TELEGRAM_ADMIN_CHAT_ID'),

    'webhook_secret' => telegram_env('TELEGRAM_WEBHOOK_SECRET'),

    'pedidos_minutes' => (int) env('TELEGRAM_PEDIDOS_MINUTES', 30),

];
