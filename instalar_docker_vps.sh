#!/bin/bash

# ============================================
# Script de Instalación de Docker en VPS
# Ubuntu 20.04+ / Debian
# ============================================

set -e

echo "🐳 Instalando Docker y Docker Compose en VPS..."
echo ""

# Colores
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

# Verificar si es root o tiene sudo
if [ "$EUID" -ne 0 ] && ! command -v sudo &> /dev/null; then
    echo -e "${RED}❌ Este script necesita permisos de root o sudo${NC}"
    exit 1
fi

SUDO=""
if [ "$EUID" -ne 0 ]; then
    SUDO="sudo"
fi

# Actualizar sistema
echo "📦 Actualizando sistema..."
$SUDO apt-get update
$SUDO apt-get upgrade -y
echo -e "${GREEN}✅ Sistema actualizado${NC}"
echo ""

# Instalar dependencias
echo "📦 Instalando dependencias..."
$SUDO apt-get install -y \
    ca-certificates \
    curl \
    gnupg \
    lsb-release \
    git \
    ufw
echo -e "${GREEN}✅ Dependencias instaladas${NC}"
echo ""

# Instalar Docker
if command -v docker &> /dev/null; then
    echo -e "${YELLOW}⚠️  Docker ya está instalado${NC}"
    docker --version
else
    echo "🐳 Instalando Docker..."
    curl -fsSL https://get.docker.com -o get-docker.sh
    $SUDO sh get-docker.sh
    rm get-docker.sh
    echo -e "${GREEN}✅ Docker instalado${NC}"
fi
echo ""

# Agregar usuario al grupo docker
if [ "$EUID" -ne 0 ]; then
    echo "👤 Agregando usuario al grupo docker..."
    $SUDO usermod -aG docker $USER
    echo -e "${GREEN}✅ Usuario agregado al grupo docker${NC}"
    echo -e "${YELLOW}⚠️  Necesitas cerrar sesión y volver a entrar para que los cambios surtan efecto${NC}"
fi
echo ""

# Instalar Docker Compose
if command -v docker-compose &> /dev/null; then
    echo -e "${YELLOW}⚠️  Docker Compose ya está instalado${NC}"
    docker-compose --version
else
    echo "🔧 Instalando Docker Compose..."
    COMPOSE_VERSION=$(curl -s https://api.github.com/repos/docker/compose/releases/latest | grep 'tag_name' | cut -d\" -f4)
    $SUDO curl -L "https://github.com/docker/compose/releases/download/${COMPOSE_VERSION}/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
    $SUDO chmod +x /usr/local/bin/docker-compose
    echo -e "${GREEN}✅ Docker Compose instalado${NC}"
fi
echo ""

# Configurar firewall
echo "🔥 Configurando firewall..."
$SUDO ufw --force enable
$SUDO ufw allow 22/tcp comment 'SSH'
$SUDO ufw allow 80/tcp comment 'HTTP'
$SUDO ufw allow 443/tcp comment 'HTTPS'
$SUDO ufw allow 8081/tcp comment 'phpMyAdmin'
echo -e "${GREEN}✅ Firewall configurado${NC}"
echo ""

# Iniciar Docker
echo "🚀 Iniciando Docker..."
$SUDO systemctl enable docker
$SUDO systemctl start docker
echo -e "${GREEN}✅ Docker iniciado${NC}"
echo ""

# Verificar instalación
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "✅ INSTALACIÓN COMPLETADA"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "📊 Versiones instaladas:"
docker --version
docker-compose --version
echo ""
echo "🔥 Estado del firewall:"
$SUDO ufw status
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo -e "${YELLOW}⚠️  IMPORTANTE:${NC}"
echo "Si no eres root, cierra sesión y vuelve a entrar para que"
echo "los cambios del grupo docker surtan efecto:"
echo ""
echo "  exit"
echo "  ssh usuario@TU_IP_VPS"
echo ""
echo "Luego verifica con:"
echo "  docker ps"
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "🎉 ¡Listo! Ahora puedes desplegar tu aplicación"
echo ""
