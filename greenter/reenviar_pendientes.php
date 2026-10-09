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

/**
 * Reporta los PENDIENTE que requieren atención:
 * - en plazo pero con más de 24 h sin aceptarse (la cola los sigue reintentando)
 * - fuera del plazo de 3 días (la cola NO los envía: revisión manual)
 */
function emitirAlertasPendientes($pdo, $logPath)
{
    $sql = "SELECT serie, correlativo, fecha_emision,
                   (fecha_emision >= CURDATE() - INTERVAL 3 DAY) AS en_plazo
            FROM comprobantes
            WHERE estado_documento = 'ACTIVO'
              AND estado_sunat = 'PENDIENTE'
              AND TIMESTAMPDIFF(HOUR, COALESCE(created_at, fecha_emision), NOW()) >= 24
            ORDER BY fecha_emision DESC";
    $filas = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    $enPlazo = array_filter($filas, function ($r) { return (int)$r['en_plazo'] === 1; });
    $vencidos = array_filter($filas, function ($r) { return (int)$r['en_plazo'] !== 1; });

    $resumir = function ($lista) {
        $nums = array_map(function ($r) {
            return $r['serie'] . '-' . $r['correlativo'] . ' (' . $r['fecha_emision'] . ')';
        }, array_slice(array_values($lista), 0, 10));
        $resto = count($lista) - count($nums);
        return implode(', ', $nums) . ($resto > 0 ? " y {$resto} más" : '');
    };

    $msg = '';
    if (!empty($enPlazo)) {
        $msg .= "🚨 ALERTA: " . count($enPlazo) . " comprobante(s) PENDIENTE con más de 24 h, aún en plazo "
            . "(3 días desde la emisión). La cola sigue reintentando: " . $resumir($enPlazo) . "\n";
    }
    if (!empty($vencidos)) {
        $msg .= "🛑 REVISIÓN MANUAL: " . count($vencidos) . " comprobante(s) PENDIENTE fuera del plazo de 3 días. "
            . "La cola NO los envía. Más recientes: " . $resumir($vencidos) . "\n";
    }
    if ($msg !== '') {
        echo "\n{$msg}";
        file_put_contents($logPath, $msg, FILE_APPEND);
    }
}

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
              -- Solo dentro del plazo SUNAT (3 días calendario); los vencidos requieren revisión manual
              AND c.fecha_emision >= CURDATE() - INTERVAL 3 DAY
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
        emitirAlertasPendientes($pdo, $logPath);
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
    // Si SUNAT falla varias veces seguidas, está caído: cortar la ronda y esperar al siguiente CRON
    $fallosTemporalesSeguidos = 0;
    $maxFallosTemporalesSeguidos = 3;

    foreach ($pendientes as $row) {
        $id = (int)$row['id_comprobante'];
        $numero = $row['serie'] . '-' . str_pad((string)$row['correlativo'], 8, '0', STR_PAD_LEFT);

        echo "\n➡️ Reintentando: {$numero} (ID {$id})\n";

        // Un solo intento por comprobante: el siguiente reintento lo hace la próxima ronda del CRON
        $cmd = escapeshellarg($phpBin) . ' ' . escapeshellarg($scriptEnvio) . ' ' . $id . ' --intentos=1 2>&1';
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
            $fallosTemporalesSeguidos = 0;
        } elseif ($esTemporal) {
            $pend++;
            $estado = 'PENDIENTE';
            $fallosTemporalesSeguidos++;
        } else {
            $rej++;
            $estado = 'RECHAZADO/OTRO';
            $fallosTemporalesSeguidos = 0;
        }

        file_put_contents(
            $logPath,
            "ID: {$id} | NUM: {$numero} | RESULTADO: {$estado}\n" .
            "OUTPUT:\n{$output}\n\n",
            FILE_APPEND
        );

        if ($fallosTemporalesSeguidos >= $maxFallosTemporalesSeguidos) {
            $msgCorte = "⏸️ SUNAT falló {$fallosTemporalesSeguidos} veces seguidas. Se corta la ronda; se reintentará en el siguiente CRON.\n";
            echo "\n{$msgCorte}";
            file_put_contents($logPath, $msgCorte, FILE_APPEND);
            break;
        }
    }

    emitirAlertasPendientes($pdo, $logPath);

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
