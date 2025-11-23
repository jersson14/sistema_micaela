# ✅ CHECKLIST DE DESPLIEGUE VPS

## 📋 ANTES DE EMPEZAR

- [ ] Tengo acceso SSH al VPS
- [ ] Tengo la IP del VPS: `___________________`
- [ ] Tengo usuario y contraseña del VPS
- [ ] Tengo el archivo de backup de la base de datos (.sql)
- [ ] He cambiado las contraseñas por defecto en `.env`

---

## 🔧 INSTALACIÓN DE DOCKER (Primera vez)

- [ ] Conectado al VPS por SSH
- [ ] Subir script: `scp instalar_docker_vps.sh usuario@IP:/home/usuario/`
- [ ] Dar permisos: `chmod +x instalar_docker_vps.sh`
- [ ] Ejecutar: `./instalar_docker_vps.sh`
- [ ] Cerrar sesión y volver a entrar
- [ ] Verificar: `docker ps` (debe funcionar sin sudo)

---

## 📦 SUBIR PROYECTO AL VPS

### Opción A: Git (Recomendado)
- [ ] Proyecto subido a GitHub/GitLab
- [ ] En VPS: `git clone URL_DEL_REPO tours-micaela`
- [ ] Entrar al directorio: `cd tours-micaela`

### Opción B: SCP/SFTP
- [ ] Comprimir proyecto localmente
- [ ] Subir: `scp -r proyecto/ usuario@IP:/home/usuario/tours-micaela`
- [ ] En VPS: `cd tours-micaela`

---

## ⚙️ CONFIGURACIÓN

- [ ] Copiar archivo de ejemplo: `cp .env.vps .env`
- [ ] Editar contraseñas: `nano .env`
- [ ] Cambiar `MYSQL_ROOT_PASSWORD`
- [ ] Cambiar `MYSQL_PASSWORD`
- [ ] Guardar archivo (CTRL+O, ENTER, CTRL+X)
- [ ] Verificar que las contraseñas sean seguras

---

## 🚀 DESPLIEGUE

- [ ] Dar permisos al script: `chmod +x deploy_vps.sh`
- [ ] Ejecutar despliegue: `./deploy_vps.sh`
- [ ] Esperar a que termine (puede tomar 5-10 minutos)
- [ ] Verificar que no haya errores en la salida
- [ ] Verificar contenedores: `docker ps` (deben aparecer 3)

---

## 🔥 FIREWALL

- [ ] Permitir puerto 80: `sudo ufw allow 80/tcp`
- [ ] Permitir puerto 8081: `sudo ufw allow 8081/tcp`
- [ ] Permitir puerto 443: `sudo ufw allow 443/tcp`
- [ ] Habilitar firewall: `sudo ufw enable`
- [ ] Verificar: `sudo ufw status`

---

## 📊 IMPORTAR BASE DE DATOS

### Usando phpMyAdmin:
- [ ] Abrir: `http://IP_VPS:8081`
- [ ] Login con usuario `root` y contraseña de `.env`
- [ ] Seleccionar base de datos `micaela`
- [ ] Ir a pestaña "Importar"
- [ ] Seleccionar archivo `.sql`
- [ ] Clic en "Continuar"
- [ ] Esperar a que termine la importación
- [ ] Verificar que las tablas aparezcan

### O usando línea de comandos:
- [ ] Subir archivo SQL al VPS
- [ ] Ejecutar: `docker exec -i tours_micaela_db_vps mysql -uroot -p"CONTRASEÑA" micaela < backup.sql`

---

## ✅ VERIFICACIÓN

### Verificar Aplicación Web:
- [ ] Abrir: `http://IP_VPS`
- [ ] La página carga correctamente
- [ ] Puedo hacer login
- [ ] Las imágenes cargan
- [ ] Los formularios funcionan

