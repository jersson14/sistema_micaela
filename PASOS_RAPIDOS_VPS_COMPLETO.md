# ⚡ PASOS RÁPIDOS - DESPLIEGUE VPS

## 🎯 RESUMEN EJECUTIVO

Este es el proceso más rápido para desplegar tu aplicación en el VPS con MySQL y phpMyAdmin.

---

## 📋 REQUISITOS

- VPS con Ubuntu 20.04+
- Docker y Docker Compose instalados
- Acceso SSH al VPS
- Tu archivo de backup de base de datos (.sql)

---

## 🚀 DESPLIEGUE EN 5 PASOS

### 1️⃣ CONECTAR AL VPS Y CLONAR PROYECTO

```bash
# Conectar por SSH
ssh usuario@TU_IP_VPS

# Clonar proyecto (o subir archivos)
git clone https://github.com/TU_USUARIO/TU_REPO.git tours-micaela
cd tours-micaela

# O si subes archivos manualmente:
# scp -r /ruta/local/proyecto usuario@TU_IP_VPS:/home/usuario/tours-micaela
```

---

### 2️⃣ CONFIGURAR CONTRASEÑAS

```bash
# Copiar archivo de ejemplo
cp .env.vps .env

# Editar y cambiar contraseñas
nano .env
```

**Cambia estas líneas:**
```env
MYSQL_ROOT_PASSWORD=TuContraseñaSuperSegura123!
MYSQL_PASSWORD=OtraContraseñaSegura456!
```

Guarda con `CTRL+O`, `ENTER`, `CTRL+X`

---

### 3️⃣ EJECUTAR SCRIPT DE DESPLIEGUE

```bash
# Dar permisos de ejecución
chmod +x deploy_vps.sh

# Ejecutar despliegue automático
./deploy_vps.sh
```

**Esto hará automáticamente:**
- ✅ Crear directorios necesarios
- ✅ Configurar archivo de conexión
- ✅ Construir imágenes Docker
- ✅ Levantar MySQL, phpMyAdmin y la aplicación
- ✅ Mostrar URLs de acceso

---

### 4️⃣ CONFIGURAR FIREWALL

```bash
# Permitir puertos necesarios
sudo ufw allow 80/tcp      # Aplicación web
sudo ufw allow 8081/tcp    # phpMyAdmin
sudo ufw allow 443/tcp     # HTTPS (futuro)
sudo ufw enable
```

---

### 5️⃣ IMPORTAR BASE DE DATOS

**Opción A: Usando phpMyAdmin (MÁS FÁCIL)**

1. Abre: `http://TU_IP_VPS:8081`
2. Login con:
   - Usuario: `root`
   - Contraseña: La que pusiste en `.env`
3. Selecciona base de datos `micaela`
4. Clic en "Importar"
5. Selecciona tu archivo `.sql`
6. Clic en "Continuar"

**Opción B: Línea de comandos**

```bash
# Si tienes el archivo en tu VPS
docker exec -i tours_micaela_db_vps mysql -uroot -p"TuContraseña" micaela < /ruta/a/backup.sql

# O si está en la carpeta backup del proyecto
docker exec -i tours_micaela_db_vps mysql -uroot -p"TuContraseña" micaela < ./backup/backup.sql
```

---

## ✅ VERIFICAR QUE TODO FUNCIONA

### Abrir en navegador:

- **Aplicación:** `http://TU_IP_VPS`
- **phpMyAdmin:** `http://TU_IP_VPS:8081`

### Ver logs:

```bash
# Ver todos los logs
docker-compose -f docker-compose.vps.yml logs -f

# Ver solo logs de la app
docker-compose -f docker-compose.vps.yml logs -f app

# Ver solo logs de MySQL
docker-compose -f docker-compose.vps.yml logs -f db
```

---

## 🔄 COMANDOS ÚTILES

```bash
# Ver estado de contenedores
docker ps

# Reiniciar todo
docker-compose -f docker-compose.vps.yml restart

# Detener todo
docker-compose -f docker-compose.vps.yml down

# Actualizar código y reconstruir
git pull
docker-compose -f docker-compose.vps.yml up -d --build app

# Backup de base de datos
docker exec tours_micaela_db_vps mysqldump -uroot -p"TuContraseña" micaela > backup_$(date +%Y%m%d).sql
```

---

## 🐛 SOLUCIÓN RÁPIDA DE PROBLEMAS

### No puedo acceder a la aplicación

```bash
# Verificar que los contenedores estén corriendo
docker ps

# Ver logs de errores
docker logs tours_micaela_app_vps

# Verificar firewall
sudo ufw status
```

### Error de conexión a base de datos

```bash
# Verificar que MySQL esté corriendo
docker ps | grep db

# Probar conexión
docker exec tours_micaela_db_vps mysqladmin ping -h localhost -uroot -p"TuContraseña"

# Ver logs de MySQL
docker logs tours_micaela_db_vps
```

### phpMyAdmin no carga

```bash
# Reiniciar phpMyAdmin
docker restart tours_micaela_phpmyadmin_vps

# Ver logs
docker logs tours_micaela_phpmyadmin_vps
```

---

## 📊 INFORMACIÓN DE ACCESO

### Aplicación Web
- **URL:** `http://TU_IP_VPS`

### phpMyAdmin
- **URL:** `http://TU_IP_VPS:8081`
- **Usuario:** `root`
- **Contraseña:** La de `MYSQL_ROOT_PASSWORD` en `.env`

### MySQL (acceso externo)
- **Host:** `TU_IP_VPS`
- **Puerto:** `3307`
- **Usuario:** `micaela_user`
- **Contraseña:** La de `MYSQL_PASSWORD` en `.env`
- **Base de datos:** `micaela`

---

## 🎉 ¡LISTO!

Tu aplicación debería estar funcionando con:
- ✅ MySQL 8.0
- ✅ phpMyAdmin
- ✅ Aplicación PHP/Apache
- ✅ Datos persistentes
- ✅ Sesiones funcionando

---

## 📞 SOPORTE

Si algo no funciona:
1. Revisa los logs: `docker-compose -f docker-compose.vps.yml logs`
2. Verifica el firewall: `sudo ufw status`
3. Verifica las contraseñas en `.env`
4. Consulta el archivo `DESPLIEGUE_VPS_COMPLETO.md` para más detalles
