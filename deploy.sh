#!/bin/bash

# ============================================
# Script de Despliegue Automático
# Tours Micaela - Producción VPS
# ============================================

set -e  # Salir si hay algún error

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Funciones de utilidad
print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

print_info() {
    echo -e "${BLUE}ℹ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

# Banner
echo -e "${BLUE}"
echo "╔════════════════════════════════════════╗"
echo "║   Tours Micaela - Deploy Script       ║"
echo "║   Despliegue en Producción VPS         ║"
echo "╚════════════════════════════════════════╝"
echo -e "${NC}"

# Verificar que estamos en el directorio correcto
if [ ! -f "Dockerfile.production" ]; then
    print_error "No se encontró Dockerfile.production"
    print_info "Asegúrate de estar en el directorio raíz del proyecto"
    exit 1
fi

print_success "Directorio del proyecto verificado"

# Verificar Docker
if ! command -v docker &> /dev/null; then
    print_error "Docker no está instalado"
    print_info "Instala Docker: https://docs.docker.com/engine/install/"
    exit 1
fi

print_success "Docker instalado"

# Verificar Docker Compose
if ! command -v docker-compose &> /dev/null; then
    print_error "Docker Compose no está instalado"
    print_info "Instala Docker Compose: https://docs.docker.com/compose/install/"
    exit 1
fi

print_success "Docker Compose instalado"

# Menú de opciones
echo ""
print_info "Selecciona una opción:"
echo "1) Construir imagen"
echo "2) Iniciar contenedor"
echo "3) Detener contenedor"
echo "4) Reconstruir y reiniciar"
echo "5) Ver logs"
echo "6) Ver estado"
echo "7) Limpiar todo"
echo "8) Backup"
echo "9) Salir"
echo ""
read -p "Opción: " option

case $option in
    1)
        print_info "Construyendo imagen de producción..."
        docker build -f Dockerfile.production -t tours-micaela:production .
        print_success "Imagen construida exitosamente"
        ;;
    2)
        print_info "Iniciando contenedor..."
        docker-compose -f docker-compose.production.yml up -d
        print_success "Contenedor iniciado"
        print_info "Accede a: http://localhost"
        ;;
    3)
        print_info "Deteniendo contenedor..."
        docker-compose -f docker-compose.production.yml down
        print_success "Contenedor detenido"
        ;;
    4)
        print_info "Reconstruyendo y reiniciando..."
        docker-compose -f docker-compose.production.yml down
        docker build -f Dockerfile.production -t tours-micaela:production .
        docker-compose -f docker-compose.production.yml up -d
        print_success "Aplicación actualizada y reiniciada"
        ;;
    5)
        print_info "Mostrando logs (Ctrl+C para salir)..."
        docker-compose -f docker-compose.production.yml logs -f
        ;;
    6)
        print_info "Estado de contenedores:"
        docker-compose -f docker-compose.production.yml ps
        echo ""
        print_info "Uso de recursos:"
        docker stats --no-stream tours_micaela_prod
        ;;
    7)
        print_warning "Esto eliminará el contenedor y la imagen"
        read -p "¿Estás seguro? (s/n): " confirm
        if [ "$confirm" = "s" ]; then
            docker-compose -f docker-compose.production.yml down
            docker rmi tours-micaela:production
            print_success "Limpieza completada"
        else
            print_info "Operación cancelada"
        fi
        ;;
    8)
        print_info "Creando backup..."
        BACKUP_DIR="backups"
        mkdir -p $BACKUP_DIR
        TIMESTAMP=$(date +%Y%m%d_%H%M%S)
        
        # Backup de archivos
        tar -czf "$BACKUP_DIR/tours-micaela-$TIMESTAMP.tar.gz" \
            --exclude='greenter/xml/*' \
            --exclude='greenter/cdr/*' \
            --exclude='greenter/pdf/*' \
            --exclude='Fotos/*' \
            --exclude='logs/*' \
            .
        
        print_success "Backup creado: $BACKUP_DIR/tours-micaela-$TIMESTAMP.tar.gz"
        ;;
    9)
        print_info "Saliendo..."
        exit 0
        ;;
    *)
        print_error "Opción inválida"
        exit 1
        ;;
esac

echo ""
print_success "Operación completada"
