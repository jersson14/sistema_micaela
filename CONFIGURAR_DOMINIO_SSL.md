# 🌐 CONFIGURAR DOMINIO CON SSL - micaela-tours.com

## 📋 REQUISITOS PREVIOS

1. ✅ Docker funcionando en el VPS
2. ✅ Aplicación desplegada y funcionando
3. ⚠️ **IMPORTANTE:** Dominio apuntando a la IP del VPS

---

## 🔧 PASO 1: CONFIGURAR DNS DEL DOMINIO

En el panel de tu proveedor de dominio (GoDaddy, Namecheap, etc.), configura estos registros DNS:

### Registros DNS necesarios:

| Tipo | Nombre | Valor | TTL |
|------|--------|-------|-----|
| **A** | `@` | `TU_IP_VPS` | 3600 |
| **A** | `www` | `TU_IP_VPS` | 3600 |

**Ejemplo:**
```
Tipo: A
Nombre: @
Valor: 123.45.67.89  (tu IP del VPS)
TTL: 3600

Tipo: A
Nombre: www
Valor: 123.45.67.89  (tu IP del VPS)
TTL: 3600
```

### ⏰ Esperar propagación DNS

Después de configurar el DNS, **espera 10-30 minutos** para que se propague.

**Verificar propagación:**
```bash
# Desde tu máquina local o el VPS
nslookup micaela-tours.com
nslookup www.micaela-tours.com

# O usar herramientas online:
# https://dnschecker.org
```

---

## 🚀 PASO 2: EJECUTAR SCRIPT DE CONFIGURACIÓN SSL

Una vez que el DNS esté propagado:

```bash
# En el VPS, dentro del directorio del proyecto
cd /root/tours-micaela

# Dar permisos al script
chmod +x configurar_dominio_ssl.sh

# Ejecutar configuración
./configurar_dominio_ssl.sh
```

### ¿Qué hace este script?

1. ✅ Instala Certbot (herramienta de Let's Encrypt)
2. ✅ Obtiene certificado SSL gratuito
3. ✅ Configura Apache para usar HTTPS
4. ✅ Redirige automáticamente HTTP → HTTPS
5. ✅ Configura renovación automática del certificado
6. ✅ Reconstruye los contenedores con SSL

---

## ✅ PASO 3: VERIFICAR QUE FUNCIONA

### Abrir en navegador:

- `https://micaela-tours.com` ✅
- `https://www.micaela-tours.com` ✅
- `http://micaela-tours.com` → Redirige a HTTPS ✅

### Verificar certificado:

1. Abre `https://micaela-tours.com`
2. Haz clic en el candado 🔒 en la barra de direcciones
3. Deberías ver "Conexión segura"

### Verificar contenedores:

```bash
docker ps
```

Deberías ver los 3 contenedores corriendo.

---

## 🔄 RENOVACIÓN AUTOMÁTICA

El certificado SSL se renueva automáticamente cada 2 meses.

**Script de renovación:** `/root/renovar_ssl.sh`

**Renovar manualmente (si es necesario):**
```bash
/root/renovar_ssl.sh
```

**Ver log de renovaciones:**
```bash
cat /var/log/ssl-renewal.log
```

---

## 🔒 SEGURIDAD ADICIONAL

El script configura automáticamente:

- ✅ Redirección HTTP → HTTPS
- ✅ HSTS (HTTP Strict Transport Security)
- ✅ Protección contra clickjacking (X-Frame-Options)
- ✅ Protección XSS
- ✅ Solo protocolos TLS modernos (TLS 1.2+)
- ✅ Cookies seguras

---

## 🐛 SOLUCIÓN DE PROBLEMAS

### Error: "Failed to obtain certificate"

**Causa:** El dominio no apunta al VPS o el DNS no se ha propagado.

**Solución:**
```bash
# Verificar DNS
nslookup micaela-tours.com

# Verificar que el puerto 80 esté libre
netstat -tulpn | grep :80

# Verificar firewall
ufw status
```

### Error: "Port 80 already in use"

**Solución:**
```bash
# Detener contenedores
docker-compose -f docker-compose.vps.yml down

# Volver a ejecutar el script
./configurar_dominio_ssl.sh
```

### El sitio no carga con HTTPS

**Verificar logs:**
```bash
docker logs tours_micaela_app_vps
```

**Verificar certificados:**
```bash
ls -la ./ssl/
```

**Reiniciar contenedores:**
```bash
docker-compose -f docker-compose.ssl.yml restart
```

---

## 📝 COMANDOS ÚTILES

```bash
# Ver estado de contenedores
docker ps

# Ver logs
docker-compose -f docker-compose.ssl.yml logs -f

# Reiniciar servicios
docker-compose -f docker-compose.ssl.yml restart

# Verificar certificado SSL
openssl s_client -connect micaela-tours.com:443 -servername micaela-tours.com

# Ver fecha de expiración del certificado
certbot certificates

# Renovar certificado manualmente
/root/renovar_ssl.sh
```

---

## 🎯 RESUMEN DE ACCESOS

Después de la configuración:

| Servicio | URL | Protocolo |
|----------|-----|-----------|
| **Aplicación** | `https://micaela-tours.com` | HTTPS ✅ |
| **Aplicación (www)** | `https://www.micaela-tours.com` | HTTPS ✅ |
| **phpMyAdmin** | `http://TU_IP:8081` | HTTP |

---

## ⚠️ NOTAS IMPORTANTES

1. **Certificado válido por 90 días** - Se renueva automáticamente cada 60 días
2. **HTTP redirige a HTTPS** - Todo el tráfico es seguro
3. **phpMyAdmin sin SSL** - Solo accesible por IP (más seguro)
4. **Backup de configuración** - Los archivos originales se mantienen

---

## 📊 ARQUITECTURA CON SSL

```
Internet
   ↓
https://micaela-tours.com (Puerto 443)
   ↓
[Certificado SSL Let's Encrypt]
   ↓
Apache con SSL (Contenedor Docker)
   ↓
PHP 8.2 Application
   ↓
MySQL 8.0 (Puerto interno 3306)
```

---

## ✅ CHECKLIST DE CONFIGURACIÓN

- [ ] DNS configurado (registros A para @ y www)
- [ ] DNS propagado (verificado con nslookup)
- [ ] Firewall permite puertos 80 y 443
- [ ] Script ejecutado sin errores
- [ ] Sitio accesible en https://micaela-tours.com
- [ ] Certificado SSL válido (candado verde)
- [ ] HTTP redirige a HTTPS
- [ ] Renovación automática configurada

---

## 🆘 SOPORTE

Si tienes problemas:

1. Verifica que el DNS esté configurado correctamente
2. Espera 30 minutos para la propagación DNS
3. Revisa los logs: `docker logs tours_micaela_app_vps`
4. Verifica el firewall: `ufw status`
5. Consulta los logs de Certbot: `cat /var/log/letsencrypt/letsencrypt.log`

---

**¡Tu sitio estará seguro con HTTPS!** 🔒✅
