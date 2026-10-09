# 🧾 Guía Completa para Vincular Facturación Electrónica SUNAT con Greenter (PHP - MVC)

## 📌 Requisitos previos

### 1. Greenter (última versión)

Descargar e instalar vía Composer:
```bash
composer require greenter/greenter
```

### 2. Certificado digital (.pfx → .pem) — instalación y renovación

El certificado digital firma cada factura y boleta. **Si vence o no coincide con el registrado en SUNAT, no se puede facturar.**

#### 📌 Certificado actual

| Dato | Valor |
|---|---|
| Proveedor | Llama.pe (emisor: *Llama.pe SHA256 Standard CA*) |
| Titular | SOFTWARE DE FACTURACION ELECTRONICA |
| Serie | `11631c652473cc11` |
| Válido desde | 10/11/2025 |
| **Válido hasta** | **10/11/2026** |
| Registrado en SUNAT | 10/11/2025 10:21 |
| Archivo en el sistema | `greenter/certificados/certificado_produccion.pem` |

> ⏰ **Renovar al menos 2 semanas antes del vencimiento.** El trámite con el proveedor puede demorar varios días.
>
> En SUNAT también figura activo un certificado anterior (serie `342a4e50…`, válido hasta 13/12/2026). El sistema **no** lo usa.

---

#### Paso 1 — Comprar o renovar el certificado

- Se compra o renueva con un proveedor autorizado por SUNAT (el actual es **Llama.pe**).
- El proveedor entrega un archivo **`.pfx`** (o `.p12`) y una **contraseña**. Guárdalos en un lugar seguro; los necesitas en los pasos 2 y 3.

#### Paso 2 — Registrar el certificado en SUNAT

> ⚠️ Requiere el **usuario principal** de Clave SOL (no FACTURA1). Verifica antes que puedes ingresar con él.

1. Ingresa a **SUNAT Operaciones en Línea (SOL)**, pestaña **RUC**, con el RUC `20603540647`, el usuario principal y su clave.
2. Ve a:
   ```
   COMPROBANTES DE PAGO
     └─ SEE - Del Contribuyente y Envío de Documentos
          └─ Certificado Digital
               └─ Registro y Mantenimiento de Correo y Certificados Digitales
   ```
   > ❗ **No** uses el menú "Certificado Digital Tributario - CDT": es otro trámite.
3. En la pantalla **"REGISTRO Y MANTENIMIENTO - CORREO ELECTRÓNICO Y CERTIFICADOS DIGITALES"**, verifica que:
   - Esté marcado **"Deseo emitir a través del SEE - Del Contribuyente"**.
   - Haya un **correo electrónico registrado**; sin correo no deja registrar el certificado.
4. En **"REGISTRE AQUÍ SU CERTIFICADO DIGITAL"**, sube el archivo **`.pfx`** e ingresa su contraseña.
5. Confirma que el nuevo certificado aparece en la tabla con:
   - **Estado:** `Activo`
   - **Válido desde / Válido hasta:** las fechas del nuevo certificado
   - **Serie de certificado:** anótala para el paso 3.

> ⚠️ **No des de baja el certificado anterior** hasta confirmar que el nuevo funciona (paso 6). Si algo falla, el anterior te permite seguir facturando mientras siga vigente.

#### Paso 3 — Convertir el `.pfx` a `.pem`

Greenter (PHP) necesita el certificado en formato `.pem`.

**En Windows (XAMPP)**, abre una terminal en la carpeta donde está el `.pfx`:
```bash
C:\xampp\apache\bin\openssl.exe pkcs12 -in certificado.pfx -out certificado_produccion.pem -nodes
```
**En Linux/VPS:**
```bash
openssl pkcs12 -in certificado.pfx -out certificado_produccion.pem -nodes
```
- Pedirá la contraseña del `.pfx`.
- Si sale un error como `unsupported` o `RC2-40-CBC` (pasa con OpenSSL 3 y certificados antiguos), agrega `-legacy` al comando.

