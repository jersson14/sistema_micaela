#!/bin/bash

# ============================================
# Script de Despliegue Directo para VPS
# Tours Micaela - Docker sin Nginx
# ============================================

set -e

echo "🚀 Iniciando despliegue en VPS..."
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

echo "📍 IP del servidor: $SERVER_IP"
echo ""

# Verificar archivos necesarios
if [ ! -f "docker-compose.vps.yml" ]; then
    echo -e "${RED}❌ Error: No se encuentra docker-compose.vps.yml${NC}"
    exit 1
fi

# Verificar Docker
if ! command -v docker &> /dev/null; then
    echo -e "${RED}❌ Docker no está instalado${NC}"
    echo "Instalando Docker..."
    curl -fsSL https://get.docker.com | sh
    systemctl start docker
    systemctl enable docker
fi

if ! command -v docker-compose &> /dev/null; then
    echo -e "${RED}❌ Docker Compose no está instalado${NC}"
    echo "Instalando Docker Compose..."
    curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
    chmod +x /usr/local/bin/docker-compose
fi

echo -e "${GREEN}✅ Docker y Docker Compose listos${NC}"
echo ""

# Verificar .env
if [ ! -f ".env" ]; then
    echo -e "${YELLOW}⚠️  Creando archivo .env...${NC}"
    if [ -f ".env.vps" ]; then
        cp .env.vps .env
    else
        cat > .env << 'ENVEOF'
MYSQL_ROOT_PASSWORD=root_password_123
MYSQL_DATABASE=bd_micaela
MYSQL_USER=micaela_user
MYSQL_PASSWORD=micaela_pass_123
ENVEOF
    fi
    echo -e "${YELLOW}⚠️  Archivo .env creado. Edítalo si necesitas cambiar contraseñas${NC}"
fi

# Crear directorios
echo "📁 Creando directorios necesarios..."
mkdir -p greenter/{xml,cdr,pdf,certificados}
mkdir -p Fotos 
mkdir -p controller/usuario/fotos 
mkdir -p controller/choferes/fotos 
mkdir -p controller/empresa/fotos 
mkdir -p controller/empresa/FOTOS
mkdir -p backup
chmod -R 755 greenter Fotos controller backup
echo -e "${GREEN}✅ Directorios creados${NC}"
echo ""

# Backup y actualizar conexión
if [ -f "model/model_conexion.php" ] && [ ! -f "model/model_conexion_local.php.bak" ]; then
    echo "💾 Backup de model_conexion.php..."
    cp model/model_conexion.php model/model_conexion_local.php.bak
fi

if [ -f "model/model_conexion_vps.php" ]; then
    echo "🔄 Configurando archivo de conexión..."
    cp model/model_conexion_vps.php model/model_conexion.php
    echo -e "${GREEN}✅ Conexión actualizada${NC}"
fi
echo ""

# Detener servicios que usen puertos 80/443
echo "🛑 Liberando puertos 80 y 443..."

if systemctl is-active --quiet apache2 2>/dev/null; then
    echo "   Deteniendo Apache2..."
    systemctl stop apache2
    systemctl disable apache2
    echo -e "${GREEN}   ✅ Apache2 detenido${NC}"
fi

if systemctl is-active --quiet nginx 2>/dev/null; then
    echo "   Deteniendo Nginx..."
    systemctl stop nginx
    systemctl disable nginx
    echo -e "${GREEN}   ✅ Nginx detenido${NC}"
fi

# Verificar puertos (solo LISTEN, no conexiones salientes)
echo "🔍 Verificando que puertos estén libres..."
PORT_80=$(netstat -tlnp 2>/dev/null | grep -c ":80 " || echo "0")
PORT_443=$(netstat -tlnp 2>/dev/null | grep -c ":443 " || echo "0")

if [ "$PORT_80" -gt 0 ]; then
    echo -e "${RED}❌ Puerto 80 ocupado:${NC}"
    netstat -tlnp | grep ":80 "
    exit 1
fi

if [ "$PORT_443" -gt 0 ]; then
    echo -e "${RED}❌ Puerto 443 ocupado:${NC}"
    netstat -tlnp | grep ":443 "
    exit 1
fi

echo -e "${GREEN}✅ Puertos 80 y 443 disponibles${NC}"
echo ""

# Detener contenedores existentes
echo "🛑 Deteniendo contenedores Docker existentes..."
docker-compose -f docker-compose.vps.yml down 2>/dev/null || true
docker-compose -f docker-compose.vps.direct.yml down 2>/dev/null || true
echo ""

# Crear docker-compose.vps.direct.yml
echo "⚙️  Creando configuración Docker optimizada..."
cat > docker-compose.vps.direct.yml << 'COMPOSE_EOF'
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
COMPOSE_EOF

echo -e "${GREEN}✅ Configuración Docker creada${NC}"
echo ""

# Construir y levantar servicios
echo "🏗️  Construyendo y levantando servicios..."
echo "Esto puede tomar varios minutos la primera vez..."
echo ""

