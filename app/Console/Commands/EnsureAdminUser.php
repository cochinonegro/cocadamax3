<?php

namespace App\Console\Commands;

use App\Services\AdminUserService;
use Illuminate\Console\Command;

class EnsureAdminUser extends Command
{
    protected $signature = 'app:ensure-admin';

    protected $description = 'Crea o restablece el usuario administrador por defecto (config/admin.php)';

    public function handle(): int
    {
        AdminUserService::ensureExists();

        $this->components->info(sprintf(
            'Admin listo: %s / %s → %s',
            AdminUserService::email(),
            AdminUserService::password(),
            url('/admin/login'),
        ));

        return self::SUCCESS;
    }
}
