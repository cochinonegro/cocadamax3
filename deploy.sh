#!/bin/bash
set -e

php artisan optimize:clear
php artisan app:ensure-admin

echo "Listo. Raíz: / | Admin: /admin/login"
