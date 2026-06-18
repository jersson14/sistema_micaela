# ⚡ CONFIGURAR SSL - PASOS RÁPIDOS

## 🎯 DOMINIO: micaela-tours.com

---

## 1️⃣ CONFIGURAR DNS (EN TU PROVEEDOR DE DOMINIO)

Ve al panel de tu proveedor de dominio y agrega estos registros:

```
Tipo: A
Nombre: @
Valor: [IP_DE_TU_VPS]
TTL: 3600

Tipo: A  
Nombre: www
Valor: [IP_DE_TU_VPS]
TTL: 3600
```

**⏰ ESPERA 15-30 MINUTOS** para que el DNS se propague.

---

## 2️⃣ VERIFICAR DNS

```bash
# Desde cualquier lugar (tu PC o el VPS)
nslookup micaela-tours.com
nslookup www.micaela-tours.com

# Debe mostrar la IP de tu VPS
```

O usa: https://dnschecker.org

---

## 3️⃣ EJECUTAR SCRIPT EN EL VPS

```bash
# Conectar al VPS
ssh root@TU_IP_VPS

# Ir al directorio del proyecto
cd /root/tours-micaela

# Dar permisos y ejecutar
chmod +x configurar_dominio_ssl.sh
./configurar_dominio_ssl.sh
```

**El script te preguntará si el DNS está configurado. Responde "s" (sí).**

---

## 4️⃣ ESPERAR A QUE TERMINE

El script hará automáticamente:
- ✅ Instalar Certbot
- ✅ Obtener certificado SSL
- ✅ Configurar Apache con HTTPS
- ✅ Reconstruir contenedores
- ✅ Configurar renovación automática

**Tiempo estimado: 3-5 minutos**

---

## 5️⃣ VERIFICAR

Abre en tu navegador:
- `https://micaela-tours.com` ✅
- `https://www.micaela-tours.com` ✅

Deberías ver el **candado verde** 🔒 en la barra de direcciones.

---

## ✅ ¡LISTO!

Tu sitio ahora tiene:
- ✅ HTTPS activo
- ✅ Certificado SSL válido
- ✅ Renovación automática
- ✅ Redirección HTTP → HTTPS

---

## 🐛 SI ALGO FALLA

### Error: "Failed to obtain certificate"

**Causa:** DNS no está propagado o no apunta al VPS.

**Solución:**
1. Verifica DNS: `nslookup micaela-tours.com`
2. Espera 30 minutos más
3. Vuelve a ejecutar el script

### Error: "Port 80 already in use"

**Solución:**
```bash
docker-compose -f docker-compose.vps.yml down
./configurar_dominio_ssl.sh
```

---

## 📞 COMANDOS ÚTILES

```bash
# Ver estado
docker ps

# Ver logs
docker logs tours_micaela_app_vps

# Reiniciar
docker-compose -f docker-compose.ssl.yml restart

# Renovar SSL manualmente
/root/renovar_ssl.sh
```

---

**¡Tu sitio estará seguro en minutos!** 🚀🔒
