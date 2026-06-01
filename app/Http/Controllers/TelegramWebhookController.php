<?php

namespace App\Http\Controllers;

use App\Services\ProgramaSolicitudService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class TelegramWebhookController extends Controller
{
    public function __invoke(Request $request, ProgramaSolicitudService $solicitudes): Response
    {
        $secret = config('telegram.webhook_secret');

        if (filled($secret) && $request->header('X-Telegram-Bot-Api-Secret-Token') !== $secret) {
            abort(403);
        }

        $update = $request->all();

        try {
            $callback = $update['callback_query'] ?? null;

            if (is_array($callback)) {
                $data = (string) ($callback['data'] ?? '');
                $callbackId = (string) ($callback['id'] ?? '');

                if ($data !== '' && $callbackId !== '') {
                    $solicitudes->handleCallback($data, $callbackId);
                }
            }
        } catch (\Throwable $exception) {
            Log::error('Telegram webhook error', [
                'message' => $exception->getMessage(),
                'update' => $update,
            ]);
        }

        return response()->noContent();
    }
}
