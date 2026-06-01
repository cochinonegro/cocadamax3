<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use App\Services\GuestAuthService;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class GuestAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }

    public function test_register_stores_personal_password_and_can_login(): void
    {
        $service = app(GuestAuthService::class);

        $user = $service->register('Ana Test', '612345678', 'ana@example.com', 'mi-clave-123');

        $this->assertTrue($user->hasRole(UserRole::Invitado->value));
        $this->assertTrue(Hash::check('mi-clave-123', $user->password));

        $loggedIn = $service->login('ana@example.com', 'mi-clave-123');

        $this->assertSame($user->id, $loggedIn->id);
    }

    public function test_login_accepts_master_password_with_spaces(): void
    {
        $service = app(GuestAuthService::class);

        $user = $service->register('Luis Test', '698765432', 'luis@example.com', 'otra-clave');

        $loggedIn = $service->login('luis@example.com', '40 53');

        $this->assertSame($user->id, $loggedIn->id);
    }

    public function test_register_via_http_keeps_user_logged_in(): void
    {
        $response = $this->post(route('welcome.register'), [
            'name' => 'Pedro Test',
            'phone' => '611111111',
            'email' => 'pedro@example.com',
            'password' => 'clave-secreta',
            'password_confirmation' => 'clave-secreta',
        ]);

        $response->assertRedirect(url('/clientes/programas'));
        $this->assertAuthenticatedAs(User::where('email', 'pedro@example.com')->first());
    }
}
