<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use App\Services\AdminUserService;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        AdminUserService::ensureExists();

        User::updateOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User', 'password' => '123456'],
        )->syncRoles([UserRole::Administrador->value]);

        User::updateOrCreate(
            ['email' => 'invitado@gmail.com'],
            ['name' => 'Cliente Invitado', 'password' => '123456'],
        )->syncRoles([UserRole::Invitado->value]);
    }
}
