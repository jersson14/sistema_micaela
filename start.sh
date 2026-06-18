#!/bin/bash

# Script de inicio rápido para Tours Micaela
# Uso: ./start.sh

echo "🚀 =========================================="
echo "   Sistema Tours Micaela - Inicio Rápido"
echo "=========================================="
echo ""

# Verificar si Docker está instalado
if ! command -v docker &> /dev/null; then
    echo "❌ Docker no está instalado"
    echo "   Instala Docker Desktop desde: https://www.docker.com/products/docker-desktop"
    exit 1
fi

# Verificar si Docker Compose está instalado
if ! command -v docker-compose &> /dev/null; then
    echo "❌ Docker Compose no está instalado"
    exit 1
fi

echo "✅ Docker está instalado"
echo ""

# Verificar si existe backup de BD
if [ ! -d "backup" ]; then
    mkdir -p backup
    echo "📁 Carpeta 'backup' creada"
fi

if [ ! -f "backup/micaela.sql" ]; then
    echo "⚠️  ADVERTENCIA: No se encontró backup/micaela.sql"
    echo "   Copia tu backup de base de datos a: backup/micaela.sql"
    echo ""
    read -p "¿Deseas continuar sin backup? (s/n): " -n 1 -r
    echo ""
    if [[ ! $REPLY =~ ^[Ss]$ ]]; then
        exit 1
    fi
fi

# Verificar certificado digital
if [ ! -f "greenter/certificados/certificado.pem" ]; then
    echo "⚠️  ADVERTENCIA: No se encontró certificado digital"
    echo "   Copia tu certificado a: greenter/certificados/certificado.pem"
    echo "   (Necesario para facturación electrónica)"
    echo ""
fi

# Construir imágenes
echo "🔨 Construyendo imágenes Docker..."
docker-compose build

if [ $? -ne 0 ]; then
    echo "❌ Error al construir las imágenes"
    exit 1
fi

echo ""
echo "✅ Imágenes construidas exitosamente"
echo ""

# Levantar servicios
echo "🚀 Levantando servicios..."
docker-compose up -d

if [ $? -ne 0 ]; then
    echo "❌ Error al levantar los servicios"
    exit 1
fi

echo ""
echo "⏳ Esperando a que los servicios estén listos..."
sleep 5

# Verificar estado
echo ""
echo "📊 Estado de los contenedores:"
docker-compose ps

echo ""
echo "=========================================="
echo "✅ Sistema iniciado correctamente"
echo "=========================================="
echo ""
echo "📍 Accede a:"
echo "   • Aplicación: http://localhost:8080"
echo "   • phpMyAdmin: http://localhost:8081"
echo "     Usuario: root"
echo "     Contraseña: root_password_2024"
echo ""
echo "📝 Comandos útiles:"
echo "   • Ver logs: docker-compose logs -f"
echo "   • Detener: docker-compose down"
echo "   • Reiniciar: docker-compose restart"
echo ""
echo "📖 Guía completa: DOCKER_SETUP.md"
echo "=========================================="
