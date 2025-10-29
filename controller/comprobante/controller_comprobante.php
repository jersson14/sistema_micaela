<?php
session_start();

require_once '../../model/model_comprobante.php';
$MC = new Modelo_Comprobantes();

$accion = isset($_POST['accion']) ? $_POST['accion'] : '';

// ============================================================
// 1. OBTENER CORRELATIVO
// ============================================================
if ($accion == 'OBTENER_CORRELATIVO') {
    $serie = strtoupper($_POST['serie']);
    $tipo_comprobante = $_POST['tipo_comprobante'];
    
    $correlativo = $MC->Obtener_Correlativo($serie, $tipo_comprobante);
    echo json_encode(array('correlativo' => $correlativo));
}

// ============================================================
// 2. REGISTRAR COMPROBANTE
// ============================================================
elseif ($accion == 'REGISTRAR_COMPROBANTE') {
    header('Content-Type: application/json; charset=utf-8');
    date_default_timezone_set('America/Lima');

    // DATOS DEL COMPROBANTE
    $tipo_comprobante = $_POST['tipo_comprobante'];
    $serie = strtoupper($_POST['serie']);
    $correlativo = $_POST['correlativo'];
    $fecha_emision = !empty($_POST['fecha_emision'])
        ? date('Y-m-d', strtotime($_POST['fecha_emision']))
        : date('Y-m-d');
    $hora_emision = date('H:i:s');
    $moneda = $_POST['moneda'];
    
    // DATOS DEL CLIENTE
    $tipo_documento = $_POST['tipo_documento_cliente'];
    $numero_documento = $_POST['numero_documento'];
    $razon_social = strtoupper($_POST['razon_social']);
    $direccion = strtoupper($_POST['direccion']);
    
    // 🔍 DEBUG 1: Ver qué llega en $_POST['celular']
    $telefono_raw = isset($_POST['celular']) ? $_POST['celular'] : null;
    $telefono = isset($_POST['celular']) ? trim($_POST['celular']) : '';
    
    // 📝 LOG DETALLADO
    $debug_info = [
        'celular_existe' => isset($_POST['celular']) ? 'SI' : 'NO',
        'celular_raw' => $telefono_raw,
        'celular_length' => strlen($telefono_raw ?? ''),
        'telefono_final' => $telefono,
        'telefono_empty' => empty($telefono) ? 'SI' : 'NO',
        'telefono_is_string' => is_string($telefono) ? 'SI' : 'NO'
    ];
    file_put_contents('debug_controller.log', 
        '[' . date('Y-m-d H:i:s') . '] POST COMPLETO: ' . print_r($_POST, true) . PHP_EOL .
        'DEBUG TELEFONO: ' . print_r($debug_info, true) . PHP_EOL .
        str_repeat('=', 80) . PHP_EOL,
        FILE_APPEND
    );
    
    $departamento = strtoupper($_POST['departamento']);
    $provincia = strtoupper($_POST['provincia']);
    $distrito = isset($_POST['distrito']) ? strtoupper($_POST['distrito']) : 'ABANCAY';
    $ubigeo = '030101';
    
    // DATOS DEL SERVICIO
    $id_servicio = $_POST['id_servicio'];
    $cantidad = floatval($_POST['cantidad']);
    $fecha_viaje = $_POST['fecha_viaje'];
    $id_conductor = $_POST['id_conductor'];
    $id_origen = $_POST['id_origen'];
    $id_destino = $_POST['id_destino'];
    $observaciones = isset($_POST['observaciones']) ? strtoupper($_POST['observaciones']) : '';
    
    // TOTALES
    $base_gravada = floatval($_POST['base_gravada']);
    $igv = floatval($_POST['igv']);
    $total = floatval($_POST['total']);
    $forma_pago = $_POST['forma_pago'];
    $id_tipo_pago = $_POST['id_tipo_pago'];
    
    // ESTADO Y USUARIO
    $estado_sunat = isset($_POST['estado_sunat']) ? $_POST['estado_sunat'] : 'PENDIENTE';
    $id_usuario = isset($_POST['id_usuario']) ? intval($_POST['id_usuario']) : 0;

    // VALIDACIONES
    if ($id_usuario <= 0) {
        echo json_encode(array('status' => 'error', 'message' => 'No se ha identificado el usuario en la sesión.'));
        exit;
    }
    if (empty($tipo_comprobante) || empty($serie) || empty($fecha_emision)) {
        echo json_encode(array('status' => 'error', 'message' => 'Faltan datos del comprobante'));
        exit;
    }
    if (empty($numero_documento) || empty($razon_social)) {
        echo json_encode(array('status' => 'error', 'message' => 'Faltan datos del cliente'));
        exit;
    }
    if ($tipo_comprobante == '01' && $tipo_documento != '6') {
        echo json_encode(array('status' => 'error', 'message' => 'Las facturas solo se emiten con RUC'));
        exit;
    }
    if ($base_gravada <= 0 || $total <= 0) {
        echo json_encode(array('status' => 'error', 'message' => 'Los montos deben ser mayores a 0'));
        exit;
    }

    // 🔍 DEBUG 2: Antes de llamar la función
    file_put_contents('debug_controller.log', 
        '[' . date('Y-m-d H:i:s') . '] ANTES DE REGISTRAR CLIENTE' . PHP_EOL .
        '  - Tipo Doc: ' . $tipo_documento . PHP_EOL .
        '  - Num Doc: ' . $numero_documento . PHP_EOL .
        '  - Razon: ' . $razon_social . PHP_EOL .
        '  - Telefono: "' . $telefono . '" (length: ' . strlen($telefono) . ')' . PHP_EOL,
        FILE_APPEND
    );

    // PASO 1: Registrar o actualizar cliente
    $id_cliente = $MC->Registrar_Cliente_SUNAT(
        $tipo_documento,
        $numero_documento,
        $razon_social,
        $direccion,
        $telefono,
        $departamento,
        $provincia,
        $distrito,
        $ubigeo
    );
    
    // 🔍 DEBUG 3: Después de registrar
    file_put_contents('debug_controller.log', 
        '[' . date('Y-m-d H:i:s') . '] RESULTADO REGISTRO CLIENTE: ID=' . $id_cliente . PHP_EOL,
        FILE_APPEND
    );
    
    if ($id_cliente == 0) {
        echo json_encode(array('status' => 'error', 'message' => 'Error al registrar cliente'));
        exit;
    }
    
    // PASO 2: Registrar comprobante
    $resultado = $MC->Registrar_Comprobante(
        $tipo_comprobante,
        $serie,
        $correlativo,
        $fecha_emision,
        $hora_emision,
        $moneda,
        $id_cliente,
        $base_gravada,
        $igv,
        $total,
        $forma_pago,
        $id_tipo_pago,
        $estado_sunat,
        $id_usuario,
        $id_servicio,
        $cantidad,
        $fecha_viaje,
        $id_conductor,
        $id_origen,
        $id_destino,
        $observaciones
    );
    
    if ($resultado > 0) {
        echo json_encode(array(
            'status' => 'success', 
            'message' => 'Comprobante registrado correctamente',
            'id_comprobante' => $resultado,
            'serie' => $serie,
            'correlativo' => $correlativo
        ));
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'Error al registrar comprobante'));
    }
}

