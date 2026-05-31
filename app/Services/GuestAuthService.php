<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class GuestAuthService
{
    public function register(string $name, string $phone, string $email): User
    {
        $user = User::create([
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
            'password' => Hash::make($phone),
        ]);

        $user->assignRole(UserRole::Invitado->value);

        return $user;
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

        return url('/clientes');
    }
}
