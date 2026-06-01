<?php

namespace App\Filament\Support;

use App\Models\Programas;
use App\Support\PedidosVisibility;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;

class ProgramasTableColumns
{
    public static function make(
        bool $withStatus = false,
        bool $withWebOficial = false,
        bool $withDirectDownloadUrl = false,
        bool $withDownloadColumn = true,
        bool $copyDownloadUrlOnly = false,
        bool $clientProgramNameStyle = false,
        bool $withSolicitarColumn = false,
    ): array {
        $prognameColumn = TextColumn::make('progname')
            ->label('Programa')
            ->sortable()
            ->searchable()
            ->limit(35);

        if ($clientProgramNameStyle) {
            $prognameColumn
                ->formatStateUsing(fn (?string $state): string => mb_strtoupper((string) $state))
                ->color('amber')
                ->weight(FontWeight::Bold);
        } else {
            $prognameColumn
                ->badge()
                ->color('amber');
        }

        $columns = [
            TextColumn::make('id')
                ->label('ID')
                ->sortable()
                ->badge()
                ->color('blue'),

            $prognameColumn,
        ];

        if ($withSolicitarColumn) {
            $columns[] = ProgramaSolicitudTableColumn::make();
        }

        if ($withDownloadColumn) {
            $downloadColumn = TextColumn::make('descargar')
                ->label($withDirectDownloadUrl ? 'DESCARGAS' : 'DESCARGAR')
                ->badge()
                ->alignCenter();

            if ($copyDownloadUrlOnly) {
                $downloadColumn
                    ->color(fn (Programas $record): string => filled($record->url) ? 'rose' : 'gray')
                    ->state(fn (Programas $record): string => filled($record->url) ? 'DESCARGAR' : 'Sin enlace')
                    ->copyable(fn (Programas $record): bool => filled(self::downloadUrl($record->url)))
                    ->copyableState(fn (Programas $record): ?string => self::downloadUrl($record->url))
                    ->copyMessage('Enlace copiado al portapapeles');
            } else {
                $downloadColumn
                    ->color(fn (Programas $record) => $withDirectDownloadUrl && ! filled($record->url) ? 'gray' : 'rose')
                    ->state(fn (Programas $record) => $withDirectDownloadUrl
                        ? (filled($record->url) ? 'DESCARGAR' : 'Sin enlace')
                        : 'DESCARGAR')
                    ->url(fn (Programas $record): ?string => $withDirectDownloadUrl
                        ? self::downloadUrl($record->url)
                        : route('invitado.descarga', $record))
                    ->openUrlInNewTab();
            }

            $columns[] = $downloadColumn;
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
                ->toggledHiddenByDefault(! $withWebOficial),

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
                ->formatStateUsing(fn (?string $state) => ProgramaCategories::label($state))
                ->colors([
                    'pink' => 'diseño grafico',
                    'violet' => 'kontakt',
                    'orange' => 'arquitectura',
                    'blue' => 'aplicaciones',
                    'fuchsia' => 'video',
                    'amber' => 'music',
                    'emerald' => 'office-pdf',
                    'gray' => fn ($state) => ! in_array($state, [
                        'diseño grafico', 'kontakt', 'arquitectura', 'aplicaciones', 'video', 'music', 'office-pdf',
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

                ToggleColumn::make('pedidos_visible')
                    ->label('Pedidos')
                    ->getStateUsing(fn (Programas $record): bool => $record->isPedidosTimerActive())
                    ->updateStateUsing(function (Programas $record, bool $state): bool {
                        if ($state) {
                            PedidosVisibility::enableForMinutes($record);
                        } else {
                            PedidosVisibility::disableFor($record);
                        }

                        return $state;
                    })
                    ->onColor('warning')
                    ->tooltip(fn (Programas $record): string => $record->isPedidosTimerActive()
                        ? 'Visible en Pedidos (30 min)'
                        : 'Oculto en Pedidos'),
            ]);
        }

        return self::withToggleableColumns($columns);
    }

    /**
     * @param  array<int, Column>  $columns
     * @return array<int, Column>
     */
    private static function withToggleableColumns(array $columns): array
    {
        return array_map(function (Column $column): Column {
            $hiddenByDefault = $column->isToggledHiddenByDefault();

            return $column->toggleable(
                condition: true,
                isToggledHiddenByDefault: $hiddenByDefault,
            );
        }, $columns);
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
        return self::downloadUrl($web);
    }

    public static function downloadUrl(?string $url): ?string
    {
        if (blank($url)) {
            return null;
        }

        $url = trim($url);

        if (preg_match('/^https?:\/\//i', $url)) {
            return $url;
        }

        return 'https://'.$url;
    }
}
