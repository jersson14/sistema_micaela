<?php
/**
 * Emite un comprobante REAL de prueba por S/ 1.00 con una serie de prueba (F999 / B999)
 * a través de Nubefact, sin chocar con la numeración de producción (FPP1 / BPP1).
 *
 * Uso:
 *   php greenter/nubefact/emitir_prueba.php factura [id_cliente]   (por defecto: cliente 71, tu RUC)
 *   php greenter/nubefact/emitir_prueba.php boleta  [id_cliente]   (por defecto: cliente 4, tu DNI)
 *   php greenter/nubefact/emitir_prueba.php nc [id_comprobante]     nota de crédito (anulación) de S/ 1.00
 *   php greenter/nubefact/emitir_prueba.php nd [id_comprobante]     nota de débito (aumento) de S/ 1.00
 *     Por defecto la nota se hace sobre la última factura de prueba (F999) aceptada.
 *     Series de prueba: FN99/FD99 (sobre factura) y BN99/BD99 (sobre boleta).
 *
 * El comprobante queda en la lista del sistema: desde ahí se puede descargar el XML/CDR,
 * imprimir el ticket y ANULARLO con el botón de siempre.
 *
 * Requisitos: .env.nubefact activo y las series F999 / B999 creadas en el panel de Nubefact.
 */
require_once __DIR__ . '/../../model/model_conexion.php';
require_once __DIR__ . '/nubefact_api.php';

date_default_timezone_set('America/Lima');

$tipos = [
    'factura' => ['tipo' => '01', 'serie' => 'F999', 'cliente' => 71],
    'boleta'  => ['tipo' => '03', 'serie' => 'B999', 'cliente' => 4],
    'nc'      => ['tipo' => '07'],
    'nd'      => ['tipo' => '08'],
];
$clave = strtolower($argv[1] ?? '');
if (!isset($tipos[$clave])) {
    exit("Uso: php greenter/nubefact/emitir_prueba.php factura|boleta [id_cliente]  |  nc|nd [id_comprobante]\n");
}

// Seguridad: sin Nubefact, una serie de prueba saldría directo a SUNAT
if (!nubefactActivo()) {
    exit("❌ Nubefact no está activo (.env.nubefact). Esta prueba solo se envía por Nubefact.\n");
}

$tipo = $tipos[$clave]['tipo'];
$esNota = in_array($tipo, ['07', '08'], true);

$pdo = (new conexionBD())->conexionPDO();
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$referencia = null;
if ($esNota) {
    // Documento que se modifica: el indicado, o la última factura de prueba aceptada
    if (isset($argv[2])) {
        $referencia = $pdo->prepare("SELECT * FROM comprobantes WHERE id_comprobante = ?");
        $referencia->execute([(int)$argv[2]]);
    } else {
        $referencia = $pdo->query("SELECT * FROM comprobantes WHERE serie = 'F999' AND estado_sunat = 'ACEPTADO' ORDER BY id_comprobante DESC LIMIT 1");
    }
    $referencia = $referencia->fetch();
    if (!$referencia || !in_array($referencia['tipo_comprobante'], ['01', '03'], true)) {
        exit("❌ No se encontró la factura o boleta a modificar. Indique su ID: emitir_prueba.php $clave <id_comprobante>\n");
    }
    if ($referencia['estado_sunat'] !== 'ACEPTADO') {
        exit("❌ {$referencia['numero_completo']} no está ACEPTADO; primero debe estar aceptado en SUNAT.\n");
    }
    $serie = ($referencia['tipo_comprobante'] === '01' ? 'F' : 'B') . ($tipo === '07' ? 'N99' : 'D99');
    $idCliente = (int)$referencia['id_cliente'];
} else {
    $serie = $tipos[$clave]['serie'];
    $idCliente = isset($argv[2]) ? (int)$argv[2] : $tipos[$clave]['cliente'];
}

$cliente = $pdo->prepare("SELECT tipo_documento, numero_documento, razon_social FROM cliente_sunat WHERE id_cliente = ?");
$cliente->execute([$idCliente]);
$cliente = $cliente->fetch();
if (!$cliente) {
    exit("❌ No existe el cliente $idCliente\n");
}

