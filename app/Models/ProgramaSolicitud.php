<?php

namespace App\Models;

use App\Enums\ProgramaSolicitudStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgramaSolicitud extends Model
{
    protected $table = 'programa_solicitudes';

    protected $fillable = [
        'user_id',
        'programas_id',
        'status',
        'telegram_chat_id',
        'telegram_message_id',
        'responded_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => ProgramaSolicitudStatus::class,
            'responded_at' => 'datetime',
            'telegram_message_id' => 'integer',
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

    public function isPending(): bool
    {
        return $this->status === ProgramaSolicitudStatus::Pending;
    }
}
