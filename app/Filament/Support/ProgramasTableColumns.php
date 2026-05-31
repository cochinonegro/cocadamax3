<?php

namespace App\Filament\Support;

use App\Models\Programas;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;

class ProgramasTableColumns
{
    public static function make(
        bool $withStatus = false,
        bool $withWebOficial = false,
        bool $withDirectDownloadUrl = false,
        bool $withDownloadColumn = true,
    ): array {
        $columns = [
            TextColumn::make('id')
                ->label('ID')
                ->sortable()
                ->badge()
                ->color('blue'),

            TextColumn::make('progname')
                ->label('Programa')
                ->sortable()
                ->searchable()
                ->limit(35)
                ->badge()
                ->color('amber'),
        ];

        if ($withDownloadColumn) {
            $columns[] = TextColumn::make('descargar')
                ->label($withDirectDownloadUrl ? 'DESCARGAS' : 'DESCARGAR')
                ->badge()
                ->color(fn (Programas $record) => $withDirectDownloadUrl && ! filled($record->url) ? 'gray' : 'rose')
                ->state(fn (Programas $record) => $withDirectDownloadUrl
                    ? (filled($record->url) ? 'DESCARGAR' : 'Sin enlace')
                    : 'DESCARGAR')
                ->url(fn (Programas $record): ?string => $withDirectDownloadUrl
                    ? (filled($record->url) ? $record->url : null)
                    : route('invitado.descarga', $record))
                ->openUrlInNewTab()
                ->alignCenter();
        }

        $columns = array_merge($columns, [
            TextColumn::make('os_required')
                ->label('Sistema')
                ->badge()
                ->colors([
                    'blue' => 'windows',
                    'rose' => 'mac',
                    'gray' => 'win-mac',
                ])
                ->formatStateUsing(fn ($state) => match ($state) {
                    'windows' => 'WINDOWS',
                    'mac' => 'MAC',
                    'win-mac' => 'WIN & MAC',
                    default => strtoupper((string) $state),
                })
                ->sortable(),

            TextColumn::make('idioma')
                ->label('Idioma')
                ->badge()
                ->color('orange')
                ->formatStateUsing(fn (?string $state) => strtoupper($state ?? '-'))
                ->sortable(),

            TextColumn::make('required')
                ->label('Requerido')
                ->badge()
                ->color('teal')
                ->placeholder('-')
                ->sortable(),

            TextColumn::make('size')
                ->label('Tamaño')
                ->badge()
                ->color('green')
                ->sortable(),

            TextColumn::make('company')
                ->label('Marca')
                ->badge()
                ->color('cyan')
                ->formatStateUsing(fn (?string $state) => strtoupper($state ?? '-'))
                ->placeholder('-')
                ->sortable(),

            TextColumn::make('web_oficial')
                ->label('Web Oficial')
                ->badge()
                ->color('indigo')
                ->placeholder('-')
                ->limit(30)
                ->url(fn (Programas $record): ?string => self::webOficialUrl($record->web_oficial))
                ->openUrlInNewTab()
                ->sortable()
                ->visible($withWebOficial),

            TextColumn::make('year_prog')
                ->label('Año')
                ->badge()
                ->color('fuchsia')
                ->sortable(),

            TextColumn::make('level_inst')
                ->label('Tipo/Archivo')
                ->badge()
                ->color('violet')
                ->placeholder('-')
                ->sortable(),

            TextColumn::make('date_add')
                ->label('Fecha')
                ->badge()
                ->color('sky')
                ->date('d/m/Y')
                ->sortable(),

            TextColumn::make('category')
                ->label('Categoría')
                ->badge()
                ->formatStateUsing(fn (?string $state) => ucfirst($state ?? '-'))
                ->colors([
                    'pink' => 'diseño grafico',
                    'violet' => 'kontakt',
                    'orange' => 'arquitectura',
                    'blue' => 'aplicaciones',
                    'fuchsia' => 'video',
                    'amber' => 'music',
                    'gray' => fn ($state) => ! in_array($state, [
                        'diseño grafico', 'kontakt', 'arquitectura', 'aplicaciones', 'video', 'music',
                    ]),
                ])
                ->sortable(),
        ]);

        if ($withStatus) {
            array_splice($columns, 1, 0, [
                ToggleColumn::make('show')
                    ->label('STATUS')
                    ->sortable()
                    ->afterStateUpdated(function ($record, $state) {
                        if ($state) {
                            $record->update(['show_until' => now()->addYear()]);
                        } else {
                            $record->update(['show_until' => null]);
                        }
                    }),

                ToggleColumn::make('show_instalador')
                    ->label('Instalador')
                    ->sortable(),
            ]);
        }

        return $columns;
    }

    public static function osRequiredLabel(?string $state): string
    {
        return match ($state) {
            'windows' => 'Windows',
            'mac' => 'Mac',
            'win-mac' => 'Win & Mac',
            default => (string) $state,
        };
    }

    public static function webOficialUrl(?string $web): ?string
    {
        if (blank($web)) {
            return null;
        }

        $web = trim($web);

        if (preg_match('/^https?:\/\//i', $web)) {
            return $web;
        }

        return 'https://'.$web;
    }
}
