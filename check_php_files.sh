#!/bin/bash

# Script para verificar archivos PHP
# Busca espacios/caracteres antes de <?php que pueden causar problemas de sesión

echo "=========================================="
echo "  Verificación de Archivos PHP"
echo "  Tours Micaela"
echo "=========================================="
echo ""

# Colores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

ERRORS=0

echo -e "${YELLOW}Buscando archivos PHP con espacios antes de <?php...${NC}"
echo ""

# Buscar archivos PHP con espacios/caracteres antes de <?php
while IFS= read -r file; do
    # Verificar si el archivo tiene contenido antes de <?php
    if head -c 10 "$file" | grep -q "^[[:space:]]"; then
        echo -e "${RED}✗ $file${NC}"
        echo "  Tiene espacios/saltos de línea antes de <?php"
        ERRORS=$((ERRORS + 1))
    fi
done < <(find . -name "*.php" -type f ! -path "./vendor/*" ! -path "./node_modules/*")

echo ""

if [ $ERRORS -eq 0 ]; then
    echo -e "${GREEN}=========================================="
    echo -e "  ✓ Todos los archivos PHP están OK"
    echo -e "==========================================${NC}"
else
    echo -e "${RED}=========================================="
    echo -e "  ✗ Se encontraron $ERRORS archivos con problemas"
    echo -e "==========================================${NC}"
    echo ""
    echo "Para corregir automáticamente, ejecuta:"
    echo "bash fix_php_files.sh"
fi

echo ""
