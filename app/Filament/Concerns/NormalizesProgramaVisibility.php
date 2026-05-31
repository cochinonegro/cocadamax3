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
        $show = array_key_exists('show', $data)
            ? filter_var($data['show'], FILTER_VALIDATE_BOOLEAN)
            : true;

        $data['show'] = $show;

        if ($show) {
            $data['show_until'] ??= now()->addYear();
        } else {
            $data['show_until'] = null;
        }

        return $data;
    }
}
