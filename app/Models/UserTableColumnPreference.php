<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserTableColumnPreference extends Model
{
    protected $fillable = [
        'user_id',
        'table_key',
        'columns',
        'has_reordered_columns',
    ];

    protected function casts(): array
    {
        return [
            'columns' => 'array',
            'has_reordered_columns' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
