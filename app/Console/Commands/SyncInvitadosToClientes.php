<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\User;
use App\Services\GuestAuthService;
use Illuminate\Console\Command;

class SyncInvitadosToClientes extends Command
{
    protected $signature = 'clientes:sync-invitados';

    protected $description = 'Crea fichas en clientes para usuarios invitado que aún no tienen registro';

    public function handle(GuestAuthService $guestAuth): int
    {
        $users = User::role(UserRole::Invitado->value)->orderBy('id')->get();

        if ($users->isEmpty()) {
            $this->components->warn('No hay usuarios con rol invitado.');

            return self::SUCCESS;
        }

        $synced = 0;
        $skipped = 0;

        foreach ($users as $user) {
            $cliente = $guestAuth->createClienteRecordForUser($user, 'Registro bienvenida (sync)');

            if ($cliente) {
                $synced++;
                $this->line("Sincronizado: {$user->email} → cliente #{$cliente->id}");
            } else {
                $skipped++;
            }
        }

        $this->components->info("Listo. {$synced} creados, {$skipped} ya existían.");

        return self::SUCCESS;
    }
}
