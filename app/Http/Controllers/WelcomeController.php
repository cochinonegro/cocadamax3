<?php

namespace App\Http\Controllers;

use App\Services\GuestAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WelcomeController extends Controller
{
    public function __construct(
        protected GuestAuthService $guestAuth,
    ) {}

    public function index(Request $request): View|RedirectResponse
    {
        if ($request->user()) {
            return redirect($this->guestAuth->redirectAfterLogin($request->user()));
        }

        return view('welcome');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
        ]);

        $user = $this->guestAuth->register(
            $data['name'],
            $data['phone'],
            $data['email'],
        );

        $this->guestAuth->login($data['email'], $data['phone']);

        return redirect()
            ->to($this->guestAuth->redirectAfterLogin($user))
            ->with('success', 'Cuenta creada. Puedes entrar con tu correo o teléfono. Tu clave es tu número de teléfono.');
    }

    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'login' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $user = $this->guestAuth->login(
            $data['login'],
            $data['password'],
            (bool) ($data['remember'] ?? false),
        );

        return redirect()->to($this->guestAuth->redirectAfterLogin($user));
    }
}
