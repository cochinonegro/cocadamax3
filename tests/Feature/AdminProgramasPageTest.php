<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Programas;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProgramasPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }

    public function test_admin_can_open_programas_list_page(): void
    {
        $user = User::factory()->create();
        $user->assignRole(UserRole::Administrador->value);

        Programas::factory()->create(['show' => true]);

        $this->actingAs($user)
            ->get('/admin/programas')
            ->assertOk();
    }
}
