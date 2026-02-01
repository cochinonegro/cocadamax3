<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Clientes;

class Venta extends Model
{
    protected $fillable = [
        'cliente_id',
        'producto',
        'fecha_venta',
        'informacion_adicional',
        'dia',
        'packv'
    ];


    /*
   public function cliente()
{
    return $this->belongsTo(Clientes::class, 'cliente_id');
}
*/
public function cliente()
{
    return $this->belongsTo(Clientes::class, 'cliente_id');
}

}

