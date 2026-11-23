<?php

namespace Tests\Feature;

use App\Models\Clientes;
use App\Models\Venta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class VentaRapidaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'telegram.bot_token' => '123456789:ABCDEFghijklmnopqrstuvwxyz123456',
            'telegram.admin_chat_id' => '123456789',
        ]);
    }

    public function test_welcome_shows_venta_rapida_button(): void
    {
        $this->get(route('welcome'))
            ->assertOk()
            ->assertSee('VENTA RAPIDA')
            ->assertSee('Introduce el PIN de 4 cifras.')
            ->assertSee('YA HE COMPRADO')
            ->assertSee('Correcto, ya estás. Qué programa quieres?')
            ->assertDontSee('Enviar otro PIN')
            ->assertSee('Ingresar por primera vez');
    }

    public function test_pin_opens_form_and_saves_client_with_info_telegram(): void
    {
        Http::fake([
            'api.telegram.org/*' => Http::response([
                'ok' => true,
                'result' => ['message_id' => 41],
            ]),
        ]);

        $this->postJson(route('welcome.venta-rapida.verificar'), ['pin' => '0000'])
            ->assertStatus(422);

        Http::assertNothingSent();

        $this->postJson(route('welcome.venta-rapida.verificar'), ['pin' => '4053'])
            ->assertOk();

        $this->postJson(route('welcome.venta-rapida.store'), [
            'name' => 'Ana Ruiz',
            'phone' => '612 345 678',
            'email' => 'ana@example.com',
            'required_prog' => 'Photoshop',
        ])->assertOk();

        $this->assertDatabaseHas('clientes', [
            'name' => 'Ana Ruiz',
            'phone' => '612345678',
            'email' => 'ana@example.com',
            'required_prog' => 'Photoshop',
            'publicidad' => 'Venta rápida',
        ]);

        $this->assertSame(1, Clientes::query()->count());

        $cliente = Clientes::query()->first();

        $this->assertDatabaseHas('ventas', [
            'cliente_id' => $cliente->id,
            'producto' => 'Photoshop',
            'fecha_venta' => now()->toDateString(),
        ]);

        $this->assertSame(1, Venta::query()->count());

        Http::assertSentCount(1);

        Http::assertSent(function (Request $request) {
            $text = (string) $request['text'];

            return str_contains($text, 'Cliente Ana Ruiz Tlf: 612-345-678, completó datos para venta rápida')
                && str_contains($text, 'Programa: Photoshop')
                && ! array_key_exists('reply_markup', $request->data());
        });

        $this->postJson(route('welcome.venta-rapida.store'), [
            'name' => 'Ana Ruiz',
            'phone' => '612345678',
            'email' => 'ana@example.com',
            'required_prog' => 'Photoshop',
        ])->assertForbidden();
    }

    public function test_store_requires_a_verified_pin(): void
    {
        $this->postJson(route('welcome.venta-rapida.store'), [
            'name' => 'Ana Ruiz',
            'phone' => '612345678',
            'email' => 'ana@example.com',
            'required_prog' => 'Photoshop',
        ])->assertForbidden();

        $this->assertSame(0, Clientes::query()->count());
        $this->assertSame(0, Venta::query()->count());
    }

    public function test_ya_he_comprado_sends_telegram_when_phone_is_registered(): void
    {
        Http::fake([
            'api.telegram.org/*' => Http::response([
                'ok' => true,
                'result' => ['message_id' => 77],
            ]),
        ]);

        Clientes::query()->create([
            'name' => 'Luis Vega',
            'phone' => '612-345-678',
            'email' => 'luis@example.com',
            'date' => now()->toDateString(),
        ]);

        $this->postJson(route('welcome.venta-rapida.lookup'), [
            'phone' => '612345678',
        ])->assertForbidden();

        $this->postJson(route('welcome.venta-rapida.verificar'), [
            'pin' => '4053',
        ])->assertOk();

        $this->postJson(route('welcome.venta-rapida.lookup'), [
            'phone' => '600000000',
        ])
            ->assertNotFound()
            ->assertJsonPath('message', 'No estás en nuestra base de datos, por favor completa el formulario');

        $this->postJson(route('welcome.venta-rapida.lookup'), [
            'phone' => '612 345 678',
        ])
            ->assertOk()
            ->assertJsonPath('name', 'Luis Vega')
            ->assertJsonPath('message', 'Correcto, ya estás. Qué programa quieres?');

        Http::assertNothingSent();

        $this->postJson(route('welcome.venta-rapida.comprado'), [
            'required_prog' => 'AutoCAD',
        ])->assertOk();

        Http::assertSentCount(1);
        Http::assertSent(function (Request $request) {
            $text = (string) $request['text'];

            return str_contains($text, 'Cliente Luis Vega Tlf: 612-345-678, ya había comprado. Programa: AutoCAD')
                && ! array_key_exists('reply_markup', $request->data());
        });

        $this->assertSame(1, Clientes::query()->count());
        $this->assertSame(0, Venta::query()->count());
    }
}
