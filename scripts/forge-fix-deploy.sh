#!/bin/bash
# Forzar deploy en Forge (proyecto en raíz, sin carpeta /current)
#
# Por SSH:
#   cd /home/forge/programas.space && bash scripts/forge-fix-deploy.sh

set -euo pipefail

SITE="/home/forge/programas.space"
cd "$SITE"

echo "==> Directorio: $(pwd)"
echo "==> Commit ANTES:"
git log -1 --oneline

echo "==> Actualizando desde GitHub..."
git fetch origin main
git reset --hard origin/main

echo "==> Commit DESPUÉS:"
git log -1 --oneline

echo "==> Composer..."
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

echo "==> Laravel..."
php artisan migrate --force
php artisan optimize:clear
php artisan filament:optimize-clear 2>/dev/null || true
php artisan app:ensure-admin
php artisan config:cache

echo "==> Verificación archivos:"
head -3 resources/views/welcome.blade.php
cat public/deploy-marker.txt 2>/dev/null || echo "AVISO: falta public/deploy-marker.txt"

echo ""
echo "LISTO. Comprueba en el navegador:"
echo "  https://programas.space/deploy-marker.txt"
echo "  https://programas.space/deploy-check"
echo "  https://programas.space/"
