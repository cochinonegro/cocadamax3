<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Programas extends Model
{
    protected $fillable = [
        'progname',
        'year_prog',
        'size',
        'os_required',
        'level_inst',
        'description',
        'working',
        'date_add',
        'program_id',
        'category',
        'content',
        'foto',
        'gallery_images',
        'installation_steps',
        'info_install',
        'foto_instalador',
        'url',
        'show',
        'show_until',
        'company',
        'web_oficial',
        'required',
        'idioma',
        'disk_name',
        'file_path',
    ];

    protected $casts = [
        'show'               => 'boolean',
        'show_until'         => 'datetime',
        'date_add'           => 'date',
        'gallery_images'     => 'array',
        'installation_steps' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $programa): void {
            if ($programa->show === false) {
                return;
            }

            $programa->show = true;
            $programa->show_until ??= now()->addYear();
        });

        static::saving(function (self $programa): void {
            if (! $programa->show) {
                return;
            }

            if (blank($programa->show_until) || $programa->show_until->isPast()) {
                $programa->show_until = now()->addYear();
            }
        });
    }

    public function isVisibleToClients(): bool
    {
        return (bool) $this->show;
    }

    /**
     * Programas con STATUS activo en admin (mismo criterio que la columna STATUS).
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('show', true);
    }
}
