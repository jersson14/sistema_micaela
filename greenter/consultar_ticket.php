<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../model/model_conexion.php';
require_once __DIR__ . '/config/config_greenter.php';

use Greenter\Model\Response\StatusResult;

// =============================================
// 1️⃣ CONEXIÓN BD
// =============================================
$db = new conexionBD();
$pdo = $db->conexionPDO();

// =============================================
// 2️⃣ OBTENER PARÁMETROS
// =============================================
$ticket = null;
$id_comprobante = null;

if (isset($argv[1])) {
    $ticket = $argv[1];
}

if (isset($argv[2]) && is_numeric($argv[2])) {
    $id_comprobante = (int)$argv[2];
}

if (!$ticket) {
    die("❌ ERROR: Falta el número de ticket. Uso: php consultar_ticket.php [TICKET] [ID_COMPROBANTE]\n");
}

if (!$id_comprobante) {
    die("❌ ERROR: Falta el ID del comprobante. Uso: php consultar_ticket.php [TICKET] [ID_COMPROBANTE]\n");
}

// =============================================
// 3️⃣ VERIFICAR QUE EL COMPROBANTE EXISTA
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

echo "═══════════════════════════════════════════════════════════════\n";
echo "🔍 CONSULTA DE TICKET - COMUNICACIÓN DE BAJA\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "📄 Comprobante: {$comprobante['serie']}-{$comprobante['correlativo']}\n";
echo "🎫 Ticket: {$ticket}\n";
echo "⏰ Consultando estado en SUNAT...\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

// =============================================
// 4️⃣ CONSULTAR ESTADO DEL TICKET EN SUNAT
// =============================================
$see = getSee();

try {
    // StatusResult: Consulta el estado del ticket
    $result = $see->getStatus($ticket);
    
    if ($result->isSuccess()) {
        // ✅ TICKET PROCESADO EXITOSAMENTE
        $statusCode = $result->getStatusCode();
        
        echo "✅ TICKET PROCESADO CORRECTAMENTE\n";
        echo "   Código de Estado: {$statusCode}\n\n";
        
        if ($statusCode == '0') {
            // 🎉 ANULACIÓN ACEPTADA
            echo "🎉 ¡ANULACIÓN ACEPTADA POR SUNAT!\n";
            echo "   La boleta {$comprobante['serie']}-{$comprobante['correlativo']} ha sido anulada correctamente.\n\n";
            
            // Guardar CDR si existe
            $cdrZip = $result->getCdrZip();
            if ($cdrZip) {
                $cdrPath = __DIR__ . '/cdr/';
                if (!is_dir($cdrPath)) {
                    mkdir($cdrPath, 0777, true);
                }
                
                // Buscar el correlativo de baja en la descripción
                $descripcion = $comprobante['descripcion_respuesta_sunat'];
                preg_match('/Ticket: (\d+)/', $descripcion, $matches);
                
                // Generar nombre del CDR basado en el correlativo de baja
                $fechaHoy = date('Ymd');
                $nombreCdr = "R-RA-{$fechaHoy}-XXX.zip"; // Se puede mejorar guardando el correlativo
                
                file_put_contents($cdrPath . $nombreCdr, $cdrZip);
                echo "📦 CDR guardado: cdr/{$nombreCdr}\n\n";
            }
            
            // Actualizar estado en BD
            $upd = $pdo->prepare("UPDATE comprobantes 
                                  SET estado_sunat='ANULADO',
                                      estado_documento='ANULADO',
                                      codigo_respuesta_sunat='0',
                                      descripcion_respuesta_sunat=?,
                                      fecha_anulacion=NOW()
                                  WHERE id_comprobante=?");
            $upd->execute([
                "ANULADO - Comunicación de baja aceptada por SUNAT. Ticket: {$ticket}",
                $id_comprobante
            ]);
            
            echo "💾 Base de datos actualizada: Estado cambiado a ANULADO\n";
            
        } else if ($statusCode == '98') {
            // ⏳ EN PROCESO
            echo "⏳ TICKET EN PROCESO\n";
            echo "   SUNAT aún está procesando la comunicación de baja.\n";
            echo "   Intente consultar nuevamente en unos minutos.\n\n";
            
            $upd = $pdo->prepare("UPDATE comprobantes 
                                  SET descripcion_respuesta_sunat=?
                                  WHERE id_comprobante=?");
            $upd->execute([
                "EN PROCESO - SUNAT está procesando la anulación. Ticket: {$ticket}",
                $id_comprobante
            ]);
            
        } else if ($statusCode == '99') {
            // ❌ RECHAZADO
            echo "❌ ANULACIÓN RECHAZADA POR SUNAT\n";
            echo "   La comunicación de baja fue rechazada.\n";
            
            // Obtener observaciones/errores si existen
            if (method_exists($result, 'getNotes') && $result->getNotes()) {
                echo "\n📝 Observaciones:\n";
                foreach ($result->getNotes() as $note) {
                    echo "   - {$note}\n";
                }
            }
            
            $upd = $pdo->prepare("UPDATE comprobantes 
                                  SET codigo_respuesta_sunat='99',
                                      descripcion_respuesta_sunat=?
                                  WHERE id_comprobante=?");
            $upd->execute([
                "RECHAZADO - La anulación fue rechazada por SUNAT. Ticket: {$ticket}",
                $id_comprobante
            ]);
            
        } else {
            // OTRO ESTADO
            echo "ℹ️  Estado: {$statusCode}\n";
            
            $upd = $pdo->prepare("UPDATE comprobantes 
                                  SET descripcion_respuesta_sunat=?
                                  WHERE id_comprobante=?");
            $upd->execute([
                "Estado {$statusCode} - Ticket: {$ticket}",
                $id_comprobante
            ]);
        }
        
    } else {
        // ❌ ERROR AL CONSULTAR
        $error = $result->getError();
        
        echo "❌ ERROR AL CONSULTAR TICKET\n";
        echo "   Código: {$error->getCode()}\n";
        echo "   Mensaje: {$error->getMessage()}\n\n";
        
        // Verificar si es error de ticket no encontrado
        if ($error->getCode() == '0127' || $error->getCode() == '127') {
            echo "⚠️  El ticket no existe o aún no está disponible en SUNAT.\n";
            echo "   Intente consultar nuevamente en unos minutos.\n";
        }
        
        $upd = $pdo->prepare("UPDATE comprobantes 
                              SET descripcion_respuesta_sunat=?
                              WHERE id_comprobante=?");
        $upd->execute([
            "ERROR AL CONSULTAR TICKET - Código: {$error->getCode()} - {$error->getMessage()}",
            $id_comprobante
        ]);
    }
    
} catch (Exception $e) {
    echo "❌ EXCEPCIÓN AL CONSULTAR TICKET\n";
    echo "   Error: " . $e->getMessage() . "\n";
    echo "   Archivo: " . $e->getFile() . "\n";
    echo "   Línea: " . $e->getLine() . "\n\n";
    
    $upd = $pdo->prepare("UPDATE comprobantes 
                          SET descripcion_respuesta_sunat=?
                          WHERE id_comprobante=?");
    $upd->execute([
        "EXCEPCIÓN AL CONSULTAR - " . $e->getMessage(),
        $id_comprobante
    ]);
}

echo "═══════════════════════════════════════════════════════════════\n";
echo "✅ Consulta finalizada\n";
echo "═══════════════════════════════════════════════════════════════\n";
?>