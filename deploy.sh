#!/bin/bash
# Script de despliegue para Laravel Forge
# Site path: /home/forge/programas.space (sin /current)
# Pegar en: Forge > programas.space > Deployment Script

set -euo pipefail

cd /home/forge/programas.space

echo "==> Deploy en: $(pwd)"

git fetch origin "${FORGE_SITE_BRANCH:-main}"
git reset --hard "origin/${FORGE_SITE_BRANCH:-main}"

composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

php artisan migrate --force
php artisan optimize:clear
php artisan filament:optimize-clear 2>/dev/null || true
php artisan app:ensure-admin
php artisan config:cache

echo "Deploy OK — https://programas.space/deploy-check"
