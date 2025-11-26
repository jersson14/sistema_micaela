#!/bin/bash

# ============================================
# Script de Despliegue Simplificado para VPS
# Tours Micaela - SIN Nginx (Método Directo)
# ============================================

set -e

echo "🚀 Iniciando despliegue simplificado en VPS..."
echo ""

# Colores
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m'

# Configuración
DOMAIN="micaela-tours.com"
EMAIL="admin@micaela-tours.com"
SERVER_IP=$(hostname -I | awk '{print $1}')

# Verificar archivos necesarios
if [ ! -f "docker-compose.vps.yml" ]; then
    echo -e "${RED}❌ Error: No se encuentra docker-compose.vps.yml${NC}"
    exit 1
fi

# Verificar Docker
if ! command -v docker &> /dev/null; then
    echo -e "${RED}❌ Docker no está instalado${NC}"
    echo "Instala Docker: curl -fsSL https://get.docker.com | sh"
    exit 1
fi

if ! command -v docker-compose &> /dev/null; then
    echo -e "${RED}❌ Docker Compose no está instalado${NC}"
    exit 1
fi

echo -e "${GREEN}✅ Docker y Docker Compose detectados${NC}"
echo ""

# Verificar .env
if [ ! -f ".env" ]; then
    echo -e "${YELLOW}⚠️  No se encuentra archivo .env${NC}"
    echo "Copiando .env.vps a .env..."
    cp .env.vps .env
    echo -e "${YELLOW}⚠️  Edita .env y cambia las contraseñas${NC}"
    echo "Presiona ENTER para continuar..."
    read
fi

# Crear directorios
echo "📁 Creando directorios necesarios..."
mkdir -p greenter/{xml,cdr,pdf,certificados}
mkdir -p Fotos controller/{usuario,choferes,empresa}/fotos controller/empresa/FOTOS
mkdir -p backup
chmod -R 755 greenter Fotos controller backup
echo -e "${GREEN}✅ Directorios creados${NC}"
echo ""

# Backup de conexión
if [ -f "model/model_conexion.php" ] && [ ! -f "model/model_conexion_local.php.bak" ]; then
    echo "💾 Backup de model_conexion.php..."
    cp model/model_conexion.php model/model_conexion_local.php.bak
fi

# Actualizar conexión
echo "🔄 Configurando archivo de conexión..."
cp model/model_conexion_vps.php model/model_conexion.php
echo -e "${GREEN}✅ Conexión actualizada${NC}"
echo ""

# ==========================================
# OPCIÓN 1: DESACTIVAR SERVICIOS CONFLICTIVOS
# ==========================================
echo "🛑 Liberando puertos 80 y 443..."

# Detener Apache si existe
if systemctl is-active --quiet apache2 2>/dev/null; then
    echo "   Deteniendo Apache2..."
    systemctl stop apache2
    systemctl disable apache2
    echo -e "${GREEN}   ✅ Apache2 detenido${NC}"
fi

# Detener Nginx si existe
if systemctl is-active --quiet nginx 2>/dev/null; then
    echo "   Deteniendo Nginx..."
    systemctl stop nginx
    systemctl disable nginx
    echo -e "${GREEN}   ✅ Nginx detenido${NC}"
fi

# Verificar que los puertos estén libres
echo "🔍 Verificando puertos..."
if lsof -i :80 >/dev/null 2>&1; then
    echo -e "${RED}❌ Puerto 80 aún ocupado${NC}"
    lsof -i :80
    exit 1
fi

if lsof -i :443 >/dev/null 2>&1; then
    echo -e "${RED}❌ Puerto 443 aún ocupado${NC}"
    lsof -i :443
    exit 1
fi

echo -e "${GREEN}✅ Puertos 80 y 443 disponibles${NC}"
echo ""

# Detener contenedores existentes
echo "🛑 Deteniendo contenedores existentes..."
docker-compose -f docker-compose.vps.yml down 2>/dev/null || true
echo ""

# ==========================================
# MODIFICAR DOCKER-COMPOSE PARA PUERTO 80 DIRECTO
# ==========================================
echo "⚙️  Configurando Docker para usar puertos 80 y 443 directamente..."