**Verifica el `.pem` generado:**
```bash
openssl x509 -in certificado_produccion.pem -noout -serial -dates -subject
```
- `serial=` debe coincidir con la **Serie de certificado** que muestra SUNAT en el paso 2 (sin importar mayúsculas o minúsculas).
- `notAfter=` debe ser la nueva fecha de vencimiento.
- El archivo debe contener **ambos** bloques: `-----BEGIN CERTIFICATE-----` y `-----BEGIN PRIVATE KEY-----`. Si falta la clave privada, no podrá firmar.

#### Paso 4 — Instalar en el sistema local (XAMPP)

1. Respalda el certificado actual renombrándolo, por ejemplo:
   ```
   greenter/certificados/certificado_produccion_2025-2026.pem
   ```
2. Copia el nuevo archivo como:
   ```
   greenter/certificados/certificado_produccion.pem
   ```
   ⚠️ El nombre debe ser **exactamente** `certificado_produccion.pem`; es el que lee `greenter/config/config_greenter.php`.

#### Paso 5 — Instalar en el VPS (producción)

La carpeta `greenter/certificados` del VPS está montada dentro del contenedor (`docker-compose.vps.yml`), así que **no hace falta reconstruir la imagen**.

1. Desde tu PC, sube el archivo al VPS (reemplaza usuario, IP y ruta del proyecto):
   ```bash
   scp certificado_produccion.pem USUARIO@IP_VPS:RUTA_DEL_PROYECTO/greenter/certificados/certificado_produccion.pem.nuevo
   ```
2. En el VPS, respalda el anterior y activa el nuevo:
   ```bash
   cd RUTA_DEL_PROYECTO/greenter/certificados
   cp certificado_produccion.pem certificado_produccion_2025-2026.pem
   mv certificado_produccion.pem.nuevo certificado_produccion.pem
   chmod 644 certificado_produccion.pem
   ```
3. Verifica que el contenedor ve el nuevo certificado:
   ```bash
   docker exec tours_micaela_app_vps openssl x509 -in /var/www/html/greenter/certificados/certificado_produccion.pem -noout -serial -dates
   ```
   Debe mostrar la serie y las fechas nuevas.

#### Paso 6 — Probar

No existe una prueba en producción que no emita un comprobante real. **La primera factura o boleta después del cambio es la prueba.**

1. Emite un comprobante real en un momento tranquilo (no en hora punta).
2. Si sale **ACEPTADO** ✅, el cambio está completo. Recién entonces puedes dar de baja el certificado anterior en SUNAT, si quieres.
3. Si sale un error de **firma o certificado**:
   - Revisa que la serie del `.pem` coincida con la registrada en SUNAT (pasos 2 y 3).
   - Mientras lo resuelves, restaura el respaldo para seguir facturando con el certificado anterior (si sigue vigente):
     ```bash
     cp certificado_produccion_2025-2026.pem certificado_produccion.pem
     ```
   - Si el error es **0100** o **"El sistema no puede responder su solicitud"**, **no** es del certificado: es indisponibilidad de SUNAT. El comprobante queda **PENDIENTE** y se reenvía después.

#### 🔐 Seguridad del certificado

- Los `.pem`, `.pfx` y `.p12` están en `.gitignore`. **Nunca los subas a GitHub.**
- No los envíes por WhatsApp ni por correo sin cifrar. Quien tenga el `.pem` puede firmar comprobantes a nombre de la empresa.
- Cuando termines, borra las copias sueltas del `.pfx` y del `.pem` (Descargas, Escritorio, etc.).

#### ✅ Checklist de renovación

- [ ] Nuevo `.pfx` y contraseña recibidos del proveedor
- [ ] Acceso al **usuario principal** SOL verificado
- [ ] Certificado registrado en SUNAT y en estado **Activo**
- [ ] `.pem` generado y la serie coincide con SUNAT
- [ ] Instalado en local con el anterior respaldado
- [ ] Instalado en el VPS con el anterior respaldado y verificado con `docker exec`
- [ ] Primer comprobante **ACEPTADO**
- [ ] Tabla "Certificado actual" de esta guía actualizada con los nuevos datos

