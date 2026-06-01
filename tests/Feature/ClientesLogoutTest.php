<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientesLogoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }

    public function test_clientes_logout_redirects_to_public_site(): void
    {
        $user = User::factory()->create();
        $user->assignRole(UserRole::Invitado->value);

        $response = $this->actingAs($user)->post(route('filament.clientes.auth.logout'));

        $response->assertRedirect('https://www.programas.space');
        $this->assertGuest();
    }
}
