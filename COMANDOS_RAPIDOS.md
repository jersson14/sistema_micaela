# ⚡ Comandos Rápidos - GitHub y VPS

---

## 🚀 PARTE 1: Subir a GitHub (Desde tu PC)

### Paso 1: Verificar que todo está bien
```bash
# Windows
.\check-before-github.bat

# Linux/Mac
bash check-before-github.sh
```

### Paso 2: Inicializar Git
```bash
git init
git add .
git status
```

### Paso 3: Primer commit
```bash
git commit -m "Initial commit: Sistema Tours Micaela con facturación electrónica SUNAT"
```

### Paso 4: Crear repositorio en GitHub
1. Ve a: https://github.com/new
2. Nombre: `sistema-tours-micaela`
3. **NO** marques "Initialize with README"
4. Click en "Create repository"

### Paso 5: Subir código
```bash
# Reemplaza TU_USUARIO con tu usuario de GitHub
git remote add origin https://github.com/TU_USUARIO/sistema-tours-micaela.git
git branch -M main
git push -u origin main
```

---

## 🖥️ PARTE 2: Desplegar en VPS (Docker + MySQL nativo)

### Paso 1: Conectar al VPS
```bash
ssh usuario@tu-vps-ip
```

### Paso 2: Verificar MySQL (ya lo tienes instalado)
```bash
# Verificar que MySQL está corriendo
sudo systemctl status mysql
```

### Paso 3: Crear base de datos y usuario (si no lo hiciste)
```bash
# Entrar a MySQL
sudo mysql -u root -p

# Dentro de MySQL:
CREATE DATABASE micaela CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'micaela_user'@'%' IDENTIFIED BY 'TU_PASSWORD_SEGURO';
GRANT ALL PRIVILEGES ON micaela.* TO 'micaela_user'@'%';
FLUSH PRIVILEGES;
EXIT;
```

**Nota:** Usamos `'%'` en lugar de `'localhost'` para que Docker pueda conectarse.

### Paso 4: Importar base de datos (ya lo estás haciendo)
```bash
mysql -u micaela_user -p micaela < /ruta/al/backup.sql
```

### Paso 5: Instalar Docker (si no está instalado)
```bash
# Instalar Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Instalar Docker Compose
sudo apt install docker-compose -y

# Agregar usuario al grupo docker
sudo usermod -aG docker $USER

# Cerrar sesión y volver a entrar
exit
ssh usuario@tu-vps-ip
```

### Paso 6: Clonar repositorio
```bash
# Ir a tu directorio home
cd ~

# Clonar el repositorio
git clone https://github.com/TU_USUARIO/sistema-tours-micaela.git
cd sistema-tours-micaela
```

### Paso 7: Configurar archivos sensibles

#### 7.1 Configurar conexión principal
```bash
cp model/model_conexion.example.php model/model_conexion.php
nano model/model_conexion.php
```

Editar:
```php
$this->host = "host.docker.internal";  // Para conectar desde Docker al MySQL del host
$this->usuario = "micaela_user";
$this->contrasena = "TU_PASSWORD_SEGURO";
$this->bdName = "micaela";
$this->puerto = 3306;
```

#### 7.2 Configurar conexión mPDF
```bash
cp view/MPDF/conexion.example.php view/MPDF/conexion.php
nano view/MPDF/conexion.php
```

Editar con las mismas credenciales (host = "host.docker.internal").

#### 7.3 Configurar variables de entorno
```bash
cp .env.example .env
nano .env
```

Editar:
```env
DB_HOST=host.docker.internal
DB_PORT=3306
DB_NAME=micaela
DB_USER=micaela_user
DB_PASSWORD=TU_PASSWORD_SEGURO

SUNAT_MODE=beta
RUC_EMPRESA=20XXXXXXXXX
RAZON_SOCIAL_EMPRESA=TU EMPRESA

CERT_PATH=/var/www/html/greenter/certificados/certificado.pem
```