### 3. Usuario y Clave SOL

Debes tener:

- ✔ RUC real
- ✔ Usuario principal SOL
- ✔ Clave SOL

Con esto crearás un usuario secundario.

#### 👤 3.1 Creación del usuario secundario SOL

**➤ Pasos:**

1. Entrar al Portal SUNAT Operaciones en Línea (SOL).
2. Ir a **Mi RUC y Otros - Usuarios Secundarios**.
3. Crear un usuario con:
   - Nombre del usuario
   - Contraseña
   - Asignar los permisos necesarios.

#### 🔐 3.2 Permisos obligatorios para facturación electrónica

Asigna **TODOS** estos permisos:

## 📂 4. Permisos SUNAT necesarios

**IMPORTANTE:**  
Si no otorgas uno de estos permisos, tendrás errores como:
- `1033 – Usuario no válido`
- `110.13 – No se pudo autenticar`
- `2012 – No tiene acceso al servicio`, etc.

### 4.1 TRIBUTARIOS

#### ✔ 1. Comprobantes de Pago
- Consulta de Comprobantes de Pago
- Nueva consulta
- Descarga de Resultados

#### ✔ 2. Certificado Digital Tributario – CDT
- Certificado Digital Tributario
- Cancelar Certificado
- Registro/Mantenimiento

#### ✔ 3. SEE - SOL
- Consulta Integrada de Validez
- Credenciales de API SUNAT

#### ✔ 4. Factura Electrónica
- Emitir Factura
- Emitir Nota de Crédito
- Emitir Nota de Débito
- Rechazar Factura
- Catálogo de productos
- Consultar Factura y Nota
- Emisión Simplificada

#### ✔ 5. Boleta de Venta Electrónica
- Emitir Boleta
- Emitir NC / ND
- Consultar Boletas y Notas

#### ✔ 6. Guía de Remisión (GRE & GRE-BF)
- Todos los permisos (emitir, confirmar, consultar, masivo).

#### ✔ 7. Comprobante de Percepción
- Todos los permisos.

#### ✔ 8. Comprobante de Retención
- Todos los permisos.

#### ✔ 9. SEE - Contribuyente y Envío de Documentos
- Envío por Web Service
- Consultar CPE enviados
- Comunicación de bajas
- Resumen diario
- Registro y Mantenimiento de Certificados
- Disponibilidad del servidor

### 4.2 Sistema Integrado de Registros Electrónicos (SIRE)
- Reporte de ventas
- Registros electrónicos (ventas / compras)

## 🗂 5. Configuración Greenter (PHP - MVC)

**Archivo:** `config/config_greenter.php`
```php
<?php
use Greenter\Ws\Services\SunatEndpoints;
use Greenter\See;

require __DIR__ . '/../../vendor/autoload.php';

function getSee($pdo = null) {
    if ($pdo === null) {
        $pdo = getConnection();
    }

    $stmt = $pdo->query("SELECT * FROM empresa LIMIT 1");
    $empresa = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$empresa) {
        die("❌ No se encontró información de la empresa");
    }

    $see = new See();

    // PEM de producción
    $certPath = __DIR__ . '/../certificados/certificado_produccion.pem';
    if (!file_exists($certPath)) {
        die("❌ No se encontró el certificado: $certPath");
    }

    $see->setCertificate(file_get_contents($certPath));

    // Determinar ambiente
    $estado = (int)$empresa['modo_prueba'];

    if ($estado === 1) {
        echo "🧪 Entorno activo: SUNAT BETA\n";
        $see->setService(SunatEndpoints::FE_BETA);
    } else {
        echo "🚀 Entorno activo: SUNAT PRODUCCIÓN\n";
        $see->setService(SunatEndpoints::FE_PRODUCCION);
    }

    // Credenciales SOL
    $ruc     = trim($empresa['ruc']);
    $usuario = trim($empresa['usuario_sol']);
    $clave   = trim($empresa['clave_sol']);

    if (empty($ruc) || empty($usuario) || empty($clave)) {
        die("❌ Falta configuración SOL\n");
    }

    echo "🔑 RUC: {$ruc}\n";
    echo "🔑 Usuario: {$usuario}\n";
    echo "📦 Certificado: {$certPath}\n\n";

    // Enviar usuario sin RUC adelante
    $see->setClaveSOL($ruc, $usuario, $clave);

    return $see;
}
```