// ============================================================
// 3. ENVIAR A SUNAT
// ============================================================
elseif ($accion == 'ENVIAR_SUNAT') {
    $id_comprobante = $_POST['id_comprobante'];
    $ruta_script = __DIR__ . '/../../greenter/factura_bd.php';
    $comando = "php \"$ruta_script\" $id_comprobante 2>&1";
    $output = shell_exec($comando);

    // ======================================================
    // 1️⃣ Registrar log del resultado
    // ======================================================
    $log_file = __DIR__ . '/../../greenter/envio_log.txt';
    file_put_contents($log_file, "=========================\n", FILE_APPEND);
    file_put_contents($log_file, "FECHA: " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
    file_put_contents($log_file, "ID COMPROBANTE: $id_comprobante\n", FILE_APPEND);
    file_put_contents($log_file, "OUTPUT:\n$output\n\n", FILE_APPEND);

    // ======================================================
    // 2️⃣ Analizar respuesta de Greenter
    // ======================================================
    $output_lower = strtolower($output); // para comparación más confiable

    if (strpos($output_lower, 'aceptado') !== false || strpos($output_lower, '¡éxito!') !== false) {
        // Aceptado por SUNAT
        $MC->Actualizar_Estado_SUNAT($id_comprobante, 'ACEPTADO');

        echo json_encode([
            'status'  => 'success',
            'message' => '✅ Comprobante ACEPTADO por SUNAT',
            'output'  => nl2br($output)
        ]);
    } 
    elseif (strpos($output_lower, 'enviado') !== false) {
        // En algunos casos SUNAT devuelve "ENVIADO"
        $MC->Actualizar_Estado_SUNAT($id_comprobante, 'ENVIADO');

        echo json_encode([
            'status'  => 'info',
            'message' => '📤 Comprobante ENVIADO a SUNAT (pendiente de validación)',
            'output'  => nl2br($output)
        ]);
    } 
    elseif (strpos($output_lower, 'rechazado') !== false || strpos($output_lower, 'error') !== false) {
        // Rechazado por SUNAT
        $MC->Actualizar_Estado_SUNAT($id_comprobante, 'RECHAZADO');

        echo json_encode([
            'status'  => 'error',
            'message' => '❌ Comprobante RECHAZADO por SUNAT',
            'output'  => nl2br($output)
        ]);
    } 
    else {
        // Caso desconocido o sin coincidencias
        $MC->Actualizar_Estado_SUNAT($id_comprobante, 'ERROR');

        echo json_encode([
            'status'  => 'error',
            'message' => '⚠️ No se pudo determinar el estado del comprobante (revisa el log)',
            'output'  => nl2br($output)
        ]);
    }
}

// ============================================================
// 4. LISTAR COMPROBANTES
// ============================================================
elseif ($accion == 'LISTAR_COMPROBANTES') {
    $estado = isset($_POST['estado']) ? $_POST['estado'] : '';
    $fecha_desde = isset($_POST['fecha_desde']) ? $_POST['fecha_desde'] : '';
    $fecha_hasta = isset($_POST['fecha_hasta']) ? $_POST['fecha_hasta'] : '';
    
    $consulta = $MC->Listar_Comprobantes($estado, $fecha_desde, $fecha_hasta);
    
    $data = array();
    foreach ($consulta as $row) {
        $data[] = $row;
    }
    
    echo json_encode(array('data' => $data));
}

// ============================================================
// 5. OBTENER COMPROBANTE POR ID
// ============================================================
elseif ($accion == 'OBTENER_COMPROBANTE') {
    $id_comprobante = $_POST['id_comprobante'];
    $consulta = $MC->Obtener_Comprobante($id_comprobante);
    echo json_encode($consulta);
}

// ============================================================
// 6. ANULAR COMPROBANTE
// ============================================================
elseif ($accion == 'ANULAR_COMPROBANTE') {
    $id_comprobante = $_POST['id_comprobante'];
    $motivo = strtoupper($_POST['motivo']);
    $usuario = $_POST['usuario'];
    
    $resultado = $MC->Anular_Comprobante($id_comprobante, $motivo, $usuario);
    
    if ($resultado == 1) {
        echo json_encode(array('status' => 'success', 'message' => 'Comprobante anulado correctamente'));
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'Error al anular comprobante'));
    }
}
// ============================================================
// 7. OBTENER RESUMEN DE ENVÍOS
// ============================================================
elseif ($accion == 'OBTENER_RESUMEN_ENVIOS') {
    $datos = $MC->ObtenerResumenEnvios();
    echo json_encode($datos);
}

