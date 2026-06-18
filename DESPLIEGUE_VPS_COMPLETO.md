# 🚀 DESPLIEGUE COMPLETO EN VPS CON DOCKER

## 📋 REQUISITOS PREVIOS

1. **VPS con Ubuntu 20.04 o superior**
2. **Docker y Docker Compose instalados**
3. **Acceso SSH al VPS**
4. **Puertos abiertos: 80, 443, 8081, 3307**

---

## 🔧 PASO 1: PREPARAR EL VPS

### 1.1 Conectar al VPS
```bash
ssh usuario@TU_IP_VPS
```

### 1.2 Instalar Docker (si no está instalado)
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

# Verificar instalación
docker --version
docker-compose --version
```

### 1.3 Configurar Firewall
```bash
# Permitir puertos necesarios
sudo ufw allow 22/tcp      # SSH
sudo ufw allow 80/tcp      # HTTP
sudo ufw allow 443/tcp     # HTTPS
sudo ufw allow 8081/tcp    # phpMyAdmin
sudo ufw allow 3307/tcp    # MySQL (opcional, solo si necesitas acceso externo)
sudo ufw enable
sudo ufw status
```

---

## 📦 PASO 2: SUBIR EL PROYECTO AL VPS

### Opción A: Usando Git (RECOMENDADO)
```bash
# En el VPS
cd /home/usuario
git clone https://github.com/TU_USUARIO/TU_REPO.git tours-micaela
cd tours-micaela
```

### Opción B: Usando SCP/SFTP
```bash
# Desde tu máquina local
scp -r /ruta/local/proyecto usuario@TU_IP_VPS:/home/usuario/tours-micaela
```

---

## ⚙️ PASO 3: CONFIGURAR VARIABLES DE ENTORNO

```bash
# En el VPS, dentro del directorio del proyecto
cd /home/usuario/tours-micaela

# Copiar archivo de ejemplo
cp .env.vps .env

# Editar con contraseñas seguras
nano .env
```

**Contenido del archivo .env:**
```env
MYSQL_ROOT_PASSWORD=TuContraseñaSuperSegura123!
MYSQL_DATABASE=micaela
MYSQL_USER=micaela_user
MYSQL_PASSWORD=OtraContraseñaSegura456!
```

**IMPORTANTE:** Cambia las contraseñas por unas seguras y guárdalas en un lugar seguro.

---

## 🔄 PASO 4: REEMPLAZAR ARCHIVO DE CONEXIÓN

```bash
# Hacer backup del archivo original
cp model/model_conexion.php model/model_conexion_local.php.bak

# Reemplazar con la versión VPS
cp model/model_conexion_vps.php model/model_conexion.php
```

---

## 🏗️ PASO 5: CREAR DIRECTORIOS NECESARIOS

```bash
# Crear directorios para archivos persistentes
mkdir -p greenter/xml greenter/cdr greenter/pdf greenter/certificados
mkdir -p Fotos controller/usuario/fotos controller/choferes/fotos controller/empresa/FOTOS
mkdir -p backup

# Dar permisos
chmod -R 755 greenter Fotos controller backup
```

---

## 🚀 PASO 6: LEVANTAR LOS CONTENEDORES

```bash
# Construir y levantar todos los servicios
docker-compose -f docker-compose.vps.yml up -d --build

# Ver logs en tiempo real
docker-compose -f docker-compose.vps.yml logs -f

# Verificar que todos los contenedores estén corriendo
docker ps
```

**Deberías ver 3 contenedores:**
- `tours_micaela_db_vps` (MySQL)
- `tours_micaela_phpmyadmin_vps` (phpMyAdmin)
- `tours_micaela_app_vps` (Aplicación PHP)

---

## 📊 PASO 7: IMPORTAR LA BASE DE DATOS

### Opción A: Usando phpMyAdmin (MÁS FÁCIL)

1. Abre en tu navegador: `http://TU_IP_VPS:8081`
2. Login:
   - **Usuario:** `root`
   - **Contraseña:** La que pusiste en `MYSQL_ROOT_PASSWORD`
3. Selecciona la base de datos `micaela`
4. Ve a la pestaña "Importar"
5. Selecciona tu archivo `.sql` y haz clic en "Continuar"

### Opción B: Usando línea de comandos

```bash
# Copiar el archivo SQL al contenedor
docker cp /ruta/a/tu/backup.sql tours_micaela_db_vps:/backup.sql

# Importar la base de datos
docker exec -i tours_micaela_db_vps mysql -uroot -p"TuContraseñaRoot" micaela < /backup.sql

# O desde el VPS si tienes el archivo en ./backup/
docker exec -i tours_micaela_db_vps mysql -uroot -p"TuContraseñaRoot" micaela < /backup/backup.sql
```

---

## ✅ PASO 8: VERIFICAR QUE TODO FUNCIONA

### 8.1 Verificar la aplicación
```bash
# Abrir en navegador
http://TU_IP_VPS
```

### 8.2 Verificar phpMyAdmin
```bash
# Abrir en navegador
http://TU_IP_VPS:8081
```

### 8.3 Verificar logs
```bash
# Ver logs de la aplicación
docker logs tours_micaela_app_vps

# Ver logs de MySQL
docker logs tours_micaela_db_vps

# Ver logs de phpMyAdmin
docker logs tours_micaela_phpmyadmin_vps
```

