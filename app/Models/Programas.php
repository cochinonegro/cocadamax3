<?php

namespace App\Models;

use App\Support\PedidosVisibility;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Programas extends Model
{
    use HasFactory;

    protected $fillable = [
        'progname',
        'year_prog',
        'size',
        'os_required',
        'level_inst',
        'description',
        'foto_descr1',
        'foto_descr2',
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
        'video_instalador',
        'show_instalador',
        'url',
        'product_price',
        'show',
        'show_until',
        'pedidos_visible_until',
        'numero_pedido',
        'company',
        'web_oficial',
        'required',
        'idioma',
        'disk_name',
        'file_path',
    ];

    protected $casts = [
        'show' => 'boolean',
        'show_instalador' => 'boolean',
        'product_price' => 'decimal:2',
        'show_until' => 'datetime',
        'pedidos_visible_until' => 'datetime',
        'date_add' => 'date',
        'gallery_images' => 'array',
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

    public function isInstaladorVisibleToClients(): bool
    {
        return (bool) $this->show_instalador;
    }

    public function isPedidosTimerActive(): bool
    {
        return filled($this->pedidos_visible_until)
            && $this->pedidos_visible_until->isFuture();
    }

    public function isVisibleInPedidos(): bool
    {
        if (PedidosVisibility::isGlobalOff()) {
            return false;
        }

        return $this->isPedidosTimerActive();
    }

    /**
     * Programas con STATUS activo en admin (mismo criterio que la columna STATUS).
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('show', true);
    }

    public function scopeVisibleInPedidos(Builder $query): Builder
    {
        if (PedidosVisibility::isGlobalOff()) {
            return $query->whereRaw('0 = 1');
        }

        return $query->where('pedidos_visible_until', '>', now());
    }

    public function normalizedOsRequired(): ?string
    {
        $os = strtolower(trim((string) $this->os_required));

        return in_array($os, ['windows', 'mac', 'win-mac'], true) ? $os : null;
    }

    public function swappedOsRequired(): ?string
    {
        return match ($this->normalizedOsRequired()) {
            'windows' => 'mac',
            'mac' => 'windows',
            default => null,
        };
    }

    public function duplicateWithSwappedOs(): self
    {
        $newOs = $this->swappedOsRequired();

        if ($newOs === null) {
            throw new \InvalidArgumentException('Solo se puede duplicar productos Windows o Mac.');
        }

        $duplicate = $this->replicate([
            'pedidos_visible_until',
            'numero_pedido',
        ]);

        $duplicate->os_required = $newOs;
        $duplicate->pedidos_visible_until = null;
        $duplicate->numero_pedido = null;
        $duplicate->save();

        return $duplicate->fresh();
    }

    public function descargas(): HasMany
    {
        return $this->hasMany(Descarga::class, 'programas_id');
    }

    public function duplicateAsCopy(string $suffix = '.copia'): self
    {
        $duplicate = $this->replicate([
            'pedidos_visible_until',
            'numero_pedido',
        ]);

        $maxLength = 255;
        $baseName = mb_substr((string) $this->progname, 0, $maxLength - mb_strlen($suffix));

        $duplicate->progname = $baseName.$suffix;
        $duplicate->pedidos_visible_until = null;
        $duplicate->numero_pedido = null;
        $duplicate->save();

        return $duplicate->fresh();
    }
}
