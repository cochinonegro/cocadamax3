<?php

namespace App\Support;

use App\Models\Descarga;
use App\Models\Programas;
use Illuminate\Support\Facades\DB;

final class NumeroPedidoGenerator
{
    public static function next(): int
    {
        return DB::transaction(function (): int {
            $maxPrograma = (int) Programas::query()->lockForUpdate()->max('numero_pedido');
            $maxDescarga = (int) Descarga::query()->max('numero_pedido');

            return max($maxPrograma, $maxDescarga) + 1;
        });
    }

    public static function assignTo(Programas $programa): int
    {
        $numero = self::next();

        $programa->update(['numero_pedido' => $numero]);

        return $numero;
    }
}
