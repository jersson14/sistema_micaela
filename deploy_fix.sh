#!/bin/bash

# Script de despliegue automático para solución de sesiones
# Ejecutar en el VPS después de hacer git pull

echo "═══════════════════════════════════════════════════════════════"
echo "  🚀 DESPLIEGUE AUTOMÁTICO - SOLUCIÓN DE SESIONES"
echo "  Tours Micaela"
echo "═══════════════════════════════════════════════════════════════"
echo ""

# Colores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Función para mostrar paso
show_step() {
    echo -e "${BLUE}▶ $1${NC}"
}

# Función para mostrar éxito
show_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

# Función para mostrar error
show_error() {
    echo -e "${RED}✗ $1${NC}"
}

# Función para mostrar advertencia
show_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

# Verificar que estamos en el directorio correcto
if [ ! -f "index.php" ]; then
    show_error "No se encontró index.php. ¿Estás en el directorio correcto?"
    exit 1
fi

show_success "Directorio del proyecto verificado"
echo ""

# Paso 1: Verificar archivos necesarios
show_step "Paso 1: Verificando archivos necesarios..."
FILES=(
    "utilitario/session_config.php"
    "test_sesiones.php"
    "fix_sessions_vps.sh"
)

MISSING=0
for file in "${FILES[@]}"; do
    if [ -f "$file" ]; then
        show_success "$file encontrado"
    else
        show_error "$file NO encontrado"
        MISSING=$((MISSING + 1))
    fi
done

if [ $MISSING -gt 0 ]; then
    show_error "Faltan $MISSING archivos. Ejecuta 'git pull' primero."
    exit 1
fi
echo ""

# Paso 2: Configurar directorio de sesiones
show_step "Paso 2: Configurando directorio de sesiones..."
SESSION_DIR="/tmp/php_sessions"

if [ -d "$SESSION_DIR" ]; then
    show_success "Directorio $SESSION_DIR existe"
else
    mkdir -p "$SESSION_DIR" 2>/dev/null
    if [ $? -eq 0 ]; then
        show_success "Directorio $SESSION_DIR creado"
    else
        show_warning "No se pudo crear $SESSION_DIR (intentando con sudo)"
        sudo mkdir -p "$SESSION_DIR"
        if [ $? -eq 0 ]; then
            show_success "Directorio creado con sudo"
        else
            show_error "No se pudo crear el directorio"
            exit 1
        fi
    fi
fi
echo ""

# Paso 3: Configurar permisos
show_step "Paso 3: Configurando permisos..."
chmod 700 "$SESSION_DIR" 2>/dev/null
if [ $? -eq 0 ]; then
    show_success "Permisos configurados (700)"
else
    sudo chmod 700 "$SESSION_DIR"
    if [ $? -eq 0 ]; then
        show_success "Permisos configurados con sudo"
    else
        show_error "No se pudieron configurar permisos"
    fi
fi
echo ""

# Paso 4: Configurar propietario
show_step "Paso 4: Configurando propietario..."
WEB_USER=$(ps aux | grep -E 'apache|httpd|nginx' | grep -v root | head -1 | awk '{print $1}')

if [ -z "$WEB_USER" ]; then
    show_warning "No se detectó usuario web, usando www-data"
    WEB_USER="www-data"
else
    show_success "Usuario web detectado: $WEB_USER"
fi

chown -R $WEB_USER:$WEB_USER "$SESSION_DIR" 2>/dev/null
if [ $? -eq 0 ]; then
    show_success "Propietario configurado"
else
    sudo chown -R $WEB_USER:$WEB_USER "$SESSION_DIR"
    if [ $? -eq 0 ]; then
        show_success "Propietario configurado con sudo"
    else
        show_warning "No se pudo cambiar propietario (puede no ser necesario)"
    fi
fi
echo ""

# Paso 5: Limpiar sesiones antiguas
show_step "Paso 5: Limpiando sesiones antiguas..."
find "$SESSION_DIR" -type f -mtime +1 -delete 2>/dev/null
show_success "Sesiones antiguas eliminadas"
echo ""

# Paso 6: Verificar permisos de archivos PHP
show_step "Paso 6: Verificando permisos de archivos..."
find . -name "*.php" -type f ! -path "./vendor/*" -exec chmod 644 {} \; 2>/dev/null
show_success "Permisos de archivos PHP configurados"
echo ""

# Paso 7: Detectar y reiniciar servidor web
show_step "Paso 7: Reiniciando servidor web..."

# Detectar tipo de servidor
if systemctl is-active --quiet apache2; then
    SERVER="apache2"
elif systemctl is-active --quiet httpd; then
    SERVER="httpd"
elif systemctl is-active --quiet nginx; then
    SERVER="nginx"
else
    show_warning "No se detectó servidor web activo"
    SERVER=""
fi

if [ -n "$SERVER" ]; then
    show_success "Servidor detectado: $SERVER"
    
    sudo systemctl restart $SERVER 2>/dev/null
    if [ $? -eq 0 ]; then
        show_success "Servidor $SERVER reiniciado"
    else
        show_warning "No se pudo reiniciar $SERVER (puede requerir permisos)"
    fi
    
    # Si es nginx, también reiniciar PHP-FPM
    if [ "$SERVER" = "nginx" ]; then
        PHP_FPM=$(systemctl list-units --type=service | grep php-fpm | awk '{print $1}' | head -1)
        if [ -n "$PHP_FPM" ]; then
            sudo systemctl restart $PHP_FPM 2>/dev/null
            if [ $? -eq 0 ]; then
                show_success "PHP-FPM reiniciado"
            fi
        fi
    fi
fi
echo ""

# Paso 8: Verificar configuración PHP
show_step "Paso 8: Verificando configuración PHP..."
echo ""
php -i | grep -E "session.save_path|session.gc_maxlifetime|session.cookie_lifetime" | head -3
echo ""

# Paso 9: Mostrar resumen
echo "═══════════════════════════════════════════════════════════════"
echo -e "${GREEN}  ✓ CONFIGURACIÓN COMPLETADA${NC}"
echo "═══════════════════════════════════════════════════════════════"
echo ""
echo "📋 Resumen:"
echo "  • Directorio de sesiones: $SESSION_DIR"
echo "  • Permisos: 700"
echo "  • Propietario: $WEB_USER"
echo "  • Servidor web: ${SERVER:-No detectado}"
echo ""
echo "🔍 Próximos pasos:"
echo ""
echo "  1. Accede a: http://tu-dominio.com/test_sesiones.php"
echo "     ✅ Verifica que todo esté en verde"
echo ""
echo "  2. Prueba el login: http://tu-dominio.com"
echo "     ✅ Debe funcionar sin errores 500"
echo ""
echo "  3. Si hay problemas, revisa los logs:"
echo "     tail -f /var/log/apache2/error.log"
echo "     tail -f /var/log/php-fpm/error.log"
echo ""
echo "═══════════════════════════════════════════════════════════════"
echo ""
