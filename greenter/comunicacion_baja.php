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
// CONFIGURAR ZONA HORARIA
// =============================================
date_default_timezone_set('America/Lima');

// =============================================
// 1️⃣ CONEXIÓN BD
// =============================================
$db = new conexionBD();
$pdo = $db->conexionPDO();

// =============================================
// 2️⃣ OBTENER PARÁMETROS
// =============================================
$id_comprobante = isset($argv[1]) && is_numeric($argv[1]) ? (int)$argv[1] : null;
$correlativo_baja = isset($argv[2]) ? $argv[2] : null;

if (!$id_comprobante || !$correlativo_baja) {
    die("❌ ERROR: Faltan parámetros. Uso: php comunicacion_baja.php [ID_COMPROBANTE] [CORRELATIVO_BAJA]\n");
}

echo "\n========================================\n";
echo "   COMUNICACIÓN DE BAJA - GREENTER\n";
echo "========================================\n";
echo "Fecha/Hora: " . date('Y-m-d H:i:s') . "\n";
echo "ID Comprobante: $id_comprobante\n";
echo "Correlativo Baja: $correlativo_baja\n";

// =============================================
// 3️⃣ OBTENER COMPROBANTE
// =============================================
$sql = "SELECT c.*, 
               cl.tipo_documento, 
               cl.numero_documento, 
               cl.razon_social
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
// 4️⃣ VALIDACIONES
// =============================================
if (!in_array($comprobante['tipo_comprobante'], ['01', '03'])) {
    die("❌ ERROR: Solo se pueden anular FACTURAS (01) o BOLETAS (03). Tipo: {$comprobante['tipo_comprobante']}\n");
}

if (!in_array($comprobante['estado_sunat'], ['ACEPTADO', 'ENVIADO'])) {
    die("❌ ERROR: El comprobante debe estar ACEPTADO. Estado: {$comprobante['estado_sunat']}\n");
}

$es_boleta = ($comprobante['tipo_comprobante'] == '03');
$tipo_doc = $es_boleta ? 'BOLETA' : 'FACTURA';
$tipo_comunicacion = $es_boleta ? 'Resumen de Reversiones (RC)' : 'Comunicación de Baja (RA)';

echo "Tipo: $tipo_doc\n";
echo "Serie-Correlativo: {$comprobante['serie']}-{$comprobante['correlativo']}\n";
echo "Comunicación: $tipo_comunicacion\n";

// =============================================
// 5️⃣ PROCESAR FECHAS CORRECTAMENTE
// =============================================
// CRÍTICO: Crear objetos DateTime en zona horaria de Perú
$fecha_hoy = new DateTime('now', new DateTimeZone('America/Lima'));

// Obtener fecha de emisión del comprobante
$fecha_emision_str = $comprobante['fecha_emision']; // Formato: YYYY-MM-DD
echo "Fecha emisión DB: $fecha_emision_str\n";

// Crear DateTime con la fecha de emisión
$fecha_emision = DateTime::createFromFormat('Y-m-d', $fecha_emision_str, new DateTimeZone('America/Lima'));

if (!$fecha_emision) {
    die("❌ ERROR: Fecha de emisión inválida: $fecha_emision_str\n");
}

// Establecer hora a 00:00:00 para la fecha de emisión
$fecha_emision->setTime(0, 0, 0);

echo "Fecha emisión procesada: " . $fecha_emision->format('Y-m-d') . "\n";
echo "Fecha actual (hoy): " . $fecha_hoy->format('Y-m-d') . "\n";

// =============================================
// 6️⃣ VALIDAR PLAZO (SOLO BOLETAS)
// =============================================
$dias_transcurridos = $fecha_hoy->diff($fecha_emision)->days;

if ($es_boleta && $dias_transcurridos > 7) {
    die("❌ ERROR: Boletas solo se pueden anular dentro de 7 días. Han transcurrido: {$dias_transcurridos} días.\n");
}

