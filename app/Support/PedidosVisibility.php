<?php

namespace App\Support;

use App\Models\AppSetting;
use App\Models\Programas;

class PedidosVisibility
{
    public static function isGlobalOff(): bool
    {
        return AppSetting::getBool(AppSetting::PEDIDOS_GLOBAL_OFF);
    }

    public static function hideAll(): void
    {
        AppSetting::setBool(AppSetting::PEDIDOS_GLOBAL_OFF, true);

        Programas::query()->update([
            'pedidos_visible_until' => null,
            'numero_pedido' => null,
        ]);
    }

    public static function clearGlobalOff(): void
    {
        AppSetting::setBool(AppSetting::PEDIDOS_GLOBAL_OFF, false);
    }

    public static function enableForMinutes(Programas $programa, int $minutes = 30): void
    {
        self::clearGlobalOff();

        $numeroPedido = NumeroPedidoGenerator::next();

        $programa->update([
            'pedidos_visible_until' => now()->addMinutes($minutes),
            'numero_pedido' => $numeroPedido,
        ]);
    }

    public static function disableFor(Programas $programa): void
    {
        $programa->update([
            'pedidos_visible_until' => null,
            'numero_pedido' => null,
        ]);
    }
}