### 8.4 Probar conexión a BD desde la app
```bash
# Crear archivo de prueba
docker exec -it tours_micaela_app_vps php -r "
\$pdo = new PDO('mysql:host=db;dbname=micaela', 'micaela_user', 'TuContraseña');
echo 'Conexión exitosa!';
"
```

---

## 🔒 PASO 9: SEGURIDAD ADICIONAL (RECOMENDADO)

### 9.1 Cambiar puerto de phpMyAdmin (opcional)
Si quieres más seguridad, edita `docker-compose.vps.yml` y cambia el puerto 8081 por otro aleatorio:
```yaml
ports:
  - "9876:80"  # Usa un puerto aleatorio
```

### 9.2 Restringir acceso a phpMyAdmin por IP
```bash
# Agregar regla de firewall
sudo ufw allow from TU_IP_PERSONAL to any port 8081
```

### 9.3 Crear usuario MySQL adicional (no root)
```bash
docker exec -it tours_micaela_db_vps mysql -uroot -p

# Dentro de MySQL
CREATE USER 'admin_user'@'%' IDENTIFIED BY 'OtraContraseñaSegura';
GRANT ALL PRIVILEGES ON micaela.* TO 'admin_user'@'%';
FLUSH PRIVILEGES;
EXIT;
```

---

## 🔄 COMANDOS ÚTILES

### Detener servicios
```bash
docker-compose -f docker-compose.vps.yml down
```

### Reiniciar servicios
```bash
docker-compose -f docker-compose.vps.yml restart
```

### Ver logs
```bash
docker-compose -f docker-compose.vps.yml logs -f app
docker-compose -f docker-compose.vps.yml logs -f db
docker-compose -f docker-compose.vps.yml logs -f phpmyadmin
```

### Actualizar código
```bash
# Si usas Git
git pull origin main

# Reconstruir solo la app (sin perder datos)
docker-compose -f docker-compose.vps.yml up -d --build app
```

### Backup de base de datos
```bash
# Crear backup
docker exec tours_micaela_db_vps mysqldump -uroot -p"TuContraseña" micaela > backup_$(date +%Y%m%d_%H%M%S).sql

# O usar el script automático
docker exec tours_micaela_db_vps mysqldump -uroot -p"TuContraseña" micaela > ./backup/backup_$(date +%Y%m%d).sql
```

### Limpiar todo (CUIDADO: borra volúmenes)
```bash
docker-compose -f docker-compose.vps.yml down -v
```

---

## 🐛 SOLUCIÓN DE PROBLEMAS

### Problema: No puedo acceder a la aplicación
```bash
# Verificar que el contenedor esté corriendo
docker ps

# Ver logs
docker logs tours_micaela_app_vps

# Verificar firewall
sudo ufw status
```

### Problema: Error de conexión a BD
```bash
# Verificar que MySQL esté corriendo
docker exec tours_micaela_db_vps mysqladmin ping -h localhost -uroot -p"TuContraseña"

# Verificar variables de entorno
docker exec tours_micaela_app_vps env | grep DB_
```

### Problema: phpMyAdmin no carga
```bash
# Reiniciar phpMyAdmin
docker restart tours_micaela_phpmyadmin_vps

# Ver logs
docker logs tours_micaela_phpmyadmin_vps
```

### Problema: Permisos de archivos
```bash
# Dar permisos correctos
docker exec tours_micaela_app_vps chown -R www-data:www-data /var/www/html/greenter
docker exec tours_micaela_app_vps chown -R www-data:www-data /var/www/html/Fotos
docker exec tours_micaela_app_vps chmod -R 755 /var/www/html/greenter
```

---

## 📝 NOTAS IMPORTANTES

1. **Contraseñas:** Usa contraseñas seguras y guárdalas en un gestor de contraseñas
2. **Backups:** Configura backups automáticos de la base de datos
3. **SSL/HTTPS:** Considera instalar un certificado SSL con Let's Encrypt
4. **Monitoreo:** Configura alertas para saber si los servicios caen
5. **Actualizaciones:** Mantén Docker y las imágenes actualizadas

---

## 🎯 RESUMEN DE ACCESOS

- **Aplicación:** `http://TU_IP_VPS`
- **phpMyAdmin:** `http://TU_IP_VPS:8081`
- **MySQL (externo):** `TU_IP_VPS:3307` (si lo necesitas)

**Credenciales MySQL:**
- Usuario: `micaela_user`
- Contraseña: La que configuraste en `.env`
- Base de datos: `micaela`

**Credenciales phpMyAdmin:**
- Usuario: `root`
- Contraseña: La que configuraste en `MYSQL_ROOT_PASSWORD`

---

## 🆘 SOPORTE

Si tienes problemas:
1. Revisa los logs: `docker-compose -f docker-compose.vps.yml logs`
2. Verifica que todos los contenedores estén corriendo: `docker ps`
3. Revisa el firewall: `sudo ufw status`
4. Verifica las variables de entorno: `cat .env`

---

**¡Listo! Tu aplicación debería estar funcionando en el VPS con MySQL y phpMyAdmin** 🎉
