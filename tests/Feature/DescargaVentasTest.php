<?php

namespace Tests\Feature;

use App\Models\Descarga;
use App\Models\Programas;
use App\Models\User;
use App\Support\DescargaVentas;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DescargaVentasTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }

    public function test_totals_sum_only_paid_downloads_in_period(): void
    {
        $programa = Programas::factory()->create(['show' => true]);
        $user = User::factory()->create();

        Descarga::query()->create([
            'user_id' => $user->id,
            'programas_id' => $programa->id,
            'downloaded_at' => now(),
            'precio' => 10.50,
            'pagado' => true,
        ]);

        Descarga::query()->create([
            'user_id' => $user->id,
            'programas_id' => $programa->id,
            'downloaded_at' => now(),
            'precio' => 25.00,
            'pagado' => true,
        ]);

        Descarga::query()->create([
            'user_id' => $user->id,
            'programas_id' => $programa->id,
            'downloaded_at' => now(),
            'precio' => 99.99,
            'pagado' => false,
        ]);

        $this->assertSame(35.50, DescargaVentas::totalSemana());
        $this->assertSame(35.50, DescargaVentas::totalMes());
    }
}
