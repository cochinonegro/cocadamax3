#!/bin/bash
# Ejecutar en el servidor Forge por SSH si el deploy automático no actualiza:
#   bash scripts/forge-fix-deploy.sh

set -euo pipefail

SITE="${FORGE_SITE_PATH:-$(pwd)}"
cd "$SITE"

echo "==> Directorio: $SITE"
echo "==> Commit actual:"
git log -1 --oneline || true

echo "==> git pull..."
git fetch origin main
git reset --hard origin/main

echo "==> Composer..."
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

echo "==> Laravel..."
php artisan migrate --force
php artisan optimize:clear
php artisan filament:optimize-clear 2>/dev/null || true
php artisan app:ensure-admin
php artisan config:cache

echo "==> Verificación:"
head -3 resources/views/welcome.blade.php
test -f public/deploy-marker.txt && cat public/deploy-marker.txt || echo "FALTA deploy-marker.txt"

echo "==> LISTO. Prueba: /deploy-check y /"
