#!/bin/bash

# Script para configurar permisos de sesiones en VPS
# Ejecutar con: bash fix_sessions_vps.sh

echo "=========================================="
echo "  Configuración de Sesiones - VPS"
echo "  Tours Micaela"
echo "=========================================="
echo ""

# Colores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Obtener el directorio de sesiones
SESSION_DIR="/tmp/php_sessions"

echo -e "${YELLOW}1. Verificando directorio de sesiones...${NC}"
if [ -d "$SESSION_DIR" ]; then
    echo -e "${GREEN}✓ El directorio $SESSION_DIR existe${NC}"
else
    echo -e "${YELLOW}⚠ Creando directorio $SESSION_DIR${NC}"
    mkdir -p "$SESSION_DIR"
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✓ Directorio creado exitosamente${NC}"
    else
        echo -e "${RED}✗ Error al crear el directorio${NC}"
        exit 1
    fi
fi

echo ""
echo -e "${YELLOW}2. Configurando permisos...${NC}"
chmod 700 "$SESSION_DIR"
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Permisos configurados (700)${NC}"
else
    echo -e "${RED}✗ Error al configurar permisos${NC}"
    exit 1
fi

echo ""
echo -e "${YELLOW}3. Configurando propietario...${NC}"
# Obtener el usuario de Apache/Nginx
WEB_USER=$(ps aux | grep -E 'apache|httpd|nginx' | grep -v root | head -1 | awk '{print $1}')

if [ -z "$WEB_USER" ]; then
    echo -e "${YELLOW}⚠ No se pudo detectar el usuario web, usando www-data${NC}"
    WEB_USER="www-data"
fi

echo "Usuario web detectado: $WEB_USER"
chown -R $WEB_USER:$WEB_USER "$SESSION_DIR"
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Propietario configurado${NC}"
else
    echo -e "${YELLOW}⚠ No se pudo cambiar el propietario (puede requerir sudo)${NC}"
fi

echo ""
echo -e "${YELLOW}4. Limpiando sesiones antiguas...${NC}"
find "$SESSION_DIR" -type f -mtime +1 -delete 2>/dev/null
echo -e "${GREEN}✓ Sesiones antiguas eliminadas${NC}"

echo ""
echo -e "${YELLOW}5. Verificando configuración PHP...${NC}"
php -i | grep -E "session.save_path|session.gc_maxlifetime|session.cookie_lifetime" | head -3

echo ""
echo -e "${GREEN}=========================================="
echo -e "  ✓ Configuración completada"
echo -e "==========================================${NC}"
echo ""
echo "Pasos siguientes:"
echo "1. Accede a: http://tu-dominio.com/test_sesiones.php"
echo "2. Verifica que todo esté en verde"
echo "3. Prueba el login del sistema"
echo ""
echo "Si persisten problemas:"
echo "- Verifica los logs de PHP: tail -f /var/log/php-fpm/error.log"
echo "- Verifica los logs de Apache: tail -f /var/log/apache2/error.log"
echo "- Contacta con soporte técnico"
echo ""
