#!/bin/bash

# ============================================
# Script para configurar dominio con SSL
# Dominio: micaela-tours.com
# ============================================

set -e

DOMAIN="micaela-tours.com"
WWW_DOMAIN="www.micaela-tours.com"
EMAIL="jersson1407miranda@gmail.com"  # Cambia esto por tu email

echo "🌐 Configurando dominio $DOMAIN con SSL..."
echo ""

# Colores
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

# Verificar que estamos en el directorio correcto
if [ ! -f "docker-compose.vps.yml" ]; then
    echo -e "${RED}❌ Error: No se encuentra docker-compose.vps.yml${NC}"
    exit 1
fi

# Verificar que Docker está corriendo
if ! docker ps &> /dev/null; then
    echo -e "${RED}❌ Docker no está corriendo${NC}"
    exit 1
fi

echo -e "${YELLOW}⚠️  IMPORTANTE: Antes de continuar, asegúrate de que:${NC}"
echo "1. Tu dominio $DOMAIN apunta a la IP de este servidor"
echo "2. El registro A de DNS está configurado correctamente"
echo "3. Has esperado al menos 10-15 minutos para la propagación DNS"
echo ""
read -p "¿Has configurado el DNS correctamente? (s/n): " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Ss]$ ]]; then
    echo "Configura el DNS primero y vuelve a ejecutar este script."
    exit 0
fi

# Instalar Certbot
echo ""
echo "📦 Instalando Certbot..."
apt-get update
apt-get install -y certbot python3-certbot-apache

# Detener contenedores temporalmente
echo ""
echo "🛑 Deteniendo contenedores temporalmente..."
docker-compose -f docker-compose.vps.yml down

# Obtener certificado SSL
echo ""
echo "🔒 Obteniendo certificado SSL de Let's Encrypt..."
echo "Esto puede tomar un momento..."

certbot certonly --standalone \
    --preferred-challenges http \
    --email $EMAIL \
    --agree-tos \
    --no-eff-email \
    -d $DOMAIN \
    -d $WWW_DOMAIN

if [ $? -ne 0 ]; then
    echo -e "${RED}❌ Error al obtener el certificado SSL${NC}"
    echo "Verifica que:"
    echo "1. El dominio apunta a este servidor"
    echo "2. Los puertos 80 y 443 están abiertos"
    echo "3. No hay otro servicio usando el puerto 80"
    exit 1
fi

echo -e "${GREEN}✅ Certificado SSL obtenido exitosamente${NC}"

# Crear directorio para certificados en el proyecto
echo ""
echo "📁 Configurando certificados..."
mkdir -p ./ssl

