<?php

namespace App\Http\Responses\Filament;

use App\Filament\Admin\Resources\CardsProgramas\CardsProgramasResource;
use App\Filament\Admin\Resources\Programas\ProgramasResource;
use App\Support\Device;
use Filament\Auth\Http\Responses\Contracts\LoginResponse as LoginResponseContract;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class AdminLoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse | Redirector
    {
        $panel = Filament::getCurrentOrDefaultPanel();

        if ($panel?->getId() === 'admin') {
            $url = Device::isMobile()
                ? CardsProgramasResource::getUrl()
                : ProgramasResource::getUrl();

            return redirect()->intended($url);
        }

        return redirect()->intended(Filament::getUrl());
    }
}
