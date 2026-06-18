<?php
// test_final.php - Prueba rápida de los reportes corregidos
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'model/model_reportes.php';

echo "<h1>✅ TEST FINAL - REPORTES CORREGIDOS</h1>";
echo "<hr>";

$MR = new Modelo_Reportes();

// TEST CLIENTES
echo "<h2>👥 TEST CLIENTES (tabla 'clientes')</h2>";
try {
    $clientes = $MR->Reporte_Clientes('todos', '', '');
    echo "<p style='color:green'>✅ ÉXITO - Encontrados: " . count($clientes) . " clientes</p>";
    
    if (count($clientes) > 0) {
        echo "<h3>Primeros 3 clientes:</h3>";
        echo "<table border='1' cellpadding='5' style='border-collapse:collapse'>";
        echo "<tr style='background:#f0f0f0'>";
        echo "<th>ID</th><th>Nombre</th><th>Documento</th><th>Celular</th><th>Total Viajes</th><th>Total Gastado</th>";
        echo "</tr>";
        
        foreach (array_slice($clientes, 0, 3) as $c) {
            echo "<tr>";
            echo "<td>{$c['id_cliente']}</td>";
            echo "<td>{$c['nombre_completo']}</td>";
            echo "<td>{$c['nro_documento']}</td>";
            echo "<td>{$c['celular']}</td>";
            echo "<td>{$c['total_viajes']}</td>";
            echo "<td>S/ " . number_format($c['total_gastado'], 2) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Mostrar JSON
        echo "<h3>JSON de respuesta (primeros 2):</h3>";
        echo "<pre style='background:#f5f5f5; padding:10px'>";
        echo json_encode(['data' => array_slice($clientes, 0, 2)], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        echo "</pre>";
    }
} catch (Exception $e) {
    echo "<p style='color:red'>❌ ERROR: " . $e->getMessage() . "</p>";
}

echo "<hr>";

// TEST CHOFERES
echo "<h2>🚗 TEST CHOFERES (tabla 'choferes')</h2>";
try {
    $choferes = $MR->Reporte_Choferes('', '', '');
    echo "<p style='color:green'>✅ ÉXITO - Encontrados: " . count($choferes) . " choferes</p>";
    
    if (count($choferes) > 0) {
        echo "<h3>Primeros 3 choferes:</h3>";
        echo "<table border='1' cellpadding='5' style='border-collapse:collapse'>";
        echo "<tr style='background:#f0f0f0'>";
        echo "<th>ID</th><th>Nombre</th><th>Documento</th><th>Placa</th><th>Estado</th><th>Total Salidas</th><th>Total Facturado</th>";
        echo "</tr>";
        
        foreach (array_slice($choferes, 0, 3) as $ch) {
            echo "<tr>";
            echo "<td>{$ch['id_chofer']}</td>";
            echo "<td>{$ch['nombres_apellidos']}</td>";
            echo "<td>{$ch['nro_doc']}</td>";
            echo "<td>{$ch['placa_vehiculo']}</td>";
            echo "<td>{$ch['estado']}</td>";
            echo "<td>{$ch['total_salidas']}</td>";
            echo "<td>S/ " . number_format($ch['total_facturado'], 2) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Mostrar JSON
        echo "<h3>JSON de respuesta (primeros 2):</h3>";
        echo "<pre style='background:#f5f5f5; padding:10px'>";
        echo json_encode(['data' => array_slice($choferes, 0, 2)], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        echo "</pre>";
    }
} catch (Exception $e) {
    echo "<p style='color:red'>❌ ERROR: " . $e->getMessage() . "</p>";
}

echo "<hr>";

// TEST SIMULACIÓN AJAX
echo "<h2>🌐 TEST SIMULACIÓN AJAX</h2>";

// Simular petición AJAX de clientes
$_POST['accion'] = 'REPORTE_CLIENTES';
$_POST['tipo_filtro'] = 'todos';
$_POST['fecha_desde'] = '';
$_POST['fecha_hasta'] = '';

ob_start();
require 'controller/reportes/controller_reportes.php';
$json_clientes = ob_get_clean();

echo "<h3>Respuesta Controller (Clientes):</h3>";
echo "<pre style='background:#f5f5f5; padding:10px'>";
echo htmlspecialchars($json_clientes);
echo "</pre>";

$test_json = json_decode($json_clientes);
if (json_last_error() === JSON_ERROR_NONE) {
    echo "<p style='color:green'>✅ JSON VÁLIDO para DataTables</p>";
    if (isset($test_json->data) && is_array($test_json->data)) {
        echo "<p style='color:green'>✅ Estructura correcta: <code>{'data': [...]}</code></p>";
        echo "<p><strong>Total registros:</strong> " . count($test_json->data) . "</p>";
    }
} else {
    echo "<p style='color:red'>❌ JSON INVÁLIDO: " . json_last_error_msg() . "</p>";
}

echo "<hr>";
echo "<h2>🎉 TEST COMPLETADO</h2>";
echo "<p><strong>Siguiente paso:</strong> Si todo está en verde ✅, ve a tu navegador y prueba los reportes.</p>";
echo "<p><strong>Si hay errores rojos ❌:</strong> Revisa los mensajes de error arriba.</p>";
?>