// ============================================================
// 8. LISTAR PENDIENTES DE ENVÍO
// ============================================================
elseif ($accion == 'LISTAR_PENDIENTES_ENVIO') {
    $tipo = isset($_POST['tipo_comprobante']) ? $_POST['tipo_comprobante'] : '';
    $fecha_desde = isset($_POST['fecha_desde']) ? $_POST['fecha_desde'] : '';
    $fecha_hasta = isset($_POST['fecha_hasta']) ? $_POST['fecha_hasta'] : '';
    
    $consulta = $MC->ListarPendientesEnvio($tipo, $fecha_desde, $fecha_hasta);
    echo json_encode(array('data' => $consulta));
}

// ============================================================
// 9. LISTAR HISTORIAL DE ENVÍOS
// ============================================================
elseif ($accion == 'LISTAR_HISTORIAL_ENVIOS') {
    $tipo = isset($_POST['tipo_comprobante']) ? $_POST['tipo_comprobante'] : '';
    $estado = isset($_POST['estado_sunat']) ? $_POST['estado_sunat'] : '';
    $fecha_desde = isset($_POST['fecha_desde']) ? $_POST['fecha_desde'] : '';
    $fecha_hasta = isset($_POST['fecha_hasta']) ? $_POST['fecha_hasta'] : '';
    
    $consulta = $MC->ListarHistorialEnvios($tipo, $estado, $fecha_desde, $fecha_hasta);
    echo json_encode(array('data' => $consulta));
}

