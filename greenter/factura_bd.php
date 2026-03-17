<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../model/model_conexion.php';
require_once __DIR__ . '/config/config_greenter.php';

date_default_timezone_set('America/Lima');

use Greenter\Model\Company\Address;
use Greenter\Model\Company\Company;
use Greenter\Model\Client\Client;
use Greenter\Model\Sale\Invoice;
use Greenter\Model\Sale\SaleDetail;
use Greenter\Model\Sale\Legend;
use Greenter\Model\Sale\FormaPagos\FormaPagoContado;
use Greenter\Model\Sale\Note;

// =============================================
// 1️⃣ CONEXIÓN BD
// =============================================
$db = new conexionBD();
$pdo = $db->conexionPDO();

// =============================================
// 2️⃣ OBTENER ID DESDE ARGUMENTOS O QUERY
// =============================================
$id_comprobante = null;

if (isset($argv[1]) && is_numeric($argv[1])) {
    $id_comprobante = (int)$argv[1];
}

if (!$id_comprobante) {
    die("❌ ERROR: No se proporcionó ID de comprobante.\n");
}

// =============================================
// 3️⃣ OBTENER COMPROBANTE POR ID
// =============================================
$sql = "SELECT c.*, cl.tipo_documento, cl.numero_documento, cl.razon_social, cl.direccion,
               cl.departamento, cl.provincia, cl.distrito, cl.ubigeo
        FROM comprobantes c
        INNER JOIN cliente_sunat cl ON c.id_cliente = cl.id_cliente
        WHERE c.id_comprobante = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_comprobante]);
$comprobante = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$comprobante) {
    die("❌ No se encontró el comprobante con ID: $id_comprobante.\n");
}

$tipo = $comprobante['tipo_comprobante'];
echo "📄 Procesando comprobante {$tipo}: {$comprobante['serie']}-{$comprobante['correlativo']}\n";

// =============================================
// 4️⃣ DATOS DEL CLIENTE
// =============================================
$address = (new Address())
    ->setDireccion($comprobante['direccion'] ?? 'SIN DIRECCIÓN')
    ->setDepartamento($comprobante['departamento'] ?? 'LIMA')
    ->setProvincia($comprobante['provincia'] ?? 'LIMA')
    ->setDistrito($comprobante['distrito'] ?? 'LIMA')
    ->setUbigueo($comprobante['ubigeo'] ?? '150101');

$client = (new Client())
    ->setTipoDoc($comprobante['tipo_documento'])
    ->setNumDoc($comprobante['numero_documento'])
    ->setRznSocial($comprobante['razon_social'])
    ->setAddress($address);

// =============================================
// 5️⃣ DATOS DE LA EMPRESA EMISORA
// =============================================
$stmt = $pdo->query("SELECT * FROM empresa LIMIT 1");
$empresa = $stmt->fetch(PDO::FETCH_ASSOC);

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

// =============================================
// 6️⃣ CREAR DETALLE BÁSICO
// =============================================
$total = (float)$comprobante['total'];
$base = $comprobante['total_gravada'] > 0 ? (float)$comprobante['total_gravada'] : round($total / 1.18, 2);
$igv = $comprobante['total_igv'] > 0 ? (float)$comprobante['total_igv'] : round($total - $base, 2);

$detail = (new SaleDetail())
    ->setCodProducto('P001')
    ->setUnidad('NIU')
    ->setCantidad(1)
    ->setDescripcion('PRODUCTO O SERVICIO')
    ->setMtoValorUnitario($base)
    ->setMtoValorVenta($base)
    ->setMtoPrecioUnitario($total)
    ->setMtoBaseIgv($base)
    ->setPorcentajeIgv(18.00)
    ->setIgv($igv)
    ->setTipAfeIgv('10')
    ->setTotalImpuestos($igv);

$details = [$detail];

// =============================================
// 7️⃣ LEYENDA
// =============================================
function numeroALetras($numero) {
    $entero = floor($numero);
    $decimales = round(($numero - $entero) * 100);
    return 'SON ' . $entero . ' CON ' . str_pad($decimales, 2, '0', STR_PAD_LEFT) . '/100 SOLES';
}

$legend = (new Legend())
    ->setCode('1000')
    ->setValue(numeroALetras($total));

// =============================================
// 8️⃣ CONFIGURAR DOCUMENTO SEGÚN TIPO
// =============================================
$see = getSee();

