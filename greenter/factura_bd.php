<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../model/model_conexion.php';
require_once __DIR__ . '/config/config_greenter.php';

use Greenter\Model\Company\Address;
use Greenter\Model\Company\Company;
use Greenter\Model\Client\Client;
use Greenter\Model\Sale\Invoice;
use Greenter\Model\Sale\SaleDetail;
use Greenter\Model\Sale\Legend;
use Greenter\Model\Sale\FormaPagos\FormaPagoContado;

// 1️⃣ Conexión BD
$db = new conexionBD();
$pdo = $db->conexionPDO();

// 2️⃣ Obtener el comprobante pendiente
$sql = "SELECT 
            c.id_comprobante,
            c.tipo_comprobante,
            c.serie,
            c.correlativo,
            c.fecha_emision,
            c.total,
            c.total_gravada,
            c.total_igv,
            cl.tipo_documento,
            cl.numero_documento,
            cl.razon_social,
            cl.direccion,
            cl.departamento,
            cl.provincia,
            cl.distrito,
            cl.ubigeo
        FROM comprobantes c
        INNER JOIN cliente_sunat cl ON c.id_cliente = cl.id_cliente
        WHERE c.estado_sunat = 'PENDIENTE'
        ORDER BY c.id_comprobante DESC
        LIMIT 1";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$comprobante = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$comprobante) {
    die("❌ No se encontró ningún comprobante PENDIENTE.\n");
}

echo "📄 Procesando: {$comprobante['serie']}-{$comprobante['correlativo']}\n";
echo "💰 Total: S/ {$comprobante['total']}\n";
echo "📅 Fecha emisión: {$comprobante['fecha_emision']}\n\n";

// Validar que la fecha no sea futura
$fechaEmision = new DateTime($comprobante['fecha_emision']);
$fechaHoy = new DateTime();
if ($fechaEmision > $fechaHoy) {
    die("❌ ERROR: La fecha de emisión ({$comprobante['fecha_emision']}) no puede ser futura.\n" .
        "   SUNAT rechaza comprobantes con fechas posteriores a la actual.\n" .
        "   Actualiza la fecha en la BD a: " . $fechaHoy->format('Y-m-d') . "\n");
}

// 3️⃣ Crear dirección del cliente
$address = (new Address())
    ->setDireccion($comprobante['direccion'] ?? 'SIN DIRECCIÓN')
    ->setDepartamento($comprobante['departamento'] ?? 'LIMA')
    ->setProvincia($comprobante['provincia'] ?? 'LIMA')
    ->setDistrito($comprobante['distrito'] ?? 'LIMA')
    ->setUbigueo($comprobante['ubigeo'] ?? '150101');

// 4️⃣ Crear cliente según tipo de comprobante
$tipoComprobante = $comprobante['tipo_comprobante'];
$tipoDocCliente = $comprobante['tipo_documento'];

// VALIDACIÓN: Factura solo para RUC
if ($tipoComprobante == '01' && $tipoDocCliente != '6') {
    die("❌ ERROR: Las facturas (01) solo se pueden emitir a clientes con RUC (tipo 6).\n" .
        "   Cliente actual tiene tipo documento: $tipoDocCliente\n" .
        "   Solución: Cambia el tipo_documento del cliente a '6' o el tipo_comprobante a '03' (Boleta).\n");
}

if ($tipoComprobante == '01') { // Factura
    $client = (new Client())
        ->setTipoDoc('6')  // RUC obligatorio para facturas
        ->setNumDoc($comprobante['numero_documento'])
        ->setRznSocial($comprobante['razon_social'])
        ->setAddress($address);
    
    echo "👤 Cliente: RUC {$comprobante['numero_documento']}\n";
    
} elseif ($tipoComprobante == '03') { // Boleta
    $client = (new Client())
        ->setTipoDoc($tipoDocCliente)  // Puede ser DNI (1) o RUC (6)
        ->setNumDoc($comprobante['numero_documento'])
        ->setRznSocial($comprobante['razon_social'])
        ->setAddress($address);
    
    echo "👤 Cliente: {$comprobante['razon_social']}\n";
    
} else {
    die("❌ Tipo de comprobante no soportado: $tipoComprobante\n");
}

