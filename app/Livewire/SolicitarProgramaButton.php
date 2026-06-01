<?php

namespace App\Livewire;

use App\Models\Programas;
use App\Services\ProgramaSolicitudService;
use App\Support\ProgramaSolicitudSubmitter;
use Livewire\Component;

class SolicitarProgramaButton extends Component
{
    public int $programaId;

    public string $variant = 'table';

    public function mount(int $programaId, string $variant = 'table'): void
    {
        $this->programaId = $programaId;
        $this->variant = $variant;
    }

    public function solicitar(): void
    {
        $programa = Programas::query()->active()->find($this->programaId);

        if (! $programa) {
            return;
        }

        ProgramaSolicitudSubmitter::submit($programa);
    }

    public function statusLabel(ProgramaSolicitudService $solicitudes): string
    {
        $user = auth()->user();
        $programa = Programas::query()->find($this->programaId);

        if (! $user || ! $programa) {
            return 'disponible';
        }

        return $solicitudes->statusFor($user, $programa);
    }

    public function render(ProgramaSolicitudService $solicitudes)
    {
        return view('livewire.solicitar-programa-button', [
            'status' => $this->statusLabel($solicitudes),
        ]);
    }
}