// ============================================================
// 10. OBTENER RESPUESTA SUNAT
// ============================================================
elseif ($accion == 'OBTENER_RESPUESTA_SUNAT') {
    $id_comprobante = $_POST['id_comprobante'];
    $datos = $MC->ObtenerRespuestaSunat($id_comprobante);
    echo json_encode($datos);
}

// ============================================================
// BUSCAR COMPROBANTE PARA NOTA DE CRÉDITO
// ============================================================
elseif ($accion == 'BUSCAR_COMPROBANTE') {
    $tipo_comprobante = $_POST['tipo_comprobante'];
    $serie = strtoupper($_POST['serie']);
    $correlativo = $_POST['correlativo'];
    
    $resultado = $MC->Buscar_Comprobante_Para_NC($tipo_comprobante, $serie, $correlativo);
    
    if ($resultado && count($resultado) > 0) {
        echo json_encode([
            'status' => 'success',
            'data' => $resultado
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'No se encontró el comprobante o no está disponible para nota de crédito. Verifique que esté ACEPTADO por SUNAT.'
        ]);
    }
}

// ============================================================
// REGISTRAR NOTA DE CRÉDITO
// ============================================================
elseif ($accion == 'REGISTRAR_NOTA_CREDITO') {
    header('Content-Type: application/json; charset=utf-8');
    
    $id_comprobante_origen = intval($_POST['id_comprobante_origen']);
    $serie = isset($_POST['serie']) ? strtoupper(trim($_POST['serie'])) : ''; // AGREGAR
    $correlativo = isset($_POST['correlativo']) ? trim($_POST['correlativo']) : ''; // AGREGAR
    $motivo_nota = $_POST['motivo_nota'];
    $motivo2 = $_POST['motivo2'];

    $observaciones = strtoupper($_POST['observaciones']);
    $total_gravada = floatval($_POST['total_gravada']);
    $total_igv = floatval($_POST['total_igv']);
    $total = floatval($_POST['total']);
    $estado_sunat = isset($_POST['estado_sunat']) ? $_POST['estado_sunat'] : 'PENDIENTE';
    $id_usuario = isset($_POST['id_usuario']) ? intval($_POST['id_usuario']) : 0;
    
    // Validaciones
    if ($id_comprobante_origen <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Comprobante origen inválido']);
        exit;
    }
    
    if (empty($serie) || empty($correlativo)) { // AGREGAR
        echo json_encode(['status' => 'error', 'message' => 'Serie y correlativo son obligatorios']);
        exit;
    }
    
    if (empty($motivo_nota) || empty($observaciones)) {
        echo json_encode(['status' => 'error', 'message' => 'Complete todos los campos obligatorios']);
        exit;
    }
    
    if ($total <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'El monto debe ser mayor a 0']);
        exit;
    }
    
    if ($id_usuario <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Usuario no identificado']);
        exit;
    }
    
    // Registrar nota de crédito
    $resultado = $MC->Registrar_Nota_Credito(
        $id_comprobante_origen,
        $serie, // AGREGAR
        $correlativo, // AGREGAR
        $motivo_nota,
        $motivo2,
        $observaciones,
        $total_gravada,
        $total_igv,
        $total,
        $estado_sunat,
        $id_usuario
    );
    
    if ($resultado > 0) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Nota de crédito registrada correctamente',
            'id_comprobante' => $resultado
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al registrar la nota de crédito']);
    }
}

