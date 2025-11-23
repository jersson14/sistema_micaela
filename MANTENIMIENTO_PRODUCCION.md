# 🛡️ GUÍA DE MANTENIMIENTO EN PRODUCCIÓN

## 📋 Sistema Tours Micaela - VPS Ubuntu con Docker

Esta guía contiene todas las recomendaciones y procedimientos para mantener el sistema funcionando de forma segura y estable en producción.

---

## 🎯 OBJETIVO

Mantener el sistema operativo 24/7 con:
- ✅ Datos seguros y respaldados
- ✅ Alta disponibilidad
- ✅ Monitoreo constante
- ✅ Recuperación rápida ante fallos

---

## 🔒 SEGURIDAD DE DATOS

### ¿Qué pasa si borro el contenedor?

**TRANQUILO, tus datos están seguros gracias a los volúmenes Docker:**

```yaml
volumes:
  mysql_data_vps:  # ← Volumen persistente
    driver: local
```

**Escenarios:**

| Acción | ¿Se pierden datos? | Comando |
|--------|-------------------|---------|
| Reiniciar contenedor | ❌ NO | `docker restart tours_micaela_app_vps` |
| Detener contenedor | ❌ NO | `docker stop tours_micaela_app_vps` |
| Borrar contenedor | ❌ NO | `docker rm tours_micaela_app_vps` |
| Actualizar imagen | ❌ NO | `docker-compose up -d --build` |
| Borrar volumen | ⚠️ **SÍ** | `docker-compose down -v` |

**Regla de oro:** Nunca uses `docker-compose down -v` (el `-v` borra volúmenes)

---

## 📦 BACKUPS AUTOMÁTICOS (CRÍTICO)

### 1. Script de Backup Diario

```bash
# Crear script de backup automático
cat > /root/backup_automatico.sh << 'EOF'
#!/bin/bash

# Configuración
FECHA=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/root/backups"
DB_PASSWORD="TU_CONTRASEÑA_MYSQL"  # Cambiar por tu contraseña real
DIAS_MANTENER=30

# Crear directorio si no existe
mkdir -p $BACKUP_DIR

# Backup de base de datos
echo "Iniciando backup de base de datos..."
docker exec tours_micaela_db_vps mysqldump -uroot -p"$DB_PASSWORD" micaela > $BACKUP_DIR/db_$FECHA.sql

# Verificar que el backup se creó correctamente
if [ $? -eq 0 ]; then
    echo "✅ Backup de BD exitoso"
    
    # Comprimir backup
    gzip $BACKUP_DIR/db_$FECHA.sql
    echo "✅ Backup comprimido"
    
    # Mantener solo los últimos N backups
    ls -t $BACKUP_DIR/db_*.sql.gz | tail -n +$((DIAS_MANTENER + 1)) | xargs rm -f
    echo "✅ Backups antiguos eliminados"
    
    # Mostrar tamaño del backup
    TAMANO=$(du -h $BACKUP_DIR/db_$FECHA.sql.gz | cut -f1)
    echo "📦 Tamaño del backup: $TAMANO"
    echo "✅ Backup completado: db_$FECHA.sql.gz"
else
    echo "❌ Error al crear backup"
    exit 1
fi

# Backup de archivos importantes (opcional)
tar -czf $BACKUP_DIR/archivos_$FECHA.tar.gz \
    /root/sistema_micaela/greenter/certificados \
    /root/sistema_micaela/.env \
    /root/sistema_micaela/ssl 2>/dev/null

echo "✅ Backup de archivos completado"
echo "================================"
EOF

# Dar permisos de ejecución
chmod +x /root/backup_automatico.sh
```

### 2. Programar Backup Automático

```bash
# Abrir crontab
crontab -e

# Agregar estas líneas al final:
# Backup diario a las 3:00 AM
0 3 * * * /root/backup_automatico.sh >> /var/log/backup.log 2>&1

# Backup adicional cada 12 horas (opcional)
0 */12 * * * /root/backup_automatico.sh >> /var/log/backup.log 2>&1
```

