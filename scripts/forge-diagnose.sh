#!/bin/bash
# Diagnóstico de deploy en Forge — ejecutar en el servidor:
#   cd /home/forge/programas.space && bash scripts/forge-diagnose.sh

set -uo pipefail

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

ok()   { echo -e "${GREEN}OK${NC}    $*"; }
warn() { echo -e "${YELLOW}AVISO${NC} $*"; }
fail() { echo -e "${RED}FALLO${NC} $*"; }

SITE="${FORGE_SITE_PATH:-/home/forge/programas.space}"

echo "========================================"
echo "  Diagnóstico Forge — programas.space"
echo "========================================"
echo ""

if [ ! -d "$SITE" ]; then
    fail "No existe el directorio: $SITE"
    echo ""
    echo "Sitios posibles:"
    ls -d /home/forge/*/ 2>/dev/null | head -10 || true
    exit 1
fi

cd "$SITE"
echo "Directorio: $(pwd)"
echo ""

echo "--- Git ---"
if [ ! -d .git ]; then
    fail "No hay repositorio git aquí. Forge no clonó el proyecto en esta ruta."
    exit 1
fi

echo "Remote:"
git remote -v 2>/dev/null || fail "Sin remote configurado"
echo ""
echo "Rama local: $(git branch --show-current 2>/dev/null || echo '?')"
echo "Commit local:  $(git log -1 --oneline 2>/dev/null || echo '?')"
echo ""

echo "Comprobando origin/main..."
if git fetch origin main 2>&1; then
    REMOTE_COMMIT=$(git log -1 --oneline origin/main 2>/dev/null || echo "?")
    echo "Commit remoto: $REMOTE_COMMIT"
    if git diff --quiet HEAD origin/main 2>/dev/null; then
        ok "Local = remoto (mismo commit)"
    else
        warn "Local DESACTUALIZADO respecto a origin/main"
        echo "       Ejecuta: bash scripts/forge-fix-deploy.sh"
    fi
else
    fail "git fetch falló — revisa acceso SSH a GitHub en Forge"
fi
echo ""

echo "--- Archivo crítico (AdminResource) ---"
ADMIN_FILE="app/Filament/Admin/Resources/Admins/AdminResource.php"
if [ -f "$ADMIN_FILE" ]; then
    grep 'navigationIcon' "$ADMIN_FILE" || true
    if grep -q 'BackedEnum' "$ADMIN_FILE" 2>/dev/null; then
        fail "Código VIEJO (BackedEnum) — necesitas deploy"
    else
        ok "navigationIcon corregido (?string)"
    fi
else
    warn "No existe $ADMIN_FILE"
fi
echo ""

echo "--- Nginx / web root ---"
NGINX_ROOT=$(grep -r "root " /etc/nginx/sites-enabled/ 2>/dev/null | grep programas | head -1 || true)
if [ -n "$NGINX_ROOT" ]; then
    echo "$NGINX_ROOT"
else
    warn "No se pudo leer nginx (normal si no eres root)"
fi
echo "public/index.php: $([ -f public/index.php ] && echo existe || echo NO EXISTE)"
echo "deploy-marker:    $(cat public/deploy-marker.txt 2>/dev/null || echo '(vacío)')"
echo ""

echo "--- PHP / Composer ---"
php -v 2>/dev/null | head -1 || fail "php no encontrado"
composer --version 2>/dev/null | head -1 || warn "composer no encontrado"
echo ""

echo "--- Forge env ---"
echo "FORGE_SITE_PATH=${FORGE_SITE_PATH:-(no definido)}"
echo "FORGE_SITE_BRANCH=${FORGE_SITE_BRANCH:-main}"
echo ""

echo "========================================"
echo "Si local está desactualizado, ejecuta:"
echo "  cd $SITE && bash scripts/forge-fix-deploy.sh"
echo "========================================"