// ============================================================
// LISTAR NOTAS DE CRÉDITO
// ============================================================
elseif ($accion == 'LISTAR_NOTAS_CREDITO') {
    $estado = isset($_POST['estado']) ? $_POST['estado'] : '';
    $fecha_desde = isset($_POST['fecha_desde']) ? $_POST['fecha_desde'] : '';
    $fecha_hasta = isset($_POST['fecha_hasta']) ? $_POST['fecha_hasta'] : '';
    
    $consulta = $MC->Listar_Notas_Credito($estado, $fecha_desde, $fecha_hasta);
    
    $data = array();
    foreach ($consulta as $row) {
        $data[] = $row;
    }
    
    echo json_encode(array('data' => $data));
}
// ============================================================
// OBTENER CORRELATIVO PARA NOTA DE CRÉDITO
// ============================================================
elseif ($accion == 'OBTENER_CORRELATIVO_NC') {
    $tipo_comprobante = $_POST['tipo_comprobante'];
    $correlativo = $MC->Obtener_Correlativo_NC($tipo_comprobante);
    
    echo json_encode([
        'status' => 'success',
        'correlativo' => $correlativo
    ]);
}
// ============================================================
// REGISTRAR NOTA DE DÉBITO
// ============================================================
elseif ($accion == 'REGISTRAR_NOTA_DEBITO') {
    header('Content-Type: application/json; charset=utf-8');
    
    $id_comprobante_origen = intval($_POST['id_comprobante_origen']);
    $serie = isset($_POST['serie']) ? strtoupper(trim($_POST['serie'])) : '';
    $correlativo = isset($_POST['correlativo']) ? trim($_POST['correlativo']) : '';
    $motivo_nota = $_POST['motivo_nota'];
    $motivo2 = $_POST['motivo2'];
    $observaciones = strtoupper($_POST['observaciones']);
    $total_gravada = floatval($_POST['total_gravada']);
    $total_igv = floatval($_POST['total_igv']);
    $total = floatval($_POST['total']);
    $estado_sunat = isset($_POST['estado_sunat']) ? $_POST['estado_sunat'] : 'PENDIENTE';
    $id_usuario = isset($_POST['id_usuario']) ? intval($_POST['id_usuario']) : 0;
    
    // Validaciones
    if ($id_comprobante_origen <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Comprobante origen inválido']);
        exit;
    }
    
    if (empty($serie) || empty($correlativo)) {
        echo json_encode(['status' => 'error', 'message' => 'Serie y correlativo son obligatorios']);
        exit;
    }
    
    if (empty($motivo_nota) || empty($observaciones)) {
        echo json_encode(['status' => 'error', 'message' => 'Complete todos los campos obligatorios']);
        exit;
    }
    
    if ($total <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'El monto debe ser mayor a 0']);
        exit;
    }
    
    if ($id_usuario <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Usuario no identificado']);
        exit;
    }
    
    // Registrar nota de débito
    $resultado = $MC->Registrar_Nota_Debito(
        $id_comprobante_origen,
        $serie,
        $correlativo,
        $motivo_nota,
        $motivo2,
        $observaciones,
        $total_gravada,
        $total_igv,
        $total,
        $estado_sunat,
        $id_usuario
    );
    
    if ($resultado > 0) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Nota de débito registrada correctamente',
            'id_comprobante' => $resultado
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al registrar la nota de débito']);
    }
}

// ============================================================
// LISTAR NOTAS DE DÉBITO
// ============================================================
elseif ($accion == 'LISTAR_NOTAS_DEBITO') {
    $estado = isset($_POST['estado']) ? $_POST['estado'] : '';
    $fecha_desde = isset($_POST['fecha_desde']) ? $_POST['fecha_desde'] : '';
    $fecha_hasta = isset($_POST['fecha_hasta']) ? $_POST['fecha_hasta'] : '';
    
    $consulta = $MC->Listar_Notas_Debito($estado, $fecha_desde, $fecha_hasta);
    
    $data = array();
    foreach ($consulta as $row) {
        $data[] = $row;
    }
    
    echo json_encode(array('data' => $data));
}

// ============================================================
// OBTENER CORRELATIVO PARA NOTA DE DÉBITO
// ============================================================
elseif ($accion == 'OBTENER_CORRELATIVO_ND') {
    $tipo_comprobante = $_POST['tipo_comprobante'];
    $correlativo = $MC->Obtener_Correlativo_ND($tipo_comprobante);
    
    echo json_encode([
        'status' => 'success',
        'correlativo' => $correlativo
    ]);
}

