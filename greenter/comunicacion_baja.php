<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../model/model_conexion.php';
require_once __DIR__ . '/config/config_greenter.php';

use Greenter\Model\Company\Address;
use Greenter\Model\Company\Company;
use Greenter\Model\Summary\Summary;
use Greenter\Model\Summary\SummaryDetail;
use Greenter\Model\Voided\Voided;
use Greenter\Model\Voided\VoidedDetail;

// =============================================
// 1️⃣ CONEXIÓN BD
// =============================================
$db = new conexionBD();
$pdo = $db->conexionPDO();

// =============================================
// 2️⃣ OBTENER ID Y CORRELATIVO DESDE ARGUMENTOS
// =============================================
$id_comprobante = null;
$correlativo_baja = null;

if (isset($argv[1]) && is_numeric($argv[1])) {
    $id_comprobante = (int)$argv[1];
}

if (isset($argv[2])) {
    $correlativo_baja = $argv[2];
}

if (!$id_comprobante || !$correlativo_baja) {
    die("❌ ERROR: Faltan parámetros. Uso: php comunicacion_baja.php [ID_COMPROBANTE] [CORRELATIVO_BAJA]\n");
}

// =============================================
// 3️⃣ OBTENER COMPROBANTE A ANULAR
// =============================================
$sql = "SELECT c.*, cl.tipo_documento, cl.numero_documento, cl.razon_social
        FROM comprobantes c
        INNER JOIN cliente_sunat cl ON c.id_cliente = cl.id_cliente
        WHERE c.id_comprobante = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_comprobante]);
$comprobante = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$comprobante) {
    die("❌ No se encontró el comprobante con ID: $id_comprobante.\n");
}

// =============================================
// 4️⃣ VALIDAR TIPO DE COMPROBANTE
// =============================================
if (!in_array($comprobante['tipo_comprobante'], ['01', '03'])) {
    die("❌ ERROR: Solo se pueden anular FACTURAS (01) o BOLETAS (03). Este comprobante es tipo: {$comprobante['tipo_comprobante']}\n");
}

if (!in_array($comprobante['estado_sunat'], ['ACEPTADO', 'ENVIADO'])) {
    die("❌ ERROR: El comprobante debe estar ACEPTADO por SUNAT. Estado actual: {$comprobante['estado_sunat']}\n");
}

$es_boleta = ($comprobante['tipo_comprobante'] == '03');
$tipo_doc = $es_boleta ? 'BOLETA' : 'FACTURA';
echo "📄 Procesando anulación de {$tipo_doc}: {$comprobante['serie']}-{$comprobante['correlativo']}\n";

// =============================================
// 5️⃣ VALIDAR PLAZO DE ANULACIÓN (7 DÍAS PARA BOLETAS)
// =============================================
// Configurar zona horaria de Perú
date_default_timezone_set('America/Lima');

$fecha_emision = new DateTime($comprobante['fecha_emision'], new DateTimeZone('America/Lima'));
$fecha_actual = new DateTime('now', new DateTimeZone('America/Lima'));
$dias_transcurridos = $fecha_actual->diff($fecha_emision)->days;

if ($es_boleta && $dias_transcurridos > 7) {
    die("❌ ERROR: Solo se pueden anular boletas con máximo 7 días de emisión. Han transcurrido {$dias_transcurridos} días.\n");
}

if ($es_boleta) {
    echo "✅ Validación de plazo: {$dias_transcurridos} días (permitido: máximo 7)\n";
}

// =============================================
// 6️⃣ DATOS DE LA EMPRESA EMISORA
// =============================================
$stmt = $pdo->query("SELECT * FROM empresa LIMIT 1");
$empresa = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$empresa) {
    die("❌ ERROR: No se encontraron datos de la empresa emisora.\n");
}

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
// 7️⃣ PREPARAR DATOS COMUNES
// =============================================
$see = getSee();

// Extraer solo el número del correlativo (ej: "RA-20241028-001" -> "001")
$numero_correlativo = substr($correlativo_baja, strrpos($correlativo_baja, '-') + 1);

// Configurar zona horaria de Perú
date_default_timezone_set('America/Lima');
$fechaHoy = new DateTime('now', new DateTimeZone('America/Lima'));

// IMPORTANTE: Para anulaciones el mismo día, usar la fecha de HOY
// Si el comprobante se emitió hoy, usar fechaHoy para evitar error 2671
if ($fecha_emision->format('Y-m-d') === $fechaHoy->format('Y-m-d')) {
    echo "⚠️  Anulación el mismo día de emisión - usando fecha actual para ambos campos\n";
    $fecha_emision = clone $fechaHoy;
}

echo "📅 Fecha de generación: {$fechaHoy->format('Y-m-d')}\n";
echo "📅 Fecha de emisión del comprobante: {$fecha_emision->format('Y-m-d')}\n";

// =============================================
// 8️⃣ PREPARAR MOTIVO DE BAJA
// =============================================
$motivo_baja = $comprobante['observaciones'] ?? 'ANULACIÓN DE COMPROBANTE ELECTRÓNICO';

// Limpiar el motivo
if (strpos($motivo_baja, '[MOTIVO ANULACIÓN]') !== false) {
    preg_match('/\[MOTIVO ANULACIÓN\]\s*(.+?)(\n|$)/s', $motivo_baja, $matches);
    if (isset($matches[1])) {
        $motivo_baja = trim($matches[1]);
    }
}

// Limitar a 100 caracteres
if (strlen($motivo_baja) > 100) {
    $motivo_baja = substr($motivo_baja, 0, 97) . '...';
}

