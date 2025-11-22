#!/bin/bash

# Script de verificación antes de subir a GitHub
# Ejecuta: bash check-before-github.sh

echo "🔍 Verificando archivos sensibles antes de subir a GitHub..."
echo ""

# Colores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

ERRORS=0
WARNINGS=0

# Función para verificar que un archivo NO existe (está ignorado)
check_not_exists() {
    if [ -f "$1" ]; then
        echo -e "${RED}❌ PELIGRO: $1 existe y podría subirse a GitHub${NC}"
        echo "   Verifica que esté en .gitignore"
        ((ERRORS++))
    else
        echo -e "${GREEN}✅ OK: $1 no existe (correcto)${NC}"
    fi
}

# Función para verificar que un archivo SÍ existe
check_exists() {
    if [ -f "$1" ]; then
        echo -e "${GREEN}✅ OK: $1 existe${NC}"
    else
        echo -e "${YELLOW}⚠️  ADVERTENCIA: $1 no existe${NC}"
        echo "   Deberías crearlo antes de desplegar"
        ((WARNINGS++))
    fi
}

echo "📋 Verificando archivos de configuración..."
echo ""

# Archivos que NO deben existir (o deben estar en .gitignore)
check_not_exists "model/model_conexion.php"
check_not_exists "view/MPDF/conexion.php"
check_not_exists ".env"
check_not_exists "certificate.pem"

echo ""
echo "📋 Verificando archivos de ejemplo..."
echo ""

# Archivos de ejemplo que SÍ deben existir
check_exists "model/model_conexion.example.php"
check_exists "view/MPDF/conexion.example.php"
check_exists ".env.example"
check_exists ".gitignore"
check_exists ".dockerignore"

echo ""
echo "📋 Verificando estructura de carpetas..."
echo ""

# Verificar carpetas importantes
if [ -d "greenter/xml" ]; then
    echo -e "${GREEN}✅ OK: greenter/xml existe${NC}"
else
    echo -e "${YELLOW}⚠️  ADVERTENCIA: greenter/xml no existe${NC}"
    ((WARNINGS++))
fi

if [ -d "greenter/cdr" ]; then
    echo -e "${GREEN}✅ OK: greenter/cdr existe${NC}"
else
    echo -e "${YELLOW}⚠️  ADVERTENCIA: greenter/cdr no existe${NC}"
    ((WARNINGS++))
fi

if [ -d "greenter/pdf" ]; then
    echo -e "${GREEN}✅ OK: greenter/pdf existe${NC}"
else
    echo -e "${YELLOW}⚠️  ADVERTENCIA: greenter/pdf no existe${NC}"
    ((WARNINGS++))
fi

echo ""
echo "📋 Verificando .gitignore..."
echo ""

# Verificar que .gitignore contiene las entradas importantes
if grep -q "model_conexion.php" .gitignore; then
    echo -e "${GREEN}✅ OK: model_conexion.php está en .gitignore${NC}"
else
    echo -e "${RED}❌ ERROR: model_conexion.php NO está en .gitignore${NC}"
    ((ERRORS++))
fi

if grep -q "\.pem" .gitignore; then
    echo -e "${GREEN}✅ OK: *.pem está en .gitignore${NC}"
else
    echo -e "${RED}❌ ERROR: *.pem NO está en .gitignore${NC}"
    ((ERRORS++))
fi

if grep -q "\.env" .gitignore; then
    echo -e "${GREEN}✅ OK: .env está en .gitignore${NC}"
else
    echo -e "${RED}❌ ERROR: .env NO está en .gitignore${NC}"
    ((ERRORS++))
fi

echo ""
echo "📋 Buscando posibles credenciales en el código..."
echo ""

# Buscar patrones sospechosos (excluyendo vendor y archivos de ejemplo)
SUSPICIOUS=$(grep -r -i "password.*=.*['\"].*['\"]" --include="*.php" --exclude-dir=vendor --exclude="*.example.php" . 2>/dev/null | grep -v "PASSWORD_DEFAULT" | grep -v "password_hash" | grep -v "password_verify" | wc -l)

if [ "$SUSPICIOUS" -gt 0 ]; then
    echo -e "${YELLOW}⚠️  ADVERTENCIA: Se encontraron $SUSPICIOUS posibles credenciales hardcodeadas${NC}"
    echo "   Revisa manualmente estos archivos"
    ((WARNINGS++))
else
    echo -e "${GREEN}✅ OK: No se encontraron credenciales obvias en el código${NC}"
fi

echo ""
echo "═══════════════════════════════════════════════════════"
echo ""

if [ $ERRORS -eq 0 ] && [ $WARNINGS -eq 0 ]; then
    echo -e "${GREEN}🎉 ¡PERFECTO! Tu proyecto está listo para subir a GitHub${NC}"
    echo ""
    echo "Próximos pasos:"
    echo "1. git init"
    echo "2. git add ."
    echo "3. git commit -m 'Initial commit'"
    echo "4. git remote add origin https://github.com/TU_USUARIO/sistema-tours-micaela.git"
    echo "5. git push -u origin main"
elif [ $ERRORS -eq 0 ]; then
    echo -e "${YELLOW}⚠️  HAY $WARNINGS ADVERTENCIAS${NC}"
    echo ""
    echo "Puedes continuar, pero revisa las advertencias arriba."
    echo "Son principalmente archivos que necesitarás crear en producción."
else
    echo -e "${RED}❌ HAY $ERRORS ERRORES CRÍTICOS${NC}"
    echo ""
    echo "NO SUBAS EL PROYECTO A GITHUB HASTA CORREGIR LOS ERRORES."
    echo "Revisa los mensajes en rojo arriba."
fi

echo ""
echo "═══════════════════════════════════════════════════════"
