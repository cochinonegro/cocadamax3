<?php

namespace App\Http\Controllers;

use App\Services\GuestAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WelcomeController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        if ($request->user()) {
            return redirect(app(GuestAuthService::class)->redirectAfterLogin($request->user()));
        }

        return view('guest.welcome');
    }

    public function register(Request $request): RedirectResponse
    {
        $guestAuth = app(GuestAuthService::class);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
        ]);

        $user = $guestAuth->register(
            $data['name'],
            $data['phone'],
            $data['email'],
        );

        $guestAuth->login($data['email'], $data['phone']);

        return redirect()
            ->to($guestAuth->redirectAfterLogin($user))
            ->with('success', 'Cuenta creada. Puedes entrar con tu correo o teléfono. Tu clave es tu número de teléfono.');
    }

    public function login(Request $request): RedirectResponse
    {
        $guestAuth = app(GuestAuthService::class);

        $data = $request->validate([
            'login' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $user = $guestAuth->login(
            $data['login'],
            $data['password'],
            (bool) ($data['remember'] ?? false),
        );

        return redirect()->to($guestAuth->redirectAfterLogin($user));
    }
}
