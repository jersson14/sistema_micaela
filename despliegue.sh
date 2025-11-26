#!/bin/bash

# ============================================
# Script de Despliegue Automático para VPS
# Tours Micaela - Docker Compose + Dominio
# ============================================

set -e  # Detener en caso de error

echo "🚀 Iniciando despliegue en VPS..."
echo ""

# Colores para output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuración del dominio
DOMAIN="micaela-tours.com"
EMAIL="jersson1407miranda@gmail.com"  # Cambia esto por tu email
SERVER_IP=$(hostname -I | awk '{print $1}')

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
mkdir -p nginx/conf.d
mkdir -p certbot/conf certbot/www
chmod -R 755 greenter Fotos controller backup nginx certbot
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
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo -e "${BLUE}🌐 CONFIGURANDO DOMINIO Y SSL${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

# Verificar si Nginx está instalado en el host
if ! command -v nginx &> /dev/null; then
    echo "📦 Instalando Nginx..."
    apt-get update -qq
    apt-get install -y nginx certbot python3-certbot-nginx
    echo -e "${GREEN}✅ Nginx instalado${NC}"
else
    echo -e "${GREEN}✅ Nginx ya está instalado${NC}"
fi

# Crear configuración de Nginx para el dominio (HTTP primero)
echo "⚙️  Configurando Nginx para $DOMAIN..."
cat > /etc/nginx/sites-available/$DOMAIN << 'NGINX_EOF'
server {
    listen 80;
    listen [::]:80;
    server_name micaela-tours.com www.micaela-tours.com;

    # Configuración de logs
    access_log /var/log/nginx/micaela-tours.access.log;
    error_log /var/log/nginx/micaela-tours.error.log;

    # Let's Encrypt challenge
    location /.well-known/acme-challenge/ {
        root /var/www/certbot;
    }

    # Proxy a la aplicación Docker
    location / {
        proxy_pass http://localhost:80;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        
        # Timeouts
        proxy_connect_timeout 60s;
        proxy_send_timeout 60s;
        proxy_read_timeout 60s;
        
        # Buffer settings
        proxy_buffering on;
        proxy_buffer_size 4k;
        proxy_buffers 8 4k;
        proxy_busy_buffers_size 8k;
    }

    # phpMyAdmin en subdominio o ruta
    location /phpmyadmin {
        proxy_pass http://localhost:8081;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }

    # Límites de tamaño para uploads
    client_max_body_size 100M;
}
NGINX_EOF

# Habilitar el sitio
ln -sf /etc/nginx/sites-available/$DOMAIN /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default

# Crear directorio para certbot
mkdir -p /var/www/certbot

# Verificar configuración de Nginx
echo "🔍 Verificando configuración de Nginx..."
if nginx -t; then
    echo -e "${GREEN}✅ Configuración de Nginx válida${NC}"
else
    echo -e "${RED}❌ Error en la configuración de Nginx${NC}"
    exit 1
fi

# Reiniciar Nginx
echo "🔄 Reiniciando Nginx..."
systemctl restart nginx
systemctl enable nginx
echo -e "${GREEN}✅ Nginx reiniciado${NC}"
echo ""

# Verificar DNS antes de configurar SSL
echo "🔍 Verificando configuración DNS de $DOMAIN..."
DNS_CHECK=$(dig +short $DOMAIN | grep -E '^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+$' || echo "")

if [ -z "$DNS_CHECK" ]; then
    echo -e "${YELLOW}⚠️  ADVERTENCIA: No se pudo verificar el DNS de $DOMAIN${NC}"
    echo "   Asegúrate de que el dominio apunte a esta IP: $SERVER_IP"
    echo ""
    echo "   Configura los siguientes registros DNS:"
    echo "   - Registro A: micaela-tours.com → $SERVER_IP"
    echo "   - Registro A: www.micaela-tours.com → $SERVER_IP"
    echo ""
    echo -e "${YELLOW}¿Deseas continuar con la configuración SSL de todos modos? (s/N)${NC}"
    read -r CONTINUE_SSL
    if [[ ! $CONTINUE_SSL =~ ^[Ss]$ ]]; then
        echo "Saltando configuración SSL. Puedes ejecutarla más tarde con:"
        echo "sudo certbot --nginx -d $DOMAIN -d www.$DOMAIN"
        SSL_CONFIGURED=false
    else
        SSL_CONFIGURED=true
    fi
else
    if [ "$DNS_CHECK" != "$SERVER_IP" ]; then
        echo -e "${YELLOW}⚠️  ADVERTENCIA: El DNS apunta a $DNS_CHECK pero tu servidor es $SERVER_IP${NC}"
        echo "   Esto puede causar problemas. ¿Continuar? (s/N)"
        read -r CONTINUE_SSL
        if [[ ! $CONTINUE_SSL =~ ^[Ss]$ ]]; then
            SSL_CONFIGURED=false
        else
            SSL_CONFIGURED=true
        fi
    else
        echo -e "${GREEN}✅ DNS configurado correctamente: $DOMAIN → $SERVER_IP${NC}"
        SSL_CONFIGURED=true
    fi
fi

# Configurar SSL con Let's Encrypt
if [ "$SSL_CONFIGURED" = true ]; then
    echo ""
    echo "🔒 Configurando certificado SSL con Let's Encrypt..."
    echo "   Dominio: $DOMAIN"
    echo "   Email: $EMAIL"
    echo ""
    
    # Obtener certificado SSL
    if certbot --nginx -d $DOMAIN -d www.$DOMAIN --non-interactive --agree-tos --email $EMAIL --redirect; then
        echo -e "${GREEN}✅ ¡Certificado SSL configurado exitosamente!${NC}"
        echo -e "${GREEN}✅ HTTPS habilitado con redirección automática${NC}"
        
        # Configurar renovación automática
        echo "⚙️  Configurando renovación automática de certificados..."
        (crontab -l 2>/dev/null || echo ""; echo "0 3 * * * certbot renew --quiet --post-hook 'systemctl reload nginx'") | crontab -
        echo -e "${GREEN}✅ Renovación automática configurada${NC}"
    else
        echo -e "${YELLOW}⚠️  No se pudo configurar el certificado SSL automáticamente${NC}"
        echo "   Puedes intentarlo manualmente más tarde con:"
        echo "   sudo certbot --nginx -d $DOMAIN -d www.$DOMAIN"
    fi
fi

# Configurar firewall
echo ""
echo "🔥 Configurando firewall..."
if command -v ufw &> /dev/null; then
    ufw --force enable
    ufw allow 22/tcp    # SSH
    ufw allow 80/tcp    # HTTP
    ufw allow 443/tcp   # HTTPS
    ufw allow 8081/tcp  # phpMyAdmin (considera cerrar esto en producción)
    ufw allow 3307/tcp  # MySQL externo (considera cerrar esto en producción)
    ufw reload
    echo -e "${GREEN}✅ Firewall configurado${NC}"
else
    echo -e "${YELLOW}⚠️  UFW no está instalado. Configura el firewall manualmente:${NC}"
    echo "   - Puerto 22 (SSH)"
    echo "   - Puerto 80 (HTTP)"
    echo "   - Puerto 443 (HTTPS)"
    echo "   - Puerto 8081 (phpMyAdmin - opcional)"
    echo "   - Puerto 3307 (MySQL - opcional)"
fi

echo ""
echo -e "${GREEN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${GREEN}✅ ¡DESPLIEGUE COMPLETADO EXITOSAMENTE!${NC}"
echo -e "${GREEN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""
echo "📝 INFORMACIÓN DE ACCESO"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "🌐 Aplicación Web Principal:"
if [ "$SSL_CONFIGURED" = true ]; then
    echo "   https://$DOMAIN"
    echo "   https://www.$DOMAIN"
    echo "   (Con SSL/HTTPS configurado ✅)"
else
    echo "   http://$DOMAIN"
    echo "   http://www.$DOMAIN"
fi
echo ""
echo "🔗 Acceso directo por IP:"
echo "   http://$SERVER_IP"
echo ""
echo "🗄️  phpMyAdmin:"
echo "   https://$DOMAIN/phpmyadmin"
echo "   http://$SERVER_IP:8081"
echo "   Usuario: root"
echo "   Contraseña: (la que configuraste en .env)"
echo ""
echo "🐬 MySQL (acceso externo):"
echo "   Host: $SERVER_IP"
echo "   Puerto: 3307"
echo "   Usuario: micaela_user"
echo "   Contraseña: (la que configuraste en .env)"
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "📋 PRÓXIMOS PASOS:"
echo ""
echo "1. Verifica que el dominio funcione:"
if [ "$SSL_CONFIGURED" = true ]; then
    echo "   https://$DOMAIN"
else
    echo "   http://$DOMAIN"
fi
echo ""
echo "2. Importa tu base de datos usando phpMyAdmin"
if [ "$SSL_CONFIGURED" = true ]; then
    echo "   https://$DOMAIN/phpmyadmin"
else
    echo "   http://$DOMAIN/phpmyadmin"
fi
echo ""
echo "3. Verifica los logs:"
echo "   docker-compose -f docker-compose.vps.yml logs -f"
echo "   tail -f /var/log/nginx/micaela-tours.access.log"
echo ""
echo "4. Comandos útiles:"
echo "   - Detener app: docker-compose -f docker-compose.vps.yml down"
echo "   - Reiniciar app: docker-compose -f docker-compose.vps.yml restart"
echo "   - Reiniciar Nginx: sudo systemctl restart nginx"
echo "   - Ver logs Nginx: tail -f /var/log/nginx/micaela-tours.error.log"
echo "   - Renovar SSL: sudo certbot renew"
echo ""
if [ "$SSL_CONFIGURED" != true ]; then
    echo -e "${YELLOW}⚠️  IMPORTANTE: Configurar SSL más tarde${NC}"
    echo "   sudo certbot --nginx -d $DOMAIN -d www.$DOMAIN"
    echo ""
fi
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo -e "${GREEN}🎉 ¡Tu aplicación está lista!${NC}"
echo ""