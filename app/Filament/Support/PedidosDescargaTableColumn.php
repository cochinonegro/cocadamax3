<?php

namespace App\Filament\Support;

use App\Models\Programas;
use App\Support\PedidosDescargaHandler;
use Filament\Actions\Action;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Livewire\Component;

class PedidosDescargaTableColumn
{
    public static function make(): TextColumn
    {
        return TextColumn::make('descargas')
            ->label('DESCARGAS')
            ->alignCenter()
            ->wrap()
            ->badge()
            ->state(fn (Programas $record): string => self::label($record))
            ->color(fn (Programas $record): string => self::color($record))
            ->weight(FontWeight::Bold)
            ->disabledClick(fn (Programas $record): bool => ! self::hasDownloadUrl($record))
            ->action(
                Action::make('descargarPedido')
                    ->action(function (Programas $record, Component $livewire): void {
                        $url = PedidosDescargaHandler::consume($record);

                        if ($url) {
                            $livewire->js('window.open('.json_encode($url).', "_blank")');
                        }
                    })
                    ->visible(fn (Programas $record): bool => self::hasDownloadUrl($record)),
            );
    }

    public static function label(Programas $record): string
    {
        return self::hasDownloadUrl($record)
            ? PedidosDescargaHandler::LABEL
            : 'SIN ENLACE';
    }

    public static function color(Programas $record): string
    {
        return self::hasDownloadUrl($record) ? 'danger' : 'gray';
    }

    public static function hasDownloadUrl(Programas $record): bool
    {
        return filled(ProgramasTableColumns::downloadUrl($record->url));
    }
}
