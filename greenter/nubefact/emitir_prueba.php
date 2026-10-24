<?php
/**
 * Emite un comprobante REAL de prueba por S/ 1.00 con una serie de prueba (F999 / B999)
 * a través de Nubefact, sin chocar con la numeración de producción (FPP1 / BPP1).
 *
 * Uso:
 *   php greenter/nubefact/emitir_prueba.php factura [id_cliente]   (por defecto: cliente 71, tu RUC)
 *   php greenter/nubefact/emitir_prueba.php boleta  [id_cliente]   (por defecto: cliente 4, tu DNI)
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
];
$clave = strtolower($argv[1] ?? '');
if (!isset($tipos[$clave])) {
    exit("Uso: php greenter/nubefact/emitir_prueba.php factura|boleta [id_cliente]\n");
}

// Seguridad: sin Nubefact, una serie de prueba saldría directo a SUNAT
if (!nubefactActivo()) {
    exit("❌ Nubefact no está activo (.env.nubefact). Esta prueba solo se envía por Nubefact.\n");
}

$tipo = $tipos[$clave]['tipo'];
$serie = $tipos[$clave]['serie'];
$idCliente = isset($argv[2]) ? (int)$argv[2] : $tipos[$clave]['cliente'];

$pdo = (new conexionBD())->conexionPDO();
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$cliente = $pdo->prepare("SELECT tipo_documento, numero_documento, razon_social FROM cliente_sunat WHERE id_cliente = ?");
$cliente->execute([$idCliente]);
$cliente = $cliente->fetch();
if (!$cliente) {
    exit("❌ No existe el cliente $idCliente\n");
}

// Comprobante base del mismo tipo: se copian servicio, ruta, chofer, usuario y forma de pago
$base = $pdo->prepare("SELECT * FROM comprobantes WHERE tipo_comprobante = ? AND serie <> ? ORDER BY id_comprobante DESC LIMIT 1");
$base->execute([$tipo, $serie]);
$base = $base->fetch();
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
     id_origen, iddestino, idconductor, created_at)
    VALUES (?, ?, ?, ?, CURDATE(), CURTIME(), ?, ?,
            0.85, 0.85, 0.15, 0.15, 1.00, 'PRUEBA NUBEFACT', 'SON UNO CON 00/100 SOLES',
            'PENDIENTE', 'ACTIVO', ?, ?, ?, ?,
            ?, ?, ?, NOW())");
$ins->execute([
    $tipo, $serie, $correlativo, $numeroCompleto, $idCliente, $base['moneda'] ?: 'PEN',
    $base['id_servicio'], $base['id_usuario'], $base['id_tipo_pago'], $base['tipo_operacion'],
    $base['id_origen'], $base['iddestino'], $base['idconductor'],
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

echo "🧾 Creado {$numeroCompleto} (ID {$idNuevo}) por S/ 1.00 para {$cliente['razon_social']} ({$cliente['numero_documento']})\n\n";

// Envío por el mismo camino que la pantalla
passthru(escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg(__DIR__ . '/../factura_bd.php') . ' ' . $idNuevo . ' --intentos=1');

echo "\nAhora puedes verlo en la lista de comprobantes del sistema: descargar XML/CDR, imprimir el ticket y ANULARLO.\n";
