#!/bin/bash

# Script para agregar configuración de sesiones a todos los controladores
# que usan session_start()

echo "=========================================="
echo "  Agregando session_config.php"
echo "  a controladores con sesiones"
echo "=========================================="
echo ""

# Colores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

FIXED=0
SKIPPED=0

# Buscar archivos PHP que usan session_start()
echo -e "${YELLOW}Buscando controladores con session_start()...${NC}"
echo ""

while IFS= read -r file; do
    # Verificar si ya tiene session_config.php
    if grep -q "session_config.php" "$file"; then
        echo -e "${GREEN}✓ $file (ya tiene configuración)${NC}"
        SKIPPED=$((SKIPPED + 1))
    else
        echo -e "${YELLOW}→ Actualizando $file${NC}"
        
        # Crear backup
        cp "$file" "$file.bak"
        
        # Reemplazar session_start() con require de session_config
        sed -i 's|session_start();|require_once '"'"'../../utilitario/session_config.php'"'"';|g' "$file"
        
        if [ $? -eq 0 ]; then
            echo -e "${GREEN}  ✓ Actualizado${NC}"
            FIXED=$((FIXED + 1))
        else
            echo -e "${RED}  ✗ Error al actualizar${NC}"
            # Restaurar backup
            mv "$file.bak" "$file"
        fi
    fi
done < <(grep -rl "session_start()" controller/ --include="*.php" 2>/dev/null)

echo ""
echo -e "${GREEN}=========================================="
echo -e "  Resumen"
echo -e "==========================================${NC}"
echo "  Archivos actualizados: $FIXED"
echo "  Archivos omitidos: $SKIPPED"
echo ""
echo "Los backups están en *.bak"
echo "Si algo falla, puedes restaurarlos con:"
echo "  find controller/ -name '*.bak' -exec bash -c 'mv \"\$0\" \"\${0%.bak}\"' {} \;"
echo ""
