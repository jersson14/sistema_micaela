<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../model/model_conexion.php';
require_once __DIR__ . '/config/config_greenter.php';

use Greenter\See;
use Greenter\Ws\Services\ConsultCdrService;

// =============================================
// 1️⃣ CONEXIÓN BD
// =============================================
$db = new conexionBD();
$pdo = $db->conexionPDO();

// =============================================
// 2️⃣ OBTENER ID DEL COMPROBANTE
// =============================================
$id_comprobante = $argv[1] ?? null;

if (!$id_comprobante || !is_numeric($id_comprobante)) {
    die("❌ Uso: php verificar_estado_sunat.php [ID_COMPROBANTE]\n");
}

// =============================================
// 3️⃣ OBTENER DATOS DEL COMPROBANTE
// =============================================
$sql = "SELECT c.*, e.ruc as ruc_emisor
        FROM comprobantes c
        CROSS JOIN empresa e
        WHERE c.id_comprobante = ?
        LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_comprobante]);
$comp = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$comp) {
    die("❌ No se encontró el comprobante ID: $id_comprobante\n");
}

echo "\n" . str_repeat("=", 70) . "\n";
echo "   VERIFICACIÓN DE ESTADO EN SUNAT\n";
echo str_repeat("=", 70) . "\n\n";

echo "📄 Documento: {$comp['tipo_comprobante']} {$comp['serie']}-{$comp['correlativo']}\n";
echo "🏢 RUC Emisor: {$comp['ruc_emisor']}\n";
echo "📅 Fecha emisión: {$comp['fecha_emision']}\n";
echo "💰 Total: S/ {$comp['total']}\n\n";

// =============================================
// 4️⃣ VERIFICAR ESTADO LOCAL
// =============================================
echo "📊 ESTADO LOCAL (Base de Datos):\n";
echo str_repeat("-", 70) . "\n";
echo "Estado SUNAT: " . ($comp['estado_sunat'] ?? 'PENDIENTE') . "\n";
echo "Código respuesta: " . ($comp['codigo_respuesta_sunat'] ?? 'N/A') . "\n";
echo "Descripción: " . ($comp['descripcion_respuesta_sunat'] ?? 'N/A') . "\n";
echo "Hash: " . ($comp['codigo_hash'] ?? 'NO GENERADO') . "\n";
echo "Fecha envío: " . ($comp['fecha_envio_sunat'] ?? 'NO ENVIADO') . "\n\n";

// =============================================
// 5️⃣ VERIFICAR ARCHIVOS FÍSICOS
// =============================================
echo "📁 ARCHIVOS GENERADOS:\n";
echo str_repeat("-", 70) . "\n";

$xmlPath = __DIR__ . '/xml/' . $comp['serie'] . '-' . $comp['correlativo'] . '.xml';
$cdrPath = __DIR__ . '/cdr/R-' . $comp['serie'] . '-' . $comp['correlativo'] . '.zip';

if (file_exists($xmlPath)) {
    $xmlSize = filesize($xmlPath);
    $xmlDate = date('Y-m-d H:i:s', filemtime($xmlPath));
    echo "✅ XML: {$xmlPath}\n";
    echo "   Tamaño: " . number_format($xmlSize) . " bytes\n";
    echo "   Modificado: {$xmlDate}\n\n";
} else {
    echo "❌ XML no encontrado: {$xmlPath}\n\n";
}

if (file_exists($cdrPath)) {
    $cdrSize = filesize($cdrPath);
    $cdrDate = date('Y-m-d H:i:s', filemtime($cdrPath));
    echo "✅ CDR: {$cdrPath}\n";
    echo "   Tamaño: " . number_format($cdrSize) . " bytes\n";
    echo "   Modificado: {$cdrDate}\n\n";
} else {
    echo "❌ CDR no encontrado: {$cdrPath}\n\n";
}

// =============================================
// 6️⃣ VERIFICAR DOCUMENTO DE REFERENCIA (para NC/ND)
// =============================================
if (in_array($comp['tipo_comprobante'], ['07', '08'])) {
    echo "📌 DOCUMENTO DE REFERENCIA:\n";
    echo str_repeat("-", 70) . "\n";
    
    if ($comp['id_comprobante_origen']) {
        $sqlRef = "SELECT tipo_comprobante, serie, correlativo, estado_sunat, 
                          codigo_respuesta_sunat, descripcion_respuesta_sunat
                   FROM comprobantes 
                   WHERE id_comprobante = ?";
        $stmtRef = $pdo->prepare($sqlRef);
        $stmtRef->execute([$comp['id_comprobante_origen']]);
        $ref = $stmtRef->fetch(PDO::FETCH_ASSOC);
        
        if ($ref) {
            echo "Tipo: {$ref['tipo_comprobante']} ({$ref['serie']}-{$ref['correlativo']})\n";
            echo "Estado: " . ($ref['estado_sunat'] ?? 'PENDIENTE') . "\n";
            echo "Código: " . ($ref['codigo_respuesta_sunat'] ?? 'N/A') . "\n";
            echo "Descripción: " . ($ref['descripcion_respuesta_sunat'] ?? 'N/A') . "\n\n";
            
            if ($ref['estado_sunat'] != 'ACEPTADO') {
                echo "⚠️  ADVERTENCIA: El documento de referencia NO está ACEPTADO.\n";
                echo "   Esto puede impedir que la NC/ND aparezca en SUNAT.\n\n";
            }
        } else {
            echo "❌ ERROR: No se encontró el documento de referencia (ID: {$comp['id_comprobante_origen']})\n\n";
        }
    } else {
        echo "❌ ERROR: No hay documento de referencia asignado (id_comprobante_origen NULL)\n\n";
    }
}

