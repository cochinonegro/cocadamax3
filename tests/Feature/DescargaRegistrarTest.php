<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Descarga;
use App\Models\Programas;
use App\Models\User;
use App\Support\DescargaRegistrar;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DescargaRegistrarTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }

    public function test_registrar_creates_descarga_record(): void
    {
        $user = User::factory()->create();
        $user->assignRole(UserRole::Invitado->value);

        $programa = Programas::factory()->create([
            'show' => true,
            'url' => 'https://example.com/app.zip',
        ]);

        $descarga = DescargaRegistrar::record($programa, $user);

        $this->assertInstanceOf(Descarga::class, $descarga);
        $this->assertSame($user->id, $descarga->user_id);
        $this->assertSame($programa->id, $descarga->programas_id);
        $this->assertNotNull($descarga->downloaded_at);
    }

    public function test_programa_download_controller_logs_descarga(): void
    {
        $user = User::factory()->create();
        $user->assignRole(UserRole::Invitado->value);

        $programa = Programas::factory()->create([
            'show' => true,
            'url' => 'https://example.com/app.zip',
        ]);

        $this->actingAs($user)
            ->get(route('invitado.descarga', $programa))
            ->assertRedirect('https://example.com/app.zip');

        $this->assertDatabaseHas('descargas', [
            'user_id' => $user->id,
            'programas_id' => $programa->id,
        ]);
    }
}
