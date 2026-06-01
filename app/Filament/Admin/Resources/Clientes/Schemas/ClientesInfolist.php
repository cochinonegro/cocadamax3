<?php

namespace App\Filament\Admin\Resources\Clientes\Schemas;

use App\Filament\Support\ClienteFormatting;
use App\Models\Clientes;
use App\Support\DisplayTimezone;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class ClientesInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Datos del cliente')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nombre'),

                        TextEntry::make('phone')
                            ->label('Teléfono')
                            ->formatStateUsing(fn (?string $state): HtmlString => ClienteFormatting::phoneHtml($state))
                            ->html(),

                        TextEntry::make('email')
                            ->label('Correo')
                            ->placeholder('—'),

                        TextEntry::make('nombre_whatsapp')
                            ->label('Nombre WhatsApp')
                            ->placeholder('—'),

                        TextEntry::make('ciudad')
                            ->label('Ciudad')
                            ->placeholder('—'),

                        TextEntry::make('created_at')
                            ->label('Fecha de registro')
                            ->formatStateUsing(
                                fn (?string $state, Clientes $record): string => DisplayTimezone::formatDate($record->created_at),
                            ),

                        TextEntry::make('registration_time')
                            ->label('Hora de registro')
                            ->state(
                                fn (Clientes $record): ?string => DisplayTimezone::formatTime($record->created_at),
                            )
                            ->badge()
                            ->color('success')
                            ->placeholder('—'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Programa solicitado')
                    ->schema([
                        TextEntry::make('required_prog')
                            ->label('Programa requerido')
                            ->placeholder('—'),

                        TextEntry::make('os_required')
                            ->label('Sistema operativo')
                            ->formatStateUsing(fn (?string $state) => match ($state) {
                                'windows' => 'Windows',
                                'mac' => 'Mac',
                                'win-mac' => 'Win & Mac',
                                default => $state ?? '—',
                            })
                            ->badge()
                            ->color(fn (?string $state) => match ($state) {
                                'windows' => 'info',
                                'mac' => 'danger',
                                'win-mac' => 'gray',
                                default => 'gray',
                            }),

                        TextEntry::make('category')
                            ->label('Categoría')
                            ->placeholder('—'),

                        TextEntry::make('company')
                            ->label('Empresa / Marca')
                            ->placeholder('—'),

                        TextEntry::make('referencia')
                            ->label('Referencia')
                            ->placeholder('—'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Seguimiento')
                    ->schema([
                        TextEntry::make('date')
                            ->label('Fecha (formulario)')
                            ->date('d/m/Y')
                            ->placeholder('—'),

                        TextEntry::make('publicidad')
                            ->label('Publicidad / origen')
                            ->placeholder('—'),

                        TextEntry::make('result_client')
                            ->label('Resultado')
                            ->placeholder('—'),

                        TextEntry::make('observaciones')
                            ->label('Observaciones')
                            ->placeholder('—')
                            ->columnSpanFull(),

                        TextEntry::make('comentario_info_cliente')
                            ->label('Comentario interno')
                            ->placeholder('—')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
