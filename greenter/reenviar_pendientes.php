<?php
/**
 * Reenvio automatico de comprobantes PENDIENTE a SUNAT.
 *
 * Uso:
 *   php reenviar_pendientes.php [limite] [minutos_espera]
 *
 * Ejemplo:
 *   php reenviar_pendientes.php 25 3
 */

date_default_timezone_set('America/Lima');

require_once __DIR__ . '/../model/model_conexion.php';

$limite = isset($argv[1]) ? (int)$argv[1] : 25;
$minutosEspera = isset($argv[2]) ? (int)$argv[2] : 3;

if ($limite <= 0) {
    $limite = 25;
}
if ($limite > 200) {
    $limite = 200;
}
if ($minutosEspera < 1) {
    $minutosEspera = 1;
}

$tmpDir = __DIR__ . '/tmp';
if (!is_dir($tmpDir)) {
    mkdir($tmpDir, 0777, true);
}

$lockPath = $tmpDir . '/reenvio_pendientes.lock';
$lockHandle = fopen($lockPath, 'c');
if ($lockHandle === false) {
    echo "❌ No se pudo crear/abrir lock file: {$lockPath}\n";
    exit(1);
}

if (!flock($lockHandle, LOCK_EX | LOCK_NB)) {
    echo "ℹ️ Ya hay un proceso de reenvio en ejecucion. Saliendo.\n";
    fclose($lockHandle);
    exit(0);
}

$logPath = __DIR__ . '/auto_reenvio_log.txt';
$inicio = date('Y-m-d H:i:s');

file_put_contents(
    $logPath,
    "=========================\n" .
    "INICIO: {$inicio}\n" .
    "LIMITE: {$limite}\n" .
    "ESPERA(MIN): {$minutosEspera}\n",
    FILE_APPEND
);

try {
    $db = new conexionBD();
    $pdo = $db->conexionPDO();

    if (!$pdo) {
        throw new Exception('No se pudo abrir conexion a BD.');
    }

    $sql = "SELECT 
                c.id_comprobante,
                c.tipo_comprobante,
                c.serie,
                c.correlativo,
                c.estado_sunat,
                c.fecha_envio_sunat
            FROM comprobantes c
            WHERE c.estado_documento = 'ACTIVO'
              AND c.estado_sunat = 'PENDIENTE'
              AND (
                    c.fecha_envio_sunat IS NULL
                    OR TIMESTAMPDIFF(MINUTE, c.fecha_envio_sunat, NOW()) >= ?
                  )
            ORDER BY COALESCE(c.fecha_envio_sunat, c.created_at, c.fecha_emision) ASC
            LIMIT {$limite}";

    $query = $pdo->prepare($sql);
    $query->execute([$minutosEspera]);
    $pendientes = $query->fetchAll(PDO::FETCH_ASSOC);

    $total = count($pendientes);
    echo "📋 Pendientes a reintentar: {$total}\n";
    file_put_contents($logPath, "PENDIENTES: {$total}\n", FILE_APPEND);

    if ($total === 0) {
        file_put_contents($logPath, "FIN: " . date('Y-m-d H:i:s') . " (sin pendientes)\n\n", FILE_APPEND);
        flock($lockHandle, LOCK_UN);
        fclose($lockHandle);
        exit(0);
    }

    $phpBin = PHP_BINARY ?: 'php';
    $scriptEnvio = __DIR__ . '/factura_bd.php';

    $ok = 0;
    $pend = 0;
    $rej = 0;

    foreach ($pendientes as $row) {
        $id = (int)$row['id_comprobante'];
        $numero = $row['serie'] . '-' . str_pad((string)$row['correlativo'], 8, '0', STR_PAD_LEFT);

        echo "\n➡️ Reintentando: {$numero} (ID {$id})\n";

        $cmd = escapeshellarg($phpBin) . ' ' . escapeshellarg($scriptEnvio) . ' ' . $id . ' 2>&1';
        $output = shell_exec($cmd);
        if ($output === null) {
            $output = '';
        }

        $outLower = strtolower($output);
        $esAceptado = (
            strpos($outLower, 'aceptado por sunat') !== false ||
            strpos($outLower, 'ha sido aceptad') !== false
        );
        $esTemporal = (
            strpos($outLower, 'error transitorio sunat') !== false ||
            strpos($outLower, 'internal server error') !== false ||
            strpos($outLower, 'service unavailable') !== false ||
            strpos($outLower, 'timeout') !== false
        );

        if ($esAceptado) {
            $ok++;
            $estado = 'ACEPTADO';
        } elseif ($esTemporal) {
            $pend++;
            $estado = 'PENDIENTE';
        } else {
            $rej++;
            $estado = 'RECHAZADO/OTRO';
        }

        file_put_contents(
            $logPath,
            "ID: {$id} | NUM: {$numero} | RESULTADO: {$estado}\n" .
            "OUTPUT:\n{$output}\n\n",
            FILE_APPEND
        );
    }

    $fin = date('Y-m-d H:i:s');
    $resumen = "RESUMEN => ACEPTADOS: {$ok} | PENDIENTES: {$pend} | OTROS: {$rej}\nFIN: {$fin}\n\n";
    echo "\n{$resumen}";
    file_put_contents($logPath, $resumen, FILE_APPEND);

} catch (Exception $e) {
    $msg = "❌ ERROR reenviar_pendientes: " . $e->getMessage() . "\n";
    echo $msg;
    file_put_contents($logPath, $msg . "\n", FILE_APPEND);
    flock($lockHandle, LOCK_UN);
    fclose($lockHandle);
    exit(1);
}

flock($lockHandle, LOCK_UN);
fclose($lockHandle);
exit(0);
