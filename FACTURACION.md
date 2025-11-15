# 🧾 Guía Completa para Vincular Facturación Electrónica SUNAT con Greenter (PHP - MVC)

## 📌 Requisitos previos

### 1. Greenter (última versión)

Descargar e instalar vía Composer:
```bash
composer require greenter/greenter
```

### 2. Certificado digital (.pfx → .pem)

Debes comprar el certificado digital con alguna de las empresas proveedoras autorizadas.

**📌 El certificado te llega en .pfx, pero debes hacer 2 procesos:**

#### a) Subirlo a SUNAT (Certificados Digitales)

En el portal se sube el .pfx.

#### b) Convertirlo a .pem para usarlo en PHP con Greenter

Ejecutar en consola:
```bash
openssl pkcs12 -in cert.pfx -out certificado_produccion.pem -nodes
```

Colocarlo en:
```
/certificados/certificado_produccion.pem
```

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

### 1033 – Usuario no válido
- Usuario secundario no tiene permisos.
- **Solución:** Asignar TODOS los permisos mencionados.

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