### 3. Probar el Backup Manualmente

```bash
# Ejecutar backup manual
/root/backup_automatico.sh

# Verificar que se creó
ls -lh /root/backups/

# Ver log de backups
tail -f /var/log/backup.log
```

### 4. Restaurar desde Backup

```bash
# Listar backups disponibles
ls -lh /root/backups/

# Descomprimir backup
gunzip /root/backups/db_20241123_030000.sql.gz

# Restaurar base de datos
docker exec -i tours_micaela_db_vps mysql -uroot -p"TU_CONTRASEÑA" micaela < /root/backups/db_20241123_030000.sql

# Verificar que se restauró correctamente
docker exec tours_micaela_db_vps mysql -uroot -p"TU_CONTRASEÑA" -e "USE micaela; SHOW TABLES;"
```

---

## 💾 BACKUP EXTERNO (MUY RECOMENDADO)

### Opción 1: Descargar a tu PC (Semanal)

```bash
# Desde tu máquina local (Windows con Git Bash)
# Descargar todos los backups
scp -r root@72.61.40.91:/root/backups/ C:\backups_micaela\

# O descargar solo el más reciente
scp root@72.61.40.91:/root/backups/$(ssh root@72.61.40.91 'ls -t /root/backups/db_*.sql.gz | head -1') C:\backups_micaela\
```

### Opción 2: Subir a Google Drive (Automático)

```bash
# Instalar rclone
curl https://rclone.org/install.sh | sudo bash

# Configurar Google Drive
rclone config
# Seguir las instrucciones para conectar Google Drive

# Crear script de sincronización
cat > /root/sync_backup_drive.sh << 'EOF'
#!/bin/bash
rclone copy /root/backups gdrive:backups_micaela --update --verbose
echo "Backups sincronizados con Google Drive"
EOF

chmod +x /root/sync_backup_drive.sh

# Programar sincronización diaria
crontab -e
# Agregar:
0 5 * * * /root/sync_backup_drive.sh >> /var/log/sync_backup.log 2>&1
```

### Opción 3: Enviar por Email (Backups pequeños)

```bash
# Instalar mailutils
apt install mailutils -y

# Configurar envío de backup por email
cat > /root/enviar_backup_email.sh << 'EOF'
#!/bin/bash
ULTIMO_BACKUP=$(ls -t /root/backups/db_*.sql.gz | head -1)
echo "Backup adjunto" | mail -s "Backup Micaela $(date +%Y-%m-%d)" -A $ULTIMO_BACKUP tu@email.com
EOF

chmod +x /root/enviar_backup_email.sh

# Programar envío semanal (domingos a las 6 AM)
crontab -e
# Agregar:
0 6 * * 0 /root/enviar_backup_email.sh
```

---

## 📊 MONITOREO DEL SISTEMA

### 1. Script de Monitoreo

```bash
cat > /root/monitoreo.sh << 'EOF'
#!/bin/bash

echo "========================================" >> /var/log/monitoreo.log
echo "=== Monitoreo del Sistema $(date) ===" >> /var/log/monitoreo.log
echo "========================================" >> /var/log/monitoreo.log

# Estado de contenedores
echo "" >> /var/log/monitoreo.log
echo "📦 CONTENEDORES:" >> /var/log/monitoreo.log
docker ps --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}" >> /var/log/monitoreo.log

# Espacio en disco
echo "" >> /var/log/monitoreo.log
echo "💾 ESPACIO EN DISCO:" >> /var/log/monitoreo.log
df -h / >> /var/log/monitoreo.log

# Uso de Docker
echo "" >> /var/log/monitoreo.log
echo "🐳 USO DE DOCKER:" >> /var/log/monitoreo.log
docker system df >> /var/log/monitoreo.log

# Memoria RAM
echo "" >> /var/log/monitoreo.log
echo "🧠 MEMORIA RAM:" >> /var/log/monitoreo.log
free -h >> /var/log/monitoreo.log

# Últimos errores en logs
echo "" >> /var/log/monitoreo.log
echo "⚠️ ÚLTIMOS ERRORES:" >> /var/log/monitoreo.log
docker logs tours_micaela_app_vps --tail 10 2>&1 | grep -i error >> /var/log/monitoreo.log

echo "========================================" >> /var/log/monitoreo.log
echo "" >> /var/log/monitoreo.log
EOF

chmod +x /root/monitoreo.sh

# Programar monitoreo cada 6 horas
crontab -e
# Agregar:
0 */6 * * * /root/monitoreo.sh
```

