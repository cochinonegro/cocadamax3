<?php

/**
 * Usuario administrador por defecto del sistema.
 *
 * Siempre se restaura con: php artisan app:ensure-admin
 * (también en deploy.sh y al ejecutar db:seed).
 *
 * Acceso panel: /admin/login
 */
return [
    'email' => 'admin@gmail.com',
    'password' => '123456',
    'name' => 'Admin',
];
