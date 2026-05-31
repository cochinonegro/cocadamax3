<?php

namespace App\Filament\Support;

use Filament\Forms\Components\FileUpload;

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
            ->fetchFileInformation(false)
            ->disk('public')
            ->directory($directory)
            ->visibility('public')
            ->imagePreviewHeight('150')
            ->loadingIndicatorPosition('center')
            ->uploadProgressIndicatorPosition('right');
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
            ->label('Imagen del instalador');
    }
}