switch ($tipo) {
    case '01': // FACTURA
    case '03': // BOLETA
        $invoice = (new Invoice())
            ->setUblVersion('2.1')
            ->setTipoOperacion('0101')
            ->setTipoDoc($tipo)  // ← faltaba esta línea
            ->setSerie($comprobante['serie'])
            ->setCorrelativo(str_pad($comprobante['correlativo'], 8, '0', STR_PAD_LEFT))
            ->setFechaEmision(new DateTime($comprobante['fecha_emision'], new DateTimeZone('America/Lima')))
            ->setTipoMoneda('PEN')
            ->setCompany($company)
            ->setClient($client)
            ->setMtoOperGravadas($base)
            ->setMtoIGV($igv)
            ->setTotalImpuestos($igv)
            ->setValorVenta($base)
            ->setSubTotal($total)
            ->setMtoImpVenta($total)
            ->setFormaPago((new FormaPagoContado())->setMoneda('PEN')->setMonto($total))
            ->setDetails($details)
            ->setLegends([$legend]);
        $documento = $invoice;
        break;

    case '07': // NOTA DE CRÉDITO
    case '08': // NOTA DE DÉBITO
        // ✅ CORRECCIÓN: Validar que exista id_comprobante_origen
        if (empty($comprobante['id_comprobante_origen'])) {
            die("❌ ERROR: La nota no tiene documento de referencia (id_comprobante_origen vacío).\n");
        }

        // ✅ CORRECCIÓN: Obtener el tipo_comprobante del documento original
        $sqlRef = "SELECT tipo_comprobante, serie, correlativo 
                   FROM comprobantes 
                   WHERE id_comprobante = ?";
        $stmtRef = $pdo->prepare($sqlRef);
        $stmtRef->execute([$comprobante['id_comprobante_origen']]);
        $ref = $stmtRef->fetch(PDO::FETCH_ASSOC);

        if (!$ref) {
            die("❌ ERROR: No se encontró el comprobante de referencia (ID: {$comprobante['id_comprobante_origen']}).\n");
        }

        // ✅ VALIDACIÓN: Verificar que el tipo de documento sea válido
        if (!in_array($ref['tipo_comprobante'], ['01', '03'])) {
            die("❌ ERROR: El tipo de documento de referencia '{$ref['tipo_comprobante']}' no es válido para notas.\n");
        }

        echo "📌 Documento de referencia: {$ref['tipo_comprobante']} ({$ref['serie']}-{$ref['correlativo']})\n";

        // ✅ CORRECCIÓN: Obtener motivo correcto
        $motivo = '';
        if ($tipo == '07') {
            $motivo = !empty($comprobante['motivo_nota']) ? $comprobante['motivo_nota'] : 'Anulación de la operación';
        } else {
            $motivo = !empty($comprobante['motivo_nota']) ? $comprobante['motivo_nota'] : 'Aumento en el valor';
        }

        $note = (new Note())
            ->setUblVersion('2.1')
            ->setTipoDoc($tipo)
            ->setSerie($comprobante['serie'])
            ->setCorrelativo($comprobante['correlativo'])
            ->setFechaEmision(new DateTime($comprobante['fecha_emision']))
            ->setTipDocAfectado($ref['tipo_comprobante']) // ✅ DINÁMICO: '01' o '03'
            ->setNumDocfectado($ref['serie'] . '-' . $ref['correlativo'])
            ->setCodMotivo($tipo == '07' ? '01' : '01')
            ->setDesMotivo($motivo)
            ->setTipoMoneda('PEN')
            ->setCompany($company)
            ->setClient($client)
            ->setMtoOperGravadas($base)
            ->setMtoIGV($igv)
            ->setTotalImpuestos($igv)
            ->setValorVenta($base)
            ->setMtoImpVenta($total)
            ->setDetails($details)
            ->setLegends([$legend]);
        $documento = $note;
        break;

    default:
        die("❌ Tipo de comprobante no soportado: $tipo\n");
}

// =============================================
// 9️⃣ GENERAR XML
// =============================================
$xml = $see->getXmlSigned($documento);
$xmlPath = __DIR__ . '/xml/';
if (!is_dir($xmlPath)) mkdir($xmlPath, 0777, true);

$nombreXml = $comprobante['serie'] . '-' . str_pad($comprobante['correlativo'], 8, '0', STR_PAD_LEFT) . '.xml';
file_put_contents($xmlPath . $nombreXml, $xml);
echo "📄 XML generado: xml/{$nombreXml}\n";

