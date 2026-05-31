<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class EnsureAdminUser extends Command
{
    protected $signature = 'app:ensure-admin';

    protected $description = 'Crea o restablece el usuario admin@gmail.com (clave: 123456) con rol administrador';

    public function handle(): int
    {
        Role::firstOrCreate(['name' => UserRole::Administrador->value, 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => UserRole::Invitado->value, 'guard_name' => 'web']);

        $admin = User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            ['name' => 'Admin', 'password' => '123456'],
        );

        $admin->syncRoles([UserRole::Administrador->value]);

        $this->components->info('Admin listo: admin@gmail.com / 123456');

        return self::SUCCESS;
    }
}
