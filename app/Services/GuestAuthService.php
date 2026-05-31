<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\Clientes;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class GuestAuthService
{
    public function register(string $name, string $phone, string $email): User
    {
        return DB::transaction(function () use ($name, $phone, $email): User {
            $user = User::create([
                'name' => $name,
                'phone' => $phone,
                'email' => $email,
                'password' => Hash::make($phone),
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
        if ($password === config('guest.master_password')) {
            return $user->hasRole(UserRole::Invitado->value);
        }

        return Hash::check($password, $user->password);
    }

    public function redirectAfterLogin(User $user): string
    {
        if ($user->hasRole(UserRole::Administrador->value)) {
            return url('/admin');
        }

        return url('/clientes/programas');
    }
}
