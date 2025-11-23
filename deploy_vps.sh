#!/bin/bash

# ============================================
# Script de Despliegue Automático para VPS
# Tours Micaela - Docker Compose Completo
# ============================================

set -e  # Detener en caso de error

echo "🚀 Iniciando despliegue en VPS..."
echo ""

# Colores para output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Verificar que estamos en el directorio correcto
if [ ! -f "docker-compose.vps.yml" ]; then
    echo -e "${RED}❌ Error: No se encuentra docker-compose.vps.yml${NC}"
    echo "Asegúrate de estar en el directorio del proyecto"
    exit 1
fi

# Verificar que Docker está instalado
if ! command -v docker &> /dev/null; then
    echo -e "${RED}❌ Docker no está instalado${NC}"
    echo "Instala Docker primero: curl -fsSL https://get.docker.com | sh"
    exit 1
fi

# Verificar que Docker Compose está instalado
if ! command -v docker-compose &> /dev/null; then
    echo -e "${RED}❌ Docker Compose no está instalado${NC}"
    exit 1
fi

echo -e "${GREEN}✅ Docker y Docker Compose detectados${NC}"
echo ""

# Verificar archivo .env
if [ ! -f ".env" ]; then
    echo -e "${YELLOW}⚠️  No se encuentra archivo .env${NC}"
    echo "Copiando .env.vps a .env..."
    cp .env.vps .env
    echo -e "${YELLOW}⚠️  IMPORTANTE: Edita el archivo .env y cambia las contraseñas${NC}"
    echo "Presiona ENTER para continuar o CTRL+C para cancelar..."
    read
fi

# Crear directorios necesarios
echo "📁 Creando directorios necesarios..."
mkdir -p greenter/xml greenter/cdr greenter/pdf greenter/certificados
mkdir -p Fotos controller/usuario/fotos controller/choferes/fotos controller/empresa/FOTOS
mkdir -p backup
chmod -R 755 greenter Fotos controller backup
echo -e "${GREEN}✅ Directorios creados${NC}"
echo ""

# Backup del archivo de conexión original
if [ -f "model/model_conexion.php" ] && [ ! -f "model/model_conexion_local.php.bak" ]; then
    echo "💾 Haciendo backup de model_conexion.php..."
    cp model/model_conexion.php model/model_conexion_local.php.bak
    echo -e "${GREEN}✅ Backup creado${NC}"
fi

# Reemplazar archivo de conexión
echo "🔄 Configurando archivo de conexión para VPS..."
cp model/model_conexion_vps.php model/model_conexion.php
echo -e "${GREEN}✅ Archivo de conexión actualizado${NC}"
echo ""

# Detener contenedores existentes si los hay
echo "🛑 Deteniendo contenedores existentes (si los hay)..."
docker-compose -f docker-compose.vps.yml down 2>/dev/null || true
echo ""

# Construir y levantar servicios
echo "🏗️  Construyendo y levantando servicios..."
echo "Esto puede tomar varios minutos la primera vez..."
docker-compose -f docker-compose.vps.yml up -d --build

echo ""
echo "⏳ Esperando que los servicios estén listos..."
sleep 10

# Verificar estado de los contenedores
echo ""
echo "📊 Estado de los contenedores:"
docker-compose -f docker-compose.vps.yml ps

echo ""
echo -e "${GREEN}✅ ¡Despliegue completado!${NC}"
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "📝 INFORMACIÓN DE ACCESO"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "🌐 Aplicación Web:"
echo "   http://$(hostname -I | awk '{print $1}')"
echo ""
echo "🗄️  phpMyAdmin:"
echo "   http://$(hostname -I | awk '{print $1}'):8081"
echo "   Usuario: root"
echo "   Contraseña: (la que configuraste en .env)"
echo ""
echo "🐬 MySQL (acceso externo):"
echo "   Host: $(hostname -I | awk '{print $1}')"
echo "   Puerto: 3307"
echo "   Usuario: micaela_user"
echo "   Contraseña: (la que configuraste en .env)"
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "📋 PRÓXIMOS PASOS:"
echo ""
echo "1. Importa tu base de datos usando phpMyAdmin"
echo "   http://$(hostname -I | awk '{print $1}'):8081"
echo ""
echo "2. Verifica que la aplicación funcione correctamente"
echo ""
echo "3. Ver logs en tiempo real:"
echo "   docker-compose -f docker-compose.vps.yml logs -f"
echo ""
echo "4. Comandos útiles:"
echo "   - Detener: docker-compose -f docker-compose.vps.yml down"
echo "   - Reiniciar: docker-compose -f docker-compose.vps.yml restart"
echo "   - Ver logs: docker-compose -f docker-compose.vps.yml logs -f app"
echo ""
echo -e "${YELLOW}⚠️  IMPORTANTE: Asegúrate de haber configurado el firewall${NC}"
echo "   sudo ufw allow 80/tcp"
echo "   sudo ufw allow 8081/tcp"
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
