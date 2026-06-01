<?php

namespace Tests\Feature;

use App\Models\Programas;
use App\Support\PedidosDescargaHandler;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PedidosDescargaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }

    public function test_consume_download_opens_url_and_removes_from_pedidos(): void
    {
        $programa = Programas::factory()->create([
            'show' => true,
            'url' => 'example.com/archivo.zip',
            'pedidos_visible_until' => now()->addMinutes(30),
        ]);

        $this->assertTrue($programa->isVisibleInPedidos());

        $url = PedidosDescargaHandler::consume($programa);

        $this->assertSame('https://example.com/archivo.zip', $url);
        $this->assertFalse($programa->fresh()->isVisibleInPedidos());
    }

    public function test_consume_returns_null_when_no_url(): void
    {
        $programa = Programas::factory()->create([
            'show' => true,
            'url' => null,
            'pedidos_visible_until' => now()->addMinutes(30),
        ]);

        $this->assertNull(PedidosDescargaHandler::consume($programa));
        $this->assertTrue($programa->fresh()->isVisibleInPedidos());
    }
}
