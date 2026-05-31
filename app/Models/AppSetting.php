<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    public const PEDIDOS_GLOBAL_OFF = 'pedidos_global_off';

    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'key',
        'value',
    ];

    protected $casts = [
        'value' => 'boolean',
    ];

    public static function getBool(string $key, bool $default = false): bool
    {
        return (bool) (static::query()->find($key)?->value ?? $default);
    }

    public static function setBool(string $key, bool $value): void
    {
        static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value],
        );
    }
}
