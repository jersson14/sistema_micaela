<?php
// COLOCA ESTE ARCHIVO EN LA RAÍZ DE TU PROYECTO
// Accede vía: http://localhost/sistema_micaela/test_salidas_debug.php

// Incluir archivo de conexión
require_once 'model/model_conexion.php';

echo "<h1>🔍 DEBUG - SALIDAS DIARIAS</h1>";
echo "<hr>";

try {
    // Crear instancia de la clase de conexión
    $conexion = new conexionBD();
    $c = $conexion->conexionPDO();
    
    // ========================================
    // 1. VERIFICAR SI EXISTE LA TABLA
    // ========================================
    echo "<h2>1. ¿Existe la tabla salidas_diarias?</h2>";
    $sql = "SHOW TABLES LIKE 'salidas_diarias'";
    $result = $c->query($sql);
    
    if ($result->rowCount() > 0) {
        echo "✅ <b>SÍ existe la tabla 'salidas_diarias'</b><br><br>";
    } else {
        echo "❌ <b>NO existe la tabla 'salidas_diarias'</b><br>";
        echo "Verifica el nombre de tu tabla en la base de datos<br><br>";
        exit;
    }
    
    // ========================================
    // 2. VERIFICAR ESTRUCTURA DE LA TABLA
    // ========================================
    echo "<h2>2. Columnas de la tabla salidas_diarias:</h2>";
    $sql = "DESCRIBE salidas_diarias";
    $result = $c->query($sql);
    $columnas = $result->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th></tr>";
    foreach ($columnas as $col) {
        echo "<tr>";
        echo "<td>{$col['Field']}</td>";
        echo "<td>{$col['Type']}</td>";
        echo "<td>{$col['Null']}</td>";
        echo "<td>{$col['Key']}</td>";
        echo "</tr>";
    }
    echo "</table><br>";
    
    // ========================================
    // 3. CONTAR REGISTROS TOTALES
    // ========================================
    echo "<h2>3. Total de registros en salidas_diarias:</h2>";
    $sql = "SELECT COUNT(*) as total FROM salidas_diarias";
    $result = $c->query($sql);
    $total = $result->fetch(PDO::FETCH_ASSOC);
    
    echo "<b>Total registros: {$total['total']}</b><br><br>";
    
    if ($total['total'] == 0) {
        echo "⚠️ <b>La tabla está VACÍA. No hay datos para mostrar.</b><br>";
        echo "Debes registrar salidas primero.<br><br>";
    }
    
    // ========================================
    // 4. VERIFICAR SI EXISTE LA TABLA CHOFERES
    // ========================================
    echo "<h2>4. ¿Existe la tabla choferes?</h2>";
    $sql = "SHOW TABLES LIKE 'choferes'";
    $result = $c->query($sql);
    
    if ($result->rowCount() > 0) {
        echo "✅ <b>SÍ existe la tabla 'choferes'</b><br><br>";
        
        // Contar choferes
        $sql = "SELECT COUNT(*) as total FROM choferes WHERE estado = 'ACTIVO'";
        $result = $c->query($sql);
        $total_choferes = $result->fetch(PDO::FETCH_ASSOC);
        echo "Choferes activos: <b>{$total_choferes['total']}</b><br><br>";
        
    } else {
        echo "❌ <b>NO existe la tabla 'choferes'</b><br><br>";
    }
    
    // ========================================
    // 5. MOSTRAR LOS ÚLTIMOS 5 REGISTROS
    // ========================================
    if ($total['total'] > 0) {
        echo "<h2>5. Últimos 5 registros de salidas_diarias:</h2>";
        
        $sql = "SELECT 
                    sd.*,
                    DATE(sd.fecha_hora) as fecha,
                    TIME(sd.fecha_hora) as hora
                FROM salidas_diarias sd
                ORDER BY sd.fecha_hora DESC
                LIMIT 5";
        
        $result = $c->query($sql);
        $registros = $result->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table border='1' cellpadding='5' style='font-size:12px;'>";
        echo "<tr>";
        foreach (array_keys($registros[0]) as $columna) {
            echo "<th>{$columna}</th>";
        }
        echo "</tr>";
        
        foreach ($registros as $reg) {
            echo "<tr>";
            foreach ($reg as $valor) {
                echo "<td>{$valor}</td>";
            }
            echo "</tr>";
        }
        echo "</table><br>";
    }
    
    // ========================================
    // 6. PROBAR LA CONSULTA DEL REPORTE
    // ========================================
    echo "<h2>6. Probar consulta del reporte (Enero 2025):</h2>";
    
    $sql = "SELECT 
                sd.id_salidas_diarias AS id_salida,
                DATE(sd.fecha_hora) AS fecha_salida,
                TIME(sd.fecha_hora) AS hora_salida,
                sd.id_conductor AS id_chofer,
                ch.nombres_apellidos AS chofer_nombre,
                COALESCE(ch.placa_vehiculo, 'SIN PLACA') AS placa_vehiculo,
                COALESCE(cl.nombre_completo, 'VARIOS') AS cliente_nombre,
                COALESCE(cl.nro_documento, '-') AS cliente_documento,
                COALESCE(ro.nombre, '-') AS origen,
                COALESCE(rd.nombre, '-') AS destino,
                COALESCE(s.nombre, 'SERVICIO GENERAL') AS servicio_nombre,
                COALESCE(sd.monto, 0) AS monto,
                COALESCE(CONCAT(c.serie, '-', LPAD(c.correlativo, 8, '0')), NULL) AS numero_comprobante,
                sd.estado
            FROM salidas_diarias sd
            INNER JOIN choferes ch ON sd.id_conductor = ch.id_chofer
            LEFT JOIN clientes cl ON sd.id_cliente = cl.id_cliente
            LEFT JOIN rutas ro ON sd.id_origen = ro.idrutas
            LEFT JOIN rutas rd ON sd.id_destino = rd.idrutas
            LEFT JOIN servicios s ON sd.id_servicio = s.id_servicio
            LEFT JOIN comprobantes c ON sd.id_comprobante = c.id_comprobante
            WHERE DATE(sd.fecha_hora) BETWEEN '2025-01-01' AND '2025-11-30'
            ORDER BY sd.fecha_hora DESC
            LIMIT 10";
    
    echo "<b>📌 Consulta SQL que se ejecutará:</b><br>";
    echo "<pre style='background:#f0f0f0;padding:10px;'>" . htmlspecialchars($sql) . "</pre><br>";
    
    try {
        $result = $c->query($sql);
        $resultados = $result->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<b>Registros encontrados: " . count($resultados) . "</b><br><br>";
        
        if (count($resultados) > 0) {
            echo "✅ <b>La consulta FUNCIONA correctamente</b><br><br>";
            
            echo "<table border='1' cellpadding='5' style='font-size:11px;'>";
            echo "<tr>";
            foreach (array_keys($resultados[0]) as $col) {
                echo "<th style='background:#333;color:white;'>{$col}</th>";
            }
            echo "</tr>";
            
            foreach ($resultados as $row) {
                echo "<tr>";
                foreach ($row as $val) {
                    echo "<td>{$val}</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
            
        } else {
            echo "⚠️ <b>La consulta no devuelve resultados</b><br>";
            echo "<div style='background:#fff3cd;padding:15px;border-left:4px solid #ffc107;'>";
            echo "<b>Posibles causas:</b><br>";
            echo "1. No hay salidas registradas en el rango de fechas (2025-01-01 a 2025-11-30)<br>";
            echo "2. Los registros tienen fechas diferentes<br>";
            echo "3. Falta el JOIN con choferes (campo id_conductor no coincide)<br>";
            echo "4. La tabla salidas_diarias está vacía<br>";
            echo "</div>";
            
            // Verificar si hay salidas sin filtro de fecha
            echo "<br><h3>Verificando salidas SIN filtro de fecha:</h3>";
            $sql_sin_filtro = "SELECT 
                                COUNT(*) as total,
                                MIN(DATE(fecha_hora)) as fecha_min,
                                MAX(DATE(fecha_hora)) as fecha_max
                               FROM salidas_diarias";
            $result2 = $c->query($sql_sin_filtro);
            $info = $result2->fetch(PDO::FETCH_ASSOC);
            
            echo "Total salidas: <b>{$info['total']}</b><br>";
            echo "Fecha mínima: <b>{$info['fecha_min']}</b><br>";
            echo "Fecha máxima: <b>{$info['fecha_max']}</b><br>";
        }
        
    } catch (Exception $e) {
        echo "❌ <b>ERROR en la consulta:</b><br>";
        echo "<pre style='color:red;background:#ffe6e6;padding:10px;'>{$e->getMessage()}</pre>";
    }
    
    // ========================================
    // 7. VERIFICAR TABLAS RELACIONADAS
    // ========================================
    echo "<h2>7. Verificar tablas relacionadas:</h2>";
    
    $tablas = ['choferes', 'clientes', 'rutas', 'servicios', 'comprobantes'];
    
    echo "<table border='1' cellpadding='8'>";
    echo "<tr><th>Tabla</th><th>Estado</th><th>Registros</th></tr>";
    
    foreach ($tablas as $tabla) {
        echo "<tr>";
        echo "<td><b>{$tabla}</b></td>";
        
        $sql = "SHOW TABLES LIKE '$tabla'";
        $result = $c->query($sql);
        
        if ($result->rowCount() > 0) {
            $sql_count = "SELECT COUNT(*) as total FROM $tabla";
            $result_count = $c->query($sql_count);
            $count = $result_count->fetch(PDO::FETCH_ASSOC);
            echo "<td style='color:green;'>✅ EXISTE</td>";
            echo "<td><b>{$count['total']}</b></td>";
        } else {
            echo "<td style='color:red;'>❌ NO EXISTE</td>";
            echo "<td>-</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
    
    // Cerrar conexión
    $conexion->cerrar_conexion();
    
} catch (Exception $e) {
    echo "<h2 style='color:red;'>❌ ERROR GENERAL:</h2>";
    echo "<div style='background:#ffe6e6;padding:15px;border-left:4px solid red;'>";
    echo "<pre style='color:red;'>{$e->getMessage()}</pre>";
    echo "</div>";
}

echo "<hr>";
echo "<div style='background:#e3f2fd;padding:15px;border-left:4px solid #2196f3;margin-top:20px;'>";
echo "<h3>📋 Resumen de Verificación:</h3>";
echo "<p><b>Si todo está correcto aquí pero no funciona en el reporte, el problema está en:</b></p>";
echo "<ol>";
echo "<li><b>El controller no está recibiendo los parámetros</b> - Verifica con F12 → Network</li>";
echo "<li><b>El JavaScript no está llamando al endpoint correcto</b> - Verifica console.log</li>";
echo "<li><b>Hay un error de permisos o ruta</b> - Verifica error_log de PHP</li>";
echo "<li><b>El DataTable no se inicializa</b> - Verifica errores de JavaScript</li>";
echo "</ol>";
echo "</div>";
?>

<style>
body {
    font-family: Arial, sans-serif;
    margin: 20px;
    background: #f5f5f5;
}
table {
    background: white;
    width: 100%;
    border-collapse: collapse;
    margin: 10px 0;
}
th {
    background: #333;
    color: white;
    padding: 8px;
    text-align: left;
}
td {
    padding: 5px;
    border-bottom: 1px solid #ddd;
}
h1 { 
    color: #333;
    background: linear-gradient(90deg, #fd7e14, #ffc107);
    padding: 15px;
    color: white;
    border-radius: 5px;
}
h2 { 
    color: #0066cc; 
    margin-top: 30px;
    border-bottom: 2px solid #0066cc;
    padding-bottom: 5px;
}
h3 {
    color: #333;
    margin-top: 20px;
}
pre {
    background: #f0f0f0;
    padding: 10px;
    border-left: 3px solid #0066cc;
    overflow-x: auto;
    font-size: 12px;
}
</style>