// =============================================
// 🔟 ENVIAR A SUNAT
// =============================================
function esErrorTransitorioSunat($codigo, $mensaje)
{
    $codigo = strtoupper(trim((string)$codigo));
    $mensaje = strtolower(trim((string)$mensaje));

    if ($codigo === 'HTTP') {
        return true;
    }

    $patrones = [
        'internal server error',
        'service unavailable',
        'temporarily unavailable',
        'timeout',
        'timed out',
        'connection reset',
        'could not connect',
        'ssl',
    ];

    foreach ($patrones as $patron) {
        if (strpos($mensaje, $patron) !== false) {
            return true;
        }
    }

    return false;
}

$maxIntentos = 3;
$res = null;
$codigoUltimoError = '';
$mensajeUltimoError = '';

for ($intento = 1; $intento <= $maxIntentos; $intento++) {
    $res = $see->send($documento);

    if ($res->isSuccess()) {
        break;
    }

    $errorActual = $res->getError();
    $codigoUltimoError = $errorActual ? trim((string)$errorActual->getCode()) : '';
    $mensajeUltimoError = $errorActual ? trim((string)$errorActual->getMessage()) : 'Error desconocido';
    $esTransitorio = esErrorTransitorioSunat($codigoUltimoError, $mensajeUltimoError);

    echo "\n⚠️ INTENTO {$intento}/{$maxIntentos} FALLIDO\n";
    echo "Código: {$codigoUltimoError}\n";
    echo "Mensaje: {$mensajeUltimoError}\n";

    if (!$esTransitorio || $intento >= $maxIntentos) {
        break;
    }

    $espera = $intento * 2;
    echo "⏳ Reintentando en {$espera} segundos...\n";
    sleep($espera);
}

if ($res->isSuccess()) {
    $cdr = $res->getCdrResponse();

    preg_match('/<ds:DigestValue>(.*?)<\/ds:DigestValue>/', $xml, $matches);
    $hash = $matches[1] ?? 'NO_HASH';

    // Guardar CDR
    if ($res->getCdrZip()) {
        $cdrPath = __DIR__ . '/cdr/';
        if (!is_dir($cdrPath)) mkdir($cdrPath, 0777, true);
        $nombreCdr = 'R-' . $comprobante['serie'] . '-' . str_pad($comprobante['correlativo'], 8, '0', STR_PAD_LEFT) . '.zip';
        file_put_contents($cdrPath . $nombreCdr, $res->getCdrZip());
    }

    // Actualizar BD
    $upd = $pdo->prepare("UPDATE comprobantes 
                          SET estado_sunat='ACEPTADO', 
                              codigo_hash=?, 
                              descripcion_respuesta_sunat=?, 
                              codigo_respuesta_sunat=?, 
                              fecha_envio_sunat=NOW()
                          WHERE id_comprobante=?");
    $upd->execute([
        $hash,
        $cdr->getDescription(),
        $cdr->getCode(),
        $comprobante['id_comprobante']
    ]);

    echo "\n✅ ACEPTADO POR SUNAT\n";
    echo "   Descripción: {$cdr->getDescription()}\n";
    echo "   Código: {$cdr->getCode()}\n";
    echo "   Hash: {$hash}\n";

} else {
    $error = $res->getError();
    $codigoError = $error ? trim((string)$error->getCode()) : $codigoUltimoError;
    $mensajeError = $error ? trim((string)$error->getMessage()) : $mensajeUltimoError;
    $esTransitorio = esErrorTransitorioSunat($codigoError, $mensajeError);

    $estadoSunat = $esTransitorio ? 'PENDIENTE' : 'RECHAZADO';
    $descripcionSunat = $esTransitorio ? '[TEMPORAL] ' . $mensajeError : $mensajeError;

    // Actualizar estado en BD
    $upd = $pdo->prepare("UPDATE comprobantes 
                          SET estado_sunat=?,
                              codigo_respuesta_sunat=?,
                              descripcion_respuesta_sunat=?,
                              fecha_envio_sunat=NOW()
                          WHERE id_comprobante=?");
    $upd->execute([
        $estadoSunat,
        $codigoError,
        $descripcionSunat,
        $comprobante['id_comprobante']
    ]);

    if ($esTransitorio) {
        echo "\n⚠️ ERROR TRANSITORIO SUNAT\n";
        echo "Código: {$codigoError}\n";
        echo "Mensaje: {$mensajeError}\n";
        echo "Acción: Reintentar envío\n";
    } else {
        echo "\n❌ ERROR EN EL ENVÍO\n";
        echo "Código: {$codigoError}\n";
        echo "Mensaje: {$mensajeError}\n";
    }
}
