#!/bin/bash
set -euo pipefail

if [ -n "${FORGE_SITE_PATH:-}" ]; then
    cd "$FORGE_SITE_PATH"
fi

echo "==> Deploy en: $(pwd)"

git fetch origin "${FORGE_SITE_BRANCH:-main}"
git reset --hard "origin/${FORGE_SITE_BRANCH:-main}"

composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

php artisan migrate --force
php artisan optimize:clear
php artisan filament:optimize-clear 2>/dev/null || true
php artisan app:ensure-admin
php artisan config:cache

echo "Deploy OK — verifica /deploy-check y /deploy-marker.txt"
