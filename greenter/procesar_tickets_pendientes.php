<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../model/model_conexion.php';
require_once __DIR__ . '/config/config_greenter.php';

date_default_timezone_set('America/Lima');

echo "========================================\n";
echo "  PROCESADOR DE TICKETS PENDIENTES\n";
echo "========================================\n";
echo "Fecha/Hora: " . date('Y-m-d H:i:s') . "\n\n";

$db = new conexionBD();
$pdo = $db->conexionPDO();
$see = getSee();

// 🔍 PRIMERO: Verificar estructura de la tabla
try {
    $checkSql = "DESCRIBE comunicaciones_baja";
    $checkStmt = $pdo->query($checkSql);
    $columns = $checkStmt->fetchAll(PDO::FETCH_COLUMN);
    echo "📋 Columnas en comunicaciones_baja: " . implode(', ', $columns) . "\n\n";
} catch (Exception $e) {
    echo "⚠️ No se pudo verificar estructura: {$e->getMessage()}\n\n";
}

// Buscar tickets pendientes (últimas 24 horas)
// ✅ CORREGIDO: Usar id_comunicacion en lugar de id_comunicacion_baja
$sql = "SELECT cb.id_comunicacion,
               cb.id_comprobante,
               cb.correlativo_baja,
               cb.ticket_sunat,
               cb.estado,
               c.serie, 
               c.correlativo 
        FROM comunicaciones_baja cb
        INNER JOIN comprobantes c ON cb.id_comprobante = c.id_comprobante
        WHERE cb.estado = 'PENDIENTE'
        AND cb.fecha_comunicacion >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
        ORDER BY cb.fecha_comunicacion ASC";

try {
    $stmt = $pdo->query($sql);
    $pendientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "❌ ERROR en consulta SQL:\n";
    echo "   {$e->getMessage()}\n";
    echo "\n📌 Verifica que la tabla comunicaciones_baja existe y tiene las columnas correctas.\n";
    exit;
}

if (empty($pendientes)) {
    echo "✅ No hay tickets pendientes.\n";
    exit;
}

echo "📋 Encontrados " . count($pendientes) . " tickets pendientes\n\n";

$procesados = 0;
$aceptados = 0;
$rechazados = 0;

foreach ($pendientes as $com) {
    echo "🔍 Procesando: {$com['serie']}-{$com['correlativo']}\n";
    echo "   Ticket: {$com['ticket_sunat']}\n";
    echo "   Correlativo Baja: {$com['correlativo_baja']}\n";
    echo "   ID Comunicación: {$com['id_comunicacion']}\n";
    
    try {
        $result = $see->getStatus($com['ticket_sunat']);
        
        if ($result->isSuccess()) {
            $cdr = $result->getCdrResponse();
            $code = $cdr->getCode();
            $description = $cdr->getDescription();
            
            echo "   Código SUNAT: $code\n";
            echo "   Descripción: $description\n";
            
            // Guardar CDR
            if ($result->getCdrZip()) {
                $cdrPath = __DIR__ . '/cdr/';
                if (!is_dir($cdrPath)) mkdir($cdrPath, 0777, true);
                $cdrFile = $cdrPath . "R-{$com['correlativo_baja']}.zip";
                file_put_contents($cdrFile, $result->getCdrZip());
                echo "   📦 CDR guardado: cdr/R-{$com['correlativo_baja']}.zip\n";
            }
            
            // Códigos de éxito de SUNAT
            if (in_array($code, ['0', '00', '0000'])) {
                // ✅ ACEPTADO
                echo "   🔄 Actualizando a ACEPTADO...\n";
                
                $updCom = $pdo->prepare("UPDATE comunicaciones_baja 
                                        SET estado='ACEPTADO', 
                                            descripcion_respuesta=?, 
                                            fecha_respuesta=NOW()
                                        WHERE id_comunicacion=?");
                $updCom->execute([$description, $com['id_comunicacion']]);
                
                $updComp = $pdo->prepare("UPDATE comprobantes 
                                         SET estado_documento='ANULADO', 
                                             estado_sunat='ANULADO',
                                             codigo_respuesta_sunat=?, 
                                             descripcion_respuesta_sunat=?,
                                             fecha_anulacion=NOW()
                                         WHERE id_comprobante=?");
                $updComp->execute([$code, $description, $com['id_comprobante']]);
                
                echo "   ✅ ANULADO CORRECTAMENTE\n\n";
                $aceptados++;
            } else {
                // ❌ RECHAZADO
                echo "   🔄 Actualizando a RECHAZADO...\n";
                
                $updCom = $pdo->prepare("UPDATE comunicaciones_baja 
                                        SET estado='RECHAZADO', 
                                            descripcion_respuesta=?, 
                                            fecha_respuesta=NOW()
                                        WHERE id_comunicacion=?");
                $updCom->execute(["[$code] $description", $com['id_comunicacion']]);
                
                echo "   ❌ RECHAZADO POR SUNAT\n\n";
                $rechazados++;
            }
            
            $procesados++;
        } else {
            $error = $result->getError();
            echo "   ⏳ Aún no procesado por SUNAT\n";
            echo "   Info: {$error->getMessage()}\n\n";
        }
    } catch (Exception $e) {
        echo "   ❌ Error al procesar: {$e->getMessage()}\n\n";
        error_log("[SUNAT TICKETS] Error procesando ticket {$com['ticket_sunat']}: " . $e->getMessage());
    }
    
    sleep(2); // Pausa de 2 segundos entre consultas
}

echo "========================================\n";
echo "RESUMEN FINAL:\n";
echo "  Total procesados: $procesados\n";
echo "  ✅ Aceptados: $aceptados\n";
echo "  ❌ Rechazados: $rechazados\n";
echo "  ⏳ Pendientes: " . (count($pendientes) - $procesados) . "\n";
echo "========================================\n";
?>