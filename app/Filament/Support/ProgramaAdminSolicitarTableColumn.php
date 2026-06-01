<?php

namespace App\Filament\Support;

use App\Models\Programas;
use App\Services\ProgramaSolicitudService;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;

class ProgramaAdminSolicitarTableColumn
{
    public static function make(): TextColumn
    {
        return TextColumn::make('solicitar')
            ->label('Solicitar')
            ->alignCenter()
            ->badge()
            ->state(fn (Programas $record): string => self::label($record))
            ->color(fn (Programas $record): string => self::color($record))
            ->weight(FontWeight::Bold)
            ->tooltip(fn (Programas $record): string => self::tooltip($record))
            ->action(function (Programas $record): void {
                self::cycle($record);
            });
    }

    public static function status(Programas $record): string
    {
        return app(ProgramaSolicitudService::class)->adminSolicitarStatus($record);
    }

    public static function label(Programas $record): string
    {
        return match (self::status($record)) {
            'en_pedidos' => 'EN PEDIDOS',
            'pendiente' => 'PENDIENTE',
            default => 'OFF',
        };
    }

    public static function color(Programas $record): string
    {
        return match (self::status($record)) {
            'en_pedidos' => 'success',
            'pendiente' => 'warning',
            default => 'gray',
        };
    }

    public static function tooltip(Programas $record): string
    {
        return match (self::status($record)) {
            'en_pedidos' => 'Clic: ocultar de Pedidos',
            'pendiente' => 'Clic: aceptar solicitudes y activar en Pedidos (30 min)',
            default => 'Clic: activar en Pedidos (30 min)',
        };
    }

    public static function cycle(Programas $record): void
    {
        app(ProgramaSolicitudService::class)->adminCycleSolicitarState($record);
    }
}
