# 🛡️ SEGURIDAD CON FAIL2BAN

## 📋 Sistema Tours Micaela - Protección contra Ataques de Fuerza Bruta

Esta guía explica cómo configurar y usar Fail2ban para proteger tu VPS contra ataques automatizados.

---

## 🎯 ¿Qué es Fail2ban?

Fail2ban es un software que **protege tu servidor** contra ataques de fuerza bruta:

- 🔒 Bloquea IPs que intentan adivinar contraseñas
- 🚫 Previene ataques DDoS
- 📊 Monitorea logs y detecta patrones de ataque
- ⚡ Baneo automático temporal o permanente

---

## 📦 INSTALACIÓN

### Paso 1: Instalar Fail2ban

```bash
# Actualizar repositorios
apt update

# Instalar fail2ban
apt install fail2ban -y

# Verificar instalación
fail2ban-client --version
```

### Paso 2: Crear Configuración Personalizada

```bash
# Crear archivo de configuración local
cat > /etc/fail2ban/jail.local << 'EOF'
[DEFAULT]
# Tiempo de baneo: 1 hora (3600 segundos)
bantime = 3600

# Ventana de tiempo para contar intentos: 10 minutos (600 segundos)
findtime = 600

# Máximo de intentos fallidos antes de banear
maxretry = 5

# Ignorar IPs locales
ignoreip = 127.0.0.1/8 ::1

# Email para notificaciones (opcional - descomentar y configurar)
# destemail = tu@email.com
# sendername = Fail2Ban-Micaela
# action = %(action_mwl)s

# ============================================
# PROTECCIÓN SSH
# ============================================
[sshd]
enabled = true
port = 22
logpath = /var/log/auth.log
maxretry = 3
bantime = 3600

[sshd-ddos]
enabled = true
port = 22
logpath = /var/log/auth.log
maxretry = 6
bantime = 3600

# ============================================
# PROTECCIÓN APACHE (OPCIONAL)
# ============================================
[apache-auth]
enabled = false
port = http,https
logpath = /var/log/apache2/error.log
maxretry = 5

[apache-badbots]
enabled = false
port = http,https
logpath = /var/log/apache2/access.log
maxretry = 2

[apache-noscript]
enabled = false
port = http,https
logpath = /var/log/apache2/error.log
maxretry = 6

[apache-overflows]
enabled = false
port = http,https
logpath = /var/log/apache2/error.log
maxretry = 2
EOF
```

### Paso 3: Iniciar Fail2ban

```bash
# Habilitar inicio automático
systemctl enable fail2ban

# Iniciar servicio
systemctl start fail2ban

# Verificar estado
systemctl status fail2ban
```

**Salida esperada:**
```
● fail2ban.service - Fail2Ban Service
   Loaded: loaded (/lib/systemd/system/fail2ban.service; enabled)
   Active: active (running)
```

---

## ✅ VERIFICACIÓN

### Verificar que Fail2ban está funcionando

```bash
# Ver estado general
fail2ban-client status

# Ver estado de protección SSH
fail2ban-client status sshd

# Ver todas las jails activas
fail2ban-client status
```

**Salida esperada:**
```
Status for the jail: sshd
|- Filter
|  |- Currently failed: 0
|  |- Total failed:     0
|  `- File list:        /var/log/auth.log
`- Actions
   |- Currently banned: 0
   |- Total banned:     0
   `- Banned IP list:
```

### Ver logs de Fail2ban

```bash
# Ver logs en tiempo real
tail -f /var/log/fail2ban.log

# Ver últimas 50 líneas
tail -50 /var/log/fail2ban.log

# Buscar baneos
grep "Ban" /var/log/fail2ban.log

# Buscar desbaneos
grep "Unban" /var/log/fail2ban.log
```

### Ver intentos de login fallidos

```bash
# Ver intentos fallidos de SSH
grep "Failed password" /var/log/auth.log | tail -20

# Contar intentos por IP
grep "Failed password" /var/log/auth.log | awk '{print $(NF-3)}' | sort | uniq -c | sort -nr

# Ver IPs baneadas actualmente
iptables -L -n | grep "reject-with"
```

---

## 🔧 COMANDOS ÚTILES

### Gestión de IPs

```bash
# Ver IPs baneadas en SSH
fail2ban-client status sshd

# Banear una IP manualmente
fail2ban-client set sshd banip 123.45.67.89

# Desbanear una IP
fail2ban-client set sshd unbanip 123.45.67.89

# Ver todas las IPs baneadas en todas las jails
fail2ban-client banned
```

### Gestión del Servicio

```bash
# Reiniciar fail2ban
systemctl restart fail2ban

# Detener fail2ban
systemctl stop fail2ban

# Ver estado
systemctl status fail2ban

# Recargar configuración sin reiniciar
fail2ban-client reload

# Recargar solo una jail específica
fail2ban-client reload sshd
```

### Información y Estadísticas

```bash
# Ver configuración de una jail
fail2ban-client get sshd bantime
fail2ban-client get sshd maxretry
fail2ban-client get sshd findtime

