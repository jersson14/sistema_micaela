# Despliegue del 03/11/2026 (noche)

Hostinger vence el **04/11**. Esta noche todo queda funcionando en el **VPS nuevo (Contabo Cloud VPS 4)** con el **dominio nuevo (Cloudflare)**.
Empezar **después de las 8:00 p. m.** (los clientes facturan de 3:00 a. m. a 8:00 p. m.).

Plan completo con todos los comandos: `PLAN_MAESTRO_NUBEFACT_Y_VPS.md` (escritorio), fases 3, 4 y 5.

## Contenido de esta carpeta

| Archivo | Para qué |
|---|---|
| `01_estados_encomiendas_salidas.sql` | Migración 1: procedimientos de estados de encomiendas y salidas |
| `02_registrar_editar_encomienda.sql` | Migración 2: registrar y editar encomiendas |
| `03_verificar.sql` | Verificación (solo lectura) |
| `actualizar_bd.sh` | Hace el respaldo, aplica 01 y 02 en orden y muestra la verificación |

> Las migraciones **solo reemplazan procedimientos** (no tocan tablas ni datos) y se pueden repetir sin problema. Sin ellas, en la rama nueva fallan "marcar salida incompleta" y "eliminar salida".

---

## Antes del 03/11 (con tiempo)

| # | Qué | Hecho |
|---|---|---|
| 1 | Contabo contratado, servidor preparado (plan maestro, Fase 3) y **ensayo general** hecho | ☐ |
| 2 | **Dominio nuevo comprado en Cloudflare** | ☐ |
| 3 | En Cloudflare → DNS: crear los registros **A** `@` y `www` apuntando a la **IP de Contabo**, con la **nube en gris (Solo DNS)** por ahora | ☐ |
| 4 | Credenciales de **producción** de Nubefact (correo nuevo, RUTA y TOKEN), si ya las tienes | ☐ |
| 5 | Hostinger: **renovación automática desactivada** | ☐ |

---

## La noche del 03/11

### Parte A — Mudanza (rama `jersson`, igual que hoy)

Plan maestro, **Fase 4**, pasos 4.1 a 4.6:
1. Detener la app en Hostinger.
2. Respaldo final de la base.
3. Copiar con `rsync` **todos los XML, CDR**, PDF, certificado, fotos y respaldos.
4. Restaurar la base en Contabo.
5. **Verificar los conteos** (XML, CDR, comprobantes).
6. Levantar la app.

### Parte B — Dominio nuevo (Cloudflare)

1. Nginx: en `/etc/nginx/sites-enabled/` cambiar `server_name` por el **dominio nuevo**:
   ```nginx
   server_name DOMINIO_NUEVO www.DOMINIO_NUEVO;
   ```
   ```bash
   nginx -t && systemctl reload nginx
   ```
2. Certificado SSL (con la nube de Cloudflare **en gris**):
   ```bash
   certbot --nginx -d DOMINIO_NUEVO -d www.DOMINIO_NUEVO
   ```
3. Abrir `https://DOMINIO_NUEVO` y entrar al sistema.
4. **Opcional, después:** activar la nube **naranja** (proxy de Cloudflare) y poner en Cloudflare → SSL/TLS el modo **"Full (strict)"**. Nunca usar "Flexible" (causa redirecciones infinitas).

> El sistema **no tiene el dominio escrito en el código**, así que no hay que cambiar nada del programa. Los usuarios deberán **iniciar sesión de nuevo** en el dominio nuevo.

### Parte C — Código nuevo y base de datos

En tu PC:
```bash
git checkout jersson
git merge nubefact-integracion
git push origin jersson
```
En el VPS nuevo:
```bash
cd ~/sistema_micaela
git pull origin jersson
cd despliegue/2026-11-03
bash actualizar_bd.sh
```
La verificación debe mostrar `SP_ELIMINAR_SALIDA_DIARIA = 2`, `SP_MODIFICAR_SALIDA_INCOMPLETO_ESTATUS = 4` y **0** rutinas con otra intercalación.

Luego reconstruir la app:
```bash
cd ~/sistema_micaela
docker compose -f docker-compose.vps.yml up -d --build app
```

### Parte D — Nubefact en producción

**Solo si ya tienes las credenciales de producción.** Si no, el sistema sigue con SUNAT directo hasta el 10/11; en ese caso, en el VPS crear `.env.nubefact` con `NUBEFACT_ACTIVO=0` para usar la salida de emergencia.

1. En el **panel de Nubefact (producción)**: registrar las series reales con su último número:
   ```bash
   docker exec tours_micaela_db_vps sh -c 'mysql -uroot -p"$MYSQL_ROOT_PASSWORD" micaela -e "SELECT serie, MAX(CAST(correlativo AS UNSIGNED)) ultimo FROM comprobantes GROUP BY serie;"'
   ```
2. Crear `.env.nubefact` en el VPS:
   ```bash
   cd ~/sistema_micaela
   nano .env.nubefact
   ```
   ```
   NUBEFACT_RUTA=ruta de producción
   NUBEFACT_TOKEN=token de producción
   NUBEFACT_ACTIVO=1
   NUBEFACT_MODO=PRODUCCION
   NUBEFACT_SERIES=
   ```
   ```bash
   chmod 600 .env.nubefact
   docker compose -f docker-compose.vps.yml up -d --build app
   docker exec -u www-data tours_micaela_app_vps php /var/www/html/greenter/nubefact/probar_conexion.php
   ```
3. Emitir **una factura de S/ 1** a tu RUC → debe decir **"NUBEFACT (OSE) - PRODUCCION"** y quedar aceptada.

> ⚠️ **Importante:** desde la rama nueva, si no existe `.env.nubefact` el sistema **no envía directo a SUNAT**: deja los comprobantes PENDIENTES. Por eso, si esa noche todavía no tienes las credenciales de producción, crea el archivo con `NUBEFACT_ACTIVO=0` (emergencia, SUNAT directo) hasta que las tengas.

### Parte E — Cierre de la noche

- ☐ Instalar el **crontab** (plan maestro, paso 4.8) y la copia de respaldo fuera del servidor.
- ☐ **Prueba de reinicio**: `reboot` y comprobar que el sistema vuelve solo.
- ☐ Probar: **marcar una salida como incompleta** y **eliminar una salida** de prueba (las dos dependen de las migraciones).
- ☐ Descargar el **XML y el CDR de un comprobante antiguo**.

---

## Si algo sale mal

| Falla en | Cómo volver |
|---|---|
| Parte A (mudanza) | Encender la app en Hostinger (`docker start tours_micaela_app_vps`) y apuntar el dominio de nuevo a Hostinger. **Renovar Hostinger 1 mes** |
| Parte C (código o base) | Restaurar el respaldo que muestra `actualizar_bd.sh` al final, volver al código anterior (`git checkout antes-nubefact`) y reconstruir |
| Parte D (Nubefact) | `.env.nubefact` con `NUBEFACT_ACTIVO=0` y reconstruir: vuelve a SUNAT directo (certificado vigente hasta el 10/11) |

## Dominio anterior (micaela-tours.com)

Mientras siga vigente, conviene **redirigirlo al dominio nuevo** para que nadie se pierda: apuntarlo a la IP de Contabo y agregar en Nginx un bloque que redirija (`return 301 https://DOMINIO_NUEVO$request_uri;`), con su propio certificado `certbot`. Avisar a los usuarios del dominio nuevo.