### Verificar phpMyAdmin:
- [ ] Abrir: `http://IP_VPS:8081`
- [ ] Puedo hacer login
- [ ] Veo la base de datos `micaela`
- [ ] Veo todas las tablas

### Verificar Contenedores:
- [ ] Ejecutar: `docker ps`
- [ ] Aparece `tours_micaela_db_vps` (MySQL)
- [ ] Aparece `tours_micaela_phpmyadmin_vps` (phpMyAdmin)
- [ ] Aparece `tours_micaela_app_vps` (Aplicación)
- [ ] Todos tienen estado "Up"

### Verificar Logs:
- [ ] Ver logs: `docker-compose -f docker-compose.vps.yml logs`
- [ ] No hay errores críticos
- [ ] MySQL está listo: "ready for connections"
- [ ] Apache está corriendo

---

## 🔒 SEGURIDAD (Recomendado)

- [ ] Cambiar puerto SSH por defecto (opcional)
- [ ] Configurar fail2ban (opcional)
- [ ] Instalar certificado SSL con Let's Encrypt (recomendado)
- [ ] Configurar backups automáticos
- [ ] Restringir acceso a phpMyAdmin por IP (opcional)
- [ ] Crear usuario MySQL no-root para la aplicación

---

## 📝 DOCUMENTAR

- [ ] Anotar IP del VPS: `___________________`
- [ ] Anotar contraseña MySQL root: `___________________`
- [ ] Anotar contraseña MySQL usuario: `___________________`
- [ ] Anotar URL de la aplicación: `___________________`
- [ ] Anotar URL de phpMyAdmin: `___________________`
- [ ] Guardar credenciales en lugar seguro

---

## 🔄 COMANDOS ÚTILES ANOTADOS

```bash
# Ver estado
docker ps

# Ver logs
docker-compose -f docker-compose.vps.yml logs -f

# Reiniciar todo
docker-compose -f docker-compose.vps.yml restart

# Detener todo
docker-compose -f docker-compose.vps.yml down

# Backup de BD
docker exec tours_micaela_db_vps mysqldump -uroot -p"CONTRASEÑA" micaela > backup_$(date +%Y%m%d).sql

# Actualizar código
git pull
docker-compose -f docker-compose.vps.yml up -d --build app
```

---

## 🎯 INFORMACIÓN DE ACCESO

### Aplicación Web
- **URL:** `http://___________________`

### phpMyAdmin
- **URL:** `http://___________________:8081`
- **Usuario:** `root`
- **Contraseña:** `___________________`

### MySQL (acceso externo)
- **Host:** `___________________`
- **Puerto:** `3307`
- **Usuario:** `micaela_user`
- **Contraseña:** `___________________`
- **Base de datos:** `micaela`

---

## 🐛 PROBLEMAS COMUNES

### No puedo acceder a la aplicación
- [ ] Verificar firewall: `sudo ufw status`
- [ ] Verificar contenedores: `docker ps`
- [ ] Ver logs: `docker logs tours_micaela_app_vps`

### Error de conexión a BD
- [ ] Verificar que MySQL esté corriendo: `docker ps | grep db`
- [ ] Ver logs de MySQL: `docker logs tours_micaela_db_vps`
- [ ] Verificar contraseñas en `.env`

### phpMyAdmin no carga
- [ ] Reiniciar: `docker restart tours_micaela_phpmyadmin_vps`
- [ ] Ver logs: `docker logs tours_micaela_phpmyadmin_vps`

---

## ✅ DESPLIEGUE COMPLETADO

- [ ] Todo funciona correctamente
- [ ] He probado todas las funcionalidades principales
- [ ] He documentado las credenciales
- [ ] He configurado backups
- [ ] He notificado al equipo

---

**Fecha de despliegue:** `___________________`

**Desplegado por:** `___________________`

**Notas adicionales:**
```
_____________________________________________
_____________________________________________
_____________________________________________
```

---

🎉 **¡FELICIDADES! Tu aplicación está en producción**