# Ver IPs actualmente baneadas
fail2ban-client get sshd banip

# Ver número total de baneos
fail2ban-client get sshd banned
```

---

## ⚙️ CONFIGURACIÓN AVANZADA

### Aumentar Tiempo de Baneo

```bash
# Editar configuración
nano /etc/fail2ban/jail.local

# Cambiar bantime:
bantime = 86400  # 24 horas
# o
bantime = -1     # Permanente

# Reiniciar fail2ban
systemctl restart fail2ban
```

### Agregar IP a Lista Blanca

```bash
# Editar configuración
nano /etc/fail2ban/jail.local

# Agregar tu IP en ignoreip:
ignoreip = 127.0.0.1/8 ::1 TU_IP_PERSONAL

# Ejemplo:
ignoreip = 127.0.0.1/8 ::1 179.6.35.182

# Reiniciar fail2ban
systemctl restart fail2ban
```

### Configurar Notificaciones por Email

```bash
# Instalar mailutils
apt install mailutils -y

# Editar configuración
nano /etc/fail2ban/jail.local

# Descomentar y configurar:
destemail = tu@email.com
sendername = Fail2Ban-Micaela
action = %(action_mwl)s

# Reiniciar fail2ban
systemctl restart fail2ban
```

### Proteger Apache/HTTP (Opcional)

```bash
# Editar configuración
nano /etc/fail2ban/jail.local

# Cambiar enabled = false a enabled = true en:
[apache-auth]
enabled = true

[apache-badbots]
enabled = true

# Reiniciar fail2ban
systemctl restart fail2ban
```

---

## 📊 MONITOREO

### Script de Monitoreo de Fail2ban

```bash
cat > /root/monitoreo_fail2ban.sh << 'EOF'
#!/bin/bash

echo "========================================" >> /var/log/fail2ban_monitor.log
echo "=== Monitoreo Fail2ban $(date) ===" >> /var/log/fail2ban_monitor.log
echo "========================================" >> /var/log/fail2ban_monitor.log

# Estado general
echo "" >> /var/log/fail2ban_monitor.log
echo "📊 ESTADO GENERAL:" >> /var/log/fail2ban_monitor.log
fail2ban-client status >> /var/log/fail2ban_monitor.log

# IPs baneadas en SSH
echo "" >> /var/log/fail2ban_monitor.log
echo "🚫 IPs BANEADAS (SSH):" >> /var/log/fail2ban_monitor.log
fail2ban-client status sshd >> /var/log/fail2ban_monitor.log

# Últimos baneos
echo "" >> /var/log/fail2ban_monitor.log
echo "📝 ÚLTIMOS BANEOS:" >> /var/log/fail2ban_monitor.log
grep "Ban" /var/log/fail2ban.log | tail -10 >> /var/log/fail2ban_monitor.log

# Intentos fallidos recientes
echo "" >> /var/log/fail2ban_monitor.log
echo "⚠️ INTENTOS FALLIDOS RECIENTES:" >> /var/log/fail2ban_monitor.log
grep "Failed password" /var/log/auth.log | tail -10 >> /var/log/fail2ban_monitor.log

echo "========================================" >> /var/log/fail2ban_monitor.log
echo "" >> /var/log/fail2ban_monitor.log
EOF

chmod +x /root/monitoreo_fail2ban.sh

# Programar ejecución diaria
crontab -e
# Agregar:
0 8 * * * /root/monitoreo_fail2ban.sh
```

### Ver Reporte de Monitoreo

```bash
# Ver reporte completo
cat /var/log/fail2ban_monitor.log

# Ver último reporte
tail -100 /var/log/fail2ban_monitor.log

# Ver en tiempo real
tail -f /var/log/fail2ban_monitor.log
```

---

## 🚨 SOLUCIÓN DE PROBLEMAS

### Problema: Fail2ban no inicia

```bash
# Ver errores
systemctl status fail2ban -l

# Ver logs de error
tail -50 /var/log/fail2ban.log

# Verificar sintaxis de configuración
fail2ban-client -t

# Reiniciar servicio
systemctl restart fail2ban
```

### Problema: Me bloqueé a mí mismo

```bash
# Opción 1: Desde consola del VPS (panel del proveedor)
fail2ban-client set sshd unbanip TU_IP

# Opción 2: Detener fail2ban temporalmente
systemctl stop fail2ban

# Conectar por SSH y desbanear
fail2ban-client set sshd unbanip TU_IP

# Reiniciar fail2ban
systemctl start fail2ban

# Opción 3: Agregar tu IP a la lista blanca
nano /etc/fail2ban/jail.local
# Agregar tu IP en ignoreip
systemctl restart fail2ban
```

### Problema: No detecta ataques

```bash
# Verificar que la jail está activa
fail2ban-client status sshd

# Verificar que el log existe
ls -la /var/log/auth.log

# Verificar permisos del log
chmod 644 /var/log/auth.log

# Reiniciar fail2ban
systemctl restart fail2ban

# Probar manualmente (desde otra terminal)
ssh usuario_falso@TU_IP
# Intentar 3 veces con contraseña incorrecta

