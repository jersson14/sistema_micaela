# ☁️ Despliegue en Azure - Tours Micaela

Guía completa para desplegar el sistema en una máquina virtual de Azure.

---

## 🎯 Opciones de Despliegue

### **Opción 1: VM con Docker (RECOMENDADO)**
- ✅ Más fácil de mantener
- ✅ Mismo entorno que local
- ✅ Fácil actualización

### **Opción 2: Azure Container Instances**
- ✅ Sin gestionar VM
- ✅ Pago por uso
- ⚠️ Más costoso

### **Opción 3: Azure App Service**
- ✅ Totalmente gestionado
- ⚠️ Requiere adaptaciones

---

## 🚀 OPCIÓN 1: VM con Docker (Paso a Paso)

### **1. Crear VM en Azure**

#### Desde Azure Portal:
1. Ir a **Máquinas Virtuales** → **Crear**
2. Configuración recomendada:
   - **Imagen**: Ubuntu Server 22.04 LTS
   - **Tamaño**: Standard B2s (2 vCPUs, 4 GB RAM) mínimo
   - **Autenticación**: SSH con clave pública
   - **Puertos de entrada**: 
     - SSH (22)
     - HTTP (80)
     - HTTPS (443)
     - Custom: 8080 (para la app)

3. **Crear** y esperar a que se despliegue

---

### **2. Conectarse a la VM**

```bash
# Desde tu PC local (PowerShell o CMD)
ssh azureuser@TU_IP_PUBLICA
```

---

### **3. Instalar Docker en la VM**

```bash
# Actualizar sistema
sudo apt update && sudo apt upgrade -y

# Instalar Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Agregar usuario al grupo docker
sudo usermod -aG docker $USER

# Instalar Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose

# Cerrar sesión y volver a conectar
exit
ssh azureuser@TU_IP_PUBLICA

# Verificar instalación
docker --version
docker-compose --version
```

---

### **4. Clonar el proyecto**

```bash
# Instalar Git
sudo apt install git -y

# Clonar repositorio
git clone https://github.com/TU_USUARIO/sistema-tours-micaela.git
cd sistema-tours-micaela
```

---

### **5. Copiar archivos sensibles**

#### Opción A: Desde tu PC local (usando SCP)
```powershell
# Desde tu PC Windows (PowerShell)
scp backup/micaela.sql azureuser@TU_IP_PUBLICA:~/sistema-tours-micaela/backup/
scp greenter/certificados/certificado.pem azureuser@TU_IP_PUBLICA:~/sistema-tours-micaela/greenter/certificados/
```

#### Opción B: Desde la VM (usando wget/curl)
```bash
# Si tienes los archivos en un servidor web temporal
cd ~/sistema-tours-micaela
wget https://tu-servidor-temporal.com/micaela.sql -O backup/micaela.sql
wget https://tu-servidor-temporal.com/certificado.pem -O greenter/certificados/certificado.pem
```

#### Opción C: Copiar y pegar (para archivos pequeños)
```bash
# Crear archivo y pegar contenido
nano backup/micaela.sql
# Pegar contenido, Ctrl+X, Y, Enter

nano greenter/certificados/certificado.pem
# Pegar contenido, Ctrl+X, Y, Enter
```

---

### **6. Configurar puertos para producción**

Editar `docker-compose.yml`:
```bash
nano docker-compose.yml
```

Cambiar puertos:
```yaml
services:
  app:
    ports:
      - "80:80"      # En lugar de 8080:80
  
  phpmyadmin:
    ports:
      - "8081:80"    # Mantener o eliminar en producción
```

---

### **7. Levantar el sistema**

```bash
# Construir y levantar
docker-compose build
docker-compose up -d

# Ver logs
docker-compose logs -f

# Verificar estado
docker-compose ps
```

---

### **8. Configurar Firewall de Azure**

En Azure Portal:
1. Ir a tu VM → **Redes**
2. **Agregar regla de puerto de entrada**:
   - Puerto: 80 (HTTP)
   - Protocolo: TCP
   - Acción: Permitir
   - Nombre: Allow-HTTP

---

### **9. Acceder al sistema**

```
http://TU_IP_PUBLICA
```

---

## 🔒 Seguridad en Producción

### **1. Cambiar contraseñas**

Editar `docker-compose.yml`:
```yaml
environment:
  MYSQL_ROOT_PASSWORD: TU_CONTRASEÑA_SEGURA_AQUI
  MYSQL_PASSWORD: TU_CONTRASEÑA_SEGURA_AQUI
```

### **2. Configurar HTTPS con Let's Encrypt**

```bash
# Instalar Certbot
sudo apt install certbot python3-certbot-nginx -y

# Instalar Nginx como proxy reverso
sudo apt install nginx -y

# Configurar Nginx
sudo nano /etc/nginx/sites-available/toursmicaela
```

Contenido del archivo:
```nginx
server {
    listen 80;
    server_name tu-dominio.com;

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
sudo ln -s /etc/nginx/sites-available/toursmicaela /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx

# Obtener certificado SSL
sudo certbot --nginx -d tu-dominio.com
```