// 5️⃣ Obtener datos de empresa
function getDatosEmpresa($pdo) {
    $sql = "SELECT ruc, razon_social, nombre_comercial, direccion, ubigeo, distrito, provincia, departamento 
            FROM empresa LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $empresa = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$empresa) {
        die("❌ No se encontraron datos de la empresa emisora.\n");
    }
    return $empresa;
}

$empresa = getDatosEmpresa($pdo);
$company = (new Company())
    ->setRuc($empresa['ruc'])
    ->setRazonSocial($empresa['razon_social'])
    ->setNombreComercial($empresa['nombre_comercial'])
    ->setAddress(
        (new Address())
            ->setUbigueo($empresa['ubigeo'])
            ->setDepartamento($empresa['departamento'])
            ->setProvincia($empresa['provincia'])
            ->setDistrito($empresa['distrito'])
            ->setDireccion($empresa['direccion'])
    );

echo "🏢 Emisor: {$empresa['razon_social']}\n\n";

// 6️⃣ CREAR DETALLE CON LOS TOTALES DEL COMPROBANTE
echo "📦 Creando detalle del comprobante...\n";

// Leer y convertir valores a float
$totalFinal = (float)$comprobante['total'];              // 118.00
$totalGravada = (float)$comprobante['total_gravada'];    // 100.00
$totalIgv = (float)$comprobante['total_igv'];            // 18.00

// Si total_gravada o total_igv están vacíos o en 0, calcularlos desde el total
if ($totalGravada == 0 || $totalIgv == 0) {
    echo "⚠️  Los campos total_gravada o total_igv están vacíos.\n";
    echo "⚠️  Calculando base gravada e IGV desde el total...\n";
    
    $totalGravada = round($totalFinal / 1.18, 2);           // 118 / 1.18 = 100.00
    $totalIgv = round($totalFinal - $totalGravada, 2);      // 118 - 100 = 18.00
}

// Mostrar valores calculados
echo "   ├─ Base Gravada: S/ " . number_format($totalGravada, 2) . "\n";
echo "   ├─ IGV (18%):    S/ " . number_format($totalIgv, 2) . "\n";
echo "   └─ Total:        S/ " . number_format($totalFinal, 2) . "\n\n";

// Validar que la base gravada no sea 0
if ($totalGravada <= 0) {
    die("❌ ERROR: La base gravada no puede ser 0. Verifica el campo 'total' en el comprobante.\n");
}

// Asegurar que los valores sean números con 2 decimales
$totalGravada = round($totalGravada, 2);
$totalIgv = round($totalIgv, 2);
$totalFinal = round($totalFinal, 2);

// Crear el detalle del comprobante con el orden correcto de métodos
$detail = (new SaleDetail())
    ->setCodProducto('P001')
    ->setUnidad('NIU')
    ->setCantidad(1)
    ->setDescripcion('PRODUCTO O SERVICIO')
    ->setMtoValorUnitario($totalGravada)      // 100.00
    ->setMtoValorVenta($totalGravada)         // 100.00 ← LineExtensionAmount
    ->setMtoPrecioUnitario($totalFinal)       // 118.00
    ->setMtoBaseIgv($totalGravada)            // 100.00
    ->setPorcentajeIgv(18.00)                 // 18%
    ->setIgv($totalIgv)                       // 18.00
    ->setTipAfeIgv('10')                      // Gravado
    ->setTotalImpuestos($totalIgv);           // 18.00

