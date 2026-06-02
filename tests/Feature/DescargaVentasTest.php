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

    public function test_montos_por_fecha_sums_paid_download_prices(): void
    {
        $programa = Programas::factory()->create(['show' => true]);
        $user = User::factory()->create();
        $hoy = now()->toDateString();

        Descarga::query()->create([
            'user_id' => $user->id,
            'programas_id' => $programa->id,
            'downloaded_at' => $hoy,
            'precio' => 20,
            'pagado' => true,
        ]);

        Descarga::query()->create([
            'user_id' => $user->id,
            'programas_id' => $programa->id,
            'downloaded_at' => $hoy,
            'precio' => 5,
            'pagado' => true,
        ]);

        $montos = DescargaVentas::montosPorFecha(now()->startOfDay(), now()->endOfDay());

        $this->assertSame(25.0, $montos[$hoy]);
    }
}