# Copiar certificados
cp /etc/letsencrypt/live/$DOMAIN/fullchain.pem ./ssl/
cp /etc/letsencrypt/live/$DOMAIN/privkey.pem ./ssl/
chmod 644 ./ssl/*.pem

# Crear configuración de Apache con SSL
echo ""
echo "⚙️  Creando configuración de Apache con SSL..."

cat > ./apache-ssl.conf << 'EOF'
<VirtualHost *:80>
    ServerName micaela-tours.com
    ServerAlias www.micaela-tours.com
    
    # Redirigir todo el tráfico HTTP a HTTPS
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}$1 [R=301,L]
</VirtualHost>

<VirtualHost *:443>
    ServerAdmin jersson1407miranda@gmail.com
    ServerName micaela-tours.com
    ServerAlias www.micaela-tours.com
    DocumentRoot /var/www/html

    # SSL Configuration
    SSLEngine on
    SSLCertificateFile /etc/ssl/certs/fullchain.pem
    SSLCertificateKeyFile /etc/ssl/private/privkey.pem
    
    # Seguridad SSL moderna
    SSLProtocol all -SSLv3 -TLSv1 -TLSv1.1
    SSLCipherSuite HIGH:!aNULL:!MD5
    SSLHonorCipherOrder on

    <Directory /var/www/html>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted

        # Seguridad adicional
        <FilesMatch "\.(env|sql|md|log)$">
            Require all denied
        </FilesMatch>
    </Directory>

    # Headers de seguridad
    Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains"
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-XSS-Protection "1; mode=block"

    # Logs
    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined

    # Compresión GZIP
    <IfModule mod_deflate.c>
        AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript application/json
    </IfModule>

    # Cache de archivos estáticos
    <IfModule mod_expires.c>
        ExpiresActive On
        ExpiresByType image/jpg "access plus 1 year"
        ExpiresByType image/jpeg "access plus 1 year"
        ExpiresByType image/gif "access plus 1 year"
        ExpiresByType image/png "access plus 1 year"
        ExpiresByType text/css "access plus 1 month"
        ExpiresByType application/javascript "access plus 1 month"
    </IfModule>
</VirtualHost>
EOF

echo -e "${GREEN}✅ Configuración de Apache creada${NC}"

# Actualizar Dockerfile para incluir SSL
echo ""
echo "🔧 Actualizando Dockerfile..."

cat > ./Dockerfile.ssl << 'EOF'
FROM php:8.2-apache

LABEL maintainer="jersson1407miranda@gmail.com"
LABEL description="Sistema Tours Micaela - Producción con SSL"

ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=America/Lima

# Instalar dependencias
RUN apt-get update && apt-get install -y --no-install-recommends \
    git curl libpng-dev libonig-dev libxml2-dev libzip-dev \
    zip unzip libfreetype6-dev libjpeg62-turbo-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar extensiones PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo pdo_mysql mysqli mbstring exif pcntl bcmath gd zip soap opcache

# Configurar OPcache
RUN { \
    echo 'opcache.enable=1'; \
    echo 'opcache.memory_consumption=256'; \
    echo 'opcache.interned_strings_buffer=16'; \
    echo 'opcache.max_accelerated_files=10000'; \
    echo 'opcache.revalidate_freq=60'; \
    } > /usr/local/etc/php/conf.d/opcache.ini

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Habilitar módulos de Apache necesarios
RUN a2enmod rewrite headers expires deflate ssl

WORKDIR /var/www/html

# Copiar proyecto
COPY --chown=www-data:www-data . /var/www/html/

# Instalar dependencias
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist \
    && composer clear-cache

# Crear directorios
RUN mkdir -p greenter/xml greenter/cdr greenter/pdf greenter/certificados \
    Fotos controller/usuario/fotos controller/choferes/fotos controller/empresa/FOTOS \
    view/MPDF backup \
    && chmod -R 755 greenter Fotos controller view/MPDF backup \
    && chown -R www-data:www-data /var/www/html

# Configurar PHP
RUN { \
    echo 'upload_max_filesize = 20M'; \
    echo 'post_max_size = 20M'; \
    echo 'memory_limit = 256M'; \
    echo 'max_execution_time = 300'; \
    echo 'date.timezone = America/Lima'; \
    echo 'display_errors = Off'; \
    echo 'log_errors = On'; \
    echo 'session.cookie_httponly = 1'; \
    echo 'session.cookie_secure = 1'; \
    echo 'session.use_strict_mode = 1'; \
    } > /usr/local/etc/php/conf.d/production.ini

# Copiar configuración de Apache con SSL
COPY apache-ssl.conf /etc/apache2/sites-available/000-default.conf

# Crear directorio para sesiones
RUN mkdir -p /tmp/sessions && chmod 1777 /tmp/sessions

EXPOSE 80 443

HEALTHCHECK --interval=30s --timeout=3s --start-period=40s --retries=3 \
    CMD curl -f http://localhost/ || exit 1

USER www-data

CMD ["apache2-foreground"]
EOF

echo -e "${GREEN}✅ Dockerfile actualizado${NC}"

# Actualizar docker-compose para incluir SSL
echo ""
echo "🔧 Actualizando docker-compose..."

cat > ./docker-compose.ssl.yml << 'EOF'
version: '3.8'

services:
  db:
    image: mysql:8.0
    container_name: tours_micaela_db_vps
    restart: always
    ports:
      - "3307:3306"
    environment:
      MYSQL_ROOT_PASSWORD: ${MYSQL_ROOT_PASSWORD:-root_password_2024_VPS}
      MYSQL_DATABASE: ${MYSQL_DATABASE:-micaela}
      MYSQL_USER: ${MYSQL_USER:-micaela_user}
      MYSQL_PASSWORD: ${MYSQL_PASSWORD:-micaela_pass_2024_VPS}
      TZ: America/Lima
    volumes:
      - mysql_data_vps:/var/lib/mysql
      - ./backup:/backup
    networks:
      - tours_network_vps
    command: >
      --default-authentication-plugin=mysql_native_password
      --character-set-server=utf8mb4
      --collation-server=utf8mb4_unicode_ci
      --init-connect='SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci'
      --max_connections=200
      --innodb_buffer_pool_size=512M
    healthcheck:
      test: ["CMD", "mysqladmin", "ping", "-h", "localhost", "-u", "root", "-p${MYSQL_ROOT_PASSWORD:-root_password_2024_VPS}"]
      interval: 10s
      timeout: 5s
      retries: 5
      start_period: 30s

  phpmyadmin:
    image: phpmyadmin/phpmyadmin:latest
    container_name: tours_micaela_phpmyadmin_vps
    restart: always
    depends_on:
      db:
        condition: service_healthy
    ports:
      - "8081:80"
    environment:
      PMA_HOST: db
      PMA_PORT: 3306
      PMA_USER: root
      PMA_PASSWORD: ${MYSQL_ROOT_PASSWORD:-root_password_2024_VPS}
      UPLOAD_LIMIT: 100M
      MEMORY_LIMIT: 512M
      MAX_EXECUTION_TIME: 600
      PMA_ARBITRARY: 1
    networks:
      - tours_network_vps

  app:
    build:
      context: .
      dockerfile: Dockerfile.ssl
    image: tours-micaela:ssl
    container_name: tours_micaela_app_vps
    restart: always
    depends_on:
      db:
        condition: service_healthy
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./greenter/xml:/var/www/html/greenter/xml
      - ./greenter/cdr:/var/www/html/greenter/cdr
      - ./greenter/pdf:/var/www/html/greenter/pdf
      - ./greenter/certificados:/var/www/html/greenter/certificados
      - ./Fotos:/var/www/html/Fotos
      - ./controller/usuario/fotos:/var/www/html/controller/usuario/fotos
      - ./controller/choferes/fotos:/var/www/html/controller/choferes/fotos
      - ./controller/empresa/FOTOS:/var/www/html/controller/empresa/FOTOS
      - ./backup:/var/www/html/backup
      - sessions_data_vps:/tmp/sessions
      - ./model/model_conexion.php:/var/www/html/model/model_conexion.php
      # Certificados SSL
      - ./ssl/fullchain.pem:/etc/ssl/certs/fullchain.pem:ro
      - ./ssl/privkey.pem:/etc/ssl/private/privkey.pem:ro
    environment:
      - TZ=America/Lima
      - APACHE_RUN_USER=www-data
      - APACHE_RUN_GROUP=www-data
      - DB_HOST=db
      - DB_PORT=3306
      - DB_NAME=${MYSQL_DATABASE:-micaela}
      - DB_USER=${MYSQL_USER:-micaela_user}
      - DB_PASSWORD=${MYSQL_PASSWORD:-micaela_pass_2024_VPS}
    networks:
      - tours_network_vps
    deploy:
      resources:
        limits:
          cpus: '2'
          memory: 2G
        reservations:
          cpus: '0.5'
          memory: 512M

networks:
  tours_network_vps:
    driver: bridge

volumes:
  mysql_data_vps:
    driver: local
  sessions_data_vps:
    driver: local
EOF

echo -e "${GREEN}✅ docker-compose actualizado${NC}"

# Levantar servicios con SSL
echo ""
echo "🚀 Levantando servicios con SSL..."
docker-compose -f docker-compose.ssl.yml up -d --build

echo ""
echo "⏳ Esperando que los servicios estén listos..."
sleep 15

# Verificar estado
echo ""
echo "📊 Estado de los contenedores:"
docker ps

# Configurar renovación automática del certificado
echo ""
echo "🔄 Configurando renovación automática de certificado..."

# Crear script de renovación
cat > /root/renovar_ssl.sh << 'RENEWAL_SCRIPT'
#!/bin/bash
cd /root/tours-micaela
docker-compose -f docker-compose.ssl.yml down
certbot renew --quiet
cp /etc/letsencrypt/live/micaela-tours.com/fullchain.pem ./ssl/
cp /etc/letsencrypt/live/micaela-tours.com/privkey.pem ./ssl/
chmod 644 ./ssl/*.pem
docker-compose -f docker-compose.ssl.yml up -d
RENEWAL_SCRIPT

chmod +x /root/renovar_ssl.sh

# Agregar a crontab para renovación automática cada 2 meses
(crontab -l 2>/dev/null; echo "0 3 1 */2 * /root/renovar_ssl.sh >> /var/log/ssl-renewal.log 2>&1") | crontab -

echo -e "${GREEN}✅ Renovación automática configurada${NC}"

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo -e "${GREEN}✅ ¡CONFIGURACIÓN COMPLETADA!${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "🌐 Tu sitio ahora está disponible en:"
echo "   https://micaela-tours.com"
echo "   https://www.micaela-tours.com"
echo ""
echo "🔒 Certificado SSL: ✅ Activo"
echo "🔄 Renovación automática: ✅ Configurada"
echo ""
echo "📊 phpMyAdmin (sin SSL):"
echo "   http://$(hostname -I | awk '{print $1}'):8081"
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "📝 Notas importantes:"
echo "- El certificado se renovará automáticamente cada 2 meses"
echo "- Todo el tráfico HTTP se redirige automáticamente a HTTPS"
echo "- El certificado es válido por 90 días"
echo ""
