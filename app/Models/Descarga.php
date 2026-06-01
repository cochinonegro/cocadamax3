<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Descarga extends Model
{
    protected $fillable = [
        'user_id',
        'programas_id',
        'downloaded_at',
    ];

    protected function casts(): array
    {
        return [
            'downloaded_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function programa(): BelongsTo
    {
        return $this->belongsTo(Programas::class, 'programas_id');
    }
}