### Paso 8: Subir certificado digital desde tu PC
```bash
# Desde tu PC (Windows):
scp certificado.pem root@tu-vps-ip:~/sistema-tours-micaela/greenter/certificados/

# En el VPS:
chmod 600 greenter/certificados/certificado.pem
```

### Paso 9: Dar permisos a carpetas
```bash
chmod -R 775 greenter/xml greenter/cdr greenter/pdf Fotos
```

### Paso 10: Levantar Docker
```bash
# Construir y levantar el contenedor
docker-compose -f docker-compose.production.yml up -d

# Ver logs
docker-compose -f docker-compose.production.yml logs -f
```

### Paso 11: Verificar que funciona
```bash
# Ver contenedor corriendo
docker ps

# Probar la aplicación
curl http://localhost

# O desde el navegador:
# http://tu-vps-ip
```

---

## 🌐 PARTE 3: Configurar Dominio (Opcional)

### Paso 1: Instalar Nginx
```bash
sudo apt install nginx -y
```

### Paso 2: Configurar sitio
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

### Paso 3: Activar sitio
```bash
sudo ln -s /etc/nginx/sites-available/tours-micaela /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### Paso 4: Instalar SSL
```bash
sudo apt install certbot python3-certbot-nginx -y
sudo certbot --nginx -d tu-dominio.com -d www.tu-dominio.com
```

### Paso 5: Configurar firewall
```bash
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

---

## 🔄 PARTE 4: Actualizaciones Futuras

### Cuando hagas cambios en tu código:

**En tu PC:**
```bash
git add .
git commit -m "Descripción de los cambios"
git push origin main
```

**En el VPS:**
```bash
cd /var/www/sistema-tours-micaela
git pull origin main
docker-compose -f docker-compose.production.yml restart
```

**Si cambiaste dependencias de Composer:**
```bash
docker-compose -f docker-compose.production.yml down
docker-compose -f docker-compose.production.yml build --no-cache
docker-compose -f docker-compose.production.yml up -d
```

---

## 📊 PARTE 5: Comandos Útiles

### Ver logs del contenedor
```bash
docker-compose -f docker-compose.production.yml logs -f app
docker logs tours_micaela_prod -f
```

### Ver logs de Apache dentro del contenedor
```bash
docker exec tours_micaela_prod tail -f /var/log/apache2/error.log
```

### Reiniciar servicios
```bash
# Reiniciar contenedor
docker-compose -f docker-compose.production.yml restart

# Reiniciar MySQL (en el host)
sudo systemctl restart mysql
```

### Hacer backup de BD
```bash
mysqldump -u micaela_user -p micaela > backup/backup_$(date +%Y%m%d).sql
```

### Restaurar backup de BD
```bash
mysql -u micaela_user -p micaela < backup/micaela.sql
```

### Ver uso de recursos
```bash
docker stats
htop
df -h
```

### Entrar al contenedor
```bash
docker exec -it tours_micaela_prod bash
```

### Entrar a MySQL (en el host)
```bash
mysql -u micaela_user -p micaela
```

### Ver estado del contenedor
```bash
docker ps
docker-compose -f docker-compose.production.yml ps
```

---

## 🆘 Solución Rápida de Problemas

### Error: "Cannot connect to database"
```bash
# Verificar que MySQL está corriendo en el host
sudo systemctl status mysql

# Verificar que el usuario puede conectarse desde cualquier host
sudo mysql -u root -p
SELECT user, host FROM mysql.user WHERE user='micaela_user';
# Debe mostrar '%' en host, no 'localhost'

# Si muestra 'localhost', cambiar:
GRANT ALL PRIVILEGES ON micaela.* TO 'micaela_user'@'%';
FLUSH PRIVILEGES;
```

### Error: "host.docker.internal" no resuelve
```bash
# Opción 1: Usar la IP del VPS en lugar de host.docker.internal
# En model_conexion.php: $this->host = "172.17.0.1";

# Opción 2: Usar network_mode: host en docker-compose
# (ver docker-compose.production.yml)
```

