<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../model/model_conexion.php';
require_once __DIR__ . '/../model/model_comprobante.php';
require_once __DIR__ . '/config/config_greenter.php';

// =============================================
// CONFIGURAR ZONA HORARIA
// =============================================
date_default_timezone_set('America/Lima');

// =============================================
// 1️⃣ OBTENER PARÁMETROS
// =============================================
$ticket = isset($argv[1]) ? $argv[1] : null;
$id_comprobante = isset($argv[2]) && is_numeric($argv[2]) ? (int)$argv[2] : null;

if (!$ticket || !$id_comprobante) {
    die("❌ ERROR: Faltan parámetros. Uso: php consultar_ticket.php [TICKET] [ID_COMPROBANTE]\n");
}

echo "\n========================================\n";
echo "   CONSULTA DE TICKET - GREENTER\n";
echo "========================================\n";
echo "Fecha/Hora: " . date('Y-m-d H:i:s') . "\n";
echo "Ticket: $ticket\n";
echo "ID Comprobante: $id_comprobante\n";

// =============================================
// 2️⃣ OBTENER COMPROBANTE
// =============================================
$db = new conexionBD();
$pdo = $db->conexionPDO();
$MC = new Modelo_Comprobantes();

$sql = "SELECT * FROM comprobantes WHERE id_comprobante = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_comprobante]);
$comprobante = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$comprobante) {
    die("❌ No se encontró el comprobante con ID: $id_comprobante.\n");
}

echo "Comprobante: {$comprobante['serie']}-{$comprobante['correlativo']}\n";
echo "Tipo: " . ($comprobante['tipo_comprobante'] == '03' ? 'BOLETA' : 'FACTURA') . "\n";

// =============================================
// 3️⃣ CONSULTAR TICKET EN SUNAT
// =============================================
$see = getSee();

echo "\n🔍 Consultando ticket en SUNAT...\n";

try {
    $result = $see->getStatus($ticket);
    
    if ($result->isSuccess()) {
        $cdr = $result->getCdrResponse();
        $code = $cdr->getCode();
        $description = $cdr->getDescription();
        
        echo "\n✅ RESPUESTA DE SUNAT RECIBIDA\n";
        echo "========================================\n";
        echo "Código: $code\n";
        echo "Descripción: $description\n";
        
        // Guardar CDR
        $cdrZip = $result->getCdrZip();
        if ($cdrZip) {
            $cdrPath = __DIR__ . '/cdr/';
            if (!is_dir($cdrPath)) {
                mkdir($cdrPath, 0777, true);
            }
            
            // Obtener correlativo de baja
            $sql_cb = "SELECT correlativo_baja FROM comunicaciones_baja 
                       WHERE id_comprobante = ? 
                       ORDER BY fecha_comunicacion DESC LIMIT 1";
            $stmt_cb = $pdo->prepare($sql_cb);
            $stmt_cb->execute([$id_comprobante]);
            $com_baja = $stmt_cb->fetch(PDO::FETCH_ASSOC);
            
            $nombreCdr = $com_baja ? "R-{$com_baja['correlativo_baja']}.zip" : "R-$ticket.zip";
            file_put_contents($cdrPath . $nombreCdr, $cdrZip);
            echo "CDR guardado: cdr/$nombreCdr\n";
        }
        
        // Códigos de éxito de SUNAT
        $codigos_aceptado = ['0', '00', '0000'];
        
        if (in_array($code, $codigos_aceptado)) {
            echo "\n✅ ANULACIÓN ACEPTADA POR SUNAT\n";
            
            // Actualizar comprobante a ANULADO
            $sql_upd = "UPDATE comprobantes 
                        SET estado_documento = 'ANULADO',
                            estado_sunat = 'ANULADO',
                            codigo_respuesta_sunat = ?,
                            descripcion_respuesta_sunat = ?,
                            motivo_anulacion = CONCAT('ANULADO VÍA SUNAT - Ticket: ', ?),
                            fecha_anulacion = NOW()
                        WHERE id_comprobante = ?";
            
            $stmt_upd = $pdo->prepare($sql_upd);
            $stmt_upd->execute([$code, $description, $ticket, $id_comprobante]);
            
            // Actualizar comunicación de baja
            $sql_cb_upd = "UPDATE comunicaciones_baja 
                          SET estado = 'ACEPTADO',
                              descripcion_respuesta = ?,
                              fecha_respuesta = NOW()
                          WHERE ticket_sunat = ?";
            
            $stmt_cb_upd = $pdo->prepare($sql_cb_upd);
            $stmt_cb_upd->execute([$description, $ticket]);
            
            echo "✅ Comprobante actualizado a ANULADO en BD\n";
            
        } else {
            echo "\n⚠️ SUNAT RECHAZÓ LA ANULACIÓN\n";
            echo "El comprobante permanece ACTIVO\n";
            
            // Actualizar comunicación de baja como rechazada
            $sql_cb_upd = "UPDATE comunicaciones_baja 
                          SET estado = 'RECHAZADO',
                              descripcion_respuesta = ?,
                              fecha_respuesta = NOW()
                          WHERE ticket_sunat = ?";
            
            $stmt_cb_upd = $pdo->prepare($sql_cb_upd);
            $stmt_cb_upd->execute(["[$code] $description", $ticket]);
        }
        
        echo "========================================\n";
        
    } else {
        $error = $result->getError();
        echo "\n❌ ERROR AL CONSULTAR TICKET\n";
        echo "========================================\n";
        echo "Código: {$error->getCode()}\n";
        echo "Mensaje: {$error->getMessage()}\n";
        echo "========================================\n";
        
        // Si es error 0127 (ticket no encontrado), puede que aún no esté procesado
        if ($error->getCode() == '0127') {
            echo "\n⏳ El ticket aún no ha sido procesado por SUNAT.\n";
            echo "   Intenta nuevamente en 1-2 minutos.\n";
        }
    }
    
} catch (Exception $e) {
    echo "\n❌ EXCEPCIÓN AL CONSULTAR TICKET\n";
    echo "========================================\n";
    echo "Error: {$e->getMessage()}\n";
    echo "========================================\n";
}
?>