docker-compose -f docker-compose.vps.direct.yml up -d --build

echo ""
echo "⏳ Esperando que los servicios estén listos..."
sleep 15

# Verificar contenedores
echo ""
echo "📊 Estado de los contenedores:"
docker-compose -f docker-compose.vps.direct.yml ps
echo ""

# Verificar logs iniciales
echo "📝 Últimas líneas de logs del contenedor web:"
docker logs micaela_web --tail 20 2>&1 || echo "Contenedor aún iniciando..."
echo ""

# Configurar firewall
echo "🔥 Configurando firewall..."
if command -v ufw &> /dev/null; then
    ufw --force enable
    ufw allow 22/tcp    # SSH
    ufw allow 80/tcp    # HTTP
    ufw allow 443/tcp   # HTTPS
    ufw allow 8081/tcp  # phpMyAdmin
    ufw allow 3307/tcp  # MySQL
    echo "y" | ufw reload
    echo -e "${GREEN}✅ Firewall configurado${NC}"
else
    echo -e "${YELLOW}⚠️  UFW no instalado${NC}"
    echo "   Asegúrate de abrir los puertos en el panel de Hostinger"
fi
echo ""

# Verificar DNS
echo "🔍 Verificando DNS de $DOMAIN..."
DNS_CHECK=$(dig +short $DOMAIN 2>/dev/null | grep -E '^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+$' | head -1 || echo "")

if [ -z "$DNS_CHECK" ]; then
    echo -e "${YELLOW}⚠️  No se detectó configuración DNS${NC}"
    DNS_OK=false
elif [ "$DNS_CHECK" == "$SERVER_IP" ]; then
    echo -e "${GREEN}✅ DNS correcto: $DOMAIN → $SERVER_IP${NC}"
    DNS_OK=true
else
    echo -e "${YELLOW}⚠️  DNS apunta a $DNS_CHECK pero tu servidor es $SERVER_IP${NC}"
    DNS_OK=false
fi
echo ""

# Test de conectividad
echo "🧪 Probando conectividad..."
if curl -s -o /dev/null -w "%{http_code}" http://localhost | grep -q "200\|301\|302"; then
    echo -e "${GREEN}✅ Servidor web respondiendo correctamente${NC}"
else
    echo -e "${YELLOW}⚠️  Servidor web aún iniciando o con problemas${NC}"
    echo "   Revisa los logs: docker logs micaela_web"
fi
echo ""

# Resumen final
echo -e "${GREEN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${GREEN}✅ ¡DESPLIEGUE COMPLETADO!${NC}"
echo -e "${GREEN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""
echo "📝 INFORMACIÓN DE ACCESO"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "🌐 Aplicación Web:"
echo "   Por IP:      http://$SERVER_IP"
if [ "$DNS_OK" = true ]; then
    echo "   Por Dominio: http://$DOMAIN"
    echo "                http://www.$DOMAIN"
else
    echo -e "   Por Dominio: ${YELLOW}Pendiente configuración DNS${NC}"
fi
echo ""
echo "🗄️  phpMyAdmin:"
echo "   http://$SERVER_IP:8081"
if [ "$DNS_OK" = true ]; then
    echo "   http://$DOMAIN:8081"
fi
echo "   Usuario: root"
echo "   Contraseña: (ver archivo .env)"
echo ""
echo "🐬 MySQL (acceso externo):"
echo "   Host: $SERVER_IP"
echo "   Puerto: 3307"
echo "   Usuario: micaela_user"
echo "   Contraseña: (ver archivo .env)"
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
if [ "$DNS_OK" != true ]; then
    echo -e "${BLUE}📌 CONFIGURACIÓN DNS PENDIENTE${NC}"
    echo ""
    echo "En tu panel de Hostinger, configura:"
    echo ""
    echo "Tipo: A"
    echo "Nombre: @"
    echo "Apunta a: $SERVER_IP"
    echo "TTL: 14400"
    echo ""
    echo "Tipo: A"
    echo "Nombre: www"
    echo "Apunta a: $SERVER_IP"
    echo "TTL: 14400"
    echo ""
    echo "Luego espera 5-30 minutos para propagación DNS"
    echo ""
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo ""
fi
echo "📋 COMANDOS ÚTILES:"
echo ""
echo "Ver logs en tiempo real:"
echo "  docker-compose -f docker-compose.vps.direct.yml logs -f"
echo ""
echo "Ver logs solo del web:"
echo "  docker logs -f micaela_web"
echo ""
echo "Reiniciar servicios:"
echo "  docker-compose -f docker-compose.vps.direct.yml restart"
echo ""
echo "Detener servicios:"
echo "  docker-compose -f docker-compose.vps.direct.yml down"
echo ""
echo "Ver estado de contenedores:"
echo "  docker ps"
echo ""
echo "Acceder al contenedor web:"
echo "  docker exec -it micaela_web bash"
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo -e "${GREEN}🎉 ¡Sistema desplegado!${NC}"
echo ""
echo "Prueba primero: http://$SERVER_IP"
echo ""