<?php
session_start();

// 🔧 DETECTAR RUTA AUTOMÁTICAMENTE
$ruta_model = '';

// Opción 1: Ruta estándar (desde controller/reportes/ hacia model/)
if (file_exists(__DIR__ . '/../../model/model_reportes.php')) {
    $ruta_model = __DIR__ . '/../../model/model_reportes.php';
}
// Opción 2: Si model está en la raíz del proyecto
elseif (file_exists(__DIR__ . '/../model/model_reportes.php')) {
    $ruta_model = __DIR__ . '/../model/model_reportes.php';
}
// Opción 3: Búsqueda desde raíz
elseif (file_exists($_SERVER['DOCUMENT_ROOT'] . '/sistema_micaela/model/model_reportes.php')) {
    $ruta_model = $_SERVER['DOCUMENT_ROOT'] . '/sistema_micaela/model/model_reportes.php';
}
else {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'data' => [],
        'error' => 'No se encontró el archivo model_reportes.php. Ubicación del controller: ' . __DIR__
    ]);
    exit;
}

require_once $ruta_model;
$MR = new Modelo_Reportes();

$accion = isset($_POST['accion']) ? $_POST['accion'] : '';

// ============================================================
// 1. LISTAR FACTURAS ARCHIVADAS (ANULADAS)
// ============================================================
if ($accion == 'LISTAR_FACTURAS_ARCHIVADAS') {
    $tipo = isset($_POST['tipo']) ? $_POST['tipo'] : '';
    $fecha_desde = isset($_POST['fecha_desde']) ? $_POST['fecha_desde'] : '';
    $fecha_hasta = isset($_POST['fecha_hasta']) ? $_POST['fecha_hasta'] : '';
    
    $consulta = $MR->Listar_Facturas_Archivadas($tipo, $fecha_desde, $fecha_hasta);
    
    echo json_encode(array('data' => $consulta));
}

// ============================================================
// 2. REPORTE INGRESOS VS GASTOS
// ============================================================
elseif ($accion == 'REPORTE_INGRESOS_GASTOS') {
    $fecha_desde = $_POST['fecha_desde'];
    $fecha_hasta = $_POST['fecha_hasta'];
    
    if (empty($fecha_desde) || empty($fecha_hasta)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Debe seleccionar rango de fechas'
        ]);
        exit;
    }
    
    $resultado = $MR->Reporte_Ingresos_Gastos($fecha_desde, $fecha_hasta);
    
    if ($resultado) {
        echo json_encode([
            'status' => 'success',
            'data' => $resultado
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'No se pudo generar el reporte'
        ]);
    }
}

// ============================================================
// 3. REPORTE SERVICIOS PRESTADOS
// ============================================================
elseif ($accion == 'REPORTE_SERVICIOS_PRESTADOS') {
    $fecha_desde = $_POST['fecha_desde'];
    $fecha_hasta = $_POST['fecha_hasta'];
    
    if (empty($fecha_desde) || empty($fecha_hasta)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Debe seleccionar rango de fechas'
        ]);
        exit;
    }
    
    $resultado = $MR->Reporte_Servicios_Prestados($fecha_desde, $fecha_hasta);
    
    if ($resultado) {
        echo json_encode([
            'status' => 'success',
            'data' => $resultado
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'No se encontraron datos'
        ]);
    }
}

// ============================================================
// 4. REPORTE SALIDAS DIARIAS
// ============================================================
elseif ($accion == 'REPORTE_SALIDAS_DIARIAS') {
    header('Content-Type: application/json; charset=utf-8');
    
    try {
        $fecha_desde = isset($_POST['fecha_desde']) ? $_POST['fecha_desde'] : '';
        $fecha_hasta = isset($_POST['fecha_hasta']) ? $_POST['fecha_hasta'] : '';
        $id_chofer = isset($_POST['id_chofer']) ? $_POST['id_chofer'] : '';
        
        if (empty($fecha_desde) || empty($fecha_hasta)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Debe seleccionar rango de fechas',
                'data' => []
            ]);
            exit;
        }
        
        $resultado = $MR->Reporte_Salidas_Diarias($fecha_desde, $fecha_hasta, $id_chofer);
        
        // Asegurar que siempre sea un array
        if (!is_array($resultado)) {
            $resultado = [];
        }
        
        echo json_encode([
            'status' => 'success',
            'data' => $resultado
        ], JSON_UNESCAPED_UNICODE);
        
    } catch (Exception $e) {
        error_log("Error en REPORTE_SALIDAS_DIARIAS: " . $e->getMessage());
        echo json_encode([
            'status' => 'error',
            'message' => 'Error al generar el reporte: ' . $e->getMessage(),
            'data' => []
        ]);
    }
}

