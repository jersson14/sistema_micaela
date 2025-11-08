#!/bin/bash
set -e

echo "🚀 Iniciando Sistema Tours Micaela..."

# Esperar a que MySQL esté listo
echo "⏳ Esperando a que MySQL esté disponible..."
until mysql -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASSWORD" -e "SELECT 1" &> /dev/null
do
    echo "   MySQL no está listo - esperando..."
    sleep 3
done

echo "✅ MySQL está listo!"

# Verificar permisos de directorios
echo "🔐 Configurando permisos..."
chown -R www-data:www-data /var/www/html/greenter
chown -R www-data:www-data /var/www/html/Fotos
chown -R www-data:www-data /var/www/html/controller/usuario/fotos
chown -R www-data:www-data /var/www/html/controller/choferes/fotos
chown -R www-data:www-data /var/www/html/controller/empresa/FOTOS

# Verificar certificado digital
if [ ! -f "/var/www/html/greenter/certificados/certificado.pem" ]; then
    echo "⚠️  ADVERTENCIA: No se encontró certificado digital en greenter/certificados/"
    echo "   Por favor, copia tu certificado .pem antes de usar facturación electrónica"
fi

echo "✅ Sistema listo!"
echo "📍 Accede a: http://localhost:8080"
echo "📊 phpMyAdmin: http://localhost:8081"

# Iniciar Apache
exec apache2-foreground
