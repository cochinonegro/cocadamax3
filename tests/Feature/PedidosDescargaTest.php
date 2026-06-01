<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Programas;
use App\Models\User;
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
        $user = User::factory()->create();
        $user->assignRole(UserRole::Invitado->value);

        $this->actingAs($user);

        $programa = Programas::factory()->create([
            'show' => true,
            'url' => 'example.com/archivo.zip',
            'pedidos_visible_until' => now()->addMinutes(30),
        ]);

        $this->assertTrue($programa->isVisibleInPedidos());

        $url = PedidosDescargaHandler::consume($programa);

        $this->assertSame('https://example.com/archivo.zip', $url);
        $this->assertFalse($programa->fresh()->isVisibleInPedidos());
        $this->assertDatabaseHas('descargas', [
            'user_id' => $user->id,
            'programas_id' => $programa->id,
        ]);
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

    public function test_pedidos_download_route_redirects_to_external_url(): void
    {
        $user = User::factory()->create();
        $user->assignRole(UserRole::Invitado->value);

        $programa = Programas::factory()->create([
            'show' => true,
            'url' => 'example.com/archivo.zip',
            'pedidos_visible_until' => now()->addMinutes(30),
        ]);

        $response = $this->actingAs($user)->get(route('pedidos.descarga', $programa));

        $response->assertRedirect('https://example.com/archivo.zip');
        $this->assertFalse($programa->fresh()->isVisibleInPedidos());
        $this->assertDatabaseHas('descargas', [
            'user_id' => $user->id,
            'programas_id' => $programa->id,
        ]);
    }
}
