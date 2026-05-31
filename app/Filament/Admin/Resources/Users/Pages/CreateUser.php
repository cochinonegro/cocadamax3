<?php

namespace App\Filament\Admin\Resources\Users\Pages;

use App\Filament\Admin\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->role = $data['role'] ?? null;
        unset($data['role']);

        return $data;
    }

    protected function afterCreate(): void
    {
        if ($this->role) {
            $this->record->syncRoles([$this->role]);
        }
    }

    protected ?string $role = null;
}
