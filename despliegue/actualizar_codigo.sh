#!/usr/bin/env bash
# =====================================================================
# Actualiza el código del sistema en el VPS SIN reconstruir la imagen.
# El código está montado en el contenedor (docker-compose.vps.yml), así que
# basta con traer los cambios de git y dar permisos.
#
#   cd ~/sistema_micaela
#   bash despliegue/actualizar_codigo.sh            # cambios visibles en ~1 minuto
#   bash despliegue/actualizar_codigo.sh --ya       # además reinicia la app (inmediato, corte de ~5 s)
#
# Cuándo SÍ hay que reconstruir (build --pull): parches de PHP, cambios en el
# Dockerfile, en composer.json/vendor de la raíz o en docker-compose.vps.yml.
# Si un cambio necesita base de datos (procedimientos/tablas), aplicar su migración aparte.
# =====================================================================
set -euo pipefail

CARPETA="$(cd "$(dirname "$0")/.." && pwd)"
RAMA="${RAMA:-jersson}"
CONTENEDOR="tours_micaela_app_vps"
cd "$CARPETA"

echo "== 1/3 Cambios de git (rama $RAMA)"
ANTES="$(git rev-parse --short HEAD)"
git pull --ff-only origin "$RAMA"
DESPUES="$(git rev-parse --short HEAD)"
if [ "$ANTES" = "$DESPUES" ]; then
  echo "   No había cambios nuevos ($DESPUES)."
else
  echo "   $ANTES -> $DESPUES"
  git --no-pager log --oneline "$ANTES..$DESPUES"
  # Avisar si el cambio requiere reconstruir la imagen
  if git diff --name-only "$ANTES" "$DESPUES" | grep -qE '^(Dockerfile|Dockerfile\.production|docker-compose\.vps\.yml|composer\.(json|lock)|\.dockerignore)$'; then
    echo "   ⚠️  Este cambio toca Docker o composer: hay que reconstruir:"
    echo "      docker compose -f docker-compose.vps.yml build --pull app && docker compose -f docker-compose.vps.yml up -d app"
  fi
  if git diff --name-only "$ANTES" "$DESPUES" | grep -qE '\.sql$'; then
    echo "   ⚠️  Este cambio trae archivos .sql: revisa si hay una migración de base de datos que aplicar."
  fi
fi

echo "== 2/3 Permisos (la app corre como www-data, uid 33)"
chown -R 33:33 controller model view js img plantilla utilitario greenter Fotos backup index.php seguimiento.php default.php
chmod 640 .env.nubefact 2>/dev/null && chown 33:33 .env.nubefact 2>/dev/null || true

echo "== 3/3 Aplicar"
if [ "${1:-}" = "--ya" ]; then
  docker restart "$CONTENEDOR" >/dev/null
  echo "   ✅ App reiniciada: los cambios ya están activos."
else
  echo "   ✅ Listo. PHP toma los cambios en máximo 1 minuto (caché OPcache)."
  echo "      Para verlos al instante: bash despliegue/actualizar_codigo.sh --ya"
fi
