#!/bin/bash
# Deploy manual cuando Forge no actualiza el servidor.
#
# Opción 1 — Terminal web Forge: Server > programas.space > Terminal (o SSH)
# Opción 2 — SSH local:
#   ssh forge@TU_IP
#   cd /home/forge/programas.space && bash scripts/forge-fix-deploy.sh
#
# Si el script no existe aún en el servidor, pega el bloque de abajo (ONELINER).

set -euo pipefail

SITE="${FORGE_SITE_PATH:-/home/forge/programas.space}"
BRANCH="${FORGE_SITE_BRANCH:-main}"

cd "$SITE"

echo "==> Deploy manual en: $(pwd)"
echo "==> Rama: $BRANCH"
echo "==> Commit ANTES: $(git log -1 --oneline)"

echo "==> git fetch..."
git fetch origin "$BRANCH"

echo "==> git reset --hard origin/$BRANCH"
git reset --hard "origin/$BRANCH"

COMMIT=$(git rev-parse --short HEAD)
echo "==> Commit DESPUÉS: $(git log -1 --oneline)"

echo "==> composer install..."
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts
composer dump-autoload --optimize

echo "==> npm build (Vite / Filament theme)..."
npm ci --no-audit --no-fund
npm run build

echo "==> Laravel..."
php artisan migrate --force
php artisan optimize:clear
php artisan filament:optimize-clear 2>/dev/null || true
php artisan app:ensure-admin
php artisan config:cache

echo "$COMMIT-$(date +%Y%m%d-%H%M)" > public/deploy-marker.txt

echo ""
echo "==> Verificación:"
grep 'navigationIcon' app/Filament/Admin/Resources/Admins/AdminResource.php | head -1
echo "deploy-marker: $(cat public/deploy-marker.txt)"
echo ""
echo "LISTO. Prueba en el navegador:"
echo "  https://programas.space/"
echo "  https://programas.space/deploy-marker.txt  (debe empezar con $COMMIT)"
echo "  https://programas.space/admin/login"
