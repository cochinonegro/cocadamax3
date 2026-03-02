<?php

namespace App\Filament\Admin\Resources\Invitados\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Admin\Resources\Invitados\InvitadoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInvitado extends EditRecord
{
    protected static string $resource = InvitadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
