# 🚀 Instalación en VPS sin Docker (Apache + PHP nativo)

## ✅ Ventajas
- Sesiones PHP funcionan perfectamente
- JWT funciona sin problemas
- Greenter funciona igual que en local
- Más rápido y fácil de mantener
- Exactamente como XAMPP en tu PC

---

## 🗑️ PASO 1: Limpiar Docker del VPS

```bash
# Conectar al VPS
ssh root@72.61.40.91

# Detener y eliminar contenedores
cd /var/www/html/sistema_micaela
docker-compose -f docker-compose.production.yml down -v

# Eliminar imágenes de Docker (opcional)
docker system prune -a -f

# Hacer backup del proyecto
cp -r /var/www/html/sistema_micaela /root/backup_sistema_micaela
```

---

## 📦 PASO 2: Instalar Apache y PHP

```bash
# Actualizar sistema
sudo apt update && sudo apt upgrade -y

# Instalar Apache
sudo apt install apache2 -y

# Instalar PHP 8.2 y extensiones necesarias
sudo apt install php8.2 libapache2-mod-php8.2 php8.2-mysql php8.2-xml php8.2-zip php8.2-mbstring php8.2-curl php8.2-soap php8.2-gd php8.2-bcmath -y

# Habilitar módulos de Apache
sudo a2enmod rewrite
sudo a2enmod headers

# Reiniciar Apache
sudo systemctl restart apache2

# Verificar que funciona
sudo systemctl status apache2
```

---

## 🎼 PASO 3: Instalar Composer

```bash
# Descargar Composer
curl -sS https://getcomposer.org/installer | php

# Mover a directorio global
sudo mv composer.phar /usr/local/bin/composer

# Verificar instalación
composer --version
```

---

## 📂 PASO 4: Configurar el proyecto

```bash
# Mover proyecto a la ubicación correcta
sudo rm -rf /var/www/html/index.html
sudo cp -r /var/www/html/sistema_micaela/* /var/www/html/

# O crear un VirtualHost específico (recomendado)
sudo mkdir -p /var/www/sistema
sudo cp -r /var/www/html/sistema_micaela/* /var/www/sistema/

# Instalar dependencias de Composer (Greenter, JWT, etc.)
cd /var/www/sistema
sudo composer install --no-dev --optimize-autoloader

# Dar permisos correctos
sudo chown -R www-data:www-data /var/www/sistema
sudo chmod -R 755 /var/www/sistema
sudo chmod -R 775 /var/www/sistema/greenter/xml
sudo chmod -R 775 /var/www/sistema/greenter/cdr
sudo chmod -R 775 /var/www/sistema/greenter/pdf
sudo chmod -R 775 /var/www/sistema/Fotos
```

---

## ⚙️ PASO 5: Configurar archivos de conexión

```bash
# Editar conexión a BD
sudo nano /var/www/sistema/model/model_conexion.php
```

Cambiar a:
```php
private static $host = "localhost";  // MySQL local
private static $usuario = "jersson";
private static $contrasena = "TU_PASSWORD";
private static $bdName = "micaela";
private static $puerto = 3306;
```

---

## 🌐 PASO 6: Configurar Apache VirtualHost

```bash
# Crear configuración del sitio
sudo nano /etc/apache2/sites-available/sistema.conf
```

Contenido:
```apache
<VirtualHost *:80>
    ServerAdmin jersson1407miranda@gmail.com
    ServerName 72.61.40.91
    DocumentRoot /var/www/sistema

    <Directory /var/www/sistema>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    # Logs
    ErrorLog ${APACHE_LOG_DIR}/sistema-error.log
    CustomLog ${APACHE_LOG_DIR}/sistema-access.log combined

    # PHP settings
    php_value upload_max_filesize 20M
    php_value post_max_size 20M
    php_value memory_limit 256M
    php_value max_execution_time 300
</VirtualHost>
```

```bash
# Desactivar sitio por defecto
sudo a2dissite 000-default.conf

# Activar nuevo sitio
sudo a2ensite sistema.conf

# Verificar configuración
sudo apache2ctl configtest

# Reiniciar Apache
sudo systemctl restart apache2
```

---

## 🔒 PASO 7: Configurar PHP

```bash
# Editar php.ini
sudo nano /etc/php/8.2/apache2/php.ini
```

Buscar y cambiar:
```ini
upload_max_filesize = 20M
post_max_size = 20M
memory_limit = 256M
max_execution_time = 300
date.timezone = America/Lima
session.gc_maxlifetime = 7200
session.cookie_lifetime = 7200
```

```bash
# Reiniciar Apache
sudo systemctl restart apache2
```

---

## 🔥 PASO 8: Configurar Firewall

```bash
# Permitir HTTP y HTTPS
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

# Verificar
sudo ufw status
```

---

## ✅ PASO 9: Verificar instalación

```bash
# Ver logs de Apache
sudo tail -f /var/log/apache2/sistema-error.log

# Verificar PHP
php -v

# Verificar extensiones
php -m | grep -E "mysql|xml|zip|mbstring|curl|soap|gd"

# Probar en navegador
curl http://72.61.40.91
```

---

## 🧪 PASO 10: Probar el sistema

1. Abre en navegador: `http://72.61.40.91`
2. Haz login con tu usuario
3. Verifica que:
   - ✅ Entra al menú principal
   - ✅ Muestra tu nombre, rol y sucursal
   - ✅ Puedes navegar entre módulos
   - ✅ Puedes emitir comprobantes

---

## 🔄 Actualizaciones futuras

```bash
# Cuando hagas cambios en GitHub
cd /var/www/sistema
sudo git pull origin jersson
sudo composer install --no-dev
sudo systemctl restart apache2
```

---

## 🆘 Solución de problemas

### Error: "Cannot connect to database"
```bash
# Verificar MySQL
sudo systemctl status mysql

# Verificar credenciales
mysql -u jersson -p micaela
```

### Error 500
```bash
# Ver logs
sudo tail -f /var/log/apache2/sistema-error.log
```

### Permisos
```bash
sudo chown -R www-data:www-data /var/www/sistema
sudo chmod -R 755 /var/www/sistema
```

---

## 📊 Comparación Docker vs Nativo

| Característica | Docker | Apache Nativo |
|----------------|--------|---------------|
| Sesiones PHP | ❌ Problemas | ✅ Funciona |
| JWT | ⚠️ Complejo | ✅ Funciona |
| Greenter | ✅ Funciona | ✅ Funciona |
| Velocidad | 🐢 Más lento | ⚡ Más rápido |
| Mantenimiento | 😰 Complejo | 😊 Simple |
| Como tu local | ❌ Diferente | ✅ Igual |

---

**¡Listo! Tu sistema funcionará exactamente como en tu local con XAMPP.** 🎉
