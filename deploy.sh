#!/bin/bash
set -euo pipefail

# Script de despliegue para Laravel Forge
# Pegar en: Site > Deployment Script (Forge ejecuta desde $FORGE_SITE_PATH)

if [ -n "${FORGE_SITE_PATH:-}" ]; then
    cd "$FORGE_SITE_PATH"
fi

echo "==> Deploy en: $(pwd)"

git pull origin "${FORGE_SITE_BRANCH:-main}"

composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

php artisan migrate --force

# Limpiar TODAS las cachés (crítico para rutas/vistas antiguas)
php artisan optimize:clear
php artisan filament:optimize-clear 2>/dev/null || true

php artisan app:ensure-admin

# Marcador visible para verificar despliegue: https://tudominio/deploy-marker.txt
echo "guest-welcome-$(date +%Y%m%d-%H%M%S)" > public/deploy-marker.txt

# NO usar route:cache ni view:cache aquí (evita servir rutas/vistas viejas)
php artisan config:cache

echo "Deploy OK — / = bienvenida | /admin/login = admin"