# Verificar que se baneó
fail2ban-client status sshd
```

---

## 📈 ESTADÍSTICAS Y REPORTES

### Ver Estadísticas Generales

```bash
# Total de IPs baneadas históricamente
grep "Ban" /var/log/fail2ban.log | wc -l

# IPs más baneadas
grep "Ban" /var/log/fail2ban.log | awk '{print $NF}' | sort | uniq -c | sort -nr | head -10

# Países de origen de ataques (requiere geoip)
apt install geoip-bin -y
grep "Ban" /var/log/fail2ban.log | awk '{print $NF}' | while read ip; do geoiplookup $ip; done | sort | uniq -c
```

### Generar Reporte Semanal

```bash
cat > /root/reporte_fail2ban.sh << 'EOF'
#!/bin/bash

FECHA=$(date +%Y-%m-%d)
REPORTE="/root/reportes/fail2ban_$FECHA.txt"
mkdir -p /root/reportes

echo "========================================" > $REPORTE
echo "REPORTE SEMANAL FAIL2BAN - $FECHA" >> $REPORTE
echo "========================================" >> $REPORTE
echo "" >> $REPORTE

echo "📊 RESUMEN:" >> $REPORTE
echo "Total de baneos esta semana: $(grep "Ban" /var/log/fail2ban.log | grep -c "$(date +%Y-%m)")" >> $REPORTE
echo "IPs únicas baneadas: $(grep "Ban" /var/log/fail2ban.log | awk '{print $NF}' | sort -u | wc -l)" >> $REPORTE
echo "" >> $REPORTE

echo "🔝 TOP 10 IPs MÁS BANEADAS:" >> $REPORTE
grep "Ban" /var/log/fail2ban.log | awk '{print $NF}' | sort | uniq -c | sort -nr | head -10 >> $REPORTE
echo "" >> $REPORTE

echo "📝 ÚLTIMOS 20 BANEOS:" >> $REPORTE
grep "Ban" /var/log/fail2ban.log | tail -20 >> $REPORTE

echo "Reporte generado: $REPORTE"
EOF

chmod +x /root/reporte_fail2ban.sh

# Ejecutar cada lunes a las 9 AM
crontab -e
# Agregar:
0 9 * * 1 /root/reporte_fail2ban.sh
```

---

## 🎯 MEJORES PRÁCTICAS

### Configuración Recomendada para Producción

```bash
# Editar configuración
nano /etc/fail2ban/jail.local

# Configuración recomendada:
[DEFAULT]
bantime = 86400      # 24 horas
findtime = 600       # 10 minutos
maxretry = 3         # 3 intentos

[sshd]
enabled = true
maxretry = 3         # Más estricto
bantime = 86400      # Baneo de 24 horas

# Reiniciar
systemctl restart fail2ban
```

### Checklist de Seguridad

- [ ] Fail2ban instalado y funcionando
- [ ] Jail SSH activa y configurada
- [ ] Tu IP en lista blanca (ignoreip)
- [ ] Logs monitoreados regularmente
- [ ] Notificaciones por email configuradas (opcional)
- [ ] Backups de configuración realizados
- [ ] Script de monitoreo programado

### Comandos de Mantenimiento Regular

```bash
# Semanal: Ver estadísticas
fail2ban-client status sshd
grep "Ban" /var/log/fail2ban.log | tail -50

# Mensual: Limpiar logs antiguos
find /var/log -name "fail2ban.log.*" -mtime +30 -delete

# Trimestral: Revisar y actualizar configuración
nano /etc/fail2ban/jail.local
systemctl restart fail2ban
```

---

## 📋 RESUMEN DE COMANDOS IMPORTANTES

```bash
# INSTALACIÓN
apt install fail2ban -y
systemctl enable fail2ban
systemctl start fail2ban

# ESTADO
systemctl status fail2ban
fail2ban-client status
fail2ban-client status sshd

# GESTIÓN DE IPS
fail2ban-client set sshd banip IP
fail2ban-client set sshd unbanip IP

# LOGS
tail -f /var/log/fail2ban.log
grep "Ban" /var/log/fail2ban.log
grep "Failed password" /var/log/auth.log

# REINICIAR
systemctl restart fail2ban
fail2ban-client reload
```

---

## 📞 INFORMACIÓN

**Sistema:** Tours Micaela  
**VPS IP:** 72.61.40.91  
**Puerto SSH:** 22  
**Dominio:** https://micaela-tours.com  

---

## 📚 DOCUMENTACIÓN RELACIONADA

- **[DESPLIEGUE_FINAL.md](DESPLIEGUE_FINAL.md)** - Guía completa de despliegue
- **[MANTENIMIENTO_PRODUCCION.md](MANTENIMIENTO_PRODUCCION.md)** - Mantenimiento del sistema
- **[COMANDOS_VPS_DOCKER.txt](COMANDOS_VPS_DOCKER.txt)** - Referencia de comandos

---

**🛡️ ¡TU VPS ESTÁ PROTEGIDO CONTRA ATAQUES!**

Última actualización: Noviembre 2024