// Comprobante base: se copian servicio, ruta, chofer, usuario y forma de pago
// (en las notas, del documento que se modifica)
if ($esNota) {
    $base = $referencia;
} else {
    $base = $pdo->prepare("SELECT * FROM comprobantes WHERE tipo_comprobante = ? AND serie <> ? ORDER BY id_comprobante DESC LIMIT 1");
    $base->execute([$tipo, $serie]);
    $base = $base->fetch();
}
if (!$base) {
    exit("❌ No hay un comprobante base de tipo $tipo para copiar sus datos.\n");
}

$correlativo = $pdo->prepare("SELECT COALESCE(MAX(CAST(correlativo AS UNSIGNED)), 0) + 1 FROM comprobantes WHERE serie = ?");
$correlativo->execute([$serie]);
$correlativo = (int)$correlativo->fetchColumn();
$numeroCompleto = $serie . '-' . str_pad($correlativo, 8, '0', STR_PAD_LEFT);

$pdo->beginTransaction();

$ins = $pdo->prepare("INSERT INTO comprobantes
    (tipo_comprobante, serie, correlativo, numero_completo, fecha_emision, hora_emision, id_cliente, moneda,
     subtotal, total_gravada, total_igv, total_impuestos, total, observaciones, leyendas,
     estado_sunat, estado_documento, id_servicio, id_usuario, id_tipo_pago, tipo_operacion,
     id_origen, iddestino, idconductor, id_comprobante_origen, motivo_nota, created_at)
    VALUES (?, ?, ?, ?, CURDATE(), CURTIME(), ?, ?,
            0.85, 0.85, 0.15, 0.15, 1.00, 'PRUEBA NUBEFACT', 'SON UNO CON 00/100 SOLES',
            'PENDIENTE', 'ACTIVO', ?, ?, ?, ?,
            ?, ?, ?, ?, ?, NOW())");
$motivo = [
    '07' => 'PRUEBA NUBEFACT - ANULACION DE LA OPERACION',
    '08' => 'PRUEBA NUBEFACT - AUMENTO EN EL VALOR',
][$tipo] ?? null;
$ins->execute([
    $tipo, $serie, $correlativo, $numeroCompleto, $idCliente, $base['moneda'] ?: 'PEN',
    $base['id_servicio'], $base['id_usuario'], $base['id_tipo_pago'], $base['tipo_operacion'],
    $base['id_origen'], $base['iddestino'], $base['idconductor'],
    $esNota ? $referencia['id_comprobante'] : null, $motivo,
]);
$idNuevo = (int)$pdo->lastInsertId();

// Detalle: una línea de S/ 1.00 (0.85 + IGV 0.15) a partir del primer ítem del comprobante base
$det = $pdo->prepare("SELECT * FROM comprobante_detalle WHERE id_comprobante = ? ORDER BY orden_item LIMIT 1");
$det->execute([$base['id_comprobante']]);
$det = $det->fetch() ?: ['codigo_producto' => 'SERV-1', 'descripcion' => 'SERVICIO DE TRANSPORTE', 'unidad_medida' => 'NIU'];
unset($det['id_detalle'], $det['updated_at']);
$det = array_merge($det, [
    'id_comprobante' => $idNuevo,
    'orden_item' => 1,
    'descripcion' => 'PRUEBA NUBEFACT - ' . ($det['descripcion'] ?? 'SERVICIO'),
    'cantidad' => 1,
    'precio_unitario' => 1.00,
    'valor_unitario' => 0.85,
    'valor_venta' => 0.85,
    'afectacion_igv' => '10',
    'porcentaje_igv' => 18,
    'igv' => 0.15,
    'total_impuestos_item' => 0.15,
    'total_item' => 1.00,
    'descuento' => 0,
    'created_at' => date('Y-m-d H:i:s'),
]);
$columnas = array_keys($det);
$pdo->prepare("INSERT INTO comprobante_detalle (" . implode(', ', $columnas) . ") VALUES (" . implode(', ', array_fill(0, count($columnas), '?')) . ")")
    ->execute(array_values($det));

$pdo->commit();

echo "🧾 Creado {$numeroCompleto} (ID {$idNuevo}) por S/ 1.00 para {$cliente['razon_social']} ({$cliente['numero_documento']})\n";
if ($esNota) {
    echo "   Modifica a: {$referencia['numero_completo']} (ID {$referencia['id_comprobante']})\n";
}
echo "\n";

// Envío por el mismo camino que la pantalla
passthru(escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg(__DIR__ . '/../factura_bd.php') . ' ' . $idNuevo . ' --intentos=1');

echo "\nAhora puedes verlo en la lista de comprobantes del sistema: descargar XML/CDR, imprimir el ticket y ANULARLO.\n";
