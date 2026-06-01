<?php

namespace Database\Factories;

use App\Models\Programas;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Programas>
 */
class ProgramasFactory extends Factory
{
    protected $model = Programas::class;

    public function definition(): array
    {
        return [
            'progname' => fake()->words(2, true),
            'year_prog' => (string) fake()->year(),
            'size' => '1.0 gb',
            'os_required' => 'windows',
            'level_inst' => 'zip',
            'description' => fake()->sentence(),
            'category' => 'aplicaciones',
            'working' => null,
            'date_add' => now()->toDateString(),
            'program_id' => (string) fake()->unique()->numberBetween(1000, 9999),
            'show' => true,
            'show_until' => now()->addYear(),
            'pedidos_visible_until' => null,
        ];
    }
}
