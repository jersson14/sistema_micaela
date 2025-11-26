#!/bin/bash

# ============================================
# SSL Seguro Plug-and-Play para Tours Micaela
# Dominio: micaela-tours.com
# ============================================

set -e

# ---------------- Configuración ----------------
DOMAIN="micaela-tours.com"
WWW_DOMAIN="www.micaela-tours.com"
EMAIL="jersson1407miranda@gmail.com"  # Cambia por tu correo real
APP_CONTAINER="tours_micaela_app_vps"
PROXY_CONTAINER="micaela_nginx_ssl"
NETWORK="tours_network_vps"
SSL_DIR="$HOME/micaela_ssl"
# -----------------------------------------------

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${GREEN}🌐 Configurando SSL para $DOMAIN${NC}"

# 1️⃣ Instalar Certbot y Docker Nginx si no existen
if ! command -v certbot &>/dev/null; then
    echo -e "${YELLOW}⚠️ Instalando Certbot...${NC}"
    apt update && apt install -y certbot
fi

# 2️⃣ Crear carpeta de certificados
mkdir -p "$SSL_DIR"

# 3️⃣ Obtener certificados SSL (solo si no existen)
if [ ! -f "$SSL_DIR/fullchain.pem" ] || [ ! -f "$SSL_DIR/privkey.pem" ]; then
    echo -e "${GREEN}🔒 Obteniendo certificado SSL con Certbot...${NC}"
    certbot certonly --standalone \
        --preferred-challenges http \
        --email "$EMAIL" \
        --agree-tos \
        --no-eff-email \
        -d "$DOMAIN" -d "$WWW_DOMAIN"

    cp "/etc/letsencrypt/live/$DOMAIN/fullchain.pem" "$SSL_DIR/"
    cp "/etc/letsencrypt/live/$DOMAIN/privkey.pem" "$SSL_DIR/"
    chmod 644 "$SSL_DIR"/*.pem
else
    echo -e "${GREEN}✅ Certificados ya existen${NC}"
fi

# 4️⃣ Crear archivo de configuración Nginx para proxy inverso
cat > "$SSL_DIR/nginx.conf" <<EOF
server {
    listen 80;
    server_name $DOMAIN $WWW_DOMAIN;
    location / {
        return 301 https://\$host\$request_uri;
    }
}

server {
    listen 443 ssl;
    server_name $DOMAIN $WWW_DOMAIN;

    ssl_certificate /etc/ssl/fullchain.pem;
    ssl_certificate_key /etc/ssl/privkey.pem;

    location / {
        proxy_pass http://$APP_CONTAINER:80;
        proxy_set_header Host \$host;
        proxy_set_header X-Real-IP \$remote_addr;
        proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto \$scheme;
    }
}
EOF

# 5️⃣ Levantar contenedor Nginx proxy
docker rm -f "$PROXY_CONTAINER" 2>/dev/null || true
docker run -d --name "$PROXY_CONTAINER" \
    -p 80:80 -p 443:443 \
    -v "$SSL_DIR/fullchain.pem:/etc/ssl/fullchain.pem:ro" \
    -v "$SSL_DIR/privkey.pem:/etc/ssl/privkey.pem:ro" \
    -v "$SSL_DIR/nginx.conf:/etc/nginx/conf.d/default.conf:ro" \
    --network "$NETWORK" \
    nginx:stable

echo -e "${GREEN}✅ Proxy Nginx con SSL levantado correctamente${NC}"
echo -e "${GREEN}🌐 Ahora tu dominio https://$DOMAIN está activo${NC}"

# 6️⃣ Configurar renovación automática del certificado
(crontab -l 2>/dev/null; echo "0 3 1 */2 * certbot renew --quiet && cp /etc/letsencrypt/live/$DOMAIN/fullchain.pem $SSL_DIR/ && cp /etc/letsencrypt/live/$DOMAIN/privkey.pem $SSL_DIR/ && docker restart $PROXY_CONTAINER") | crontab -

echo -e "${GREEN}🔄 Renovación automática configurada cada 2 meses${NC}"
echo -e "${GREEN}✅ Todo listo!${NC}"
