<?php

namespace App\Services;

use App\Exceptions\TelegramException;
use App\Filament\Support\ClienteFormatting;
use App\Models\Clientes;
use App\Services\Telegram\TelegramBotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VentaRapidaService
{
    public const PIN = '4053';

    private const SESSION_KEY = 'venta_rapida';

    private const VERIFIED_MINUTES = 20;

    private const MAX_ATTEMPTS = 5;

    public function __construct(private TelegramBotService $telegram) {}

    public function verifyPin(Request $request, string $pin): bool
    {
        $state = $this->state($request) ?? [
            'attempts' => 0,
            'verified_until' => null,
        ];

        if ((int) ($state['attempts'] ?? 0) >= self::MAX_ATTEMPTS) {
            return false;
        }

        $given = preg_replace('/\s+/', '', trim($pin)) ?? '';

        if (! hash_equals(self::PIN, $given)) {
            $state['attempts'] = (int) ($state['attempts'] ?? 0) + 1;
            $state['verified_until'] = null;
            $request->session()->put(self::SESSION_KEY, $state);

            return false;
        }

        $request->session()->put(self::SESSION_KEY, [
            'attempts' => 0,
            'verified_until' => now()->addMinutes(self::VERIFIED_MINUTES)->getTimestamp(),
        ]);

        return true;
    }

    public function isVerified(Request $request): bool
    {
        $state = $this->state($request);

        if ($state === null) {
            return false;
        }

        return (int) ($state['verified_until'] ?? 0) >= now()->getTimestamp();
    }

    /**
     * @param  array{name: string, phone: string, email: string, required_prog: string}  $data
     */
    public function register(Request $request, array $data): Clientes
    {
        $cliente = Clientes::query()->create([
            'name' => $data['name'],
            'phone' => ClienteFormatting::phoneDigits($data['phone']),
            'email' => $data['email'],
            'required_prog' => $data['required_prog'],
            'date' => now()->toDateString(),
            'publicidad' => 'Venta rápida',
            'observaciones' => 'Alta desde venta rápida en el portal.',
        ]);

        $request->session()->forget(self::SESSION_KEY);

        $this->notifyCompleted($cliente);

        return $cliente;
    }

    private function notifyCompleted(Clientes $cliente): void
    {
        $phone = $this->phoneWithDashes($cliente->phone);
        $text = implode("\n", [
            'Cliente '.$this->escape($cliente->name).' Tlf: '.$phone.', completó datos para venta rápida',
            'Correo: '.$this->escape($cliente->email ?? '—'),
            'Programa: '.$this->escape($cliente->required_prog ?? '—'),
        ]);

        try {
            $this->telegram->sendMessage($text);
        } catch (TelegramException $exception) {
            Log::error('Venta rápida: no se pudo avisar por Telegram', [
                'cliente_id' => $cliente->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    private function phoneWithDashes(?string $phone): string
    {
        $digits = ClienteFormatting::phoneDigits($phone);

        if ($digits === '') {
            return '—';
        }

        return implode('-', str_split($digits, 3));
    }

    private function escape(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * @return array<string, mixed>|null
     */
    private function state(Request $request): ?array
    {
        $state = $request->session()->get(self::SESSION_KEY);

        return is_array($state) ? $state : null;
    }
}