### 2. Ver Logs de Monitoreo

```bash
# Ver log completo
cat /var/log/monitoreo.log

# Ver últimas entradas
tail -100 /var/log/monitoreo.log

# Ver en tiempo real
tail -f /var/log/monitoreo.log
```

### 3. Verificación Manual Rápida

```bash
# Estado de contenedores
docker ps

# Uso de recursos
docker stats --no-stream

# Espacio en disco
df -h

# Logs de errores recientes
docker logs tours_micaela_app_vps --tail 50 | grep -i error
```

---

## 🚨 ALERTAS AUTOMÁTICAS

### Script de Verificación de Contenedores

```bash
cat > /root/check_containers.sh << 'EOF'
#!/bin/bash

# Verificar contenedor de aplicación
if ! docker ps | grep -q tours_micaela_app_vps; then
    echo "⚠️ ALERTA: Contenedor de aplicación caído - $(date)" >> /var/log/alertas.log
    docker restart tours_micaela_app_vps
    echo "✅ Contenedor reiniciado automáticamente" >> /var/log/alertas.log
fi

# Verificar contenedor de base de datos
if ! docker ps | grep -q tours_micaela_db_vps; then
    echo "⚠️ ALERTA: Contenedor de BD caído - $(date)" >> /var/log/alertas.log
    docker restart tours_micaela_db_vps
    echo "✅ Contenedor de BD reiniciado automáticamente" >> /var/log/alertas.log
fi

# Verificar espacio en disco (alerta si queda menos del 20%)
ESPACIO=$(df / | tail -1 | awk '{print $5}' | sed 's/%//')
if [ $ESPACIO -gt 80 ]; then
    echo "⚠️ ALERTA: Espacio en disco bajo: ${ESPACIO}% usado - $(date)" >> /var/log/alertas.log
fi
EOF

chmod +x /root/check_containers.sh

# Ejecutar cada 5 minutos
crontab -e
# Agregar:
*/5 * * * * /root/check_containers.sh
```

---

## 🔄 ACTUALIZACIONES SEGURAS

### Procedimiento de Actualización Estándar

```bash
# 1. SIEMPRE hacer backup antes de actualizar
/root/backup_automatico.sh

# 2. Conectar al VPS
ssh root@72.61.40.91

# 3. Ir al directorio del proyecto
cd /root/sistema_micaela

# 4. Descargar cambios
git pull origin jersson

# 5. Reiniciar contenedor (NO reconstruir)
docker restart tours_micaela_app_vps

# 6. Verificar logs
docker logs tours_micaela_app_vps --tail 50

# 7. Probar la aplicación
curl -I https://micaela-tours.com
```

### Cuándo Reconstruir la Imagen

**Solo reconstruye si modificaste:**
- Dockerfile
- docker-compose.yml
- Dependencias de Composer
- Configuración de Apache

```bash
# Backup primero
/root/backup_automatico.sh

# Reconstruir
cd /root/sistema_micaela
git pull origin jersson
docker-compose -f docker-compose.ssl.yml down
docker-compose -f docker-compose.ssl.yml up -d --build

# Verificar
docker ps
docker logs tours_micaela_app_vps --tail 50
```

---

## 🧹 LIMPIEZA Y MANTENIMIENTO

### Script de Limpieza Mensual