### **3. Deshabilitar phpMyAdmin en producción**

```bash
# Editar docker-compose.yml y comentar la sección de phpmyadmin
nano docker-compose.yml
```

```yaml
  # phpmyadmin:
  #   image: phpmyadmin/phpmyadmin:latest
  #   ...
```

```bash
# Reiniciar
docker-compose down
docker-compose up -d
```

---

## 🔄 Actualizar el Sistema

```bash
# Conectarse a la VM
ssh azureuser@TU_IP_PUBLICA

# Ir al proyecto
cd sistema-tours-micaela

# Obtener últimos cambios
git pull

# Reconstruir y reiniciar
docker-compose build
docker-compose up -d
```

---

## 💾 Backups Automáticos

### **Script de backup diario**

```bash
# Crear script
nano ~/backup_db.sh
```

Contenido:
```bash
#!/bin/bash
BACKUP_DIR="/home/azureuser/backups"
DATE=$(date +%Y%m%d_%H%M%S)

mkdir -p $BACKUP_DIR

docker exec tours_micaela_db mysqldump -uroot -proot_password_2024 micaela > $BACKUP_DIR/micaela_$DATE.sql

# Mantener solo últimos 7 días
find $BACKUP_DIR -name "micaela_*.sql" -mtime +7 -delete

echo "Backup completado: micaela_$DATE.sql"
```

```bash
# Dar permisos
chmod +x ~/backup_db.sh

# Programar con cron (diario a las 2 AM)
crontab -e
```

Agregar:
```
0 2 * * * /home/azureuser/backup_db.sh >> /home/azureuser/backup.log 2>&1
```

---

## 📊 Monitoreo

### **Ver logs en tiempo real**
```bash
docker-compose logs -f app
```

### **Ver uso de recursos**
```bash
docker stats
```

### **Reiniciar servicios**
```bash
docker-compose restart
```

---

## 🐛 Solución de Problemas

### **Error: Puerto 80 ocupado**
```bash
# Ver qué está usando el puerto
sudo lsof -i :80

# Detener Apache si existe
sudo systemctl stop apache2
sudo systemctl disable apache2
```

### **Error: Sin espacio en disco**
```bash
# Limpiar imágenes Docker no usadas
docker system prune -a

# Ver uso de disco
df -h
```

### **Error: Contenedor no inicia**
```bash
# Ver logs detallados
docker-compose logs app

# Reiniciar desde cero
docker-compose down -v
docker-compose up -d
```

---

## 💰 Costos Estimados en Azure

### **VM Standard B2s (Recomendado)**
- **Especificaciones**: 2 vCPUs, 4 GB RAM
- **Costo aproximado**: $30-40 USD/mes
- **Disco**: 30 GB SSD incluido

### **VM Standard B1ms (Mínimo)**
- **Especificaciones**: 1 vCPU, 2 GB RAM
- **Costo aproximado**: $15-20 USD/mes
- ⚠️ Puede ser lento con muchos usuarios

### **Optimización de costos**
- Apagar VM fuera de horario laboral
- Usar Azure Reserved Instances (descuento 30-70%)
- Monitorear uso con Azure Cost Management

---

## 📞 Comandos Útiles

```bash
# Ver estado de contenedores
docker-compose ps

# Reiniciar todo
docker-compose restart

# Ver logs
docker-compose logs -f

# Acceder al contenedor
docker exec -it tours_micaela_app bash

# Backup manual
docker exec tours_micaela_db mysqldump -uroot -proot_password_2024 micaela > backup_manual.sql

# Restaurar backup
docker exec -i tours_micaela_db mysql -uroot -proot_password_2024 micaela < backup_manual.sql
```

---

## ✅ Checklist de Despliegue

- [ ] VM creada en Azure
- [ ] Docker y Docker Compose instalados
- [ ] Repositorio clonado
- [ ] Archivos sensibles copiados (backup.sql, certificado.pem)
- [ ] Contraseñas cambiadas en docker-compose.yml
- [ ] Puertos configurados (80, 443)
- [ ] Firewall de Azure configurado
- [ ] Sistema levantado con docker-compose
- [ ] Acceso verificado desde navegador
- [ ] HTTPS configurado (opcional pero recomendado)
- [ ] Backups automáticos configurados
- [ ] phpMyAdmin deshabilitado en producción

---

## 🎯 Resumen Rápido

```bash
# 1. Crear VM en Azure (Ubuntu 22.04)
# 2. Conectarse
ssh azureuser@TU_IP

# 3. Instalar Docker
curl -fsSL https://get.docker.com | sh
sudo usermod -aG docker $USER

# 4. Clonar proyecto
git clone https://github.com/TU_USUARIO/sistema-tours-micaela.git
cd sistema-tours-micaela

# 5. Copiar archivos sensibles
# (usar scp desde tu PC)

# 6. Levantar
docker-compose up -d

# 7. Acceder
# http://TU_IP_PUBLICA
```

---

## 📧 Soporte

Para más ayuda:
- Email: jersson14071996@gmail.com
- Documentación Azure: https://docs.microsoft.com/azure
- Documentación Docker: https://docs.docker.com
