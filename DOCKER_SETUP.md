# 🐳 Guía de Instalación con Docker - Tours Micaela

Esta guía te ayudará a desplegar el sistema en cualquier PC usando Docker.

---

## 📋 Requisitos Previos

1. **Docker Desktop** instalado:
   - Windows: https://docs.docker.com/desktop/install/windows-install/
   - Mac: https://docs.docker.com/desktop/install/mac-install/
   - Linux: https://docs.docker.com/desktop/install/linux-install/

2. **Docker Compose** (incluido en Docker Desktop)

3. **Backup de la base de datos** (archivo .sql)

---

## 🚀 Instalación Paso a Paso

### 1. Preparar el proyecto

```bash
# Clonar o copiar el proyecto
cd sistema-tours-micaela

# Crear carpeta para backup de BD
mkdir -p backup
```

### 2. Copiar el backup de la base de datos

Coloca tu archivo SQL en la carpeta `backup/`:
```bash
cp /ruta/a/tu/backup.sql backup/micaela.sql
```

### 3. Copiar certificado digital (IMPORTANTE)

Copia tu certificado .pem a la carpeta de certificados:
```bash
cp /ruta/a/tu/certificado.pem greenter/certificados/certificado.pem
```

### 4. Configurar variables de entorno (opcional)

Si quieres personalizar la configuración:
```bash
cp .env.example .env
# Edita .env con tus valores
```

### 5. Construir y levantar los contenedores

```bash
# Construir la imagen
docker-compose build

# Levantar los servicios
docker-compose up -d
```

Esto iniciará:
- ✅ Aplicación PHP/Apache en puerto **8080**
- ✅ MySQL en puerto **3307**
- ✅ phpMyAdmin en puerto **8081**

### 6. Verificar que todo esté corriendo

```bash
docker-compose ps
```

Deberías ver 3 contenedores corriendo:
- `tours_micaela_app`
- `tours_micaela_db`
- `tours_micaela_phpmyadmin`

### 7. Acceder al sistema

- **Aplicación**: http://localhost:8080
- **phpMyAdmin**: http://localhost:8081
  - Usuario: `root`
  - Contraseña: `root_password_2024`

---

## 📦 Importar Base de Datos Manualmente

Si el backup no se importó automáticamente:

### Opción 1: Desde phpMyAdmin
1. Accede a http://localhost:8081
2. Selecciona la base de datos `micaela`
3. Ve a "Importar"
4. Selecciona tu archivo .sql
5. Haz clic en "Continuar"

### Opción 2: Desde línea de comandos
```bash
# Copiar el backup al contenedor
docker cp backup/micaela.sql tours_micaela_db:/tmp/

# Importar
docker exec -i tours_micaela_db mysql -uroot -proot_password_2024 micaela < /tmp/micaela.sql
```

---

## 🔧 Comandos Útiles

### Ver logs de la aplicación
```bash
docker-compose logs -f app
```

### Ver logs de MySQL
```bash
docker-compose logs -f db
```

### Reiniciar servicios
```bash
docker-compose restart
```

### Detener servicios
```bash
docker-compose down
```

### Detener y eliminar volúmenes (⚠️ CUIDADO: Borra la BD)
```bash
docker-compose down -v
```

### Acceder al contenedor de la aplicación
```bash
docker exec -it tours_micaela_app bash
```

### Acceder al contenedor de MySQL
```bash
docker exec -it tours_micaela_db mysql -uroot -proot_password_2024
```

---

## 🔄 Actualizar el Sistema

Si haces cambios en el código:

```bash
# Reconstruir la imagen
docker-compose build app

# Reiniciar el contenedor
docker-compose up -d app
```

---

## 💾 Hacer Backup de la Base de Datos

### Desde Docker
```bash
docker exec tours_micaela_db mysqldump -uroot -proot_password_2024 micaela > backup/micaela_$(date +%Y%m%d_%H%M%S).sql
```

### Desde phpMyAdmin
1. Accede a http://localhost:8081
2. Selecciona la base de datos `micaela`
3. Ve a "Exportar"
4. Selecciona "Rápido" o "Personalizado"
5. Haz clic en "Continuar"

---

## 📤 Migrar a Otra PC

### 1. Exportar el proyecto completo

```bash
# Crear backup de BD
docker exec tours_micaela_db mysqldump -uroot -proot_password_2024 micaela > backup/micaela_backup.sql

# Comprimir el proyecto (sin node_modules ni vendor)
tar -czf tours_micaela_backup.tar.gz \
  --exclude='vendor' \
  --exclude='node_modules' \
  --exclude='.git' \
  .
```

### 2. En la nueva PC

```bash
# Descomprimir
tar -xzf tours_micaela_backup.tar.gz

# Levantar servicios
docker-compose up -d

# Verificar
docker-compose ps
```

---

## 🐛 Solución de Problemas

### Error: "Port already in use"
Si los puertos 8080, 8081 o 3307 están ocupados, edita `docker-compose.yml`:
```yaml
ports:
  - "9090:80"  # Cambiar 8080 por 9090
```

### Error: "Cannot connect to MySQL"
```bash
# Verificar que MySQL esté corriendo
docker-compose ps

# Ver logs de MySQL
docker-compose logs db

# Reiniciar MySQL
docker-compose restart db
```

### Error: "Permission denied" en carpetas
```bash
# Dar permisos desde el contenedor
docker exec -it tours_micaela_app bash
chown -R www-data:www-data /var/www/html/greenter
chown -R www-data:www-data /var/www/html/Fotos
exit
```

### La aplicación no carga
```bash
# Ver logs
docker-compose logs -f app

# Verificar que Apache esté corriendo
docker exec tours_micaela_app service apache2 status

# Reiniciar Apache
docker exec tours_micaela_app service apache2 restart
```

### Certificado digital no funciona
1. Verifica que el archivo .pem esté en `greenter/certificados/`
2. Verifica permisos:
```bash
docker exec tours_micaela_app ls -la /var/www/html/greenter/certificados/
```

---

## 🔐 Seguridad en Producción

Si vas a usar esto en producción:

1. **Cambia las contraseñas** en `docker-compose.yml`
2. **Usa HTTPS** con un proxy reverso (nginx)
3. **Limita acceso a phpMyAdmin** o elimínalo
4. **Configura firewall** para los puertos
5. **Haz backups regulares** de la BD

---

## 📞 Soporte

Si tienes problemas:
1. Revisa los logs: `docker-compose logs`
2. Verifica que todos los servicios estén corriendo: `docker-compose ps`
3. Contacta al desarrollador: jersson14071996@gmail.com

---

## ✅ Checklist de Migración

- [ ] Docker Desktop instalado
- [ ] Proyecto copiado a la nueva PC
- [ ] Backup de BD en carpeta `backup/`
- [ ] Certificado digital en `greenter/certificados/`
- [ ] Ejecutado `docker-compose build`
- [ ] Ejecutado `docker-compose up -d`
- [ ] Verificado que los 3 contenedores estén corriendo
- [ ] Accedido a http://localhost:8080
- [ ] Probado login en el sistema
- [ ] Verificado conexión a BD desde phpMyAdmin
- [ ] Probado emisión de comprobante de prueba
