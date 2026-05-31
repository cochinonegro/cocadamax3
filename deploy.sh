#!/bin/bash
# Script de despliegue para Laravel Forge
# Pegar en: Forge > Sites > programas.space > App > Deployment Script
#
# Si Forge no despliega automáticamente, usa por SSH:
#   cd /home/forge/programas.space && bash scripts/forge-fix-deploy.sh

set -euo pipefail

cd "${FORGE_SITE_PATH:-/home/forge/programas.space}"

echo "==> Deploy en: $(pwd)"

git fetch origin "${FORGE_SITE_BRANCH:-main}"
git reset --hard "origin/${FORGE_SITE_BRANCH:-main}"

COMMIT=$(git rev-parse --short HEAD)
echo "==> Commit: $(git log -1 --oneline)"

composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

php artisan migrate --force
php artisan optimize:clear
php artisan filament:optimize-clear 2>/dev/null || true
php artisan app:ensure-admin
php artisan config:cache

echo "$COMMIT-$(date +%Y%m%d-%H%M)" > public/deploy-marker.txt

echo "Deploy OK — https://programas.space/deploy-marker.txt ($COMMIT)"
