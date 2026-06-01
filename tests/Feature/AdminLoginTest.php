<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Services\AdminUserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_admin_exists_with_configured_credentials(): void
    {
        $admin = AdminUserService::ensureExists();

        $this->assertSame(AdminUserService::email(), $admin->email);
        $this->assertTrue(Hash::check(AdminUserService::password(), $admin->password));
        $this->assertTrue($admin->hasRole(UserRole::Administrador->value));
    }

    public function test_default_admin_can_login_via_welcome_and_reach_admin_area(): void
    {
        AdminUserService::ensureExists();

        $response = $this->post(route('welcome.login'), [
            'login' => AdminUserService::email(),
            'password' => AdminUserService::password(),
        ]);

        $response->assertRedirect(url('/admin'));
        $this->assertAuthenticated();
    }
}
