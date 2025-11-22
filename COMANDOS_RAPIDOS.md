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

## 🖥️ PARTE 2: Desplegar en VPS

### Paso 1: Conectar al VPS
```bash
ssh usuario@tu-vps-ip
```

### Paso 2: Instalar Docker (si no está instalado)
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

# Cerrar sesión y volver a entrar
exit
ssh usuario@tu-vps-ip
```

### Paso 3: Clonar repositorio
```bash
cd /var/www
sudo git clone https://github.com/TU_USUARIO/sistema-tours-micaela.git
cd sistema-tours-micaela
sudo chown -R $USER:$USER .
```

### Paso 4: Configurar archivos sensibles

#### 4.1 Configurar conexión principal
```bash
cp model/model_conexion.example.php model/model_conexion.php
nano model/model_conexion.php
```

Editar:
```php
$this->host = "db";
$this->usuario = "micaela_user";
$this->contrasena = "TU_PASSWORD_SEGURO_AQUI";
$this->bdName = "micaela";
$this->puerto = 3306;
```

#### 4.2 Configurar conexión mPDF
```bash
cp view/MPDF/conexion.example.php view/MPDF/conexion.php
nano view/MPDF/conexion.php
```

Editar igual que arriba.

#### 4.3 Configurar variables de entorno
```bash
cp .env.example .env
nano .env
```

Editar:
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

#### 4.4 Configurar Docker Compose
```bash
nano docker-compose.production.yml
```

Cambiar las contraseñas:
```yaml
MYSQL_ROOT_PASSWORD: TU_ROOT_PASSWORD_SEGURO
MYSQL_PASSWORD: TU_PASSWORD_SEGURO
```

### Paso 5: Subir archivos sensibles desde tu PC

**Abrir nueva terminal en tu PC (no cerrar la del VPS):**

```bash
# Subir certificado digital
scp certificado.pem usuario@tu-vps-ip:/var/www/sistema-tours-micaela/greenter/certificados/

# Subir backup de base de datos
scp backup/micaela.sql usuario@tu-vps-ip:/var/www/sistema-tours-micaela/backup/
```

### Paso 6: Dar permisos al certificado (en el VPS)
```bash
chmod 600 greenter/certificados/certificado.pem
```

### Paso 7: Levantar servicios Docker
```bash
# Instalar dependencias PHP
docker run --rm -v $(pwd):/app composer install

# Levantar servicios
docker-compose -f docker-compose.production.yml up -d

# Ver logs
docker-compose -f docker-compose.production.yml logs -f
```

### Paso 8: Verificar que funciona
```bash
# Ver contenedores corriendo
docker ps

# Debería mostrar 3 contenedores:
# - tours_micaela_app
# - tours_micaela_db
# - tours_micaela_phpmyadmin

# Probar la aplicación
curl http://localhost:8080
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

---

## 📊 PARTE 5: Comandos Útiles

### Ver logs en tiempo real
```bash
docker-compose -f docker-compose.production.yml logs -f app
docker-compose -f docker-compose.production.yml logs -f db
```

### Reiniciar servicios
```bash
docker-compose -f docker-compose.production.yml restart
```

### Detener servicios
```bash
docker-compose -f docker-compose.production.yml down
```

### Hacer backup de BD
```bash
docker exec tours_micaela_db mysqldump -uroot -pTU_ROOT_PASSWORD micaela > backup/backup_$(date +%Y%m%d).sql
```

### Restaurar backup de BD
```bash
docker exec -i tours_micaela_db mysql -uroot -pTU_ROOT_PASSWORD micaela < backup/micaela.sql
```

### Ver uso de recursos
```bash
docker stats
```

### Entrar al contenedor de la aplicación
```bash
docker exec -it tours_micaela_app bash
```

### Entrar al contenedor de MySQL
```bash
docker exec -it tours_micaela_db mysql -uroot -pTU_ROOT_PASSWORD
```

---

## 🆘 Solución Rápida de Problemas

### Error: "Cannot connect to database"
```bash
docker-compose -f docker-compose.production.yml logs db
docker-compose -f docker-compose.production.yml restart db
```

### Error: "Permission denied"
```bash
chmod -R 755 greenter/xml greenter/cdr greenter/pdf Fotos
```

### Reconstruir contenedores
```bash
docker-compose -f docker-compose.production.yml down
docker-compose -f docker-compose.production.yml build --no-cache
docker-compose -f docker-compose.production.yml up -d
```

### Ver todos los contenedores (incluso detenidos)
```bash
docker ps -a
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

### Antes de VPS:
- [ ] Tengo acceso SSH al VPS
- [ ] Docker está instalado
- [ ] Cloné el repositorio
- [ ] Configuré archivos sensibles
- [ ] Subí certificado y backup
- [ ] Levanté servicios Docker

### Verificación Final:
- [ ] `docker ps` muestra 3 contenedores
- [ ] `curl http://localhost:8080` responde
- [ ] Puedo acceder desde el navegador
- [ ] El login funciona
- [ ] Puedo emitir comprobantes

---

## 🎯 Resumen Ultra-Rápido

```bash
# EN TU PC
git init
git add .
git commit -m "Initial commit"
git remote add origin https://github.com/TU_USUARIO/sistema-tours-micaela.git
git push -u origin main

# EN EL VPS
ssh usuario@tu-vps-ip
cd /var/www
git clone https://github.com/TU_USUARIO/sistema-tours-micaela.git
cd sistema-tours-micaela
cp model/model_conexion.example.php model/model_conexion.php
cp view/MPDF/conexion.example.php view/MPDF/conexion.php
cp .env.example .env
# Editar los 3 archivos con tus credenciales
docker-compose -f docker-compose.production.yml up -d

# LISTO! 🎉
```

---

**¡Éxito en tu despliegue!** 🚀
