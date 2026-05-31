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

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function normalizeInstallationSteps(array $data): array
    {
        if (! isset($data['installation_steps']) || ! is_array($data['installation_steps'])) {
            return $data;
        }

        $data['installation_steps'] = array_values(array_map(function (mixed $step): array {
            $step = is_array($step) ? $step : [];

            if (blank($step['description'] ?? null) && filled($step['text'] ?? null)) {
                $step['description'] = $step['text'];
            }

            unset($step['text']);

            return $step;
        }, $data['installation_steps']));

        return $data;
    }
}