### Error: "Permission denied"
```bash
chmod -R 775 greenter/xml greenter/cdr greenter/pdf Fotos
```

### Ver logs de errores
```bash
docker-compose -f docker-compose.production.yml logs --tail=100
docker exec tours_micaela_prod tail -f /var/log/apache2/error.log
```

### Reconstruir contenedor
```bash
docker-compose -f docker-compose.production.yml down
docker-compose -f docker-compose.production.yml build --no-cache
docker-compose -f docker-compose.production.yml up -d
```

### Limpiar Docker (liberar espacio)
```bash
docker system prune -a
```

---

## ✅ Checklist Rápido

### Antes de GitHub:
- [ ] Ejecuté `check-before-github.bat`
- [ ] No hay errores críticos
- [ ] Hice `git init` y `git add .`
- [ ] Creé repositorio en GitHub
- [ ] Hice `git push`

### En el VPS:
- [ ] MySQL está instalado y corriendo
- [ ] Creé la base de datos `micaela`
- [ ] Creé el usuario `micaela_user@'%'` (no localhost)
- [ ] Importé el backup de la BD
- [ ] Docker está instalado
- [ ] Cloné el repositorio en `/var/www`
- [ ] Configuré `model/model_conexion.php` (host=host.docker.internal)
- [ ] Configuré `view/MPDF/conexion.php` (host=host.docker.internal)
- [ ] Subí el certificado digital
- [ ] Di permisos a las carpetas
- [ ] Levanté Docker: `docker-compose -f docker-compose.production.yml up -d`

### Verificación Final:
- [ ] `docker ps` muestra el contenedor corriendo
- [ ] `curl http://localhost` responde
- [ ] Puedo acceder desde el navegador
- [ ] El login funciona
- [ ] Puedo emitir comprobantes
- [ ] Los PDFs se generan
- [ ] Los XMLs se envían a SUNAT

---

## 🎯 Resumen Ultra-Rápido

```bash
# ========== EN TU PC ==========
git init
git add .
git commit -m "Initial commit"
git remote add origin https://github.com/TU_USUARIO/sistema-tours-micaela.git
git push -u origin main

# ========== EN EL VPS ==========
ssh usuario@tu-vps-ip

# 1. Crear BD (si no existe)
sudo mysql -u root -p
CREATE DATABASE micaela;
CREATE USER 'micaela_user'@'%' IDENTIFIED BY 'TU_PASSWORD';
GRANT ALL PRIVILEGES ON micaela.* TO 'micaela_user'@'%';
EXIT;

# 2. Importar datos (ya lo estás haciendo)
mysql -u micaela_user -p micaela < /ruta/backup.sql

# 3. Clonar proyecto
cd ~
git clone https://github.com/TU_USUARIO/sistema-tours-micaela.git
cd sistema-tours-micaela

# 4. Configurar archivos
cp model/model_conexion.example.php model/model_conexion.php
nano model/model_conexion.php
# Cambiar: $this->host = "host.docker.internal";

cp view/MPDF/conexion.example.php view/MPDF/conexion.php
nano view/MPDF/conexion.php
# Cambiar: $host = "host.docker.internal";

# 5. Subir certificado desde tu PC
# scp certificado.pem root@vps-ip:~/sistema-tours-micaela/greenter/certificados/

# 6. Dar permisos
chmod -R 775 greenter/xml greenter/cdr greenter/pdf Fotos
chmod 600 greenter/certificados/certificado.pem

# 7. Levantar Docker (incluye Apache, PHP, Composer, todo!)
docker-compose -f docker-compose.production.yml up -d

# 8. Ver logs
docker-compose -f docker-compose.production.yml logs -f

# ¡LISTO! 🎉
# Accede a: http://tu-vps-ip
```

---

**¡Éxito en tu despliegue!** 🚀
