<?php

namespace Tests\Feature;

use App\Enums\ProgramaSolicitudStatus;
use App\Enums\UserRole;
use App\Models\ProgramaSolicitud;
use App\Models\Programas;
use App\Models\User;
use App\Services\ProgramaSolicitudService;
use App\Services\Telegram\TelegramBotService;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ProgramaSolicitudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        config([
            'telegram.bot_token' => '123456789:ABCDEFghijklmnopqrstuvwxyz123456',
            'telegram.admin_chat_id' => '123456789',
            'telegram.pedidos_minutes' => 30,
        ]);
    }

    public function test_submit_creates_pending_solicitud_and_notifies_telegram(): void
    {
        Http::fake([
            'api.telegram.org/*' => Http::response([
                'ok' => true,
                'result' => ['message_id' => 99],
            ]),
        ]);

        $user = User::factory()->create();
        $user->assignRole(UserRole::Invitado->value);

        $programa = Programas::factory()->create([
            'show' => true,
            'pedidos_visible_until' => null,
        ]);

        $solicitud = app(ProgramaSolicitudService::class)->submit($user, $programa);

        $this->assertSame(ProgramaSolicitudStatus::Pending, $solicitud->status);
        $this->assertSame(99, $solicitud->telegram_message_id);
        Http::assertSentCount(1);
    }

    public function test_accept_enables_pedidos_visibility(): void
    {
        Http::fake([
            'api.telegram.org/*' => Http::response(['ok' => true]),
        ]);

        $programa = Programas::factory()->create([
            'show' => true,
            'pedidos_visible_until' => null,
        ]);

        $solicitud = ProgramaSolicitud::query()->create([
            'user_id' => User::factory()->create()->id,
            'programas_id' => $programa->id,
            'status' => ProgramaSolicitudStatus::Pending,
            'telegram_chat_id' => '123456789',
            'telegram_message_id' => 1,
        ]);

        app(ProgramaSolicitudService::class)->accept($solicitud);

        $programa->refresh();
        $solicitud->refresh();

        $this->assertTrue($programa->isVisibleInPedidos());
        $this->assertSame(ProgramaSolicitudStatus::Accepted, $solicitud->status);
    }

    public function test_reject_does_not_enable_pedidos(): void
    {
        Http::fake([
            'api.telegram.org/*' => Http::response(['ok' => true]),
        ]);

        $programa = Programas::factory()->create([
            'show' => true,
            'pedidos_visible_until' => null,
        ]);

        $solicitud = ProgramaSolicitud::query()->create([
            'user_id' => User::factory()->create()->id,
            'programas_id' => $programa->id,
            'status' => ProgramaSolicitudStatus::Pending,
            'telegram_chat_id' => '123456789',
            'telegram_message_id' => 1,
        ]);

        app(ProgramaSolicitudService::class)->reject($solicitud);

        $programa->refresh();

        $this->assertFalse($programa->isVisibleInPedidos());
    }

    public function test_webhook_handles_accept_callback(): void
    {
        Http::fake([
            'api.telegram.org/*' => Http::response(['ok' => true]),
        ]);

        $programa = Programas::factory()->create([
            'show' => true,
            'pedidos_visible_until' => null,
        ]);

        $solicitud = ProgramaSolicitud::query()->create([
            'user_id' => User::factory()->create()->id,
            'programas_id' => $programa->id,
            'status' => ProgramaSolicitudStatus::Pending,
            'telegram_chat_id' => '123456789',
            'telegram_message_id' => 1,
        ]);

        $this->postJson(route('telegram.webhook'), [
            'callback_query' => [
                'id' => 'cb-1',
                'data' => "ps:{$solicitud->id}:a",
            ],
        ])->assertNoContent();

        $this->assertTrue($programa->fresh()->isVisibleInPedidos());
    }

    public function test_telegram_service_reports_configured(): void
    {
        $this->assertTrue(app(TelegramBotService::class)->isConfigured());
    }

    public function test_admin_solicitar_cycle_toggles_pedidos_visibility(): void
    {
        Http::fake([
            'api.telegram.org/*' => Http::response(['ok' => true]),
        ]);

        $programa = Programas::factory()->create([
            'show' => true,
            'pedidos_visible_until' => null,
        ]);

        $service = app(ProgramaSolicitudService::class);

        $this->assertSame('inactivo', $service->adminSolicitarStatus($programa));

        $service->adminCycleSolicitarState($programa);

        $this->assertTrue($programa->fresh()->isVisibleInPedidos());
        $this->assertSame('en_pedidos', $service->adminSolicitarStatus($programa->fresh()));

        $service->adminCycleSolicitarState($programa->fresh());

        $this->assertFalse($programa->fresh()->isVisibleInPedidos());
        $this->assertSame('inactivo', $service->adminSolicitarStatus($programa->fresh()));
    }

    public function test_admin_solicitar_cycle_accepts_pending_solicitudes(): void
    {
        Http::fake([
            'api.telegram.org/*' => Http::response(['ok' => true]),
        ]);

        $programa = Programas::factory()->create([
            'show' => true,
            'pedidos_visible_until' => null,
        ]);

        $solicitud = ProgramaSolicitud::query()->create([
            'user_id' => User::factory()->create()->id,
            'programas_id' => $programa->id,
            'status' => ProgramaSolicitudStatus::Pending,
            'telegram_chat_id' => '123456789',
            'telegram_message_id' => 1,
        ]);

        $service = app(ProgramaSolicitudService::class);

        $this->assertSame('pendiente', $service->adminSolicitarStatus($programa));

        $service->adminCycleSolicitarState($programa);

        $programa->refresh();
        $solicitud->refresh();

        $this->assertTrue($programa->isVisibleInPedidos());
        $this->assertSame(ProgramaSolicitudStatus::Accepted, $solicitud->status);
        $this->assertSame('en_pedidos', $service->adminSolicitarStatus($programa));
    }
}
