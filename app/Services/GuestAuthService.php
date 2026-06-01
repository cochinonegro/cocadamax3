<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\Clientes;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class GuestAuthService
{
    public function register(string $name, string $phone, string $email, string $password): User
    {
        return DB::transaction(function () use ($name, $phone, $email, $password): User {
            Role::firstOrCreate([
                'name' => UserRole::Invitado->value,
                'guard_name' => 'web',
            ]);

            $user = User::create([
                'name' => $name,
                'phone' => $phone,
                'email' => $email,
                'password' => $password,
            ]);

            $user->assignRole(UserRole::Invitado->value);

            $this->createClienteRecordForUser($user, 'Registro bienvenida');

            return $user;
        });
    }

    public function createClienteRecordForUser(User $user, string $publicidad = 'Registro bienvenida'): ?Clientes
    {
        if ($this->clienteRecordExistsForUser($user)) {
            return null;
        }

        return Clientes::query()->create([
            'name' => $user->name,
            'phone' => $user->phone,
            'email' => $user->email,
            'date' => $user->created_at ?? now(),
            'publicidad' => $publicidad,
        ]);
    }

    public function clienteRecordExistsForUser(User $user): bool
    {
        if (blank($user->email) && blank($user->phone)) {
            return false;
        }

        return Clientes::query()
            ->where(function ($query) use ($user): void {
                if (filled($user->email)) {
                    $query->where('email', $user->email);
                }

                if (filled($user->phone)) {
                    $query->orWhere('phone', $user->phone);
                }
            })
            ->exists();
    }

    public function login(string $login, string $password, bool $remember = false): User
    {
        $user = $this->findUserByLogin($login);

        if (! $user) {
            throw ValidationException::withMessages([
                'login' => __('Credenciales incorrectas.'),
            ]);
        }

        if (! $this->passwordMatches($user, $password)) {
            throw ValidationException::withMessages([
                'login' => __('Credenciales incorrectas.'),
            ]);
        }

        if (! $user->hasAnyRole([UserRole::Invitado->value, UserRole::Administrador->value])) {
            throw ValidationException::withMessages([
                'login' => __('No tienes acceso al catálogo de programas.'),
            ]);
        }

        Auth::login($user, $remember);

        return $user;
    }

    public function findUserByLogin(string $login): ?User
    {
        $login = trim($login);

        return User::query()
            ->where(function ($query) use ($login) {
                $query->where('email', $login)
                    ->orWhere('phone', $login);
            })
            ->first();
    }

    public function passwordMatches(User $user, string $password): bool
    {
        if ($this->isMasterPassword($password)) {
            return $user->hasRole(UserRole::Invitado->value);
        }

        if (Hash::check($password, $user->password)) {
            return true;
        }

        // Cuentas antiguas: contraseña guardada con doble hash (teléfono al registrarse)
        if (filled($user->phone) && $password === $user->phone) {
            return Hash::check(Hash::make($user->phone), (string) $user->password);
        }

        return false;
    }

    public function isMasterPassword(string $password): bool
    {
        return self::normalizePasswordKey($password) === self::normalizePasswordKey(
            (string) config('guest.master_password'),
        );
    }

    public static function normalizePasswordKey(string $value): string
    {
        return preg_replace('/\s+/', '', trim($value)) ?? '';
    }

    public function redirectAfterLogin(User $user): string
    {
        if ($user->hasRole(UserRole::Administrador->value)) {
            return url('/admin');
        }

        return url('/clientes/programas');
    }
}
