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

    /**
     * Solo programas activos (encendidos y no vencidos)
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->where('show', true)
            ->whereNotNull('show_until')
            ->where('show_until', '>=', now());
    }
}
