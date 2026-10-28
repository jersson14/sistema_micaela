#!/usr/bin/env bash
# =====================================================================
# Actualiza la base de datos de producción para la rama nueva
# (encomiendas/salidas + Nubefact). Ejecutar en el VPS NUEVO, en la
# carpeta del proyecto, DESPUÉS de "git pull" de la rama con estos cambios:
#
#   cd ~/sistema_micaela/despliegue/2026-11-03
#   bash actualizar_bd.sh
#
# 1. Hace un respaldo completo de la base (en ~/respaldos/).
# 2. Aplica las dos migraciones en orden, con --comments.
# 3. Muestra la verificación.
# Si algo falla, se detiene y NO sigue con el paso siguiente.
# =====================================================================
set -euo pipefail

CONTENEDOR="tours_micaela_db_vps"
CARPETA="$(cd "$(dirname "$0")" && pwd)"
FECHA="$(date +%F_%H%M)"
RESPALDO="$HOME/respaldos/antes_migraciones_$FECHA.sql"

mysql_bd() {
  docker exec -i "$CONTENEDOR" sh -c 'mysql --comments -uroot -p"$MYSQL_ROOT_PASSWORD" micaela' 2>&1 | grep -v "Using a password on the command line"
}

echo "== 1/3 Respaldo completo de la base de datos"
mkdir -p "$HOME/respaldos"
docker exec "$CONTENEDOR" sh -c 'mysqldump -uroot -p"$MYSQL_ROOT_PASSWORD" --single-transaction --routines --triggers --events micaela' 2>/dev/null > "$RESPALDO"
if [ ! -s "$RESPALDO" ]; then
  echo "❌ El respaldo quedó vacío. No se aplicó ninguna migración."
  exit 1
fi
echo "   ✅ $RESPALDO ($(du -h "$RESPALDO" | cut -f1))"

echo "== 2/3 Migraciones"
for archivo in 01_estados_encomiendas_salidas.sql 02_registrar_editar_encomienda.sql; do
  echo "   → $archivo"
  mysql_bd < "$CARPETA/$archivo"
done
echo "   ✅ Migraciones aplicadas"

echo "== 3/3 Verificación (esperado: SP_ELIMINAR_SALIDA_DIARIA=2, SP_MODIFICAR_SALIDA_INCOMPLETO_ESTATUS=4, 0 con otra intercalación)"
mysql_bd < "$CARPETA/03_verificar.sql"

echo
echo "Listo. Para volver atrás:"
echo "  docker exec -i $CONTENEDOR sh -c 'mysql --comments -uroot -p\"\$MYSQL_ROOT_PASSWORD\" micaela' < $RESPALDO"
