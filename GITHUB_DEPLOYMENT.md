# 🚀 Guía de Despliegue desde GitHub a VPS

Esta guía te ayudará a subir tu proyecto a GitHub y desplegarlo en tu VPS de forma segura.

---

## 📋 Checklist Pre-GitHub

Antes de subir tu código a GitHub, verifica:

### ✅ Archivos de Seguridad
- [x] `.gitignore` configurado correctamente
- [x] `model/model_conexion.php` está en `.gitignore`
- [x] `view/MPDF/conexion.php` está en `.gitignore`
- [x] Certificados `.pem` están en `.gitignore`
- [x] Archivos `.sql` con datos reales están en `.gitignore`
- [x] `.env` está en `.gitignore`

### ✅ Archivos de Ejemplo Creados
- [x] `model/model_conexion.example.php`
- [x] `view/MPDF/conexion.example.php`
- [x] `.env.example`
- [x] `greenter/certificados/README.md`

### ✅ Estructura de Carpetas
- [x] Carpetas vacías tienen `.gitkeep`
- [x] `greenter/xml/.gitkeep`
- [x] `greenter/cdr/.gitkeep`
- [x] `greenter/pdf/.gitkeep`
- [x] `Fotos/.gitkeep`

---

## 🔐 Paso 1: Verificar que NO subes archivos sensibles

Ejecuta estos comandos para verificar:

```bash
# Ver qué archivos se subirán
git status

# Ver qué archivos están siendo ignorados
git status --ignored

# Verificar que estos archivos NO aparezcan:
# - model/model_conexion.php
# - view/MPDF/conexion.php
# - certificate.pem
# - *.sql (excepto en backup/)
# - .env
```

---

## 📤 Paso 2: Subir a GitHub

### Opción A: Crear nuevo repositorio en GitHub

1. Ve a https://github.com/new
2. Crea un repositorio (público o privado)
3. **NO inicialices con README** (ya tienes uno)

### Opción B: Comandos Git

```bash
# 1. Inicializar Git (si no lo has hecho)
git init

# 2. Agregar todos los archivos
git add .

# 3. Verificar qué se agregará
git status

# 4. Hacer el primer commit
git commit -m "Initial commit: Sistema Tours Micaela con facturación electrónica"

# 5. Agregar el repositorio remoto
git remote add origin https://github.com/TU_USUARIO/sistema-tours-micaela.git

# 6. Subir a GitHub
git push -u origin main
```

Si tu rama principal se llama `master`:
```bash
git branch -M main
git push -u origin main
```

---

## 🖥️ Paso 3: Desplegar en tu VPS

### Requisitos del VPS
- Ubuntu 20.04+ / Debian 11+
- Docker y Docker Compose instalados
- Acceso SSH
- Puertos 80 y 443 disponibles

### 3.1 Conectar al VPS

```bash
ssh usuario@tu-vps-ip
```

### 3.2 Instalar Docker (si no está instalado)

```bash
# Actualizar sistema
sudo apt update && sudo apt upgrade -y

# Instalar Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Instalar Docker Compose
sudo apt install docker-compose -y

# Agregar usuario al grupo docker
sudo usermod -aG docker $USER

# Reiniciar sesión
exit
ssh usuario@tu-vps-ip
```

### 3.3 Clonar el repositorio

```bash
# Ir al directorio de aplicaciones
cd /var/www

# Clonar desde GitHub
sudo git clone https://github.com/TU_USUARIO/sistema-tours-micaela.git
cd sistema-tours-micaela

# Dar permisos
sudo chown -R $USER:$USER .
```

### 3.4 Configurar archivos sensibles

```bash
# 1. Crear archivo de conexión principal
cp model/model_conexion.example.php model/model_conexion.php
nano model/model_conexion.php
```

Edita con tus credenciales:
```php
$this->host = "db";              // Para Docker
$this->usuario = "micaela_user";
$this->contrasena = "TU_PASSWORD_SEGURO";
$this->bdName = "micaela";
$this->puerto = 3306;            // Dentro de Docker es 3306
```

```bash
# 2. Crear archivo de conexión para mPDF
cp view/MPDF/conexion.example.php view/MPDF/conexion.php
nano view/MPDF/conexion.php
```

```bash
# 3. Configurar variables de entorno
cp .env.example .env
nano .env
```

Edita `.env`:
```env
DB_HOST=db
DB_PORT=3306
DB_NAME=micaela
DB_USER=micaela_user
DB_PASSWORD=TU_PASSWORD_SEGURO_AQUI

SUNAT_MODE=beta
RUC_EMPRESA=20XXXXXXXXX
RAZON_SOCIAL_EMPRESA=TU EMPRESA

CERT_PATH=/var/www/html/greenter/certificados/certificado.pem
```

### 3.5 Subir certificado digital

```bash
# Desde tu PC local, sube el certificado
scp certificado.pem usuario@tu-vps-ip:/var/www/sistema-tours-micaela/greenter/certificados/

# En el VPS, verifica permisos
chmod 600 greenter/certificados/certificado.pem
```

