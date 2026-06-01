<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AdminUserService
{
    public static function email(): string
    {
        return (string) config('admin.email');
    }

    public static function password(): string
    {
        return (string) config('admin.password');
    }

    public static function name(): string
    {
        return (string) config('admin.name');
    }

    /**
     * Crea o restablece el administrador por defecto (email, clave y rol).
     */
    public static function ensureExists(): User
    {
        Role::firstOrCreate([
            'name' => UserRole::Administrador->value,
            'guard_name' => 'web',
        ]);

        Role::firstOrCreate([
            'name' => UserRole::Invitado->value,
            'guard_name' => 'web',
        ]);

        $admin = User::updateOrCreate(
            ['email' => self::email()],
            [
                'name' => self::name(),
                'password' => self::password(),
            ],
        );

        $admin->syncRoles([UserRole::Administrador->value]);

        return $admin->fresh();
    }
}
