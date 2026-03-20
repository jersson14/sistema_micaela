<?php
require_once '../../utilitario/session_config.php';

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
    $correlativo = $MC->Obtener_Correlativo($serie, $tipo_comprobante);
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
    file_put_contents(
        'debug_controller.log',
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
    file_put_contents(
        'debug_controller.log',
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
    file_put_contents(
        'debug_controller.log',
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
// 3. ENVIAR A SUNAT (CORREGIDO)
// ============================================================
// ============================================================
// 3. ENVIAR A SUNAT (CORREGIDO - LÍNEA ~195 del controller)
// ============================================================
elseif ($accion == 'ENVIAR_SUNAT') {
    header('Content-Type: application/json; charset=utf-8');
    if (function_exists('set_time_limit')) {
        @set_time_limit(240);
    }

    // Resolver binario PHP CLI real (evita usar binarios de Apache en Windows).
    $resolverPhpCli = function () {
        $es_windows = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
        $php_binario = PHP_BINARY ?: '';
        $nombre_bin = strtolower(basename((string)$php_binario));

        $binario_invalido = (
            empty($php_binario) ||
            strpos($nombre_bin, 'httpd') !== false ||
            strpos($nombre_bin, 'apache') !== false ||
            strpos($nombre_bin, '.dll') !== false
        );

        if (!$binario_invalido && @is_file($php_binario)) {
            return $php_binario;
        }

        if ($es_windows) {
            $candidatos = [
                rtrim((string)PHP_BINDIR, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'php.exe',
                'C:\\xampp\\php\\php.exe',
                'php'
            ];
        } else {
            $candidatos = [
                rtrim((string)PHP_BINDIR, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'php',
                '/usr/bin/php',
                '/usr/local/bin/php',
                'php'
            ];
        }

        foreach ($candidatos as $cand) {
            if ($cand === 'php') {
                return $cand;
            }
            if (@is_file($cand)) {
                return $cand;
            }
        }

        return 'php';
    };

    $php_cli_real = $resolverPhpCli();

    // 🔍 Verificar que llegue el ID
    if (!isset($_POST['id_comprobante']) || empty($_POST['id_comprobante'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'No se recibió el ID del comprobante'
        ]);
        exit;
    }

    $id_comprobante = intval($_POST['id_comprobante']);

    // 🔍 DEBUG
    file_put_contents(
        'debug_envio_sunat.log',
        '[' . date('Y-m-d H:i:s') . '] Intentando enviar comprobante ID: ' . $id_comprobante . PHP_EOL,
        FILE_APPEND
    );

    // 1️⃣ Obtener datos del comprobante
    $comprobante = $MC->Obtener_Datos_Basicos_Comprobante($id_comprobante);

    // 🔍 DEBUG
    file_put_contents(
        'debug_envio_sunat.log',
        '[' . date('Y-m-d H:i:s') . '] Datos obtenidos: ' . print_r($comprobante, true) . PHP_EOL,
        FILE_APPEND
    );

    if (!$comprobante || !is_array($comprobante)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Comprobante no encontrado. Verifique que el ID ' . $id_comprobante . ' exista en la base de datos.'
        ]);
        exit;
    }

    // Verificar campos esenciales
    if (empty($comprobante['serie']) || empty($comprobante['correlativo'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'El comprobante no tiene serie o correlativo'
        ]);
        exit;
    }

    // 🔧 DETERMINAR NOMBRE DEL TIPO DE COMPROBANTE
    $tipo_nombre = 'Comprobante'; // Valor por defecto

    if (isset($comprobante['tipo_comprobante']) && !empty($comprobante['tipo_comprobante'])) {
        switch ($comprobante['tipo_comprobante']) {
            case '01':
                $tipo_nombre = 'Factura';
                break;
            case '03':
                $tipo_nombre = 'Boleta';
                break;
            case '07':
                $tipo_nombre = 'Nota de Crédito';
                break;
            case '08':
                $tipo_nombre = 'Nota de Débito';
                break;
        }
    }

    // 2️⃣ Generar nombres de archivos
    $numero_completo = $comprobante['serie'] . '-' . str_pad($comprobante['correlativo'], 8, '0', STR_PAD_LEFT);
    $nombre_cdr = 'R-' . $numero_completo . '.zip';

    // 2.1️⃣ Modo envío en segundo plano (deshabilitado para flujo estricto)
    // Aunque el frontend antiguo envíe background=1, aquí forzamos modo síncrono
    // para que el ticket solo se emita después de respuesta SUNAT.
    $permitir_background = false;
    $enviar_background = $permitir_background && isset($_POST['background']) && $_POST['background'] == '1';
    if ($enviar_background) {
        $ruta_script_bg = __DIR__ . '/../../greenter/factura_bd.php';
        $es_windows_bg = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
        $lanzado_bg = false;
        $comando_bg = '';

        if ($es_windows_bg) {
            $php_win = '"' . str_replace('"', '\"', $php_cli_real) . '"';
            $script_win = '"' . str_replace('"', '\"', $ruta_script_bg) . '"';
            $comando_bg = "start /B \"\" {$php_win} {$script_win} {$id_comprobante} >NUL 2>&1";
            $proc = @popen($comando_bg, 'r');
            if (is_resource($proc)) {
                @pclose($proc);
                $lanzado_bg = true;
            }
        } else {
            $php_bg = escapeshellarg($php_cli_real);
            $script_bg = escapeshellarg($ruta_script_bg);
            $id_bg = escapeshellarg((string)$id_comprobante);
            $comando_bg = "nohup {$php_bg} {$script_bg} {$id_bg} > /dev/null 2>&1 &";
            @exec($comando_bg, $tmp_out_bg, $code_bg);
            $lanzado_bg = ($code_bg === 0);
        }

        // Log específico de cola/background
        $log_file_bg = __DIR__ . '/../../greenter/envio_log.txt';
        file_put_contents($log_file_bg, "=========================\n", FILE_APPEND);
        file_put_contents($log_file_bg, "FECHA: " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
        file_put_contents($log_file_bg, "MODO: BACKGROUND\n", FILE_APPEND);
        file_put_contents($log_file_bg, "ID COMPROBANTE: $id_comprobante\n", FILE_APPEND);
        file_put_contents($log_file_bg, "TIPO: $tipo_nombre ($comprobante[tipo_comprobante])\n", FILE_APPEND);
        file_put_contents($log_file_bg, "NUMERO: $numero_completo\n", FILE_APPEND);
        file_put_contents($log_file_bg, "CMD: $comando_bg\n", FILE_APPEND);
        file_put_contents($log_file_bg, "LANZADO: " . ($lanzado_bg ? 'SI' : 'NO') . "\n\n", FILE_APPEND);

        if ($lanzado_bg) {
            $MC->Actualizar_Estado_SUNAT(
                $id_comprobante,
                'PENDIENTE',
                'QUEUE',
                '[COLA] Envío a SUNAT en segundo plano iniciado'
            );

            echo json_encode([
                'status' => 'queued',
                'message' => "📨 {$tipo_nombre} registrada. Envío a SUNAT en segundo plano iniciado.",
                'id_comprobante' => $id_comprobante,
                'numero' => $numero_completo
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => "No se pudo iniciar el envío en segundo plano para {$tipo_nombre}. Intente reenviar desde la lista de pendientes."
            ]);
        }
        exit;
    }

    // 3️⃣ Ejecutar script de Greenter
    $ruta_script = __DIR__ . '/../../greenter/factura_bd.php';
    $php_bin = escapeshellarg($php_cli_real);
    $script_arg = escapeshellarg($ruta_script);
    $id_arg = escapeshellarg((string)$id_comprobante);
    $comando_base = "{$php_bin} {$script_arg} {$id_arg}";
    $es_windows = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
    if ($es_windows) {
        $comando = "{$comando_base} 2>&1";
    } else {
        $timeout_cmd = trim((string)@shell_exec('command -v timeout 2>/dev/null'));
        $comando = !empty($timeout_cmd)
            ? "timeout 180s {$comando_base} 2>&1"
            : "{$comando_base} 2>&1";
    }

    $output_lines = [];
    $codigo_salida_comando = 0;
    @exec($comando, $output_lines, $codigo_salida_comando);
    $output = trim(implode(PHP_EOL, $output_lines));
    if ($output === null) {
        $output = '';
    }

    $fue_timeout_comando = (!$es_windows && (int)$codigo_salida_comando === 124);
    if ($fue_timeout_comando) {
        $output .= "\n⚠️ ERROR TRANSITORIO SUNAT\n";
        $output .= "Código: TIMEOUT\n";
        $output .= "Mensaje: Tiempo de espera agotado al enviar a SUNAT\n";
    }

    // 4️⃣ Registrar log
    $log_file = __DIR__ . '/../../greenter/envio_log.txt';
    file_put_contents($log_file, "=========================\n", FILE_APPEND);
    file_put_contents($log_file, "FECHA: " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
    file_put_contents($log_file, "ID COMPROBANTE: $id_comprobante\n", FILE_APPEND);
    file_put_contents($log_file, "TIPO: $tipo_nombre ($comprobante[tipo_comprobante])\n", FILE_APPEND);
    file_put_contents($log_file, "NUMERO: $numero_completo\n", FILE_APPEND);
    file_put_contents($log_file, "NOMBRE CDR: $nombre_cdr\n", FILE_APPEND);
    file_put_contents($log_file, "EXIT CODE: $codigo_salida_comando\n", FILE_APPEND);
    file_put_contents($log_file, "OUTPUT:\n$output\n\n", FILE_APPEND);

    // 5️⃣ Analizar respuesta
    $output_lower = strtolower($output);
    $codigo_error = null;
    $mensaje_error = null;

    if (preg_match('/^c[oó]digo:\s*([^\r\n]+)/mi', $output, $match_codigo)) {
        $codigo_error = strtoupper(trim($match_codigo[1]));
    }
    if (preg_match('/^mensaje:\s*([^\r\n]+)/mi', $output, $match_mensaje)) {
        $mensaje_error = trim($match_mensaje[1]);
    }
    if ($fue_timeout_comando) {
        if (empty($codigo_error)) {
            $codigo_error = 'TIMEOUT';
        }
        if (empty($mensaje_error)) {
            $mensaje_error = 'Tiempo de espera agotado al enviar a SUNAT';
        }
    }

    $codigo_error_norm = strtoupper(trim((string)$codigo_error));
    $codigo_es_error = (
        $codigo_error_norm !== '' &&
        $codigo_error_norm !== '0' &&
        $codigo_error_norm !== '0000' &&
        $codigo_error_norm !== 'OK'
    );

    $es_aceptado = (
        strpos($output_lower, 'aceptado por sunat') !== false ||
        strpos($output_lower, 'ha sido aceptad') !== false
    );
    $tiene_ticket = (strpos($output_lower, 'ticket recibido') !== false);
    $es_error_explicito = (
        $fue_timeout_comando ||
        strpos($output_lower, '❌ error en el envío') !== false ||
        strpos($output_lower, '❌ error en el envio') !== false ||
        strpos($output_lower, 'error en el envío') !== false ||
        strpos($output_lower, 'error en el envio') !== false ||
        strpos($output_lower, 'error transitorio sunat') !== false ||
        strpos($output_lower, 'rechazado') !== false ||
        $codigo_es_error ||
        !empty($mensaje_error)
    );
    $mensaje_error_lower = strtolower((string)$mensaje_error);
    $es_error_transitorio = (
        $fue_timeout_comando ||
        $codigo_error_norm === 'TIMEOUT' ||
        $codigo_error_norm === 'HTTP' ||
        strpos($output_lower, 'error transitorio sunat') !== false ||
        strpos($output_lower, 'internal server error') !== false ||
        strpos($output_lower, 'service unavailable') !== false ||
        strpos($output_lower, 'temporarily unavailable') !== false ||
        strpos($output_lower, 'gateway timeout') !== false ||
        strpos($output_lower, 'timeout') !== false ||
        strpos($mensaje_error_lower, 'internal server error') !== false ||
        strpos($mensaje_error_lower, 'timeout') !== false ||
        strpos($mensaje_error_lower, 'service unavailable') !== false ||
        strpos($mensaje_error_lower, 'temporarily unavailable') !== false
    );

    if ($es_aceptado) {
        // 6️⃣ Generar hash del CDR si existe
        $ruta_cdr_info = $MC->Obtener_Ruta_CDR($comprobante['serie'], $comprobante['correlativo'], $comprobante['fecha_emision']);
        $hash_cdr = null;

        if (file_exists($ruta_cdr_info['ruta_completa'])) {
            $hash_cdr = hash_file('sha256', $ruta_cdr_info['ruta_completa']);
        }

        // 7️⃣ Actualizar estado con mensaje correcto
        $mensaje_aceptacion = "La {$tipo_nombre} numero {$numero_completo}, ha sido aceptada";

        $MC->Actualizar_Estado_SUNAT(
            $id_comprobante,
            'ACEPTADO',
            '0',
            $mensaje_aceptacion,
            $nombre_cdr,
            $hash_cdr
        );

        echo json_encode([
            'status'  => 'success',
            'message' => "✅ {$tipo_nombre} ACEPTADA por SUNAT",
            'output'  => nl2br($output),
            'nombre_cdr' => $nombre_cdr,
            'hash_cdr' => $hash_cdr
        ]);
    } elseif ($tiene_ticket) {
        $MC->Actualizar_Estado_SUNAT($id_comprobante, 'ENVIADO');

        echo json_encode([
            'status'  => 'info',
            'message' => "📤 {$tipo_nombre} ENVIADA a SUNAT",
            'output'  => nl2br($output)
        ]);
    } elseif ($es_error_explicito) {
        if ($es_error_transitorio) {
            $descripcion_temporal = $mensaje_error ?: 'Error temporal al comunicar con SUNAT';

            $MC->Actualizar_Estado_SUNAT(
                $id_comprobante,
                'PENDIENTE',
                $codigo_error,
                '[TEMPORAL] ' . $descripcion_temporal
            );

            echo json_encode([
                'status'  => 'pending',
                'message' => "⚠️ {$tipo_nombre} en estado PENDIENTE por error temporal de SUNAT. Reintente el envío en 1-2 minutos y no anule el comprobante.",
                'output'  => nl2br($output)
            ]);
        } else {
            $descripcion_rechazo = $mensaje_error ?: $output;

            $MC->Actualizar_Estado_SUNAT(
                $id_comprobante,
                'RECHAZADO',
                $codigo_error,
                $descripcion_rechazo
            );

            echo json_encode([
                'status'  => 'error',
                'message' => "❌ {$tipo_nombre} RECHAZADA por SUNAT",
                'output'  => nl2br($output)
            ]);
        }
    } else {
        $MC->Actualizar_Estado_SUNAT($id_comprobante, 'PENDIENTE', null, $output);

        echo json_encode([
            'status'  => 'error',
            'message' => '⚠️ SUNAT no devolvió un estado concluyente. Reintente el envío.',
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
// ANULAR COMPROBANTE (FACTURA O BOLETA) Y COMUNICAR A SUNAT
// ============================================================
// ============================================================
// ANULAR COMPROBANTE (FACTURA O BOLETA) Y COMUNICAR A SUNAT
// ============================================================
elseif ($accion == 'ANULAR_COMPROBANTE_SUNAT') {
    header('Content-Type: application/json; charset=utf-8');

    $id_comprobante = $_POST['id_comprobante'];
    $motivo = strtoupper(trim($_POST['motivo']));
    $usuario = $_POST['usuario'];
    $tipo_comprobante = $_POST['tipo_comprobante'] ?? null;

    // 1️⃣ Obtener datos básicos del comprobante
    $comp = $MC->Obtener_Datos_Basicos_Comprobante($id_comprobante);

    if (!$comp) {
        echo json_encode(['status' => 'error', 'message' => 'Comprobante no encontrado']);
        exit;
    }

    // Determinar tipo de comprobante
    $tipo = $tipo_comprobante ?? $comp['tipo_comprobante'];
    $es_boleta = ($tipo == '03');
    $es_factura = ($tipo == '01');
    $tipo_texto = $es_boleta ? 'BOLETA' : ($es_factura ? 'FACTURA' : 'COMPROBANTE');

    // 2️⃣ Validaciones según tipo de documento
    if (!in_array($tipo, ['01', '03'])) {
        echo json_encode([
            'status' => 'error', 
            'message' => 'Solo se pueden anular Facturas (01) o Boletas (03). Tipo recibido: ' . $tipo
        ]);
        exit;
    }

    // 3️⃣ Verificar estado del comprobante
    if ($comp['estado_documento'] == 'ANULADO') {
        echo json_encode([
            'status' => 'error', 
            'message' => 'El comprobante ya está anulado'
        ]);
        exit;
    }

    if (!in_array($comp['estado_sunat'], ['ACEPTADO', 'ENVIADO'])) {
        echo json_encode([
            'status' => 'error', 
            'message' => 'El comprobante debe estar ACEPTADO por SUNAT. Estado actual: ' . $comp['estado_sunat']
        ]);
        exit;
    }

    // 4️⃣ Validación especial para BOLETAS (máximo 7 días)
    if ($es_boleta) {
        $verificacion = $MC->Verificar_Boleta_Anulable($id_comprobante);
        if (!$verificacion['anulable']) {
            echo json_encode([
                'status' => 'error', 
                'message' => $verificacion['motivo']
            ]);
            exit;
        }
    }

    // 5️⃣ Actualizar observaciones con el motivo
    $motivo_completo = "[MOTIVO ANULACIÓN]\n" . $motivo;
    $MC->Actualizar_Observaciones_Comprobante($id_comprobante, $motivo_completo);

    // 6️⃣ Obtener correlativo de comunicación de baja
    $correlativo_baja = $MC->Obtener_Correlativo_Comunicacion_Baja($comp['fecha_emision']);

    // 7️⃣ Registrar comunicación en BD
    $id_comunicacion = $MC->Registrar_Comunicacion_Baja($id_comprobante, $correlativo_baja, null);

    if (!$id_comunicacion) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error al registrar la comunicación de baja en BD'
        ]);
        exit;
    }

    // 8️⃣ Ejecutar script de comunicación a SUNAT
    $ruta_script = __DIR__ . '/../../greenter/comunicacion_baja.php';
    $comando = "php \"$ruta_script\" $id_comprobante \"$correlativo_baja\" 2>&1";
    
    // Registrar inicio de proceso
    $log_file = __DIR__ . '/../../greenter/anulacion_log.txt';
    file_put_contents($log_file, "\n=========================\n", FILE_APPEND);
    file_put_contents($log_file, "FECHA: " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
    file_put_contents($log_file, "TIPO: $tipo_texto\n", FILE_APPEND);
    file_put_contents($log_file, "ID COMPROBANTE: $id_comprobante\n", FILE_APPEND);
    file_put_contents($log_file, "SERIE-CORRELATIVO: {$comp['serie']}-{$comp['correlativo']}\n", FILE_APPEND);
    file_put_contents($log_file, "CORRELATIVO BAJA: $correlativo_baja\n", FILE_APPEND);
    file_put_contents($log_file, "COMANDO: $comando\n", FILE_APPEND);
    
    $output = shell_exec($comando);
    
    file_put_contents($log_file, "OUTPUT:\n$output\n", FILE_APPEND);

    $output_lower = strtolower($output ?? '');

    // 9️⃣ Extraer ticket de la respuesta
    preg_match('/ticket[:\s]+([A-Za-z0-9\-]+)/i', $output ?? '', $matches);
    $ticket = $matches[1] ?? null;

    // 🔟 Analizar resultado de SUNAT
    if (strpos($output_lower, '✅') !== false || 
        strpos($output_lower, 'aceptado') !== false ||
        strpos($output_lower, 'ticket recibido') !== false) {

        // ✅ SUNAT ACEPTÓ O DEVOLVIÓ TICKET
        
        if ($ticket) {
            // Tiene ticket - proceso asíncrono
            $MC->Actualizar_Ticket_Comunicacion_Baja($id_comprobante, $ticket);
            $MC->Actualizar_Estado_Comunicacion_Baja($id_comunicacion, 'ENVIADO', 'Ticket: ' . $ticket);
            
            echo json_encode([
                'status' => 'success',
                'message' => "✅ $tipo_texto enviada a SUNAT. Ticket recibido correctamente.",
                'ticket' => $ticket,
                'correlativo_baja' => $correlativo_baja,
                'comprobante' => $comp['serie'] . '-' . $comp['correlativo'],
                'info' => 'Debe consultar el ticket para confirmar la anulación'
            ]);
        } else {
            // Respuesta inmediata (raro, pero posible)
            $resultado = $MC->Anular_Comprobante_Local($id_comprobante, $motivo, $usuario);
            $MC->Actualizar_Estado_Comunicacion_Baja($id_comunicacion, 'ACEPTADO', 'Comunicación aceptada por SUNAT');
            
            echo json_encode([
                'status' => 'success',
                'message' => "✅ $tipo_texto anulada y comunicada a SUNAT correctamente",
                'correlativo_baja' => $correlativo_baja,
                'comprobante' => $comp['serie'] . '-' . $comp['correlativo']
            ]);
        }

    } elseif (strpos($output_lower, '❌') !== false || 
              strpos($output_lower, 'error') !== false ||
              strpos($output_lower, 'rechazado') !== false) {

        // ❌ SUNAT RECHAZÓ
        $MC->Actualizar_Estado_Comunicacion_Baja($id_comunicacion, 'RECHAZADO', substr($output, 0, 500));

        // Extraer mensaje de error específico
        preg_match('/código[:\s]+(\d+)/i', $output ?? '', $code_match);
        $error_code = $code_match[1] ?? 'desconocido';

        echo json_encode([
            'status' => 'error',
            'message' => "❌ SUNAT rechazó la comunicación de baja. El $tipo_texto permanece ACTIVO.",
            'error_code' => $error_code,
            'output' => $output
        ]);

    } else {

        // ⚠️ RESPUESTA INESPERADA
        file_put_contents($log_file, "⚠️ RESPUESTA INESPERADA DE SUNAT\n", FILE_APPEND);
        
        echo json_encode([
            'status' => 'warning',
            'message' => '⚠️ Respuesta inesperada de SUNAT. Verifique manualmente el estado.',
            'output' => $output,
            'correlativo_baja' => $correlativo_baja
        ]);
    }

    exit;
}

// ============================================================
// CONSULTAR TICKET DE ANULACIÓN
// ============================================================
elseif ($accion == 'CONSULTAR_TICKET_ANULACION') {
    header('Content-Type: application/json; charset=utf-8');

    $id_comprobante = $_POST['id_comprobante'];

    // Obtener ticket de la última comunicación de baja
    $sql = "SELECT cb.*, c.serie, c.correlativo 
            FROM comunicaciones_baja cb
            INNER JOIN comprobantes c ON cb.id_comprobante = c.id_comprobante
            WHERE cb.id_comprobante = ? 
            AND cb.ticket_sunat IS NOT NULL
            ORDER BY cb.fecha_comunicacion DESC 
            LIMIT 1";

    $c = conexionBD::conexionPDO();
    $query = $c->prepare($sql);
    $query->execute([$id_comprobante]);
    $comunicacion = $query->fetch(PDO::FETCH_ASSOC);

    if (!$comunicacion || !$comunicacion['ticket_sunat']) {
        echo json_encode([
            'status' => 'error',
            'message' => 'No se encontró ticket asociado a este comprobante'
        ]);
        exit;
    }

    $ticket = $comunicacion['ticket_sunat'];

    // Ejecutar script de consulta
    $ruta_script = __DIR__ . '/../../greenter/consultar_ticket.php';
    $comando = "php \"$ruta_script\" \"$ticket\" $id_comprobante 2>&1";
    
    $output = shell_exec($comando);
    $output_lower = strtolower($output ?? '');

    // Registrar en log
    $log_file = __DIR__ . '/../../greenter/consulta_ticket_log.txt';
    file_put_contents($log_file, "\n=========================\n", FILE_APPEND);
    file_put_contents($log_file, "FECHA: " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
    file_put_contents($log_file, "TICKET: $ticket\n", FILE_APPEND);
    file_put_contents($log_file, "ID COMPROBANTE: $id_comprobante\n", FILE_APPEND);
    file_put_contents($log_file, "OUTPUT:\n$output\n", FILE_APPEND);

    // Analizar resultado
    if (strpos($output_lower, '✅ anulación aceptada') !== false) {
        
        // Extraer código y descripción
        preg_match('/código[:\s]+([0-9]+)/i', $output, $code_match);
        preg_match('/descripción[:\s]+(.+)/i', $output, $desc_match);
        
        echo json_encode([
            'status' => 'success',
            'ticket' => $ticket,
            'estado' => 'ACEPTADO',
            'descripcion' => $desc_match[1] ?? 'Anulación aceptada por SUNAT',
            'comprobante_anulado' => true,
            'message' => 'La anulación fue procesada correctamente por SUNAT'
        ]);

    } elseif (strpos($output_lower, 'ticket aún no ha sido procesado') !== false ||
              strpos($output_lower, '0127') !== false) {
        
        echo json_encode([
            'status' => 'pending',
            'ticket' => $ticket,
            'message' => 'El ticket aún está siendo procesado por SUNAT. Intente nuevamente en 1-2 minutos.'
        ]);

    } elseif (strpos($output_lower, 'rechazó') !== false || 
              strpos($output_lower, '❌') !== false) {
        
        echo json_encode([
            'status' => 'error',
            'ticket' => $ticket,
            'message' => 'SUNAT rechazó la anulación. El comprobante permanece ACTIVO.',
            'output' => nl2br($output)
        ]);

    } else {
        
        echo json_encode([
            'status' => 'warning',
            'ticket' => $ticket,
            'message' => 'Respuesta inesperada de SUNAT. Verifique manualmente.',
            'output' => nl2br($output)
        ]);
    }

    exit;
}
// ============================================================
// OBTENER COMPROBANTE PARA EDITAR
// ============================================================
elseif ($accion == 'OBTENER_COMPROBANTE_EDITAR') {
    header('Content-Type: application/json; charset=utf-8');

    // 🔍 DEBUG
    file_put_contents(
        'debug_editar.log',
        '[' . date('Y-m-d H:i:s') . '] POST recibido: ' . print_r($_POST, true) . PHP_EOL,
        FILE_APPEND
    );

    $id_comprobante = intval($_POST['id_comprobante']);

    // 🔍 DEBUG
    file_put_contents(
        'debug_editar.log',
        '[' . date('Y-m-d H:i:s') . '] ID Comprobante: ' . $id_comprobante . PHP_EOL,
        FILE_APPEND
    );

    $resultado = $MC->Obtener_Comprobante_Completo($id_comprobante);

    // 🔍 DEBUG
    file_put_contents(
        'debug_editar.log',
        '[' . date('Y-m-d H:i:s') . '] Resultado: ' . print_r($resultado, true) . PHP_EOL,
        FILE_APPEND
    );

    if ($resultado) {
        // Verificar que sea PENDIENTE
        if ($resultado['estado_sunat'] !== 'PENDIENTE') {
            echo json_encode(array(
                'status' => 'error',
                'message' => 'Solo se pueden editar comprobantes con estado PENDIENTE'
            ));
            exit;
        }

        echo json_encode($resultado);
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'Comprobante no encontrado'));
    }
}

// ============================================================
// ACTUALIZAR COMPROBANTE
// ============================================================
elseif ($accion == 'ACTUALIZAR_COMPROBANTE') {
    header('Content-Type: application/json; charset=utf-8');
    date_default_timezone_set('America/Lima');

    $id_comprobante = intval($_POST['id_comprobante']);
    $tipo_comprobante = $_POST['tipo_comprobante'];
    $serie = strtoupper($_POST['serie']);
    $correlativo = $_POST['correlativo'];
    $fecha_emision = date('Y-m-d', strtotime($_POST['fecha_emision']));
    $moneda = $_POST['moneda'];

    // Datos del cliente
    $tipo_documento = $_POST['tipo_documento_cliente'];
    $numero_documento = $_POST['numero_documento'];
    $razon_social = strtoupper($_POST['razon_social']);
    $direccion = strtoupper($_POST['direccion']);
    $celular = trim($_POST['celular']);
    $departamento = strtoupper($_POST['departamento']);
    $provincia = strtoupper($_POST['provincia']);
    $distrito = strtoupper($_POST['distrito']);
    $ubigeo = '030101';

    // Datos del servicio
    $id_servicio = $_POST['id_servicio'];
    $cantidad = floatval($_POST['cantidad']);
    $id_conductor = $_POST['id_conductor'];
    $id_origen = $_POST['id_origen'];
    $id_destino = $_POST['id_destino'];
    $fecha_viaje = $_POST['fecha_viaje'];
    $observaciones = strtoupper($_POST['observaciones']);

    // Montos
    $base_gravada = floatval($_POST['base_gravada']);
    $igv = floatval($_POST['igv']);
    $total = floatval($_POST['total']);
    $id_tipo_pago = $_POST['id_tipo_pago'];
    $id_usuario = intval($_POST['id_usuario']);

    // Validaciones
    if ($id_usuario <= 0) {
        echo json_encode(array('status' => 'error', 'message' => 'Usuario no identificado'));
        exit;
    }

    if (empty($tipo_comprobante) || empty($serie) || empty($correlativo)) {
        echo json_encode(array('status' => 'error', 'message' => 'Faltan datos del comprobante'));
        exit;
    }

    if (empty($numero_documento) || empty($razon_social)) {
        echo json_encode(array('status' => 'error', 'message' => 'Faltan datos del cliente'));
        exit;
    }

    if ($base_gravada <= 0 || $total <= 0) {
        echo json_encode(array('status' => 'error', 'message' => 'Los montos deben ser mayores a 0'));
        exit;
    }

    // PASO 1: Actualizar o registrar cliente
    $id_cliente = $MC->Actualizar_Cliente_SUNAT(
        $tipo_documento,
        $numero_documento,
        $razon_social,
        $direccion,
        $celular,
        $departamento,
        $provincia,
        $distrito,
        $ubigeo
    );

    if ($id_cliente == 0) {
        echo json_encode(array('status' => 'error', 'message' => 'Error al actualizar cliente'));
        exit;
    }

    // PASO 2: Actualizar comprobante
    $resultado = $MC->Actualizar_Comprobante(
        $id_comprobante,
        $tipo_comprobante,
        $serie,
        $correlativo,
        $fecha_emision,
        $moneda,
        $id_cliente,
        $base_gravada,
        $igv,
        $total,
        $id_tipo_pago,
        $id_usuario,
        $id_servicio,
        $cantidad,
        $fecha_viaje,
        $id_conductor,
        $id_origen,
        $id_destino,
        $observaciones
    );

    if ($resultado) {
        echo json_encode(array(
            'status' => 'success',
            'message' => 'Comprobante actualizado correctamente'
        ));
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'Error al actualizar comprobante'));
    }

    
}

// ============================================================
// REINTENTAR TODOS LOS PENDIENTES EN LOTE
// ============================================================
elseif ($accion == 'REINTENTAR_PENDIENTES_LOTE') {
    header('Content-Type: application/json; charset=utf-8');
    if (function_exists('set_time_limit')) {
        @set_time_limit(0);
    }

    $limite = isset($_POST['limite']) ? max(1, min(200, intval($_POST['limite']))) : 50;

    $php_cli = 'php';
    $candidatos_win = ['C:\\xampp\\php\\php.exe'];
    $es_win = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
    if ($es_win) {
        foreach ($candidatos_win as $c) {
            if (@is_file($c)) { $php_cli = $c; break; }
        }
    } else {
        foreach ([PHP_BINARY, '/usr/bin/php', '/usr/local/bin/php'] as $c) {
            if ($c && @is_file($c)) { $php_cli = $c; break; }
        }
    }

    $script_reenvio = __DIR__ . '/../../greenter/reenviar_pendientes.php';
    $cmd = escapeshellarg($php_cli) . ' ' . escapeshellarg($script_reenvio) . ' ' . intval($limite) . ' 0 2>&1';

    $output_lines = [];
    $exit_code = 0;
    @exec($cmd, $output_lines, $exit_code);
    $output = trim(implode(PHP_EOL, $output_lines));

    $aceptados = 0;
    $pendientes_restantes = 0;
    if (preg_match('/ACEPTADOS:\s*(\d+)/i', $output, $m)) {
        $aceptados = (int)$m[1];
    }
    if (preg_match('/PENDIENTES:\s*(\d+)/i', $output, $m)) {
        $pendientes_restantes = (int)$m[1];
    }

    echo json_encode([
        'status'               => 'success',
        'aceptados'            => $aceptados,
        'pendientes_restantes' => $pendientes_restantes,
        'output'               => nl2br(htmlspecialchars($output)),
        'message'              => "Proceso completado: {$aceptados} aceptados, {$pendientes_restantes} aún pendientes."
    ]);
}

// DECLARACION SUNAT
elseif ($accion == 'OBTENER_DATOS_DECLARACION_SUNAT') {
    $tipo = isset($_POST['tipo_comprobante']) ? $_POST['tipo_comprobante'] : '';
    $estado = isset($_POST['estado_sunat']) ? $_POST['estado_sunat'] : '';
    $fecha_desde = isset($_POST['fecha_desde']) ? $_POST['fecha_desde'] : '';
    $fecha_hasta = isset($_POST['fecha_hasta']) ? $_POST['fecha_hasta'] : '';
    
    $consulta = $MC->Obtener_Datos_Declaracion_SUNAT($tipo, $estado, $fecha_desde, $fecha_hasta);
    
    echo json_encode(array('data' => $consulta));
}