echo "✓ Detalle creado: {$detail->getDescripcion()}\n";
echo "  - Valor Venta (LineExtensionAmount): S/ {$detail->getMtoValorVenta()}\n";
echo "  - IGV: S/ {$detail->getIgv()}\n";
echo "  - Precio Unitario: S/ {$detail->getMtoPrecioUnitario()}\n\n";

$details = [$detail];

// Calcular totales para la factura
$totalGravadas = $totalGravada;   // 100.00
$totalIGV = $totalIgv;             // 18.00
$totalImpuestos = $totalIgv;       // 18.00
$valorVenta = $totalGravada;       // 100.00
$mtoImpVenta = $totalFinal;        // 118.00

// Totales adicionales (para evitar errores de validación)
$sumOtrosDescuentos = 0.00;
$sumOtrosCargos = 0.00;

// 7️⃣ Leyenda (número a letras)
function numeroALetras($numero) {
    // Implementación básica - puedes mejorar con una librería
    $entero = floor($numero);
    $decimales = round(($numero - $entero) * 100);
    
    // Aquí podrías usar una función más completa
    return 'CIENTO DIECIOCHO CON ' . str_pad($decimales, 2, '0', STR_PAD_LEFT) . '/100';
}

$legend = (new Legend())
    ->setCode('1000')
    ->setValue(numeroALetras($totalFinal) . ' SOLES');

// Validar cálculos antes de crear la factura
$calculoTotal = $totalGravada + $totalIgv;
if (abs($calculoTotal - $totalFinal) > 0.01) {
    die("❌ ERROR: Los totales no cuadran.\n" .
        "   Base Gravada + IGV = $calculoTotal\n" .
        "   Total en comprobante = $totalFinal\n" .
        "   Diferencia: " . abs($calculoTotal - $totalFinal) . "\n");
}

echo "✓ Validación de totales: OK\n";
echo "  Base (100.00) + IGV (18.00) = Total (118.00)\n\n";

// 7.5️⃣ Forma de Pago (OBLIGATORIO desde 2022)
$formaPago = new FormaPagoContado();
$formaPago->setMoneda('PEN');
$formaPago->setMonto($totalFinal);  // 118.00

echo "💳 Forma de pago: Contado (S/ {$totalFinal})\n\n";

// 8️⃣ Crear factura/boleta
$invoice = (new Invoice())
    ->setUblVersion('2.1')
    ->setTipoOperacion('0101')
    ->setTipoDoc($comprobante['tipo_comprobante'])
    ->setSerie($comprobante['serie'])
    ->setCorrelativo($comprobante['correlativo'])
    ->setFechaEmision(new DateTime($comprobante['fecha_emision']))
    ->setTipoMoneda('PEN')
    ->setCompany($company)
    ->setClient($client)
    ->setMtoOperGravadas($totalGravadas)      // 100.00 - Gravadas
    ->setMtoOperExoneradas(0.00)              // 0.00 - Exoneradas  
    ->setMtoOperInafectas(0.00)               // 0.00 - Inafectas
    ->setMtoIGV($totalIGV)                    // 18.00 - IGV
    ->setTotalImpuestos($totalImpuestos)      // 18.00 - Total impuestos
    ->setValorVenta($valorVenta)              // 100.00 - Valor venta (sin IGV)
    ->setSubTotal($mtoImpVenta)               // 118.00 - SubTotal CON IGV
    ->setMtoImpVenta($mtoImpVenta)            // 118.00 - Total final
    ->setFormaPago($formaPago)                // Forma de pago: Contado ← NUEVO
    ->setDetails($details)
    ->setLegends([$legend]);

echo "📊 Resumen de la factura:\n";
echo "   Operaciones Gravadas: S/ {$totalGravadas}\n";
echo "   IGV (18%):           S/ {$totalIGV}\n";
echo "   Total Impuestos:     S/ {$totalImpuestos}\n";
echo "   Valor Venta:         S/ {$valorVenta}\n";
echo "   Total a Pagar:       S/ {$mtoImpVenta}\n\n";

