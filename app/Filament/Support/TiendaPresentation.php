<?php

namespace App\Filament\Support;

class TiendaPresentation
{
    /** @return array{icon: string, tone: string} */
    public static function categoryMeta(string $key): array
    {
        return match ($key) {
            'aplicaciones' => ['icon' => 'heroicon-o-squares-2x2', 'tone' => 'sky'],
            'diseño grafico' => ['icon' => 'heroicon-o-paint-brush', 'tone' => 'pink'],
            'arquitectura' => ['icon' => 'heroicon-o-building-office-2', 'tone' => 'stone'],
            'music' => ['icon' => 'heroicon-o-musical-note', 'tone' => 'violet'],
            'video' => ['icon' => 'heroicon-o-film', 'tone' => 'rose'],
            'office-pdf' => ['icon' => 'heroicon-o-document-text', 'tone' => 'blue'],
            'kontakt' => ['icon' => 'heroicon-o-speaker-wave', 'tone' => 'amber'],
            default => ['icon' => 'heroicon-o-tag', 'tone' => 'slate'],
        };
    }

    /** @return array{icon: string, tone: string} */
    public static function subcategoryMeta(string $category, string $key): array
    {
        if ($category === 'music') {
            return match ($key) {
                'daw' => ['icon' => 'heroicon-o-adjustments-horizontal', 'tone' => 'violet'],
                'fx' => ['icon' => 'heroicon-o-sparkles', 'tone' => 'fuchsia'],
                'vst' => ['icon' => 'heroicon-o-cpu-chip', 'tone' => 'indigo'],
                'kontakt' => ['icon' => 'heroicon-o-speaker-wave', 'tone' => 'amber'],
                default => ['icon' => 'heroicon-o-ellipsis-horizontal-circle', 'tone' => 'slate'],
            };
        }

        if ($category === 'office-pdf') {
            return match ($key) {
                'office' => ['icon' => 'heroicon-o-briefcase', 'tone' => 'blue'],
                'pdf' => ['icon' => 'heroicon-o-document', 'tone' => 'red'],
                default => ['icon' => 'heroicon-o-tag', 'tone' => 'slate'],
            };
        }

        if ($category === 'aplicaciones') {
            return match ($key) {
                'internet' => ['icon' => 'heroicon-o-globe-alt', 'tone' => 'cyan'],
                'tools' => ['icon' => 'heroicon-o-wrench-screwdriver', 'tone' => 'orange'],
                'drivers' => ['icon' => 'heroicon-o-circle-stack', 'tone' => 'emerald'],
                'sist_op' => ['icon' => 'heroicon-o-computer-desktop', 'tone' => 'sky'],
                default => ['icon' => 'heroicon-o-ellipsis-horizontal-circle', 'tone' => 'slate'],
            };
        }

        if ($category === 'diseño grafico') {
            return match ($key) {
                'video' => ['icon' => 'heroicon-o-video-camera', 'tone' => 'rose'],
                'diseno' => ['icon' => 'heroicon-o-swatch', 'tone' => 'pink'],
                default => ['icon' => 'heroicon-o-photo', 'tone' => 'purple'],
            };
        }

        return ['icon' => 'heroicon-o-tag', 'tone' => 'slate'];
    }

    /** @return list<array{label: string, active: bool, done: bool}> */
    public static function steps(int $current): array
    {
        $labels = ['Sistema', 'Categoría', 'Catálogo'];

        return collect($labels)
            ->map(fn (string $label, int $index): array => [
                'label' => $label,
                'active' => ($index + 1) === $current,
                'done' => ($index + 1) < $current,
            ])
            ->values()
            ->all();
    }
}
