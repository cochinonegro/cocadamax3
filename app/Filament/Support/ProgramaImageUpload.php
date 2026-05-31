<?php

namespace App\Filament\Support;

use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Flysystem\UnableToCheckFileExistence;
use Throwable;

class ProgramaImageUpload
{
    /** @var list<string> */
    public const MIME_TYPES = [
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/webp',
        'image/gif',
        'image/heic',
        'image/heif',
    ];

    public static function make(string $name, string $directory): FileUpload
    {
        return FileUpload::make($name)
            ->acceptedFileTypes(self::MIME_TYPES)
            ->maxSize(25600)
            ->disk('public')
            ->directory($directory)
            ->visibility('public')
            ->image()
            ->imagePreviewHeight('150')
            ->loadingIndicatorPosition('center')
            ->uploadProgressIndicatorPosition('right')
            ->getUploadedFileUsing(function (
                BaseFileUpload $component,
                string $file,
                string | array | null $storedFileNames,
            ): ?array {
                $directory = (string) $component->getDirectory();
                $file = self::normalizeStoredPath($file, $directory);

                if (blank($file)) {
                    return null;
                }

                $storage = $component->getDisk();

                try {
                    if (! $storage->exists($file)) {
                        return null;
                    }
                } catch (UnableToCheckFileExistence) {
                    return null;
                }

                try {
                    $size = $storage->size($file);
                    $type = $storage->mimeType($file) ?: self::mimeTypeFromPath($file);
                } catch (Throwable) {
                    $size = 0;
                    $type = self::mimeTypeFromPath($file);
                }

                return [
                    'name' => ($component->isMultiple()
                        ? ($storedFileNames[$file] ?? $storedFileNames[basename($file)] ?? null)
                        : $storedFileNames) ?? basename($file),
                    'size' => max($size, 1),
                    'type' => $type,
                    'url' => self::storageUrl($file, $directory),
                ];
            });
    }

    public static function gallery(): FileUpload
    {
        return self::make('gallery_images', 'programas/gallery')
            ->label('Imágenes')
            ->multiple()
            ->maxFiles(4)
            ->reorderable()
            ->columnSpanFull();
    }

    public static function installationStep(): FileUpload
    {
        return self::make('image', 'programas/instalacion')
            ->label('Foto del paso');
    }

    public static function installerPhoto(): FileUpload
    {
        return self::make('foto_instalador', 'programas/instalador')
            ->label('Foto del instalador');
    }

    public static function normalizeStoredPath(?string $path, string $directory): ?string
    {
        if (blank($path)) {
            return null;
        }

        $path = str_replace('\\', '/', trim($path));
        $path = ltrim($path, '/');

        if (Str::startsWith($path, 'storage/')) {
            $path = Str::after($path, 'storage/');
        }

        if (Str::contains($path, '/')) {
            return $path;
        }

        return trim($directory, '/').'/'.$path;
    }

    /**
     * @param  list<string|null>|null  $paths
     * @return list<string>
     */
    public static function normalizeStoredPaths(?array $paths, string $directory): array
    {
        if (! is_array($paths)) {
            return [];
        }

        return array_values(array_filter(array_map(
            fn (?string $path): ?string => self::normalizeStoredPath($path, $directory),
            $paths,
        )));
    }

    /**
     * @param  list<string|null>|null  $paths
     * @return list<string>
     */
    public static function existingStoredPaths(?array $paths, string $directory): array
    {
        return array_values(array_filter(
            self::normalizeStoredPaths($paths, $directory),
            static function (string $path): bool {
                if (str_starts_with($path, 'livewire-file:')) {
                    return false;
                }

                try {
                    return Storage::disk('public')->exists($path);
                } catch (Throwable) {
                    return false;
                }
            },
        ));
    }

    public static function storagePath(?string $path, string $directory): ?string
    {
        return self::normalizeStoredPath($path, $directory);
    }

    public static function storageUrl(?string $path, string $directory): ?string
    {
        $path = self::normalizeStoredPath($path, $directory);

        if (blank($path)) {
            return null;
        }

        return '/storage/'.ltrim($path, '/');
    }

    public static function publicUrl(?string $path, string $directory): ?string
    {
        return self::storageUrl($path, $directory);
    }

    public static function mimeTypeFromPath(string $path): ?string
    {
        return match (strtolower(pathinfo($path, PATHINFO_EXTENSION))) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            'gif' => 'image/gif',
            'heic' => 'image/heic',
            'heif' => 'image/heif',
            default => null,
        };
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function normalizeFormImagePaths(array $data): array
    {
        if (isset($data['gallery_images']) && is_array($data['gallery_images'])) {
            $data['gallery_images'] = self::existingStoredPaths($data['gallery_images'], 'programas/gallery');
        }

        if (filled($data['foto_instalador'] ?? null)) {
            $fotoInstalador = self::normalizeStoredPath(
                (string) $data['foto_instalador'],
                'programas/instalador',
            );

            $data['foto_instalador'] = filled($fotoInstalador)
                && Storage::disk('public')->exists($fotoInstalador)
                ? $fotoInstalador
                : null;
        }

        if (isset($data['installation_steps']) && is_array($data['installation_steps'])) {
            $data['installation_steps'] = array_values(array_map(function (mixed $step): array {
                $step = is_array($step) ? $step : [];

                if (filled($step['image'] ?? null)) {
                    $image = self::normalizeStoredPath(
                        (string) $step['image'],
                        'programas/instalacion',
                    );

                    $step['image'] = filled($image) && Storage::disk('public')->exists($image)
                        ? $image
                        : null;
                }

                return $step;
            }, $data['installation_steps']));
        }

        return $data;
    }
}
