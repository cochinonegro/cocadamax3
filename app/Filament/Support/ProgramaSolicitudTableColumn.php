<?php

namespace App\Filament\Support;

use App\Models\Programas;
use App\Services\ProgramaSolicitudService;
use App\Support\ProgramaSolicitudSubmitter;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Livewire\Component;

class ProgramaSolicitudTableColumn
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
            ->disabledClick(fn (Programas $record): bool => self::status($record) !== 'disponible')
            ->action(function (Programas $record, Component $livewire): void {
                if (self::status($record) !== 'disponible') {
                    return;
                }

                if (! ProgramaSolicitudSubmitter::submit($record, notifyOnSuccess: false)) {
                    return;
                }

                $livewire->dispatch('solicitud-enviada', programasId: $record->id)->self();
            });
    }

    public static function status(Programas $record): string
    {
        $user = auth()->user();

        if (! $user) {
            return 'disponible';
        }

        return app(ProgramaSolicitudService::class)->statusFor($user, $record);
    }

    public static function label(Programas $record): string
    {
        return match (self::status($record)) {
            'en_pedidos' => 'En Pedidos',
            'pendiente' => 'Pendiente',
            default => 'SOLICITAR YA',
        };
    }

    public static function color(Programas $record): string
    {
        return match (self::status($record)) {
            'pendiente' => 'warning',
            default => 'success',
        };
    }
}