// ============================================================
// ANULAR BOLETA Y COMUNICAR A SUNAT
// ============================================================
elseif ($accion == 'ANULAR_BOLETA_SUNAT') {
    header('Content-Type: application/json; charset=utf-8');
    
    // ❌ YA NO NECESITAS $c = conexionBD::conexionPDO();
    
    $id_comprobante = $_POST['id_comprobante'];
    $motivo = strtoupper($_POST['motivo']);
    $usuario = $_POST['usuario'];
    
    // 1️⃣ Verificar si la boleta puede ser anulada (USA MODELO)
    $verificacion = $MC->Verificar_Boleta_Anulable($id_comprobante);
    
    if (!$verificacion['anulable']) {
        echo json_encode(['status' => 'error', 'message' => $verificacion['motivo']]);
        exit;
    }
    
    // 2️⃣ Obtener datos del comprobante (USA MODELO)
    $comp = $MC->Obtener_Datos_Basicos_Comprobante($id_comprobante);
    
    if (!$comp) {
        echo json_encode(['status' => 'error', 'message' => 'Comprobante no encontrado']);
        exit;
    }
    
    // 3️⃣ Actualizar observaciones (USA MODELO)
    $MC->Actualizar_Observaciones_Comprobante($id_comprobante, $motivo);
    
    // 4️⃣ Obtener correlativo (USA MODELO)
    $correlativo_baja = $MC->Obtener_Correlativo_Comunicacion_Baja($comp['fecha_emision']);
    
    // 5️⃣ Registrar comunicación (USA MODELO)
    $id_comunicacion = $MC->Registrar_Comunicacion_Baja($id_comprobante, $correlativo_baja, null);
    
    // 6️⃣ Comunicar a SUNAT
    $ruta_script = __DIR__ . '/../../greenter/comunicacion_baja.php';
    $comando = "php \"$ruta_script\" $id_comprobante \"$correlativo_baja\" 2>&1";
    $output = shell_exec($comando);
    
    // 7️⃣ Registrar log
    $log_file = __DIR__ . '/../../greenter/anulacion_log.txt';
    file_put_contents($log_file, "=========================\n", FILE_APPEND);
    file_put_contents($log_file, "FECHA: " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
    file_put_contents($log_file, "ID COMPROBANTE: $id_comprobante\n", FILE_APPEND);
    file_put_contents($log_file, "CORRELATIVO BAJA: $correlativo_baja\n", FILE_APPEND);
    file_put_contents($log_file, "COMANDO: $comando\n", FILE_APPEND);
    file_put_contents($log_file, "OUTPUT:\n$output\n\n", FILE_APPEND);
    
    $output_lower = strtolower($output);
    
    // 8️⃣ Extraer ticket
    preg_match('/ticket[:\s]+([A-Za-z0-9\-]+)/i', $output, $matches);
    $ticket = isset($matches[1]) ? $matches[1] : null;
    
    // 9️⃣ Analizar resultado
    if (strpos($output_lower, 'aceptada') !== false || strpos($output_lower, '✅') !== false) {
        
        // ✅ SUNAT ACEPTÓ → Anular localmente (USA MODELO)
        $resultado = $MC->Anular_Boleta_SUNAT($id_comprobante, $motivo, $usuario);
        $MC->Actualizar_Estado_Comunicacion_Baja($id_comunicacion, 'ACEPTADO', 'Comunicación de baja aceptada');
        
        echo json_encode([
            'status' => 'success',
            'message' => '✅ Boleta anulada y comunicada a SUNAT correctamente',
            'ticket' => $ticket,
            'correlativo_baja' => $correlativo_baja,
            'comprobante' => $comp['serie'] . '-' . $comp['correlativo']
        ]);
        
    } elseif (strpos($output_lower, '❌') !== false || strpos($output_lower, 'error') !== false) {
        
        // ❌ SUNAT RECHAZÓ (USA MODELO)
        $MC->Actualizar_Estado_Comunicacion_Baja($id_comunicacion, 'RECHAZADO', $output);
        
        echo json_encode([
            'status' => 'error',
            'message' => '❌ SUNAT rechazó la comunicación de baja. La boleta permanece ACTIVA.',
            'output' => nl2br($output)
        ]);
        
    } else {
        
        echo json_encode([
            'status' => 'warning',
            'message' => '⚠️ Respuesta inesperada de SUNAT. Verifique manualmente.',
            'output' => nl2br($output)
        ]);
    }
    
    exit;
}


?>

