<?php

namespace App\Http\Responses\Filament;

use App\Filament\Admin\Resources\Programas\ProgramasResource;
use Filament\Auth\Http\Responses\Contracts\LoginResponse as LoginResponseContract;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class AdminLoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        $panel = Filament::getCurrentOrDefaultPanel();

        if ($panel?->getId() === 'admin') {
            return redirect()->intended(ProgramasResource::getUrl());
        }

        return redirect()->intended(Filament::getUrl());
    }
}