```bash
cat > /root/limpieza.sh << 'EOF'
#!/bin/bash

echo "🧹 Iniciando limpieza del sistema - $(date)" >> /var/log/limpieza.log

# Limpiar contenedores e imágenes no usadas
docker system prune -f >> /var/log/limpieza.log 2>&1

# Limpiar imágenes antiguas (más de 30 días)
docker image prune -a -f --filter "until=720h" >> /var/log/limpieza.log 2>&1

# Limpiar logs antiguos de Apache (más de 30 días)
find /var/log -name "*.log" -type f -mtime +30 -delete

# Limpiar backups muy antiguos (más de 60 días)
find /root/backups -name "*.sql.gz" -type f -mtime +60 -delete

echo "✅ Limpieza completada - $(date)" >> /var/log/limpieza.log
echo "================================" >> /var/log/limpieza.log
EOF

chmod +x /root/limpieza.sh

# Ejecutar el primer día de cada mes a las 4 AM
crontab -e
# Agregar:
0 4 1 * * /root/limpieza.sh
```

### Limpieza Manual

```bash
# Ver espacio usado por Docker
docker system df

# Limpiar todo lo no usado (cuidado)
docker system prune -a

# Limpiar solo contenedores detenidos
docker container prune

# Limpiar solo imágenes no usadas
docker image prune -a
```

---

## 🔐 SEGURIDAD ADICIONAL

### 1. Actualizar Sistema Operativo

```bash
# Actualización mensual del sistema
apt update && apt upgrade -y

# Reiniciar si es necesario
reboot
```

### 2. Instalar Fail2Ban (Protección contra ataques)

```bash
# Instalar fail2ban
apt install fail2ban -y

# Configurar
cat > /etc/fail2ban/jail.local << 'EOF'
[DEFAULT]
bantime = 3600
findtime = 600
maxretry = 5

[sshd]
enabled = true
port = 22
logpath = /var/log/auth.log
EOF

# Iniciar servicio
systemctl enable fail2ban
systemctl start fail2ban

# Ver IPs bloqueadas
fail2ban-client status sshd
```

### 3. Cambiar Puerto SSH (Opcional)

```bash
# Editar configuración SSH
nano /etc/ssh/sshd_config

# Cambiar línea:
# Port 22
# Por:
Port 2222

# Reiniciar SSH
systemctl restart sshd

# Actualizar firewall
ufw allow 2222/tcp
ufw delete allow 22/tcp

# Conectar con nuevo puerto
ssh -p 2222 root@72.61.40.91
```

### 4. Configurar Firewall Estricto

```bash
# Resetear firewall
ufw --force reset

# Configurar reglas básicas
ufw default deny incoming
ufw default allow outgoing

# Permitir solo lo necesario
ufw allow 22/tcp      # SSH (o 2222 si lo cambiaste)
ufw allow 80/tcp      # HTTP
ufw allow 443/tcp     # HTTPS
ufw allow 8081/tcp    # phpMyAdmin (considera restringir por IP)

# Activar firewall
ufw --force enable

# Ver estado
ufw status verbose
```

---

## 📈 OPTIMIZACIÓN DE RENDIMIENTO

### 1. Monitorear Recursos

```bash
# Ver uso de CPU y RAM en tiempo real
htop

# Ver uso de disco
df -h

# Ver uso de Docker
docker stats

# Ver logs de rendimiento
docker logs tours_micaela_app_vps --tail 100 | grep -i "slow\|timeout\|memory"
```

### 2. Optimizar MySQL

```bash
# Entrar a MySQL
docker exec -it tours_micaela_db_vps mysql -uroot -p

# Verificar tablas
USE micaela;
SHOW TABLE STATUS;

# Optimizar tablas
OPTIMIZE TABLE nombre_tabla;

# Analizar queries lentas
SHOW PROCESSLIST;
```

### 3. Limpiar Sesiones Antiguas

```bash
# Limpiar sesiones PHP antiguas (automático cada día)
docker exec tours_micaela_app_vps find /tmp/sessions -type f -mtime +7 -delete
```

---

## 🆘 RECUPERACIÓN ANTE DESASTRES

### Escenario 1: Contenedor No Inicia

```bash
# Ver logs de error
docker logs tours_micaela_app_vps

# Reiniciar contenedor
docker restart tours_micaela_app_vps

# Si no funciona, recrear contenedor
docker-compose -f docker-compose.ssl.yml up -d --force-recreate
```