if (empty(trim($motivo_baja))) {
    $motivo_baja = 'ANULACIÓN DE COMPROBANTE ELECTRÓNICO';
}

echo "📝 Motivo de baja: {$motivo_baja}\n";
echo "💰 Total del comprobante: S/ {$comprobante['total']}\n";

// =============================================
// 9️⃣ GENERAR DOCUMENTO SEGÚN TIPO
// =============================================
if ($es_boleta) {
    // ========================================
    // ✅ BOLETAS: Usar Summary (Resumen de Reversiones - RC)
    // ========================================
    echo "📋 Tipo de documento: Resumen de Reversiones (RC)\n";
    
    $detail = (new SummaryDetail())
        ->setTipoDoc('03')
        ->setSerieNro($comprobante['serie'] . '-' . $comprobante['correlativo'])
        ->setEstado('3')  // 3 = Anulado
        ->setClienteTipo($comprobante['tipo_documento'])
        ->setClienteNro($comprobante['numero_documento'])
        ->setTotal($comprobante['total'])
        ->setMtoOperGravadas($comprobante['op_gravadas'] ?? 0)
        ->setMtoIGV($comprobante['igv'] ?? 0);
    
    // FECHAS PARA RESUMEN DE REVERSIONES:
    // - setFecGeneracion() = Fecha de HOY (cuando se envía a SUNAT)
    // - setFecResumen() = Fecha de emisión de las boletas a anular
    $summary = (new Summary())
        ->setFecGeneracion($fechaHoy)           // ✅ Fecha actual
        ->setFecResumen($fecha_emision)         // ✅ Fecha de emisión de la boleta
        ->setCorrelativo($numero_correlativo)
        ->setCompany($company)
        ->setDetails([$detail]);
    
    $xml = $see->getXmlSigned($summary);
    $result = $see->send($summary);
    
} else {
    // ========================================
    // ✅ FACTURAS: Usar Voided (Comunicación de Baja - RA)
    // ========================================
    echo "📋 Tipo de documento: Comunicación de Baja (RA)\n";
    
    $detail = (new VoidedDetail())
        ->setTipoDoc('01')
        ->setSerie($comprobante['serie'])
        ->setCorrelativo($comprobante['correlativo'])
        ->setDesMotivoBaja($motivo_baja);
    
    // FECHAS PARA COMUNICACIÓN DE BAJA:
    // - setFecGeneracion() = Fecha de emisión de la factura
    // - setFecComunicacion() = Fecha de HOY (cuando se envía a SUNAT)
    $voided = (new Voided())
        ->setCorrelativo($numero_correlativo)
        ->setFecGeneracion($fecha_emision)      // ✅ Fecha de emisión de la factura
        ->setFecComunicacion($fechaHoy)         // ✅ Fecha actual
        ->setCompany($company)
        ->setDetails([$detail]);
    
    $xml = $see->getXmlSigned($voided);
    $result = $see->send($voided);
}

// =============================================
// 🔟 GUARDAR XML
// =============================================
$xmlPath = __DIR__ . '/xml/';
if (!is_dir($xmlPath)) {
    mkdir($xmlPath, 0777, true);
}

$nombreXml = $correlativo_baja . '.xml';
file_put_contents($xmlPath . $nombreXml, $xml);
echo "📄 XML generado: xml/{$nombreXml}\n";

// =============================================
// 1️⃣1️⃣ PROCESAR RESPUESTA DE SUNAT
// =============================================
echo "📤 Enviando documento a SUNAT...\n";

if ($result->isSuccess()) {
    $ticket = $result->getTicket();
    
    // IMPORTANTE: Summary y Voided devuelven TICKET, no CDR inmediato
    // El CDR se obtiene después consultando el ticket
    echo "✅ Ticket recibido: {$ticket}\n";
    
    // Registrar el ticket en base de datos
    $upd = $pdo->prepare("UPDATE comprobantes 
                          SET codigo_respuesta_sunat='TICKET',
                              descripcion_respuesta_sunat=?,
                              estado_sunat='ENVIADO'
                          WHERE id_comprobante=?");
    $upd->execute([
        ($es_boleta ? "Resumen de reversiones" : "Comunicación de baja") . " enviado. Ticket: {$ticket}",
        $id_comprobante
    ]);

    echo "\n✅ DOCUMENTO ACEPTADO POR SUNAT\n";
    echo "   Ticket: {$ticket}\n";
    echo "   Correlativo: {$correlativo_baja}\n";
    echo "   Comprobante: {$comprobante['serie']}-{$comprobante['correlativo']}\n";
    echo "   Tipo: " . ($es_boleta ? "Resumen de Reversiones (RC)" : "Comunicación de Baja (RA)") . "\n";
    echo "\n⚠️  IMPORTANTE: Debes consultar el ticket después para confirmar la anulación.\n";
    echo "   El proceso puede tardar unos minutos en SUNAT.\n";
    echo "\n📌 SIGUIENTE PASO:\n";
    echo "   php consultar_ticket.php {$ticket} {$id_comprobante}\n";

} else {
    $error = $result->getError();
    
    // Registrar el error
    $upd = $pdo->prepare("UPDATE comprobantes 
                          SET codigo_respuesta_sunat=?,
                              descripcion_respuesta_sunat=?,
                              estado_sunat='ERROR'
                          WHERE id_comprobante=?");
    $upd->execute([
        $error->getCode(),
        "ERROR AL ANULAR: " . $error->getMessage(),
        $id_comprobante
    ]);
    
    echo "\n❌ ERROR AL ENVIAR DOCUMENTO DE BAJA\n";
    echo "   Código: {$error->getCode()}\n";
    echo "   Mensaje: {$error->getMessage()}\n";
}
?>