## 🧪 6. Prueba real en producción (Factura de S/ 1.00)

Usar:
- `modo_prueba = 0`
- `usuario_sol = usuario secundario`
- `clave_sol = clave del secundario`

Código completo de prueba:

*(Ya lo ordenaste, lo dejo tal cual para que lo uses en tu PDF o documentación)*

## 🧪 7. Pruebas en ambiente BETA

Configurar:

| Campo | Valor |
|-------|-------|
| ruc | RUC real |
| usuario_sol | MODDATOS |
| clave_sol | MODDATOS |
| modo_prueba | 1 |

## 🚀 8. Ambiente de producción

| Campo | Valor |
|-------|-------|
| ruc | RUC real |
| usuario_sol | Usuario secundario |
| clave_sol | Clave secundaria |
| modo_prueba | 0 |

## 📁 9. Estructura recomendada de carpetas
```
/greenter
   /certificados
       certificado_produccion.pem
   /xml
   /cdr
   config_greenter.php
factura_prueba.php
```

## 📌 10. Recomendación importante

### ✔ Probar siempre con valores muy bajos

Primero:
1. Factura de S/ 1.00
2. Luego anularla (comunicación de baja)

### ✔ Revisar siempre los logs CDR

Si hay errores de SUNAT:
- `R-F001-123.xml`

## ❗ 11. Errores típicos y cómo solucionarlos

### 0100 – "El sistema no puede responder su solicitud"
- SUNAT no pudo atender el envío (indisponibilidad de su servicio). El comprobante **no** se procesó.
- **Solución:** ninguna en el sistema. Queda **PENDIENTE** y la cola lo reenvía sola (ver sección 14). **No anular.**

### 1033 – Comprobante registrado previamente
- SUNAT ya tiene ese número. Suele pasar cuando un envío anterior sí llegó pero la respuesta se perdió (timeout).
- **Solución:** el sistema consulta la constancia (CDR) en SUNAT. Si es del mismo cliente, lo marca **ACEPTADO**; si es de otro cliente, **RECHAZADO** (número duplicado: revisar).

### 110.13 – SOL no autorizado
- Usuario o clave inválida.
- **Solución:** Regenerar clave o revisar usuario.

### 2012 – Certificado inválido
- Certificado mal convertido a PEM.

## 📘 12. Flujo completo de trabajo (resumen)

1. Comprar certificado digital
2. Convertir `.pfx` → `.pem`
3. Subir `.pfx` a SUNAT
4. Crear usuario secundario
5. Asignar permisos
6. Configurar Greenter
7. Probar factura de S/ 1.00
8. Enviar a producción
9. Implementar en tu sistema real
## 📘 13. Configuración adicional requerida

###  13.1 Base de datos
- Crear tabla `empresa` con campos SOL
- Crear tabla `comprobantes` para registro
- Implementar catálogos SUNAT

### 13.2 Gestión de archivos
- Carpetas para XML, CDR y PDF
- Sistema de respaldo automático

### 13.3 Funcionalidades adicionales
- Envío de emails con comprobantes
- Comunicación de bajas automática
- Resúmenes diarios para boletas
- Consulta de estado en SUNAT
- Generación de código QR

