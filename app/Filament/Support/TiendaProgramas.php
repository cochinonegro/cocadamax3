<?php

namespace App\Filament\Support;

use App\Models\Programas;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class TiendaProgramas
{
    /** @return list<string> */
    public static function osOptions(): array
    {
        return ['windows', 'mac'];
    }

    public static function osLabel(string $os): string
    {
        return match ($os) {
            'windows' => 'Windows',
            'mac' => 'Mac',
            default => $os,
        };
    }

    public static function isValidOs(?string $os): bool
    {
        return filled($os) && in_array($os, self::osOptions(), true);
    }

    public static function isValidCategory(?string $category): bool
    {
        return filled($category) && array_key_exists($category, ProgramaCategories::options());
    }

    public static function isValidWorking(?string $category, ?string $working): bool
    {
        if (! ProgramaCategories::hasSubcategories($category)) {
            return blank($working);
        }

        $options = ProgramaCategories::subcategoryOptions($category);

        return filled($working) && is_array($options) && array_key_exists($working, $options);
    }

    public static function query(?string $os, ?string $category = null, ?string $working = null): Builder
    {
        return Programas::query()
            ->active()
            ->when($os === 'windows', fn (Builder $query) => $query->whereIn('os_required', ['windows', 'win-mac']))
            ->when($os === 'mac', fn (Builder $query) => $query->whereIn('os_required', ['mac', 'win-mac']))
            ->when(filled($category), fn (Builder $query) => $query->where('category', $category))
            ->when(filled($working), fn (Builder $query) => $query->where('working', $working));
    }

    public static function coverUrl(Programas $programa): ?string
    {
        $images = $programa->gallery_images ?? [];

        if (! is_array($images) || blank($images[0] ?? null)) {
            return null;
        }

        return ProgramaImageUpload::publicUrl($images[0], 'programas/gallery');
    }

    public static function plainDescription(Programas $programa, int $limit = 160): ?string
    {
        if (blank($programa->description)) {
            return null;
        }

        $text = Str::of(strip_tags(Str::markdown($programa->description)))->squish();

        if ($text->isEmpty()) {
            return null;
        }

        return (string) $text->limit($limit);
    }
}