### Escenario 2: Base de Datos Corrupta

```bash
# Restaurar desde último backup
gunzip /root/backups/db_FECHA.sql.gz
docker exec -i tours_micaela_db_vps mysql -uroot -p"CONTRASEÑA" micaela < /root/backups/db_FECHA.sql
docker restart tours_micaela_app_vps
```

### Escenario 3: VPS Comprometido

```bash
# 1. Hacer backup inmediato
/root/backup_automatico.sh

# 2. Descargar backup a tu PC
scp root@72.61.40.91:/root/backups/db_*.sql.gz C:\backups\

# 3. Reinstalar VPS desde cero
# 4. Seguir guía DESPLIEGUE_FINAL.md
# 5. Restaurar backup
```

### Escenario 4: Pérdida Total del VPS

```bash
# Si tienes backups externos:
# 1. Contratar nuevo VPS
# 2. Seguir DESPLIEGUE_FINAL.md
# 3. Restaurar último backup desde tu PC o Google Drive
```

---

## 📋 CHECKLIST DE MANTENIMIENTO

### Diario (Automático)
- [ ] Backup de base de datos (3:00 AM)
- [ ] Verificación de contenedores (cada 5 min)
- [ ] Monitoreo de recursos (cada 6 horas)

### Semanal (Manual)
- [ ] Revisar logs de errores
- [ ] Descargar backups a PC local
- [ ] Verificar espacio en disco
- [ ] Probar restauración de backup

### Mensual (Manual)
- [ ] Actualizar sistema operativo
- [ ] Limpiar archivos temporales
- [ ] Revisar logs de seguridad (fail2ban)
- [ ] Verificar certificado SSL (renovación automática)
- [ ] Optimizar base de datos

### Trimestral (Manual)
- [ ] Revisar y actualizar documentación
- [ ] Probar plan de recuperación ante desastres
- [ ] Revisar y actualizar contraseñas
- [ ] Auditoría de seguridad

---

## 🎯 RESUMEN DE COMANDOS IMPORTANTES

```bash
# BACKUPS
/root/backup_automatico.sh                    # Crear backup manual
ls -lh /root/backups/                         # Ver backups disponibles
gunzip /root/backups/db_FECHA.sql.gz          # Descomprimir backup
docker exec -i tours_micaela_db_vps mysql ... # Restaurar backup

# MONITOREO
docker ps                                      # Ver contenedores
docker logs tours_micaela_app_vps --tail 50   # Ver logs
docker stats                                   # Ver uso de recursos
tail -f /var/log/monitoreo.log                # Ver monitoreo

# MANTENIMIENTO
docker restart tours_micaela_app_vps          # Reiniciar app
docker system prune -f                         # Limpiar Docker
apt update && apt upgrade -y                   # Actualizar sistema

# EMERGENCIA
/root/check_containers.sh                      # Verificar contenedores
docker-compose -f docker-compose.ssl.yml up -d --force-recreate  # Recrear
```

---

## 📞 CONTACTOS DE EMERGENCIA

**Desarrollador:** Jersson Miranda  
**Email:** jersson1407miranda@gmail.com  
**Repositorio:** https://github.com/jersson14/sistema_micaela  
**Dominio:** https://micaela-tours.com  
**IP VPS:** 72.61.40.91  

---

## 📚 DOCUMENTACIÓN RELACIONADA

- **[DESPLIEGUE_FINAL.md](DESPLIEGUE_FINAL.md)** - Guía completa de despliegue
- **[CONFIGURAR_DOMINIO_SSL.md](CONFIGURAR_DOMINIO_SSL.md)** - Configuración SSL
- **[COMANDOS_VPS_DOCKER.txt](COMANDOS_VPS_DOCKER.txt)** - Referencia de comandos

---

**🛡️ ¡MANTÉN TU SISTEMA SEGURO Y FUNCIONANDO 24/7!**

Última actualización: Noviembre 2024