### 13.4 Seguridad
- Validar certificado no vencido
- Encriptar credenciales SOL en BD
- Logs de auditoría completos
## 🔁 14. Cola de envíos a SUNAT (CRON)

Si SUNAT no responde al emitir, el comprobante **se entrega igual** (ticket impreso) y queda en estado **PENDIENTE**. Un CRON lo reenvía automáticamente. El plazo legal para enviar es de **3 días calendario** desde la emisión.

### 14.1 Cómo funciona

```
Emisión (pantalla)
  └─ factura_bd.php --intentos=1  (un solo intento, ~15 s máx.)
       ├─ ACEPTADO   → listo
       └─ falla temporal de SUNAT (0100, HTTP, timeout…) → PENDIENTE + ticket al instante

CRON cada 5 minutos → greenter/reenviar_pendientes.php
  ├─ toma PENDIENTE emitidos en los últimos 3 días y sin intento en los últimos N minutos
  ├─ 1 intento por comprobante; si SUNAT falla 3 veces seguidas, corta la ronda
  ├─ 1033 → consulta el CDR en SUNAT y lo marca ACEPTADO si es del mismo cliente
  └─ alertas en el log:
       🚨 PENDIENTE con más de 24 h (aún en plazo)
       🛑 PENDIENTE fuera de plazo → la cola NO los envía: revisión manual
```

**Protecciones:**
- **Candado por comprobante** (`greenter/tmp/envio_{id}.lock`): la pantalla y la cola nunca envían el mismo comprobante a la vez.
- Un comprobante **ACEPTADO nunca se reenvía**.
- Se reenvía siempre con el **mismo número y la misma fecha de emisión**.
- Solo se reintentan errores **temporales**; un rechazo por datos queda RECHAZADO.

### 14.2 Instalar el CRON en el VPS

El contenedor no tiene cron: se programa en el **servidor** con `docker exec`.

```bash
crontab -e
```
Agregar la línea:
```bash
*/5 * * * * docker exec -u www-data tours_micaela_app_vps php /var/www/html/greenter/reenviar_pendientes.php 25 5 >> /var/log/micaela_cola.log 2>&1
```
- `25` = máximo de comprobantes por ronda.
- `5` = minutos mínimos entre intentos del mismo comprobante.

Verificar que quedó programado y ver su actividad:
```bash
crontab -l
tail -f /var/log/micaela_cola.log
```

Probarlo a mano una vez (seguro: solo envía PENDIENTE en plazo):
```bash
docker exec -u www-data tours_micaela_app_vps php /var/www/html/greenter/reenviar_pendientes.php 25 5
```

### 14.3 Logs

| Archivo | Contenido |
|---|---|
| `/var/log/micaela_cola.log` (servidor) | Salida de cada ronda del CRON, con alertas |
| `greenter/auto_reenvio_log.txt` | Detalle de cada reenvío de la cola |
| `greenter/envio_log.txt` | Envíos hechos desde la pantalla |

### 14.4 Probar en local sin emitir comprobantes reales

1. Poner la BD local en BETA (respaldando credenciales):
   ```sql
   CREATE TABLE empresa_respaldo AS SELECT * FROM empresa;
   UPDATE empresa SET modo_prueba = 1, usuario_sol = 'MODDATOS', clave_sol = 'moddatos';
   ```
2. Simular una caída de SUNAT (solo funciona en BETA):
   ```bash
   SUNAT_BETA_URL="http://127.0.0.1:9/billService" php greenter/reenviar_pendientes.php 25 1
   ```
3. Volver a producción:
   ```sql
   UPDATE empresa e JOIN empresa_respaldo r ON e.id_empresa = r.id_empresa
   SET e.modo_prueba = r.modo_prueba, e.usuario_sol = r.usuario_sol, e.clave_sol = r.clave_sol;
   DROP TABLE empresa_respaldo;
   ```

> ⚠️ **Nunca ejecutar `reenviar_pendientes.php` en local con la BD en PRODUCCIÓN**: enviaría comprobantes reales a SUNAT.