// =============================================
// 7️⃣ VALIDAR CDR (Constancia de Recepción)
// =============================================
if ($comp['estado_sunat'] == 'ACEPTADO' && file_exists($cdrPath)) {
    echo "🔐 VALIDACIÓN DEL CDR (Constancia de Recepción):\n";
    echo str_repeat("-", 70) . "\n";
    
    echo "✅ CDR recibido de SUNAT\n";
    echo "   Archivo: " . basename($cdrPath) . "\n";
    echo "   Tamaño: " . number_format(filesize($cdrPath)) . " bytes\n";
    echo "   Fecha: " . date('Y-m-d H:i:s', filemtime($cdrPath)) . "\n\n";
    
    echo "🎯 SIGNIFICADO DEL CDR:\n";
    echo "   El CDR (Constancia de Recepción) es la PRUEBA OFICIAL de que SUNAT\n";
    echo "   recibió, validó y ACEPTÓ tu comprobante electrónico.\n\n";
    echo "   Este archivo ZIP contiene:\n";
    echo "   - XML de respuesta firmado digitalmente por SUNAT\n";
    echo "   - Código de aceptación: {$comp['codigo_respuesta_sunat']}\n";
    echo "   - Mensaje: {$comp['descripcion_respuesta_sunat']}\n";
    echo "   - Hash del documento: {$comp['codigo_hash']}\n\n";
    
    echo "✅ Tu documento está 100% VÁLIDO y REGISTRADO en SUNAT.\n\n";
}

// =============================================
// 8️⃣ RECOMENDACIONES
// =============================================
echo "💡 RECOMENDACIONES:\n";
echo str_repeat("-", 70) . "\n";

$recomendaciones = [];

if ($comp['estado_sunat'] != 'ACEPTADO') {
    $recomendaciones[] = "El documento no está aceptado. Reenvía a SUNAT.";
}

if (empty($comp['codigo_hash'])) {
    $recomendaciones[] = "No hay código hash. El XML puede estar mal generado.";
}

if (!file_exists($cdrPath)) {
    $recomendaciones[] = "El CDR no existe. SUNAT no confirmó la recepción.";
}

if (in_array($comp['tipo_comprobante'], ['07', '08'])) {
    if (!$comp['id_comprobante_origen']) {
        $recomendaciones[] = "Falta documento de referencia. Asigna id_comprobante_origen.";
    } elseif (isset($ref) && $ref['estado_sunat'] != 'ACEPTADO') {
        $recomendaciones[] = "El documento de referencia no está aceptado en SUNAT.";
        $recomendaciones[] = "Primero acepta la factura/boleta antes de enviar la NC/ND.";
    }
}

if ($comp['estado_sunat'] == 'ACEPTADO' && file_exists($cdrPath)) {
    $horasDesdeEnvio = (strtotime('now') - strtotime($comp['fecha_envio_sunat'])) / 3600;
    
    if ($horasDesdeEnvio < 2) {
        $recomendaciones[] = "El documento se envió hace " . round($horasDesdeEnvio, 1) . " horas.";
        $recomendaciones[] = "Espera hasta 2 horas para que aparezca en el portal SUNAT.";
    } else {
        $recomendaciones[] = "El documento está aceptado y pasaron más de 2 horas.";
        $recomendaciones[] = "Busca en SUNAT: Operaciones → Comprobantes Electrónicos";
        $recomendaciones[] = "Usa: RUC {$comp['ruc_emisor']}, Serie {$comp['serie']}, Número {$comp['correlativo']}";
    }
}

if (empty($recomendaciones)) {
    echo "✅ Todo parece estar correcto.\n";
    echo "   Si no aparece en SUNAT, espera algunas horas o contacta a soporte.\n";
} else {
    foreach ($recomendaciones as $i => $rec) {
        echo ($i + 1) . ". {$rec}\n";
    }
}

echo "\n" . str_repeat("=", 70) . "\n";
echo "FIN DE LA VERIFICACIÓN\n";
echo str_repeat("=", 70) . "\n\n";