# Backup del original
if [ ! -f "docker-compose.vps.original.yml" ]; then
    cp docker-compose.vps.yml docker-compose.vps.original.yml
fi

# Crear versión modificada con puerto 80 directo
cat > docker-compose.vps.direct.yml << 'EOF'
services:
  web:
    build:
      context: .
      dockerfile: Dockerfile.vps
    container_name: micaela_web
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./:/var/www/html
      - ./greenter:/var/www/html/greenter
      - ./Fotos:/var/www/html/Fotos
      - ./controller:/var/www/html/controller
    environment:
      - APACHE_DOCUMENT_ROOT=/var/www/html
    depends_on:
      - db
    networks:
      - micaela_network
    restart: unless-stopped

  db:
    image: mysql:8.0
    container_name: micaela_mysql
    environment:
      MYSQL_ROOT_PASSWORD: ${MYSQL_ROOT_PASSWORD}
      MYSQL_DATABASE: ${MYSQL_DATABASE}
      MYSQL_USER: ${MYSQL_USER}
      MYSQL_PASSWORD: ${MYSQL_PASSWORD}
    ports:
      - "3307:3306"
    volumes:
      - mysql_data:/var/lib/mysql
      - ./backup:/backup
    networks:
      - micaela_network
    restart: unless-stopped
    command: --default-authentication-plugin=mysql_native_password

  phpmyadmin:
    image: phpmyadmin/phpmyadmin
    container_name: micaela_phpmyadmin
    environment:
      PMA_HOST: db
      PMA_PORT: 3306
      PMA_USER: root
      PMA_PASSWORD: ${MYSQL_ROOT_PASSWORD}
      UPLOAD_LIMIT: 100M
    ports:
      - "8081:80"
    depends_on:
      - db
    networks:
      - micaela_network
    restart: unless-stopped

networks:
  micaela_network:
    driver: bridge

volumes:
  mysql_data:
EOF

echo -e "${GREEN}✅ Configuración Docker actualizada${NC}"
echo ""

# ==========================================
# LEVANTAR SERVICIOS
# ==========================================
echo "🏗️  Construyendo y levantando servicios..."
echo "Esto puede tomar varios minutos..."
docker-compose -f docker-compose.vps.direct.yml up -d --build

echo ""
echo "⏳ Esperando que los servicios estén listos..."
sleep 15

# Verificar contenedores
echo ""
echo "📊 Estado de los contenedores:"
docker-compose -f docker-compose.vps.direct.yml ps

echo ""
echo "🔍 Verificando que Apache esté escuchando..."
docker exec micaela_web apache2ctl -S 2>/dev/null || echo "Verificando configuración..."
sleep 3

# ==========================================
# CONFIGURAR FIREWALL
# ==========================================
echo ""
echo "🔥 Configurando firewall..."
if command -v ufw &> /dev/null; then
    ufw --force enable
    ufw allow 22/tcp    # SSH
    ufw allow 80/tcp    # HTTP
    ufw allow 443/tcp   # HTTPS
    ufw allow 8081/tcp  # phpMyAdmin
    ufw allow 3307/tcp  # MySQL externo
    ufw reload
    echo -e "${GREEN}✅ Firewall configurado${NC}"
else
    echo -e "${YELLOW}⚠️  UFW no instalado. Asegúrate de abrir puertos 22, 80, 443 en Hostinger${NC}"
fi

# ==========================================
# VERIFICAR DNS
# ==========================================
echo ""
echo "🔍 Verificando DNS de $DOMAIN..."
DNS_CHECK=$(dig +short $DOMAIN 2>/dev/null | grep -E '^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+$' | head -1 || echo "")

if [ -z "$DNS_CHECK" ]; then
    echo -e "${YELLOW}⚠️  No se pudo verificar DNS${NC}"
    echo "   Asegúrate de tener estos registros en Hostinger:"
    echo "   - Tipo A: @ → $SERVER_IP"
    echo "   - Tipo A: www → $SERVER_IP"
else
    if [ "$DNS_CHECK" == "$SERVER_IP" ]; then
        echo -e "${GREEN}✅ DNS configurado correctamente: $DOMAIN → $SERVER_IP${NC}"
    else
        echo -e "${YELLOW}⚠️  DNS apunta a $DNS_CHECK pero tu servidor es $SERVER_IP${NC}"
        echo "   Actualiza los registros DNS en Hostinger"
    fi
