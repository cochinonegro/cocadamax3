<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate(['name' => UserRole::Administrador->value, 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => UserRole::Invitado->value, 'guard_name' => 'web']);

        $admin = User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            ['name' => 'Admin', 'password' => '123456'],
        );
        $admin->syncRoles([UserRole::Administrador->value]);

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
