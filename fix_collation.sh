#!/bin/bash

# Script para corregir collations en MySQL
# Ejecutar en el VPS

echo "=========================================="
echo "  Corrección de Collations MySQL"
echo "  Tours Micaela"
echo "=========================================="
echo ""

# Colores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Configuración de la base de datos
DB_NAME="bd_micaela"
DB_USER="root"

echo -e "${YELLOW}Este script corregirá las collations de la base de datos${NC}"
echo ""
echo "Base de datos: $DB_NAME"
echo ""
read -p "¿Continuar? (s/n): " -n 1 -r
echo ""

if [[ ! $REPLY =~ ^[Ss]$ ]]; then
    echo "Operación cancelada"
    exit 1
fi

echo ""
echo -e "${BLUE}Ingresa la contraseña de MySQL:${NC}"
read -s DB_PASS

echo ""
echo -e "${YELLOW}Paso 1: Cambiando collation de la base de datos...${NC}"

mysql -u $DB_USER -p$DB_PASS -e "ALTER DATABASE $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Base de datos actualizada${NC}"
else
    echo -e "${RED}✗ Error al actualizar base de datos${NC}"
    exit 1
fi

echo ""
echo -e "${YELLOW}Paso 2: Obteniendo lista de tablas...${NC}"

# Obtener todas las tablas
TABLES=$(mysql -u $DB_USER -p$DB_PASS -D $DB_NAME -e "SHOW TABLES;" -s --skip-column-names 2>/dev/null)

if [ -z "$TABLES" ]; then
    echo -e "${RED}✗ No se pudieron obtener las tablas${NC}"
    exit 1
fi

echo -e "${GREEN}✓ Tablas encontradas:${NC}"
echo "$TABLES"

echo ""
echo -e "${YELLOW}Paso 3: Convirtiendo tablas...${NC}"

SUCCESS=0
FAILED=0

for table in $TABLES; do
    echo -n "  Convirtiendo $table... "
    
    mysql -u $DB_USER -p$DB_PASS -D $DB_NAME -e "ALTER TABLE \`$table\` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null
    
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✓${NC}"
        SUCCESS=$((SUCCESS + 1))
    else
        echo -e "${RED}✗${NC}"
        FAILED=$((FAILED + 1))
    fi
done

echo ""
echo -e "${GREEN}=========================================="
echo -e "  Resumen"
echo -e "==========================================${NC}"
echo "  Tablas convertidas: $SUCCESS"
echo "  Tablas con error: $FAILED"
echo ""

if [ $FAILED -eq 0 ]; then
    echo -e "${GREEN}✓ Todas las tablas fueron convertidas exitosamente${NC}"
    echo ""
    echo "Ahora reinicia Apache:"
    echo "  sudo systemctl restart apache2"
    echo ""
    echo "Y prueba el sistema nuevamente"
else
    echo -e "${YELLOW}⚠ Algunas tablas no pudieron ser convertidas${NC}"
    echo "Revisa los errores manualmente"
fi

echo ""
