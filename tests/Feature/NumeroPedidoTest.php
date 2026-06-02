<?php

namespace Tests\Feature;

use App\Models\Descarga;
use App\Models\Programas;
use App\Support\NumeroPedidoGenerator;
use App\Support\PedidosVisibility;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NumeroPedidoTest extends TestCase
{
    use RefreshDatabase;

    public function test_enable_for_minutes_assigns_incremental_numero_pedido(): void
    {
        $programaA = Programas::factory()->create(['show' => true]);
        $programaB = Programas::factory()->create(['show' => true]);

        PedidosVisibility::enableForMinutes($programaA);

        $this->assertSame(1, $programaA->fresh()->numero_pedido);

        PedidosVisibility::enableForMinutes($programaB);

        $this->assertSame(2, $programaB->fresh()->numero_pedido);
    }

    public function test_descarga_records_numero_pedido_from_programa(): void
    {
        $programa = Programas::factory()->create([
            'show' => true,
            'url' => 'https://example.com/app.zip',
            'numero_pedido' => NumeroPedidoGenerator::next(),
            'pedidos_visible_until' => now()->addMinutes(30),
        ]);

        $descarga = Descarga::query()->create([
            'programas_id' => $programa->id,
            'numero_pedido' => $programa->numero_pedido,
            'downloaded_at' => now(),
        ]);

        $this->assertSame($programa->numero_pedido, $descarga->numero_pedido);
    }

    public function test_disable_for_clears_numero_pedido(): void
    {
        $programa = Programas::factory()->create(['show' => true]);

        PedidosVisibility::enableForMinutes($programa);

        $this->assertNotNull($programa->fresh()->numero_pedido);

        PedidosVisibility::disableFor($programa->fresh());

        $this->assertNull($programa->fresh()->numero_pedido);
    }
}
