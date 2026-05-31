<?php

namespace App\Filament\Concerns;

trait NormalizesProgramaVisibility
{
    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function normalizeProgramaVisibility(array $data): array
    {
        if ($data['show'] ?? false) {
            $data['show_until'] ??= now()->addYear();
        } else {
            $data['show_until'] = null;
        }

        return $data;
    }
}
