# 🐳 Despliegue con Docker en VPS

## ✅ Ventajas de usar Docker

- ✅ MySQL con collation `utf8mb4_unicode_ci` garantizada
- ✅ Mismo entorno en local, Azure y VPS
- ✅ No más problemas de configuración del servidor
- ✅ Fácil de actualizar y mantener
- ✅ Backups y migraciones simples

---

## 🚀 Pasos para Desplegar

### 1. Preparar archivos en local

```bash
# Reemplazar model_conexion.php con la versión Docker
cp model/model_conexion_docker.php model/model_conexion.php

# Commit y push
git add .
git commit -m "Feat: Configuración Docker completa con MySQL"
git push origin main
```

### 2. En el VPS - Instalar Docker

```bash
# Conectar al VPS
ssh root@72.61.40.91

# Instalar Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Instalar Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose

# Verificar instalación
docker --version
docker-compose --version
```

### 3. Detener servicios actuales

```bash
# Detener Apache y MySQL del VPS
sudo systemctl stop apache2
sudo systemctl stop mysql

# Opcional: Deshabilitar para que no inicien automáticamente
sudo systemctl disable apache2
sudo systemctl disable mysql
```

### 4. Hacer backup de la BD actual

```bash
cd /var/www/html/sistema_micaela

# Backup
mysqldump -u jersson -p micaela > backup/micaela_antes_docker_$(date +%Y%m%d).sql
```

### 5. Actualizar código

```bash
cd /var/www/html/sistema_micaela
git pull origin main
```

### 6. Construir e iniciar contenedores

```bash
# Construir imágenes
docker-compose -f docker-compose.full.yml build

# Iniciar servicios
docker-compose -f docker-compose.full.yml up -d

# Ver logs
docker-compose -f docker-compose.full.yml logs -f
```

### 7. Importar base de datos

```bash
# Esperar a que MySQL esté listo (30 segundos)
sleep 30

# Importar BD
docker exec -i tours_micaela_db mysql -u jersson -pMiranda1407 micaela < backup/micaela_antes_docker_*.sql

# Verificar
docker exec -it tours_micaela_db mysql -u jersson -pMiranda1407 -e "
SELECT DEFAULT_CHARACTER_SET_NAME, DEFAULT_COLLATION_NAME
FROM information_schema.SCHEMATA
WHERE SCHEMA_NAME = 'micaela';"
```

Debe mostrar: `utf8mb4 | utf8mb4_unicode_ci`

### 8. Probar el sistema

```
http://72.61.40.91/sistema_micaela/
```

---

## 📋 Comandos Útiles

### Ver estado de contenedores

```bash
docker-compose -f docker-compose.full.yml ps
```

### Ver logs

```bash
# Todos los servicios
docker-compose -f docker-compose.full.yml logs -f

# Solo app
docker-compose -f docker-compose.full.yml logs -f app

# Solo BD
docker-compose -f docker-compose.full.yml logs -f db
```

### Reiniciar servicios

```bash
# Reiniciar todo
docker-compose -f docker-compose.full.yml restart

# Solo app
docker-compose -f docker-compose.full.yml restart app

# Solo BD
docker-compose -f docker-compose.full.yml restart db
```

### Detener servicios

```bash
docker-compose -f docker-compose.full.yml down
```

### Backup de BD

```bash
# Backup
docker exec tours_micaela_db mysqldump -u jersson -pMiranda1407 micaela > backup/micaela_$(date +%Y%m%d).sql

# Restaurar
docker exec -i tours_micaela_db mysql -u jersson -pMiranda1407 micaela < backup/micaela_20241123.sql
```

### Acceder a MySQL

```bash
docker exec -it tours_micaela_db mysql -u jersson -pMiranda1407 micaela
```

### Acceder al contenedor de la app

```bash
docker exec -it tours_micaela_app bash
```

---

## 🔧 Solución de Problemas

### Puerto 80 ocupado

```bash
# Ver qué está usando el puerto 80
sudo lsof -i :80

# Detener Apache si está corriendo
sudo systemctl stop apache2
```

### Puerto 3306 ocupado

```bash
# Ver qué está usando el puerto 3306
sudo lsof -i :3306

# Detener MySQL si está corriendo
sudo systemctl stop mysql
```

### Contenedor no inicia

```bash
# Ver logs detallados
docker logs tours_micaela_app
docker logs tours_micaela_db

# Reconstruir
docker-compose -f docker-compose.full.yml down
docker-compose -f docker-compose.full.yml build --no-cache
docker-compose -f docker-compose.full.yml up -d
```

### Error de permisos

```bash
# Dar permisos a directorios
sudo chown -R www-data:www-data greenter/ Fotos/ controller/ backup/
```

---

## 🎯 Ventajas de esta Solución

1. **Collation garantizada:** MySQL 8.0 con `utf8mb4_unicode_ci` desde el inicio
2. **Portabilidad:** Funciona igual en cualquier servidor
3. **Aislamiento:** No afecta otros servicios del VPS
4. **Fácil actualización:** Solo `git pull` y `docker-compose restart`
5. **Backups simples:** Un comando para backup completo
6. **Rollback rápido:** Volver a versión anterior en segundos

---

## 📊 Monitoreo

### Ver uso de recursos

```bash
docker stats
```

### Ver espacio en disco

```bash
docker system df
```

### Limpiar recursos no usados

```bash
docker system prune -a
```

---

**¡Listo!** Con Docker, el problema de collations desaparece completamente. 🎉
