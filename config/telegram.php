<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Bot de Telegram — solicitudes de programas
    |--------------------------------------------------------------------------
    |
    | 1. Crea un bot con @BotFather y copia el token.
    | 2. Escribe a tu bot y obtén tu chat_id (o usa @userinfobot).
    | 3. Tras el deploy: php artisan telegram:set-webhook
    |
    */

    'bot_token' => env('TELEGRAM_BOT_TOKEN'),

    // ID numérico del chat (NO el teléfono). Obtenerlo con @userinfobot o: php artisan telegram:test --discover
    'admin_chat_id' => env('TELEGRAM_ADMIN_CHAT_ID'),

    'webhook_secret' => env('TELEGRAM_WEBHOOK_SECRET'),

    'pedidos_minutes' => (int) env('TELEGRAM_PEDIDOS_MINUTES', 30),

];