// ============================================================
// 4B. OBTENER DETALLE DE UNA SALIDA (PARA MODAL)
// ============================================================
// En controller_reportes.php, línea donde está OBTENER_SALIDA
elseif ($accion == 'OBTENER_SALIDA') {
    header('Content-Type: application/json; charset=utf-8');
    
    try {
        $id_salida = isset($_POST['id_salida']) ? intval($_POST['id_salida']) : 0;
        
        if ($id_salida <= 0) {
            echo json_encode(['error' => 'ID de salida inválido']);
            exit;
        }
        
        // 🔴 ASEGÚRATE QUE ESTA LÍNEA APUNTE AL MÉTODO CORRECTO
        $resultado = $MR->Obtener_Salida($id_salida);
        
        if ($resultado) {
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['error' => 'No se encontró la salida']);
        }
        
    } catch (Exception $e) {
        error_log("Error en OBTENER_SALIDA: " . $e->getMessage());
        echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
    }
}

// ============================================================
// 4C. LISTAR CHOFERES PARA COMBO (CONSOLIDADO AQUÍ)
// ============================================================
elseif ($accion == 'LISTAR_CHOFERES_COMBO') {
    header('Content-Type: application/json; charset=utf-8');
    
    try {
        $resultado = $MR->Listar_Choferes_Combo();
        echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        
    } catch (Exception $e) {
        error_log("Error en LISTAR_CHOFERES_COMBO: " . $e->getMessage());
        echo json_encode([]);
    }
}

// ============================================================
// 5. REPORTE DE CLIENTES
// ============================================================
elseif ($accion == 'REPORTE_CLIENTES') {
    header('Content-Type: application/json; charset=utf-8');
    
    try {
        $tipo_filtro = isset($_POST['tipo_filtro']) ? $_POST['tipo_filtro'] : 'todos';
        $fecha_desde = isset($_POST['fecha_desde']) ? $_POST['fecha_desde'] : '';
        $fecha_hasta = isset($_POST['fecha_hasta']) ? $_POST['fecha_hasta'] : '';
        
        $resultado = $MR->Reporte_Clientes($tipo_filtro, $fecha_desde, $fecha_hasta);
        
        // Asegurar que siempre sea un array
        if (!is_array($resultado)) {
            $resultado = [];
        }
        
        echo json_encode([
            'data' => $resultado
        ], JSON_UNESCAPED_UNICODE);
        
    } catch (Exception $e) {
        error_log("Error en REPORTE_CLIENTES: " . $e->getMessage());
        echo json_encode([
            'data' => [],
            'error' => $e->getMessage()
        ]);
    }
}

// ============================================================
// 6. REPORTE DE CHOFERES
// ============================================================
elseif ($accion == 'REPORTE_CHOFERES') {
    header('Content-Type: application/json; charset=utf-8');
    
    try {
        $estado = isset($_POST['estado']) ? $_POST['estado'] : '';
        $fecha_desde = isset($_POST['fecha_desde']) ? $_POST['fecha_desde'] : '';
        $fecha_hasta = isset($_POST['fecha_hasta']) ? $_POST['fecha_hasta'] : '';
        
        $resultado = $MR->Reporte_Choferes($estado, $fecha_desde, $fecha_hasta);
        
        // Asegurar que siempre sea un array
        if (!is_array($resultado)) {
            $resultado = [];
        }
        
        echo json_encode([
            'data' => $resultado
        ], JSON_UNESCAPED_UNICODE);
        
    } catch (Exception $e) {
        error_log("Error en REPORTE_CHOFERES: " . $e->getMessage());
        echo json_encode([
            'data' => [],
            'error' => $e->getMessage()
        ]);
    }
}

// ============================================================
// 7. REPORTE ESTADO SUNAT
// ============================================================
elseif ($accion == 'REPORTE_ESTADO_SUNAT') {
    $estado = isset($_POST['estado']) ? $_POST['estado'] : '';
    $fecha_desde = isset($_POST['fecha_desde']) ? $_POST['fecha_desde'] : '';
    $fecha_hasta = isset($_POST['fecha_hasta']) ? $_POST['fecha_hasta'] : '';
    
    $resultado = $MR->Reporte_Estado_SUNAT($estado, $fecha_desde, $fecha_hasta);
    
    echo json_encode(array('data' => $resultado));
}

