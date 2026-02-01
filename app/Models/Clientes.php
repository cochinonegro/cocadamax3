<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Clientes extends Model
{
    protected $table = 'clientes';

    protected $fillable = [
        'name',
        'phone',
        'email',
        'required_prog',
        'os_required',
        'publicidad',
        'date',
        'category',

    ];
}
