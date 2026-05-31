<?php

namespace App\Filament\Clientes\Resources\Programas;

use App\Filament\Clientes\Resources\Programas\Pages\ListProgramas;
use App\Filament\Clientes\Resources\Programas\Pages\ViewProgramas;
use App\Models\Programas;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\ViewEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Filament\Support\ProgramasTableColumns;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProgramasResource extends Resource
{
    protected static ?string $model = Programas::class;

    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';

    protected static ?string $navigationLabel = 'Programas';

    protected static ?string $pluralModelLabel = 'Programas';

    protected static ?string $modelLabel = 'Programa';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                ViewEntry::make('progname')
                    ->hiddenLabel()
                    ->view('filament.clientes.infolists.producto-detalle'),

                Section::make('INSTALACION')
                    ->collapsed()
                    ->schema([
                        ViewEntry::make('installation_steps')
                            ->hiddenLabel()
                            ->view('filament.clientes.infolists.instalacion'),
                    ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->active();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(ProgramasTableColumns::make(withStatus: false, withWebOficial: true))
            ->filters([
                SelectFilter::make('category')
                    ->label('Categoría')
                    ->options([
                        'aplicaciones' => 'Aplicaciones',
                        'diseño grafico' => 'Diseño gráfico',
                        'arquitectura' => 'Arquitectura',
                        'music' => 'Música',
                        'video' => 'Video',
                        'kontakt' => 'Kontakt',
                    ])
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->defaultSort('id', 'desc')
            ->emptyStateHeading('No hay programas disponibles')
            ->emptyStateDescription('Los programas aparecerán aquí cuando estén activos.');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProgramas::route('/'),
            'view' => ViewProgramas::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }
}