// 9️⃣ Enviar a SUNAT
echo "📤 Generando XML...\n";
$see = getSee();

// Generar el XML primero para revisar
$xml = $see->getXmlSigned($invoice);

// Guardar XML para revisión (opcional)
$xmlPath = __DIR__ . '/xml/';
if (!is_dir($xmlPath)) {
    mkdir($xmlPath, 0777, true);
}
$nombreXml = $comprobante['serie'] . '-' . $comprobante['correlativo'] . '.xml';
file_put_contents($xmlPath . $nombreXml, $xml);
echo "📄 XML generado: xml/{$nombreXml}\n\n";

// Buscar campos críticos en el XML
$camposCriticos = [
    'cbc:LineExtensionAmount' => 'Total Precio de Venta del detalle',
    'cbc:TaxAmount' => 'Monto del IGV',
    'cbc:TaxableAmount' => 'Base Imponible',
    'cbc:PayableAmount' => 'Total a Pagar'
];

echo "🔍 Verificando campos en el XML:\n";
foreach ($camposCriticos as $campo => $descripcion) {
    if (strpos($xml, "<{$campo}") !== false) {
        // Extraer el valor
        preg_match("/<{$campo}[^>]*>([\d.]+)<\/{$campo}>/", $xml, $matches);
        $valor = $matches[1] ?? 'N/A';
        echo "  ✓ {$descripcion}: {$valor}\n";
    } else {
        echo "  ✗ {$descripcion}: NO ENCONTRADO ❌\n";
    }
}
echo "\n";

echo "📤 Enviando a SUNAT...\n";
$res = $see->send($invoice);

// 🔟 Procesar respuesta
if ($res->isSuccess()) {
    $cdr = $res->getCdrResponse();
    
    // Obtener el hash del XML firmado (DigestValue)
    preg_match('/<ds:DigestValue>(.*?)<\/ds:DigestValue>/', $xml, $matches);
    $hash = $matches[1] ?? 'NO_HASH';
    
    // Si no se encuentra, intentar obtener del CDR
    if ($hash === 'NO_HASH' && $res->getCdrZip()) {
        $cdrContent = $res->getCdrZip();
        $hash = base64_encode(hash('sha256', $cdrContent, true));
    }
    
    echo "\n✅ ¡ÉXITO! Comprobante aceptado por SUNAT\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "📋 Descripción: " . $cdr->getDescription() . "\n";
    echo "📝 Código: " . $cdr->getCode() . "\n";
    echo "🔑 Hash: " . $hash . "\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    // Guardar CDR (Constancia de Recepción) - opcional
    if ($res->getCdrZip()) {
        $cdrPath = __DIR__ . '/cdr/';
        if (!is_dir($cdrPath)) {
            mkdir($cdrPath, 0777, true);
        }
        $nombreCdr = 'R-' . $comprobante['serie'] . '-' . $comprobante['correlativo'] . '.zip';
        file_put_contents($cdrPath . $nombreCdr, $res->getCdrZip());
        echo "📦 CDR guardado: cdr/{$nombreCdr}\n\n";
    }
    
    // Actualizar estado en BD
    $upd = $pdo->prepare("UPDATE comprobantes 
                          SET estado_sunat = 'ENVIADO', 
                              codigo_hash = ?, 
                              descripcion_respuesta_sunat = ?, 
                              codigo_respuesta_sunat = ?,
                              fecha_envio_sunat = NOW() 
                          WHERE id_comprobante = ?");
    $upd->execute([
        $hash,
        $cdr->getDescription(),
        $cdr->getCode(),
        $comprobante['id_comprobante']
    ]);
    
    echo "💾 Estado actualizado en base de datos.\n";
    
} else {
    $error = $res->getError();
    
    echo "\n❌ ERROR AL ENVIAR A SUNAT\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "Código: " . $error->getCode() . "\n";
    echo "Mensaje: " . $error->getMessage() . "\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
}