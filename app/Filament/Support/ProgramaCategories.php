<?php

namespace App\Filament\Support;

class ProgramaCategories
{
    /** @return array<string, string> */
    public static function options(): array
    {
        return [
            'aplicaciones' => 'Aplicaciones',
            'diseño grafico' => 'Diseño gráfico',
            'arquitectura' => 'Arquitectura',
            'music' => 'Audio',
            'video' => 'Video',
            'office-pdf' => 'Office-pdf',
            'kontakt' => 'Kontakt',
        ];
    }

    public static function label(?string $category): string
    {
        if (blank($category)) {
            return '-';
        }

        return self::options()[$category] ?? ucfirst($category);
    }

    /** @return list<string> */
    public static function withSubcategories(): array
    {
        return ['music', 'office-pdf', 'aplicaciones', 'diseño grafico'];
    }

    public static function hasSubcategories(?string $category): bool
    {
        return filled($category) && in_array($category, self::withSubcategories(), true);
    }

    /** @return array<string, string>|null */
    public static function subcategoryOptions(?string $category): ?array
    {
        return match ($category) {
            'music' => [
                'daw' => 'DAW',
                'fx' => 'FX',
                'vst' => 'VST',
                'kontakt' => 'KONTAKT',
                'otro' => 'OTRO',
            ],
            'office-pdf' => [
                'office' => 'OFFICE',
                'pdf' => 'PDF',
            ],
            'aplicaciones' => [
                'internet' => 'Internet',
                'tools' => 'Tools',
                'drivers' => 'Drivers',
                'sist_op' => 'Sist.Op',
                'otros' => 'Otros',
            ],
            'diseño grafico' => [
                'video' => 'Video',
                'diseno' => 'Diseño',
                'otros' => 'Otros',
            ],
            default => null,
        };
    }

    public static function subcategoryLabel(?string $category, ?string $working): string
    {
        if (blank($working)) {
            return '-';
        }

        $options = self::subcategoryOptions($category);

        if ($options !== null && isset($options[$working])) {
            return $options[$working];
        }

        return $working;
    }
}
