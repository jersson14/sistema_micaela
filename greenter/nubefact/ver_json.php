<?php
/**
 * Muestra el JSON que se enviaría a Nubefact para un comprobante, SIN enviarlo.
 * Sirve para revisar los datos antes de la primera prueba.
 *
 * Uso: php greenter/nubefact/ver_json.php ID_COMPROBANTE
 */
require_once __DIR__ . '/../../model/model_conexion.php';
require_once __DIR__ . '/nubefact_api.php';

$id = isset($argv[1]) ? (int)$argv[1] : 0;
if ($id <= 0) {
    exit("Uso: php greenter/nubefact/ver_json.php ID_COMPROBANTE\n");
}

$pdo = (new conexionBD())->conexionPDO();
$stmt = $pdo->prepare("SELECT c.*, cl.tipo_documento, cl.numero_documento, cl.razon_social, cl.direccion
                       FROM comprobantes c
                       INNER JOIN cliente_sunat cl ON c.id_cliente = cl.id_cliente
                       WHERE c.id_comprobante = ?");
$stmt->execute([$id]);
$comprobante = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$comprobante) {
    exit("No existe el comprobante $id\n");
}

if (!nubefactActivo()) {
    echo "(Aviso: .env.nubefact no está configurado; se muestran las series sin mapear)\n\n";
}

try {
    echo json_encode(nubefactArmarComprobante($pdo, $comprobante), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n";
} catch (RuntimeException $e) {
    echo "❌ " . $e->getMessage() . "\n";
}
