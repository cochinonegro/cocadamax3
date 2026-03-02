<?php

namespace App\Filament\Admin\Resources\Invitados\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Admin\Resources\Invitados\InvitadoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInvitados extends ListRecords
{
    protected static string $resource = InvitadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