// ============================================================
// 8. ESTADÍSTICAS GENERALES DASHBOARD
// ============================================================
elseif ($accion == 'ESTADISTICAS_DASHBOARD') {
    $resultado = $MR->Obtener_Estadisticas_Dashboard();
    
    echo json_encode($resultado);
}

// ============================================================
// 9. GRÁFICA DE INGRESOS MENSUALES
// ============================================================
elseif ($accion == 'GRAFICA_INGRESOS_MENSUALES') {
    $anio = isset($_POST['anio']) ? $_POST['anio'] : date('Y');
    
    $resultado = $MR->Grafica_Ingresos_Mensuales($anio);
    
    echo json_encode([
        'status' => 'success',
        'data' => $resultado
    ]);
}

// ============================================================
// 10. TOP CLIENTES
// ============================================================
elseif ($accion == 'TOP_CLIENTES') {
    $limite = isset($_POST['limite']) ? intval($_POST['limite']) : 10;
    $fecha_desde = isset($_POST['fecha_desde']) ? $_POST['fecha_desde'] : '';
    $fecha_hasta = isset($_POST['fecha_hasta']) ? $_POST['fecha_hasta'] : '';
    
    $resultado = $MR->Top_Clientes($limite, $fecha_desde, $fecha_hasta);
    
    echo json_encode(array('data' => $resultado));
}

// ============================================================
// 11. TOP SERVICIOS
// ============================================================
elseif ($accion == 'TOP_SERVICIOS') {
    $limite = isset($_POST['limite']) ? intval($_POST['limite']) : 10;
    $fecha_desde = isset($_POST['fecha_desde']) ? $_POST['fecha_desde'] : '';
    $fecha_hasta = isset($_POST['fecha_hasta']) ? $_POST['fecha_hasta'] : '';
    
    $resultado = $MR->Top_Servicios($limite, $fecha_desde, $fecha_hasta);
    
    echo json_encode(array('data' => $resultado));
}

// ===========================================================OBTENER_SALIDA=
// 12. EXPORTAR REPORTE A EXCEL (Preparación de datos)
// ============================================================
elseif ($accion == 'PREPARAR_EXPORTACION_EXCEL') {
    $tipo_reporte = $_POST['tipo_reporte'];
    $fecha_desde = isset($_POST['fecha_desde']) ? $_POST['fecha_desde'] : '';
    $fecha_hasta = isset($_POST['fecha_hasta']) ? $_POST['fecha_hasta'] : '';
    
    // Aquí prepararías los datos específicos para cada tipo de reporte
    $datos = [];
    
    switch ($tipo_reporte) {
        case 'ingresos_gastos':
            $datos = $MR->Reporte_Ingresos_Gastos($fecha_desde, $fecha_hasta);
            break;
        case 'servicios':
            $datos = $MR->Reporte_Servicios_Prestados($fecha_desde, $fecha_hasta);
            break;
        case 'clientes':
            $datos = $MR->Reporte_Clientes('todos', $fecha_desde, $fecha_hasta);
            break;
        case 'salidas':
            $datos = $MR->Reporte_Salidas_Diarias($fecha_desde, $fecha_hasta);
            break;
    }
    
    echo json_encode([
        'status' => 'success',
        'data' => $datos
    ]);
}
// ============================================================
// OBTENER DETALLE DE UN CHOFER ESPECÍFICO
// ============================================================
elseif ($accion == 'OBTENER_DETALLE_CHOFER') {
    header('Content-Type: application/json; charset=utf-8');
    
    try {
        $id_chofer = isset($_POST['id_chofer']) ? intval($_POST['id_chofer']) : 0;
        
        if ($id_chofer <= 0) {
            echo json_encode(['error' => 'ID de chofer inválido']);
            exit;
        }
        
        $resultado = $MR->Obtener_Detalle_Chofer($id_chofer);
        
        if ($resultado) {
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['error' => 'No se encontró el chofer']);
        }
        
    } catch (Exception $e) {
        error_log("Error en OBTENER_DETALLE_CHOFER: " . $e->getMessage());
        echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
    }
}
?>