fi

# ==========================================
# CONFIGURAR SSL (OPCIONAL)
# ==========================================
echo ""
echo "🔒 ¿Deseas configurar SSL/HTTPS ahora? (s/N)"
read -r SETUP_SSL

if [[ $SETUP_SSL =~ ^[Ss]$ ]]; then
    echo "📦 Instalando Certbot..."
    apt-get update -qq
    apt-get install -y certbot python3-certbot-apache
    
    echo "🔒 Obteniendo certificado SSL..."
    echo "   Esto modificará la configuración de Apache dentro del contenedor"
    
    # Instalar certbot dentro del contenedor
    docker exec micaela_web bash -c "apt-get update && apt-get install -y certbot python3-certbot-apache"
    
    # Obtener certificado
    docker exec micaela_web certbot --apache -d $DOMAIN -d www.$DOMAIN --non-interactive --agree-tos --email $EMAIL --redirect
    
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✅ SSL configurado exitosamente${NC}"
        
        # Configurar renovación automática
        (crontab -l 2>/dev/null | grep -v certbot; echo "0 3 * * * docker exec micaela_web certbot renew --quiet") | crontab -
        echo -e "${GREEN}✅ Renovación automática configurada${NC}"
    else
        echo -e "${YELLOW}⚠️  Error al configurar SSL${NC}"
        echo "   Puedes intentarlo manualmente después"
    fi
fi

# ==========================================
# RESUMEN FINAL
# ==========================================
echo ""
echo -e "${GREEN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${GREEN}✅ ¡DESPLIEGUE COMPLETADO!${NC}"
echo -e "${GREEN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""
echo "📝 INFORMACIÓN DE ACCESO"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "🌐 Aplicación Web Principal:"
echo "   http://$DOMAIN"
echo "   http://www.$DOMAIN"
echo "   http://$SERVER_IP"
echo ""
echo "🗄️  phpMyAdmin:"
echo "   http://$DOMAIN:8081"
echo "   http://$SERVER_IP:8081"
echo "   Usuario: root"
echo "   Contraseña: (la de tu .env)"
echo ""
echo "🐬 MySQL (acceso externo):"
echo "   Host: $SERVER_IP"
echo "   Puerto: 3307"
echo "   Usuario: micaela_user"
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "📋 VERIFICACIONES:"
echo ""
echo "1. Prueba en tu navegador:"
echo "   http://$DOMAIN"
echo ""
echo "2. Si no funciona con el dominio, verifica:"
echo "   - DNS en Hostinger apunta a: $SERVER_IP"
echo "   - Firewall de Hostinger permite puertos 80, 443"
echo "   - Contenedor corriendo: docker ps"
echo ""
echo "3. Ver logs de la aplicación:"
echo "   docker-compose -f docker-compose.vps.direct.yml logs -f web"
echo ""
echo "4. Ver logs de Apache:"
echo "   docker exec micaela_web tail -f /var/log/apache2/error.log"
echo ""
echo "5. Comandos útiles:"
echo "   - Reiniciar: docker-compose -f docker-compose.vps.direct.yml restart"
echo "   - Detener: docker-compose -f docker-compose.vps.direct.yml down"
echo "   - Ver logs: docker-compose -f docker-compose.vps.direct.yml logs -f"
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "🔧 ARQUITECTURA SIMPLIFICADA:"
echo "   Internet (puerto 80/443)"
echo "        ↓"
echo "   Docker Apache (puerto 80 directo)"
echo "        ↓"
echo "   PHP + MySQL"
echo ""
echo -e "${GREEN}🎉 ¡Tu aplicación está lista!${NC}"
echo ""
echo -e "${BLUE}💡 IMPORTANTE:${NC}"
echo "   Si el dominio no funciona aún, verifica en Hostinger que:"
echo "   1. Los registros DNS A estén correctos"
echo "   2. El firewall/cortafuegos permita tráfico HTTP/HTTPS"
echo "   3. Espera 5-10 minutos para propagación DNS"
echo ""