### 3.6 Subir backup de base de datos

```bash
# Desde tu PC local
scp backup/micaela.sql usuario@tu-vps-ip:/var/www/sistema-tours-micaela/backup/

# En el VPS, verifica que existe
ls -lh backup/micaela.sql
```

### 3.7 Configurar Docker para producción

Edita `docker-compose.production.yml`:
```bash
nano docker-compose.production.yml
```

Cambia las contraseñas:
```yaml
environment:
  MYSQL_ROOT_PASSWORD: TU_ROOT_PASSWORD_SEGURO
  MYSQL_PASSWORD: TU_PASSWORD_SEGURO
```

### 3.8 Levantar servicios

```bash
# Instalar dependencias PHP
docker run --rm -v $(pwd):/app composer install

# Levantar servicios
docker-compose -f docker-compose.production.yml up -d

# Ver logs
docker-compose -f docker-compose.production.yml logs -f
```

### 3.9 Verificar que funciona

```bash
# Ver contenedores corriendo
docker ps

# Probar la aplicación
curl http://localhost
```

---

## 🌐 Paso 4: Configurar Dominio y SSL

### 4.1 Instalar Nginx como proxy reverso

```bash
sudo apt install nginx -y
```

### 4.2 Configurar Nginx

```bash
sudo nano /etc/nginx/sites-available/tours-micaela
```

Contenido:
```nginx
server {
    listen 80;
    server_name tu-dominio.com www.tu-dominio.com;

    location / {
        proxy_pass http://localhost:8080;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

```bash
# Activar sitio
sudo ln -s /etc/nginx/sites-available/tours-micaela /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 4.3 Instalar SSL con Let's Encrypt

```bash
# Instalar Certbot
sudo apt install certbot python3-certbot-nginx -y

# Obtener certificado SSL
sudo certbot --nginx -d tu-dominio.com -d www.tu-dominio.com

# Renovación automática (ya está configurada)
sudo certbot renew --dry-run
```

---

## 🔄 Paso 5: Actualizaciones Futuras

Cuando hagas cambios en tu código:

```bash
# En tu PC local
git add .
git commit -m "Descripción de cambios"
git push origin main

# En el VPS
cd /var/www/sistema-tours-micaela
git pull origin main
docker-compose -f docker-compose.production.yml restart
```

---

## 🛡️ Paso 6: Seguridad Adicional

### Firewall
```bash
sudo ufw allow 22/tcp    # SSH
sudo ufw allow 80/tcp    # HTTP
sudo ufw allow 443/tcp   # HTTPS
sudo ufw enable
```

### Backups automáticos
```bash
# Crear script de backup
nano ~/backup-db.sh
```

Contenido:
```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
docker exec tours_micaela_db mysqldump -uroot -pTU_ROOT_PASSWORD micaela > /backups/micaela_$DATE.sql
find /backups -name "micaela_*.sql" -mtime +7 -delete
```

```bash
chmod +x ~/backup-db.sh

# Agregar a crontab (diario a las 2 AM)
crontab -e
```

Agregar:
```
0 2 * * * /home/usuario/backup-db.sh
```

---

## 📊 Monitoreo

### Ver logs en tiempo real
```bash
docker-compose -f docker-compose.production.yml logs -f app
docker-compose -f docker-compose.production.yml logs -f db
```

### Ver uso de recursos
```bash
docker stats
```

### Reiniciar servicios
```bash
docker-compose -f docker-compose.production.yml restart
```

---

## 🆘 Solución de Problemas

### Error: "Cannot connect to database"
```bash
# Verificar que MySQL esté corriendo
docker ps | grep mysql

# Ver logs de MySQL
docker-compose -f docker-compose.production.yml logs db
```

### Error: "Permission denied"
```bash
# Dar permisos a carpetas
chmod -R 755 greenter/xml greenter/cdr greenter/pdf Fotos
```

### Contenedor no inicia
```bash
# Ver logs detallados
docker-compose -f docker-compose.production.yml logs --tail=100

# Reconstruir imagen
docker-compose -f docker-compose.production.yml build --no-cache
docker-compose -f docker-compose.production.yml up -d
```

---

## ✅ Checklist Final

- [ ] Código subido a GitHub sin archivos sensibles
- [ ] VPS con Docker instalado
- [ ] Repositorio clonado en VPS
- [ ] Archivos de configuración creados
- [ ] Certificado digital subido
- [ ] Base de datos importada
- [ ] Servicios Docker corriendo
- [ ] Nginx configurado
- [ ] SSL instalado
- [ ] Dominio apuntando al VPS
- [ ] Backups automáticos configurados
- [ ] Firewall activado

---

## 📞 Soporte

Si encuentras problemas, revisa:
1. Logs de Docker
2. Logs de Nginx: `/var/log/nginx/error.log`
3. Logs de la aplicación en `greenter/*.log`

---

**¡Listo! Tu sistema está desplegado en producción** 🎉
