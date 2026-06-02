<?php

namespace App\Support;

use App\Models\Descarga;
use App\Models\Programas;
use App\Models\User;
use App\Support\PrecioAcordadoPedido;
use Illuminate\Support\Carbon;

class DescargaRegistrar
{
    public static function record(Programas $programa, ?User $user = null, ?Carbon $downloadedAt = null): Descarga
    {
        $user ??= auth()->user();

        return Descarga::query()->create([
            'user_id' => $user?->id,
            'programas_id' => $programa->getKey(),
            'numero_pedido' => $programa->numero_pedido,
            'precio' => PrecioAcordadoPedido::resolveForDescarga($programa, $user),
            'downloaded_at' => $downloadedAt ?? now(),
        ]);
    }
}