if ($es_boleta) {
    echo "Días transcurridos: {$dias_transcurridos}/7\n";
}

// =============================================
// 7️⃣ DATOS DE LA EMPRESA
// =============================================
$stmt = $pdo->query("SELECT * FROM empresa LIMIT 1");
$empresa = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$empresa) {
    die("❌ ERROR: No se encontraron datos de la empresa.\n");
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
// 8️⃣ PREPARAR GREENTER
// =============================================
$see = getSee();

// Extraer número del correlativo (ej: RA-20251221-004 -> 004)
$numero_correlativo = substr($correlativo_baja, strrpos($correlativo_baja, '-') + 1);

// =============================================
// 9️⃣ PREPARAR MOTIVO
// =============================================
$motivo_baja = $comprobante['observaciones'] ?? 'ANULACIÓN DE COMPROBANTE ELECTRÓNICO';

if (strpos($motivo_baja, '[MOTIVO ANULACIÓN]') !== false) {
    preg_match('/\[MOTIVO ANULACIÓN\]\s*(.+?)(\[|$)/s', $motivo_baja, $matches);
    if (isset($matches[1])) {
        $motivo_baja = trim($matches[1]);
    }
}

// Limpiar saltos de línea y caracteres extraños
$motivo_baja = preg_replace('/\s+/', ' ', $motivo_baja);
$motivo_baja = substr($motivo_baja, 0, 100);

if (empty(trim($motivo_baja))) {
    $motivo_baja = 'ANULACIÓN DE COMPROBANTE ELECTRÓNICO';
}

echo "Motivo: $motivo_baja\n";

// =============================================
// 🔟 GENERAR DOCUMENTO SEGÚN TIPO
// =============================================
echo "\n🔄 Generando $tipo_comunicacion...\n";

if ($es_boleta) {
    // ========================================
    // BOLETAS: Summary (Resumen de Reversiones - RC)
    // ========================================
    echo "📋 Generando Summary (RC)\n";
    
    $detail = (new SummaryDetail())
        ->setTipoDoc('03')
        ->setSerieNro($comprobante['serie'] . '-' . str_pad($comprobante['correlativo'], 8, '0', STR_PAD_LEFT))
        ->setEstado('3')  // 3 = Anulado
        ->setClienteTipo($comprobante['tipo_documento'])
        ->setClienteNro($comprobante['numero_documento'])
        ->setTotal((float)$comprobante['total'])
        ->setMtoOperGravadas((float)($comprobante['total_gravada'] ?? 0))
        ->setMtoIGV((float)($comprobante['total_igv'] ?? 0));
    
    // FECHAS PARA SUMMARY:
    // - FecGeneracion: Fecha actual (HOY)
    // - FecResumen: Fecha de emisión de las boletas a anular
    $summary = (new Summary())
        ->setFecGeneracion($fecha_hoy)          // ✅ HOY
        ->setFecResumen($fecha_emision)         // ✅ Fecha de emisión boleta
        ->setCorrelativo($numero_correlativo)
        ->setCompany($company)
        ->setDetails([$detail]);
    
    echo "  FecGeneracion: {$fecha_hoy->format('Y-m-d')}\n";
    echo "  FecResumen: {$fecha_emision->format('Y-m-d')}\n";
    
    $xml = $see->getXmlSigned($summary);
    $result = $see->send($summary);
    
} else {
    // ========================================
    // FACTURAS: Voided (Comunicación de Baja - RA)
    // ========================================
    echo "📋 Generando Voided (RA)\n";

    $detail = (new VoidedDetail())
        ->setTipoDoc('01')
        ->setSerie($comprobante['serie'])
        ->setCorrelativo(str_pad($comprobante['correlativo'], 6, '0', STR_PAD_LEFT))
        ->setDesMotivoBaja($motivo_baja);

    $voided = (new Voided())
        ->setCorrelativo($numero_correlativo)
        ->setFecGeneracion($fecha_emision)      // ✅ Fecha de emisión de la factura
        ->setFecComunicacion($fecha_hoy)        // ✅ HOY
        ->setCompany($company)
        ->setDetails([$detail]);

    echo "  FecGeneracion (fecha factura): {$fecha_emision->format('Y-m-d')}\n";
    echo "  FecComunicacion (hoy): {$fecha_hoy->format('Y-m-d')}\n";
    echo "  Serie: {$comprobante['serie']}\n";
    echo "  Correlativo: " . str_pad($comprobante['correlativo'], 6, '0', STR_PAD_LEFT) . "\n";
    
    $xml = $see->getXmlSigned($voided);
    $result = $see->send($voided);
}

// =============================================
// 1️⃣1️⃣ GUARDAR XML
// =============================================
$xmlPath = __DIR__ . '/xml/';
if (!is_dir($xmlPath)) {
    mkdir($xmlPath, 0777, true);
}

$nombreXml = $correlativo_baja . '.xml';
file_put_contents($xmlPath . $nombreXml, $xml);
echo "✅ XML guardado: xml/{$nombreXml}\n";

// =============================================
// 1️⃣2️⃣ PROCESAR RESPUESTA
// =============================================
echo "\n📤 Enviando a SUNAT...\n";

if ($result->isSuccess()) {
    $ticket = $result->getTicket();
    
    echo "\n✅ DOCUMENTO ACEPTADO POR SUNAT\n";
    echo "========================================\n";
    echo "Ticket: $ticket\n";
    echo "Correlativo: $correlativo_baja\n";
    echo "Comprobante: {$comprobante['serie']}-{$comprobante['correlativo']}\n";
    echo "Tipo: $tipo_comunicacion\n";
    
    // =============================================
    // 🆕 GUARDAR EN TABLA comunicaciones_baja
    // =============================================
    try {
        $sqlInsert = "INSERT INTO comunicaciones_baja 
                      (id_comprobante, correlativo_baja, ticket_sunat, estado, fecha_comunicacion)
                      VALUES (?, ?, ?, 'PENDIENTE', NOW())";
        $stmtInsert = $pdo->prepare($sqlInsert);
        $stmtInsert->execute([$id_comprobante, $correlativo_baja, $ticket]);
        echo "✅ Registro guardado en tabla comunicaciones_baja\n";
    } catch (Exception $e) {
        echo "⚠️ Advertencia: No se pudo guardar en comunicaciones_baja: {$e->getMessage()}\n";
    }
    
    // Actualizar BD con ticket
    $upd = $pdo->prepare("UPDATE comprobantes 
                          SET codigo_respuesta_sunat='TICKET',
                              descripcion_respuesta_sunat=?
                          WHERE id_comprobante=?");
    $upd->execute([
        "$tipo_comunicacion enviado. Ticket: $ticket",
        $id_comprobante
    ]);
    
    echo "\n⚠️  IMPORTANTE:\n";
    echo "El ticket será consultado automáticamente por el proceso programado.\n";
    echo "También puedes consultar manualmente:\n";
    echo "php consultar_ticket.php $ticket $id_comprobante\n";
    echo "========================================\n";

} else {
    $error = $result->getError();
    
    echo "\n❌ ERROR AL ENVIAR DOCUMENTO\n";
    echo "========================================\n";
    echo "Código: {$error->getCode()}\n";
    echo "Mensaje: {$error->getMessage()}\n";
    echo "========================================\n";
    
    // Registrar error en BD
    $upd = $pdo->prepare("UPDATE comprobantes 
                          SET codigo_respuesta_sunat=?,
                              descripcion_respuesta_sunat=?
                          WHERE id_comprobante=?");
    $upd->execute([
        $error->getCode(),
        "ERROR: " . $error->getMessage(),
        $id_comprobante
    ]);
}
?>