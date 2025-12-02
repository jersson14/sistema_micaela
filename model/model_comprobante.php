<?php
ob_start();
require_once 'model_conexion.php';
class Modelo_Comprobantes extends conexionBD
{

    // ========================================
    // REGISTRAR O ACTUALIZAR CLIENTE
    // ========================================
    // ========================================
    // REGISTRAR O ACTUALIZAR CLIENTE
    // ========================================
    public function Registrar_Cliente_SUNAT(
        $tipo_documento,
        $numero_documento,
        $razon_social,
        $direccion,
        $celular,
        $departamento,
        $provincia,
        $distrito,
        $ubigeo
    ) {
        $c = conexionBD::conexionPDO();

        try {
            file_put_contents(
                'debug_modelo.log',
                '[' . date('Y-m-d H:i:s') . '] Registrar_Cliente_SUNAT llamado' . PHP_EOL .
                    '  Num Doc: ' . $numero_documento . PHP_EOL,
                FILE_APPEND
            );

            // ✅ PRIMERO VERIFICAR SI YA EXISTE
            $sql_check = "SELECT id_cliente FROM cliente_sunat WHERE numero_documento = ? LIMIT 1";
            $query_check = $c->prepare($sql_check);
            $query_check->execute([$numero_documento]);
            $existe = $query_check->fetch(PDO::FETCH_ASSOC);

            if ($existe) {
                // ✅ YA EXISTE → Actualizar
                file_put_contents(
                    'debug_modelo.log',
                    '[' . date('Y-m-d H:i:s') . '] Cliente ya existe (ID: ' . $existe['id_cliente'] . '), actualizando...' . PHP_EOL,
                    FILE_APPEND
                );

                $sql_update = "UPDATE cliente_sunat SET
                    tipo_documento = ?,
                    razon_social = ?,
                    direccion = ?,
                    telefono = ?,
                    departamento = ?,
                    provincia = ?,
                    distrito = ?,
                    ubigeo = ?,
                    updated_at = NOW()
                WHERE numero_documento = ?";

                $query_update = $c->prepare($sql_update);
                $resultado_update = $query_update->execute([
                    $tipo_documento,
                    $razon_social,
                    $direccion,
                    $celular,
                    $departamento,
                    $provincia,
                    $distrito,
                    $ubigeo,
                    $numero_documento
                ]);

                if (!$resultado_update) {
                    $errorInfo = $query_update->errorInfo();
                    file_put_contents(
                        'debug_modelo.log',
                        '[' . date('Y-m-d H:i:s') . '] ❌ ERROR UPDATE: ' . print_r($errorInfo, true) . PHP_EOL,
                        FILE_APPEND
                    );
                    return 0;
                }

                file_put_contents(
                    'debug_modelo.log',
                    '[' . date('Y-m-d H:i:s') . '] ✅ Cliente actualizado' . PHP_EOL,
                    FILE_APPEND
                );

                return $existe['id_cliente'];
            }

            // ✅ NO EXISTE → Insertar nuevo
            file_put_contents(
                'debug_modelo.log',
                '[' . date('Y-m-d H:i:s') . '] Cliente no existe, insertando nuevo...' . PHP_EOL,
                FILE_APPEND
            );

            $sql = "INSERT INTO cliente_sunat (
                    tipo_documento,
                    numero_documento,
                    razon_social,
                    direccion,
                    telefono,
                    departamento,
                    provincia,
                    distrito,
                    ubigeo,
                    activo,
                    created_at,
                    updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW(), NOW())";

            $query = $c->prepare($sql);
            $resultado = $query->execute([
                $tipo_documento,
                $numero_documento,
                $razon_social,
                $direccion,
                $celular,
                $departamento,
                $provincia,
                $distrito,
                $ubigeo
            ]);

            if (!$resultado) {
                $errorInfo = $query->errorInfo();
                file_put_contents(
                    'debug_modelo.log',
                    '[' . date('Y-m-d H:i:s') . '] ❌ ERROR INSERT: ' . print_r($errorInfo, true) . PHP_EOL,
                    FILE_APPEND
                );
                return 0;
            }

            $id_cliente = $c->lastInsertId();

            file_put_contents(
                'debug_modelo.log',
                '[' . date('Y-m-d H:i:s') . '] ✅ Cliente registrado con ID: ' . $id_cliente . PHP_EOL,
                FILE_APPEND
            );

            return $id_cliente;
        } catch (Exception $e) {
            file_put_contents(
                'debug_modelo.log',
                '[' . date('Y-m-d H:i:s') . '] ❌ EXCEPTION: ' . $e->getMessage() . PHP_EOL,
                FILE_APPEND
            );
            error_log("Error en Registrar_Cliente_SUNAT: " . $e->getMessage());
            return 0;
        } finally {
            conexionBD::cerrar_conexion();
        }
    }

    // ========================================
    // OBTENER CORRELATIVO
    // ========================================
    public function Obtener_Correlativo($serie, $tipo_comprobante)
    {
        $c = conexionBD::conexionPDO();

        try {
            $sql = "CALL SP_OBTENER_CORRELATIVO(?, ?, @correlativo_out)";
            $query = $c->prepare($sql);
            $query->bindParam(1, $serie);
            $query->bindParam(2, $tipo_comprobante);
            $query->execute();

            $result = $c->query("SELECT @correlativo_out as correlativo")->fetch(PDO::FETCH_ASSOC);
            return str_pad($result['correlativo'], 8, '0', STR_PAD_LEFT);
        } catch (Exception $e) {
            return '00000001';
        }

        conexionBD::cerrar_conexion();
    }

    // ========================================
    // REGISTRAR COMPROBANTE
    // ========================================
    public function Registrar_Comprobante(
        $tipo_comprobante,
        $serie,
        $correlativo,
        $fecha_emision,
        $hora_emision,
        $moneda,
        $id_cliente,
        $subtotal,
        $total_igv,
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
    ) {
        $c = conexionBD::conexionPDO();

        try {
            // ⚠️ SP tiene 23 parámetros de entrada y 1 de salida
            $sql = "CALL SP_REGISTRAR_COMPROBANTE(
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, @p_id_comprobante
        )";

            $query = $c->prepare($sql);

            // 📦 Vincular los 23 parámetros exactamente en orden
            $query->bindParam(1,  $tipo_comprobante);
            $query->bindParam(2,  $serie);
            $query->bindParam(3,  $correlativo);
            $query->bindParam(4,  $fecha_emision);
            $query->bindParam(5,  $hora_emision);
            $query->bindParam(6,  $moneda);
            $query->bindParam(7,  $id_cliente);
            $query->bindParam(8,  $subtotal);          // p_subtotal
            $query->bindParam(9,  $subtotal);          // p_total_gravada
            $query->bindParam(10, $total_igv);         // p_total_igv
            $query->bindParam(11, $total_igv);         // p_total_impuestos
            $query->bindParam(12, $total);             // p_total
            $query->bindParam(13, $forma_pago);
            $query->bindParam(14, $id_tipo_pago);
            $query->bindParam(15, $estado_sunat);
            $query->bindParam(16, $id_usuario);
            $query->bindParam(17, $id_servicio);
            $query->bindParam(18, $cantidad);
            $query->bindParam(19, $fecha_viaje);
            $query->bindParam(20, $id_conductor);
            $query->bindParam(21, $id_origen);
            $query->bindParam(22, $id_destino);
            $query->bindParam(23, $observaciones);

            $query->execute();

            // 🔍 Obtener valor del parámetro de salida
            $result = $c->query("SELECT @p_id_comprobante AS id_comprobante")->fetch(PDO::FETCH_ASSOC);

            if ($result && !empty($result['id_comprobante'])) {
                return (int)$result['id_comprobante'];
            } else {
                file_put_contents('debug_comprobante.log', "[WARN] No devolvió ID\n", FILE_APPEND);
                return 0;
            }
        } catch (Exception $e) {
            file_put_contents('debug_comprobante.log', "[ERROR] " . $e->getMessage() . "\n", FILE_APPEND);
            return 0;
        } finally {
            conexionBD::cerrar_conexion();
        }
    }



    // ========================================
    // LISTAR COMPROBANTES
    // ========================================
    public function Listar_Comprobantes($estado = '', $fecha_desde = '', $fecha_hasta = '')
    {
        $c = conexionBD::conexionPDO();

        $sql = "SELECT 
                c.id_comprobante,
                c.tipo_comprobante,
                c.serie,
                c.correlativo,
                CONCAT(c.serie, '-', LPAD(c.correlativo, 8, '0')) AS numero_comprobante,
                c.fecha_emision,
                c.hora_emision,
                c.total,
                c.total_gravada,
                c.total_igv,
                c.estado_sunat,
                c.estado_documento,
                c.descripcion_respuesta_sunat,
                c.codigo_respuesta_sunat,
                c.fecha_envio_sunat,
                c.observaciones,
                cl.razon_social,
                cl.numero_documento,
                cl.direccion,
                tp.tipo_pago,
                s.nombre AS servicio_nombre,
                r_origen.nombre AS origen,
                r_destino.nombre AS destino,
                CONCAT(u.usu_nombre, ' ', u.usu_apellido) AS usuario_nombre,
                c.created_at
            FROM comprobantes c
            INNER JOIN cliente_sunat cl ON c.id_cliente = cl.id_cliente
            LEFT JOIN tipo_pago tp ON c.id_tipo_pago = tp.id_tipo_pago
            LEFT JOIN servicios s ON c.id_servicio = s.id_servicio
            LEFT JOIN rutas r_origen ON c.id_origen = r_origen.idrutas
            LEFT JOIN rutas r_destino ON c.iddestino = r_destino.idrutas
            LEFT JOIN usuario u ON c.id_usuario = u.id_usuario
            WHERE 1=1";

        $params = [];

        // 🔹 Filtro por estado
        if (!empty($estado)) {
            $sql .= " AND c.estado_sunat = ?";
            $params[] = $estado;
        }

        // 🔹 Filtros de fecha (más robustos)
        if (!empty($fecha_desde) && !empty($fecha_hasta)) {
            $sql .= " AND DATE(c.fecha_emision) BETWEEN ? AND ?";
            $params[] = $fecha_desde;
            $params[] = $fecha_hasta;
        } elseif (!empty($fecha_desde)) {
            $sql .= " AND DATE(c.fecha_emision) >= ?";
            $params[] = $fecha_desde;
        } elseif (!empty($fecha_hasta)) {
            $sql .= " AND DATE(c.fecha_emision) <= ?";
            $params[] = $fecha_hasta;
        }

        // 🔹 Mostrar los más recientes primero
        $sql .= " ORDER BY c.fecha_emision DESC, c.id_comprobante DESC";

        $query = $c->prepare($sql);

        foreach ($params as $key => $value) {
            $query->bindValue($key + 1, $value);
        }

        $query->execute();
        $resultado = $query->fetchAll(PDO::FETCH_ASSOC);

        conexionBD::cerrar_conexion();

        return $resultado;
    }


    // ========================================
    // OBTENER COMPROBANTE POR ID
    // ========================================
    public function Obtener_Comprobante($id_comprobante)
    {
        $c = conexionBD::conexionPDO();

        $sql = "SELECT 
    c.id_comprobante,
    c.tipo_comprobante,
    c.serie,
    c.correlativo,
    CONCAT(c.serie, '-', LPAD(c.correlativo, 8, '0')) AS numero_comprobante,
    c.fecha_emision,
    c.hora_emision,
    c.total,
    c.motivo_nota,
    c.texto_motivo,
    c.total_gravada,
    c.total_igv,
    c.moneda,
    c.estado_sunat,
    c.estado_documento,
    c.descripcion_respuesta_sunat,
    c.codigo_respuesta_sunat,
    c.fecha_envio_sunat,
    c.observaciones,
    cl.razon_social,
    cl.numero_documento,
    cl.direccion,
    tp.tipo_pago AS tipo_pago_actual,                 -- ✅ tipo de pago del comprobante principal
    s.nombre AS servicio_nombre,
    r_origen.nombre AS origen,
    r_destino.nombre AS destino,
    CONCAT(u.usu_nombre, ' ', u.usu_apellido) AS usuario_nombre,
    c.created_at,
    -- ✅ COMPROBANTE AFECTADO (para notas de crédito/débito)
    c.id_comprobante_origen,
    c_origen.tipo_comprobante AS tipo_comprobante_origen,
    c_origen.serie AS serie_origen,
    c_origen.correlativo AS correlativo_origen,
    CONCAT(c_origen.serie, '-', LPAD(c_origen.correlativo, 8, '0')) AS numero_comprobante_origen,
    tp_origen.tipo_pago AS tipo_pago_origen           -- ✅ tipo de pago del comprobante afectado
FROM comprobantes c
INNER JOIN cliente_sunat cl ON c.id_cliente = cl.id_cliente
LEFT JOIN tipo_pago tp ON c.id_tipo_pago = tp.id_tipo_pago
LEFT JOIN servicios s ON c.id_servicio = s.id_servicio
LEFT JOIN rutas r_origen ON c.id_origen = r_origen.idrutas
LEFT JOIN rutas r_destino ON c.iddestino = r_destino.idrutas
LEFT JOIN usuario u ON c.id_usuario = u.id_usuario
LEFT JOIN comprobantes c_origen ON c.id_comprobante_origen = c_origen.id_comprobante
LEFT JOIN tipo_pago tp_origen ON c_origen.id_tipo_pago = tp_origen.id_tipo_pago   -- ✅ nuevo join
WHERE c.id_comprobante =?";

        $query = $c->prepare($sql);
        $query->bindParam(1, $id_comprobante, PDO::PARAM_INT);
        $query->execute();

        $resultado = $query->fetch(PDO::FETCH_ASSOC);

        conexionBD::cerrar_conexion();
        return $resultado;
    }

    // ========================================
    // VERIFICAR ESTADO SUNAT
    // ========================================
    public function Verificar_Estado_SUNAT($id_comprobante)
    {
        $c = conexionBD::conexionPDO();
        $sql = "SELECT estado_sunat FROM comprobantes WHERE id_comprobante = ?";
        $query = $c->prepare($sql);
        $query->bindParam(1, $id_comprobante);
        $query->execute();
        $resultado = $query->fetch(PDO::FETCH_ASSOC);
        conexionBD::cerrar_conexion();
        return $resultado['estado_sunat'] ?? 'PENDIENTE';
    }


    // ========================================
    // ACTUALIZAR ESTADO SUNAT
    // ========================================
    // ========================================
    // ACTUALIZAR NOMBRE ARCHIVO CDR DESPUÉS DE ENVIAR A SUNAT
    // ========================================
    public function Actualizar_Archivo_CDR($id_comprobante, $nombre_archivo_cdr, $hash_cdr = null)
    {
        $c = conexionBD::conexionPDO();

        try {
            $sql = "UPDATE comprobantes SET
                nombre_archivo_cdr = ?,
                hash_cdr = ?,
                updated_at = NOW()
            WHERE id_comprobante = ?";

            $query = $c->prepare($sql);
            $query->execute([
                $nombre_archivo_cdr,
                $hash_cdr,
                $id_comprobante
            ]);

            return true;
        } catch (Exception $e) {
            error_log("Error en Actualizar_Archivo_CDR: " . $e->getMessage());
            return false;
        } finally {
            conexionBD::cerrar_conexion();
        }
    }

    // ========================================
    // ACTUALIZAR ESTADO SUNAT (MODIFICADO PARA INCLUIR CDR)
    // ========================================
    public function Actualizar_Estado_SUNAT($id_comprobante, $estado, $codigo_respuesta = null, $descripcion_respuesta = null, $nombre_cdr = null, $hash_cdr = null)
    {
        $c = conexionBD::conexionPDO();

        try {
            $sql = "UPDATE comprobantes SET
                estado_sunat = ?,
                codigo_respuesta_sunat = ?,
                descripcion_respuesta_sunat = ?,
                fecha_envio_sunat = NOW(),
                nombre_archivo_cdr = ?,
                hash_cdr = ?,
                updated_at = NOW()
            WHERE id_comprobante = ?";

            $query = $c->prepare($sql);
            $query->execute([
                $estado,
                $codigo_respuesta,
                $descripcion_respuesta,
                $nombre_cdr,
                $hash_cdr,
                $id_comprobante
            ]);

            return true;
        } catch (Exception $e) {
            error_log("Error en Actualizar_Estado_SUNAT: " . $e->getMessage());
            return false;
        } finally {
            conexionBD::cerrar_conexion();
        }
    }

    // ========================================
    // OBTENER RUTA COMPLETA DE XML
    // ========================================
    public function Obtener_Ruta_XML($serie, $correlativo, $fecha_emision)
    {
        $anio = date('Y', strtotime($fecha_emision));
        $mes = date('m', strtotime($fecha_emision));

        $nombre_archivo = $serie . '-' . str_pad($correlativo, 8, '0', STR_PAD_LEFT) . '.xml';
        $ruta_relativa = 'xml/' . $anio . '/' . $mes . '/';
        $ruta_completa = __DIR__ . '/../../greenter/' . $ruta_relativa . $nombre_archivo;

        return [
            'nombre' => $nombre_archivo,
            'ruta_relativa' => $ruta_relativa,
            'ruta_completa' => $ruta_completa,
            'ruta_carpeta' => __DIR__ . '/../../greenter/' . $ruta_relativa
        ];
    }

    // ========================================
    // OBTENER RUTA COMPLETA DE CDR
    // ========================================
    public function Obtener_Ruta_CDR($serie, $correlativo, $fecha_emision)
    {
        $anio = date('Y', strtotime($fecha_emision));
        $mes = date('m', strtotime($fecha_emision));

        $nombre_archivo = 'R-' . $serie . '-' . str_pad($correlativo, 8, '0', STR_PAD_LEFT) . '.zip';
        $ruta_relativa = 'cdr/' . $anio . '/' . $mes . '/';
        $ruta_completa = __DIR__ . '/../../greenter/' . $ruta_relativa . $nombre_archivo;

        return [
            'nombre' => $nombre_archivo,
            'ruta_relativa' => $ruta_relativa,
            'ruta_completa' => $ruta_completa,
            'ruta_carpeta' => __DIR__ . '/../../greenter/' . $ruta_relativa
        ];
    }


    // ========================================
    // ANULAR COMPROBANTE
    // ========================================
    public function Anular_Comprobante($id_comprobante, $motivo, $usuario)
    {
        $c = conexionBD::conexionPDO();

        try {
            $sql = "CALL SP_ANULAR_COMPROBANTE(?, ?, ?)";
            $query = $c->prepare($sql);
            $query->bindParam(1, $id_comprobante);
            $query->bindParam(2, $motivo);
            $query->bindParam(3, $usuario);

            $result = $query->execute();
            return $result ? 1 : 0;
        } catch (Exception $e) {
            return 0;
        }

        conexionBD::cerrar_conexion();
    }
    public function ObtenerResumenEnvios()
    {
        $c = conexionBD::conexionPDO();

        $sql_pendientes = "SELECT COUNT(*) as total FROM comprobantes 
                          WHERE estado_sunat = 'PENDIENTE' AND estado_documento = 'ACTIVO'";

        $sql_enviados = "SELECT COUNT(*) as total FROM comprobantes 
                        WHERE estado_sunat IN ('ENVIADO','ACEPTADO') AND estado_documento = 'ACTIVO'";

        $sql_rechazados = "SELECT COUNT(*) as total FROM comprobantes 
                          WHERE estado_sunat = 'RECHAZADO' AND estado_documento = 'ACTIVO'";

        $sql_hoy = "SELECT COUNT(*) as total FROM comprobantes 
                   WHERE estado_sunat IN ('ENVIADO','ACEPTADO') 
                   AND DATE(fecha_envio_sunat) = CURDATE()";

        $resultado_pendientes = $c->query($sql_pendientes)->fetch(PDO::FETCH_ASSOC);
        $resultado_enviados = $c->query($sql_enviados)->fetch(PDO::FETCH_ASSOC);
        $resultado_rechazados = $c->query($sql_rechazados)->fetch(PDO::FETCH_ASSOC);
        $resultado_hoy = $c->query($sql_hoy)->fetch(PDO::FETCH_ASSOC);

        $resumen = array(
            'pendientes' => $resultado_pendientes['total'],
            'enviados' => $resultado_enviados['total'],
            'rechazados' => $resultado_rechazados['total'],
            'hoy' => $resultado_hoy['total']
        );

        conexionBD::cerrar_conexion();
        return $resumen;
    }

    // ========================================
    // LISTAR PENDIENTES DE ENVÍO
    // ========================================
    public function ListarPendientesEnvio($tipo = '', $fecha_desde = '', $fecha_hasta = '')
    {
        $c = conexionBD::conexionPDO();

        $sql = "SELECT 
                    c.id_comprobante,
                    c.tipo_comprobante,
                    c.serie,
                    c.correlativo,
                    CONCAT(c.serie, '-', LPAD(c.correlativo, 8, '0')) as numero_comprobante,
                    c.fecha_emision,
                    cl.razon_social,
                    cl.numero_documento,
                    c.total,
                    c.estado_sunat,
                    c.estado_documento
                FROM comprobantes c
                INNER JOIN cliente_sunat cl ON c.id_cliente = cl.id_cliente
                WHERE c.estado_sunat = 'PENDIENTE' 
                AND c.estado_documento = 'ACTIVO'";

        $params = array();

        if (!empty($tipo)) {
            $sql .= " AND c.tipo_comprobante = ?";
            $params[] = $tipo;
        }

        if (!empty($fecha_desde)) {
            $sql .= " AND c.fecha_emision >= ?";
            $params[] = $fecha_desde;
        }

        if (!empty($fecha_hasta)) {
            $sql .= " AND c.fecha_emision <= ?";
            $params[] = $fecha_hasta;
        }

        $sql .= " ORDER BY c.fecha_emision DESC, c.id_comprobante DESC";

        $query = $c->prepare($sql);

        foreach ($params as $key => $value) {
            $query->bindValue($key + 1, $value);
        }

        $query->execute();
        $resultado = $query->fetchAll(PDO::FETCH_ASSOC);

        conexionBD::cerrar_conexion();
        return $resultado;
    }

    // ========================================
    // LISTAR HISTORIAL DE ENVÍOS
    // ========================================
    public function ListarHistorialEnvios($tipo = '', $estado = '', $fecha_desde = '', $fecha_hasta = '')
    {
        $c = conexionBD::conexionPDO();

        $sql = "SELECT 
                    c.id_comprobante,
                    c.tipo_comprobante,
                    c.serie,
                    c.correlativo,
                    CONCAT(c.serie, '-', LPAD(c.correlativo, 8, '0')) as numero_comprobante,
                    c.fecha_emision,
                    cl.razon_social,
                    cl.numero_documento,
                    c.total,
                    c.estado_sunat,
                    c.fecha_envio_sunat,
                    c.codigo_respuesta_sunat,
                    c.descripcion_respuesta_sunat,
                    c.codigo_hash as hash_cpe,
                    c.estado_documento
                FROM comprobantes c
                INNER JOIN cliente_sunat cl ON c.id_cliente = cl.id_cliente
                WHERE c.estado_sunat IN ('ENVIADO', 'ACEPTADO', 'RECHAZADO')";

        $params = array();

        if (!empty($tipo)) {
            $sql .= " AND c.tipo_comprobante = ?";
            $params[] = $tipo;
        }

        if (!empty($estado)) {
            $sql .= " AND c.estado_sunat = ?";
            $params[] = $estado;
        }

        if (!empty($fecha_desde)) {
            $sql .= " AND DATE(c.fecha_envio_sunat) >= ?";
            $params[] = $fecha_desde;
        }

        if (!empty($fecha_hasta)) {
            $sql .= " AND DATE(c.fecha_envio_sunat) <= ?";
            $params[] = $fecha_hasta;
        }

        $sql .= " ORDER BY c.fecha_envio_sunat DESC, c.id_comprobante DESC";

        $query = $c->prepare($sql);

        foreach ($params as $key => $value) {
            $query->bindValue($key + 1, $value);
        }

        $query->execute();
        $resultado = $query->fetchAll(PDO::FETCH_ASSOC);

        conexionBD::cerrar_conexion();
        return $resultado;
    }

    // ========================================
    // OBTENER RESPUESTA SUNAT DETALLADA
    // ========================================
    public function ObtenerRespuestaSunat($id_comprobante)
    {
        $c = conexionBD::conexionPDO();

        $sql = "SELECT 
                c.id_comprobante,
                c.tipo_comprobante,
                c.serie,
                c.correlativo,
                CONCAT(c.serie, '-', LPAD(c.correlativo, 8, '0')) as numero_comprobante,
                c.fecha_emision,
                c.fecha_envio_sunat,
                c.estado_sunat,
                c.codigo_respuesta_sunat,
                c.descripcion_respuesta_sunat,
                c.codigo_hash as hash_cpe,
                c.observaciones as notas_sunat
            FROM comprobantes c
            WHERE c.id_comprobante = ?";

        $query = $c->prepare($sql);
        $query->bindParam(1, $id_comprobante, PDO::PARAM_INT);
        $query->execute();

        $resultado = $query->fetch(PDO::FETCH_ASSOC);

        conexionBD::cerrar_conexion();
        return $resultado;
    }

    // ========================================
    // BUSCAR COMPROBANTE PARA NOTA DE CRÉDITO
    // ========================================
    public function Buscar_Comprobante_Para_NC($tipo_comprobante, $serie, $correlativo)
    {
        $c = conexionBD::conexionPDO();

        try {
            $sql = "SELECT 
                    c.id_comprobante,
                    c.tipo_comprobante,
                    c.serie,
                    c.correlativo,
                    c.fecha_emision,
                    c.total,
                    cl.razon_social,
                    cl.numero_documento,
                    c.estado_sunat,
                    c.estado_documento
                FROM comprobantes c
                INNER JOIN cliente_sunat cl ON c.id_cliente = cl.id_cliente
                WHERE c.tipo_comprobante = ?
                AND c.serie = ?
                AND c.correlativo = ?
                AND c.estado_documento = 'ACTIVO'
                AND c.estado_sunat IN ('ENVIADO', 'ACEPTADO')
                LIMIT 1";

            $query = $c->prepare($sql);
            $query->execute([$tipo_comprobante, $serie, $correlativo]);

            return $query->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return null;
        } finally {
            conexionBD::cerrar_conexion();
        }
    }

    // ========================================
    // REGISTRAR NOTA DE CRÉDITO
    // ========================================
    public function Registrar_Nota_Credito(
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
    ) {
        $c = conexionBD::conexionPDO();

        try {
            // ✅ CONFIGURAR ZONA HORARIA PERÚ (UTC-5)
            date_default_timezone_set('America/Lima');

            // Obtener datos del comprobante origen
            $sql_origen = "SELECT tipo_comprobante, serie, correlativo, id_cliente, moneda,
                              id_servicio, id_tipo_pago, id_origen, iddestino, idconductor
                      FROM comprobantes 
                      WHERE id_comprobante = ?";
            $query_origen = $c->prepare($sql_origen);
            $query_origen->execute([$id_comprobante_origen]);
            $origen = $query_origen->fetch(PDO::FETCH_ASSOC);

            if (!$origen) {
                return 0;
            }

            // ✅ Fecha y hora actual con zona horaria de Perú
            $fecha_emision = date('Y-m-d');
            $hora_emision = date('H:i:s');

            // Insertar nota de crédito
            $sql = "INSERT INTO comprobantes (
                    tipo_comprobante,
                    id_comprobante_origen,
                    serie,
                    correlativo,
                    fecha_emision,
                    hora_emision,
                    id_cliente,
                    moneda,
                    subtotal,
                    total_gravada,
                    total_igv,
                    total_impuestos,
                    total,
                    observaciones,
                    motivo_nota,
                    texto_motivo,
                    estado_sunat,
                    estado_documento,
                    id_servicio,
                    id_tipo_pago,
                    id_origen,
                    iddestino,
                    idconductor,
                    id_usuario,
                    created_at
                ) VALUES (
                    '07',
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    'ACTIVO',
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    NOW()
                )";

            $query = $c->prepare($sql);
            $query->execute([
                $id_comprobante_origen,
                $serie,
                $correlativo,
                $fecha_emision,
                $hora_emision,
                $origen['id_cliente'],
                $origen['moneda'] ?? 'PEN',
                $total_gravada,
                $total_gravada,
                $total_igv,
                $total_igv,
                $total,
                $observaciones,
                $motivo_nota,
                $motivo2,
                $estado_sunat,
                $origen['id_servicio'],
                $origen['id_tipo_pago'],
                $origen['id_origen'],
                $origen['iddestino'],
                $origen['idconductor'],
                $id_usuario
            ]);

            return $c->lastInsertId();
        } catch (Exception $e) {
            file_put_contents('error_nc.log', '[' . date('Y-m-d H:i:s') . '] ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
            return 0;
        } finally {
            conexionBD::cerrar_conexion();
        }
    }

    // ========================================
    // OBTENER CORRELATIVO NOTA DE CRÉDITO
    // ========================================
    public function Obtener_Correlativo_NC($tipo_comprobante)
    {
        $c = conexionBD::conexionPDO();

        // Determinar serie según tipo de comprobante afectado
        // Para Notas de Crédito según SUNAT:
        // - Si afecta FACTURA (01): Serie inicia con F (Ej: FN01)
        // - Si afecta BOLETA (03): Serie inicia con B (Ej: BN01)
        $serie = ($tipo_comprobante == '01') ? 'FN01' : 'BN01';

        try {
            $sql = "SELECT IFNULL(MAX(CAST(correlativo AS UNSIGNED)), 0) + 1 as correlativo
                FROM comprobantes
                WHERE tipo_comprobante = '07'
                AND serie = ?";

            $query = $c->prepare($sql);
            $query->execute([$serie]);
            $result = $query->fetch(PDO::FETCH_ASSOC);

            return str_pad($result['correlativo'], 8, '0', STR_PAD_LEFT);
        } catch (Exception $e) {
            return '00000001';
        } finally {
            conexionBD::cerrar_conexion();
        }
    }

    // ========================================
    // LISTAR NOTAS DE CRÉDITO
    // ========================================
    public function Listar_Notas_Credito($estado = '', $fecha_desde = '', $fecha_hasta = '')
    {
        $c = conexionBD::conexionPDO();

        try {
            $sql = "SELECT 
                    c.id_comprobante,
                    c.serie,
                    c.correlativo,
                    CONCAT(c.serie, '-', c.correlativo) as numero_comprobante,
                    c.fecha_emision,
                    c.motivo_nota,
                    c.observaciones,
                    c.total,
                    c.estado_sunat,
                    c.estado_documento,
                    cl.razon_social,
                    cl.numero_documento,
                    u.usu_nombre,
                    CONCAT(co.serie, '-', co.correlativo) as comprobante_afectado
                FROM comprobantes c
                INNER JOIN cliente_sunat cl ON c.id_cliente = cl.id_cliente
                LEFT JOIN usuario u ON c.id_usuario = u.id_usuario
                LEFT JOIN comprobantes co ON c.id_comprobante_origen = co.id_comprobante
                WHERE c.tipo_comprobante = '07'";

            $params = [];

            if (!empty($estado)) {
                $sql .= " AND c.estado_sunat = ?";
                $params[] = $estado;
            }

            if (!empty($fecha_desde)) {
                $sql .= " AND c.fecha_emision >= ?";
                $params[] = $fecha_desde;
            }

            if (!empty($fecha_hasta)) {
                $sql .= " AND c.fecha_emision <= ?";
                $params[] = $fecha_hasta;
            }

            $sql .= " ORDER BY c.fecha_emision DESC, c.id_comprobante DESC";

            $query = $c->prepare($sql);
            $query->execute($params);

            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        } finally {
            conexionBD::cerrar_conexion();
        }
    }
    // ============================================================
    // AGREGAR ESTAS FUNCIONES AL FINAL DE model_comprobante.php
    // (Dentro de la clase Modelo_Comprobantes)
    // ============================================================

    // ========================================
    // REGISTRAR NOTA DE DÉBITO
    // ========================================
    public function Registrar_Nota_Debito(
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
    ) {
        $c = conexionBD::conexionPDO();

        try {
            // ✅ CONFIGURAR ZONA HORARIA PERÚ (UTC-5)
            date_default_timezone_set('America/Lima');

            // Obtener datos del comprobante origen
            $sql_origen = "SELECT tipo_comprobante, serie, correlativo, id_cliente, moneda,
                              id_servicio, id_tipo_pago, id_origen, iddestino, idconductor
                      FROM comprobantes 
                      WHERE id_comprobante = ?";
            $query_origen = $c->prepare($sql_origen);
            $query_origen->execute([$id_comprobante_origen]);
            $origen = $query_origen->fetch(PDO::FETCH_ASSOC);

            if (!$origen) {
                return 0;
            }

            // ✅ Fecha y hora actual con zona horaria de Perú
            $fecha_emision = date('Y-m-d');
            $hora_emision = date('H:i:s');

            // Insertar nota de débito
            $sql = "INSERT INTO comprobantes (
                    tipo_comprobante,
                    id_comprobante_origen,
                    serie,
                    correlativo,
                    fecha_emision,
                    hora_emision,
                    id_cliente,
                    moneda,
                    subtotal,
                    total_gravada,
                    total_igv,
                    total_impuestos,
                    total,
                    observaciones,
                    motivo_nota,
                    texto_motivo,
                    estado_sunat,
                    estado_documento,
                    id_servicio,
                    id_tipo_pago,
                    id_origen,
                    iddestino,
                    idconductor,
                    id_usuario,
                    created_at
                ) VALUES (
                    '08',
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    'ACTIVO',
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    NOW()
                )";

            $query = $c->prepare($sql);
            $query->execute([
                $id_comprobante_origen,
                $serie,
                $correlativo,
                $fecha_emision,
                $hora_emision,
                $origen['id_cliente'],
                $origen['moneda'] ?? 'PEN',
                $total_gravada,
                $total_gravada,
                $total_igv,
                $total_igv,
                $total,
                $observaciones,
                $motivo_nota,
                $motivo2,
                $estado_sunat,
                $origen['id_servicio'],
                $origen['id_tipo_pago'],
                $origen['id_origen'],
                $origen['iddestino'],
                $origen['idconductor'],
                $id_usuario
            ]);

            return $c->lastInsertId();
        } catch (Exception $e) {
            file_put_contents('error_nd.log', '[' . date('Y-m-d H:i:s') . '] ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
            return 0;
        } finally {
            conexionBD::cerrar_conexion();
        }
    }

    // ========================================
    // OBTENER CORRELATIVO NOTA DE DÉBITO
    // ========================================
    public function Obtener_Correlativo_ND($tipo_comprobante)
    {
        $c = conexionBD::conexionPDO();

        // Determinar serie según tipo de comprobante afectado
        // Para Notas de Débito según SUNAT:
        // - Si afecta FACTURA (01): Serie inicia con F (Ej: FD01)
        // - Si afecta BOLETA (03): Serie inicia con B (Ej: BD01)
        $serie = ($tipo_comprobante == '01') ? 'FD01' : 'BD01';

        try {
            $sql = "SELECT IFNULL(MAX(CAST(correlativo AS UNSIGNED)), 0) + 1 as correlativo
                FROM comprobantes
                WHERE tipo_comprobante = '08'
                AND serie = ?";

            $query = $c->prepare($sql);
            $query->execute([$serie]);
            $result = $query->fetch(PDO::FETCH_ASSOC);

            return str_pad($result['correlativo'], 8, '0', STR_PAD_LEFT);
        } catch (Exception $e) {
            return '00000001';
        } finally {
            conexionBD::cerrar_conexion();
        }
    }

    // ========================================
    // LISTAR NOTAS DE DÉBITO
    // ========================================
    public function Listar_Notas_Debito($estado = '', $fecha_desde = '', $fecha_hasta = '')
    {
        $c = conexionBD::conexionPDO();

        try {
            $sql = "SELECT 
                    c.id_comprobante,
                    c.serie,
                    c.correlativo,
                    CONCAT(c.serie, '-', c.correlativo) as numero_comprobante,
                    c.fecha_emision,
                    c.motivo_nota,
                    c.observaciones,
                    c.total,
                    c.estado_sunat,
                    c.estado_documento,
                    cl.razon_social,
                    cl.numero_documento,
                    u.usu_nombre,
                    CONCAT(co.serie, '-', co.correlativo) as comprobante_afectado
                FROM comprobantes c
                INNER JOIN cliente_sunat cl ON c.id_cliente = cl.id_cliente
                LEFT JOIN usuario u ON c.id_usuario = u.id_usuario
                LEFT JOIN comprobantes co ON c.id_comprobante_origen = co.id_comprobante
                WHERE c.tipo_comprobante = '08'";

            $params = [];

            if (!empty($estado)) {
                $sql .= " AND c.estado_sunat = ?";
                $params[] = $estado;
            }

            if (!empty($fecha_desde)) {
                $sql .= " AND c.fecha_emision >= ?";
                $params[] = $fecha_desde;
            }

            if (!empty($fecha_hasta)) {
                $sql .= " AND c.fecha_emision <= ?";
                $params[] = $fecha_hasta;
            }

            $sql .= " ORDER BY DATE(c.fecha_emision) DESC, c.id_comprobante DESC";

            $query = $c->prepare($sql);
            $query->execute($params);

            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        } finally {
            conexionBD::cerrar_conexion();
        }
    }
    // ============================================================
    // ANULAR BOLETA Y GENERAR COMUNICACIÓN DE BAJA
    // ============================================================
    public function Anular_Boleta_SUNAT($id_comprobante, $motivo, $usuario)
    {
        $c = conexionBD::conexionPDO();

        try {
            // Solo anular localmente (ya no validar estado)
            $sql_anular = "UPDATE comprobantes 
                       SET estado_documento = 'ANULADO',
                           estado_sunat = 'ANULADO',
                           fecha_anulacion = NOW(),
                           usuario_anulacion = ?
                       WHERE id_comprobante = ?";

            $query_anular = $c->prepare($sql_anular);
            $resultado = $query_anular->execute([$usuario, $id_comprobante]);

            if ($resultado) {
                // Registrar en historial
                $this->Registrar_Historial_Anulacion($id_comprobante, $motivo, $usuario);
                return ['status' => 'success', 'message' => 'Boleta anulada correctamente'];
            } else {
                return ['status' => 'error', 'message' => 'Error al anular la boleta'];
            }
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        } finally {
            conexionBD::cerrar_conexion();
        }
    }

    // ============================================================
    // REGISTRAR HISTORIAL DE ANULACIÓN
    // ============================================================
    public function Registrar_Historial_Anulacion($id_comprobante, $motivo, $usuario)
    {
        $c = conexionBD::conexionPDO();

        try {
            $sql = "INSERT INTO historial_anulaciones 
                (id_comprobante, motivo_anulacion, usuario_anulacion, fecha_anulacion)
                VALUES (?, ?, ?, NOW())";

            $query = $c->prepare($sql);
            $query->execute([$id_comprobante, $motivo, $usuario]);

            return true;
        } catch (Exception $e) {
            // Si la tabla no existe, continuar sin error
            return false;
        } finally {
            conexionBD::cerrar_conexion();
        }
    }

    // ============================================================
    // OBTENER CORRELATIVO PARA COMUNICACIÓN DE BAJA
    // ============================================================
    public function Obtener_Correlativo_Comunicacion_Baja($fecha)
    {
        $c = conexionBD::conexionPDO();

        try {
            // Formato: RA-YYYYMMDD-###
            $fecha_formato = date('Ymd', strtotime($fecha));

            $sql = "SELECT IFNULL(MAX(CAST(SUBSTRING_INDEX(correlativo_baja, '-', -1) AS UNSIGNED)), 0) + 1 as correlativo
                FROM comunicaciones_baja
                WHERE DATE(fecha_comunicacion) = ?";

            $query = $c->prepare($sql);
            $query->execute([$fecha]);
            $result = $query->fetch(PDO::FETCH_ASSOC);

            $correlativo = str_pad($result['correlativo'], 3, '0', STR_PAD_LEFT);

            return "RA-{$fecha_formato}-{$correlativo}";
        } catch (Exception $e) {
            // Si falla, generar uno por defecto
            return "RA-" . date('Ymd') . "-001";
        } finally {
            conexionBD::cerrar_conexion();
        }
    }

    // ============================================================
    // REGISTRAR COMUNICACIÓN DE BAJA
    // ============================================================
    public function Registrar_Comunicacion_Baja($id_comprobante, $correlativo_baja, $ticket_sunat = null)
    {
        $c = conexionBD::conexionPDO();

        try {
            $sql = "INSERT INTO comunicaciones_baja 
                (id_comprobante, correlativo_baja, fecha_comunicacion, ticket_sunat, estado)
                VALUES (?, ?, NOW(), ?, 'ENVIADO')";

            $query = $c->prepare($sql);
            $query->execute([$id_comprobante, $correlativo_baja, $ticket_sunat]);

            return $c->lastInsertId();
        } catch (Exception $e) {
            file_put_contents(
                'error_comunicacion_baja.log',
                '[' . date('Y-m-d H:i:s') . '] ' . $e->getMessage() . PHP_EOL,
                FILE_APPEND
            );
            return 0;
        } finally {
            conexionBD::cerrar_conexion();
        }
    }

    // ============================================================
    // ACTUALIZAR ESTADO COMUNICACIÓN DE BAJA
    // ============================================================
    public function Actualizar_Estado_Comunicacion_Baja($id_comunicacion, $estado, $descripcion = null)
    {
        $c = conexionBD::conexionPDO();

        try {
            $sql = "UPDATE comunicaciones_baja 
                SET estado = ?,
                    descripcion_respuesta = ?,
                    fecha_respuesta = NOW()
                WHERE id_comunicacion = ?";

            $query = $c->prepare($sql);
            $query->execute([$estado, $descripcion, $id_comunicacion]);

            return true;
        } catch (Exception $e) {
            return false;
        } finally {
            conexionBD::cerrar_conexion();
        }
    }

    // ============================================================
    // LISTAR COMUNICACIONES DE BAJA
    // ============================================================
    public function Listar_Comunicaciones_Baja($fecha_desde = '', $fecha_hasta = '')
    {
        $c = conexionBD::conexionPDO();

        try {
            $sql = "SELECT 
                    cb.*,
                    c.tipo_comprobante,
                    c.serie,
                    c.correlativo,
                    CONCAT(c.serie, '-', c.correlativo) as numero_comprobante,
                    cl.razon_social,
                    cl.numero_documento
                FROM comunicaciones_baja cb
                INNER JOIN comprobantes c ON cb.id_comprobante = c.id_comprobante
                INNER JOIN cliente_sunat cl ON c.id_cliente = cl.id_cliente
                WHERE 1=1";

            $params = [];

            if (!empty($fecha_desde)) {
                $sql .= " AND DATE(cb.fecha_comunicacion) >= ?";
                $params[] = $fecha_desde;
            }

            if (!empty($fecha_hasta)) {
                $sql .= " AND DATE(cb.fecha_comunicacion) <= ?";
                $params[] = $fecha_hasta;
            }

            $sql .= " ORDER BY cb.fecha_comunicacion DESC";

            $query = $c->prepare($sql);
            $query->execute($params);

            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        } finally {
            conexionBD::cerrar_conexion();
        }
    }

    // ============================================================
    // VERIFICAR SI BOLETA PUEDE SER ANULADA
    // ============================================================
    public function Verificar_Boleta_Anulable($id_comprobante)
    {
        $c = conexionBD::conexionPDO();

        try {
            $sql = "SELECT 
                    tipo_comprobante,
                    estado_sunat,
                    estado_documento,
                    fecha_emision,
                    DATEDIFF(CURDATE(), fecha_emision) as dias_transcurridos
                FROM comprobantes
                WHERE id_comprobante = ?";

            $query = $c->prepare($sql);
            $query->execute([$id_comprobante]);
            $comprobante = $query->fetch(PDO::FETCH_ASSOC);

            if (!$comprobante) {
                return ['anulable' => false, 'motivo' => 'Comprobante no encontrado'];
            }

            if ($comprobante['tipo_comprobante'] != '03') {
                return ['anulable' => false, 'motivo' => 'Solo se pueden anular boletas'];
            }

            if ($comprobante['estado_documento'] == 'ANULADO') {
                return ['anulable' => false, 'motivo' => 'La boleta ya está anulada'];
            }

            if (!in_array($comprobante['estado_sunat'], ['ACEPTADO', 'ENVIADO'])) {
                return ['anulable' => false, 'motivo' => 'La boleta debe estar aceptada por SUNAT'];
            }

            // SUNAT permite anular boletas hasta 7 días después de su emisión
            if ($comprobante['dias_transcurridos'] > 7) {
                return ['anulable' => false, 'motivo' => 'No se puede anular boletas con más de 7 días de emisión'];
            }

            return ['anulable' => true, 'motivo' => 'La boleta puede ser anulada'];
        } catch (Exception $e) {
            return ['anulable' => false, 'motivo' => 'Error al verificar: ' . $e->getMessage()];
        } finally {
            conexionBD::cerrar_conexion();
        }
    }

    // ============================================================
    // OBTENER DATOS BÁSICOS DEL COMPROBANTE
    // ============================================================
    // ============================================================
    // VERIFICAR Y CORREGIR EN model_comprobante.php
    // Busca la función Obtener_Datos_Basicos_Comprobante
    // ============================================================

    // ❌ SI LA FUNCIÓN ACTUAL SE VE ASÍ (INCORRECTA):


    // ✅ REEMPLÁZALA CON ESTA VERSIÓN COMPLETA:
    public function Obtener_Datos_Basicos_Comprobante($id_comprobante)
    {
        $c = conexionBD::conexionPDO();

        try {
            // 🔍 DEBUG: Ver qué ID estamos buscando
            file_put_contents(
                'debug_obtener_datos.log',
                '[' . date('Y-m-d H:i:s') . '] Buscando comprobante ID: ' . $id_comprobante . PHP_EOL,
                FILE_APPEND
            );

            $sql = "SELECT 
                    id_comprobante,
                    tipo_comprobante,
                    fecha_emision, 
                    serie, 
                    correlativo,
                    estado_sunat
                FROM comprobantes 
                WHERE id_comprobante = ?";

            $query = $c->prepare($sql);
            $query->bindParam(1, $id_comprobante, PDO::PARAM_INT);
            $query->execute();

            $resultado = $query->fetch(PDO::FETCH_ASSOC);

            // 🔍 DEBUG: Ver qué encontramos
            file_put_contents(
                'debug_obtener_datos.log',
                '[' . date('Y-m-d H:i:s') . '] Resultado: ' . print_r($resultado, true) . PHP_EOL,
                FILE_APPEND
            );

            // ✅ Si no hay resultado, devolver array vacío en lugar de false
            if (!$resultado) {
                return null;
            }

            return $resultado;
        } catch (Exception $e) {
            file_put_contents(
                'debug_obtener_datos.log',
                '[' . date('Y-m-d H:i:s') . '] ERROR: ' . $e->getMessage() . PHP_EOL,
                FILE_APPEND
            );
            error_log("Error en Obtener_Datos_Basicos_Comprobante: " . $e->getMessage());
            return null;
        } finally {
            conexionBD::cerrar_conexion();
        }
    }
    // ============================================================
    // ACTUALIZAR OBSERVACIONES DEL COMPROBANTE
    // ============================================================
    public function Actualizar_Observaciones_Comprobante($id_comprobante, $motivo)
    {
        $c = conexionBD::conexionPDO();

        try {
            $sql = "UPDATE comprobantes 
                SET observaciones = CONCAT(IFNULL(observaciones, ''), '\n[MOTIVO ANULACIÓN] ', ?)
                WHERE id_comprobante = ?";

            $query = $c->prepare($sql);
            return $query->execute([$motivo, $id_comprobante]);
        } catch (Exception $e) {
            return false;
        } finally {
            conexionBD::cerrar_conexion();
        }
    }
    // ========================================
    // OBTENER COMPROBANTE COMPLETO PARA EDITAR
    // ========================================
    public function Obtener_Comprobante_Completo($id_comprobante)
    {
        $c = conexionBD::conexionPDO();

        try {
            $sql = "SELECT 
                c.id_comprobante,
                c.tipo_comprobante,
                c.serie,
                c.correlativo,
                c.fecha_emision,
                c.moneda,
                c.subtotal,
                c.total_gravada,
                c.total_igv,
                c.total,
                c.id_tipo_pago,
                c.observaciones,
                c.estado_sunat,
                c.estado_documento,
                c.id_servicio,
                c.id_origen,
                c.iddestino,
                c.idconductor,
                COALESCE(cl.id_cliente, c.id_cliente) AS id_cliente,
                COALESCE(cl.tipo_documento, '') AS tipo_documento_cliente,
                COALESCE(cl.numero_documento, '') AS numero_documento,
                COALESCE(cl.razon_social, '') AS razon_social,
                COALESCE(cl.direccion, '') AS direccion,
                COALESCE(cl.telefono, '') AS celular,
                COALESCE(cl.departamento, '') AS departamento,
                COALESCE(cl.provincia, '') AS provincia,
                COALESCE(cl.distrito, '') AS distrito,
                COALESCE(cd.cantidad, 1) AS cantidad,
                COALESCE(cd.precio_unitario, 0) AS precio_unitario,
                COALESCE(cd.descripcion, '') AS descripcion_servicio,
                DATE_FORMAT(c.created_at, '%Y-%m-%d') AS fecha_viaje
            FROM comprobantes c
            LEFT JOIN cliente_sunat cl ON c.id_cliente = cl.id_cliente
            LEFT JOIN comprobante_detalle cd ON c.id_comprobante = cd.id_comprobante
            WHERE c.id_comprobante = ?
            LIMIT 1";

            $query = $c->prepare($sql);
            $query->bindParam(1, $id_comprobante, PDO::PARAM_INT);
            $query->execute();

            $resultado = $query->fetch(PDO::FETCH_ASSOC);

            return $resultado;
        } catch (Exception $e) {
            error_log("Error en Obtener_Comprobante_Completo: " . $e->getMessage());
            return null;
        } finally {
            conexionBD::cerrar_conexion();
        }
    }
    // ========================================
    // ACTUALIZAR CLIENTE SUNAT
    // ========================================
    public function Actualizar_Cliente_SUNAT(
        $tipo_documento,
        $numero_documento,
        $razon_social,
        $direccion,
        $celular,
        $departamento,
        $provincia,
        $distrito,
        $ubigeo
    ) {
        $c = conexionBD::conexionPDO();

        try {
            // 🔍 DEBUG
            file_put_contents(
                'debug_modelo.log',
                '[' . date('Y-m-d H:i:s') . '] Actualizar_Cliente_SUNAT llamado con:' . PHP_EOL .
                    '  Tipo Doc: ' . $tipo_documento . PHP_EOL .
                    '  Num Doc: ' . $numero_documento . PHP_EOL .
                    '  Razón: ' . $razon_social . PHP_EOL .
                    '  Celular: ' . $celular . PHP_EOL,
                FILE_APPEND
            );

            // Verificar si existe el cliente
            $sql_check = "SELECT id_cliente FROM cliente_sunat WHERE numero_documento = ? LIMIT 1";
            $query_check = $c->prepare($sql_check);
            $query_check->execute([$numero_documento]);
            $existe = $query_check->fetch(PDO::FETCH_ASSOC);

            file_put_contents(
                'debug_modelo.log',
                '[' . date('Y-m-d H:i:s') . '] Cliente existe: ' . ($existe ? 'SI (ID: ' . $existe['id_cliente'] . ')' : 'NO') . PHP_EOL,
                FILE_APPEND
            );

            if ($existe) {
                // Actualizar cliente existente
                // ⚠️ NOTA: La columna es "telefono" NO "celular"
                $sql = "UPDATE cliente_sunat SET
                    tipo_documento = ?,
                    razon_social = ?,
                    direccion = ?,
                    telefono = ?,
                    departamento = ?,
                    provincia = ?,
                    distrito = ?,
                    ubigeo = ?,
                    updated_at = NOW()
                WHERE numero_documento = ?";

                $query = $c->prepare($sql);
                $resultado = $query->execute([
                    $tipo_documento,
                    $razon_social,
                    $direccion,
                    $celular,  // Se guarda en la columna "telefono"
                    $departamento,
                    $provincia,
                    $distrito,
                    $ubigeo,
                    $numero_documento
                ]);

                if (!$resultado) {
                    $errorInfo = $query->errorInfo();
                    file_put_contents(
                        'debug_modelo.log',
                        '[' . date('Y-m-d H:i:s') . '] ❌ ERROR UPDATE cliente: ' . print_r($errorInfo, true) . PHP_EOL,
                        FILE_APPEND
                    );
                    return 0;
                }

                file_put_contents(
                    'debug_modelo.log',
                    '[' . date('Y-m-d H:i:s') . '] ✅ Cliente actualizado correctamente' . PHP_EOL,
                    FILE_APPEND
                );

                return $existe['id_cliente'];
            } else {
                // Insertar nuevo cliente
                file_put_contents(
                    'debug_modelo.log',
                    '[' . date('Y-m-d H:i:s') . '] Cliente no existe, registrando nuevo...' . PHP_EOL,
                    FILE_APPEND
                );

                return $this->Registrar_Cliente_SUNAT(
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
            }
        } catch (Exception $e) {
            file_put_contents(
                'debug_modelo.log',
                '[' . date('Y-m-d H:i:s') . '] ❌ EXCEPTION en Actualizar_Cliente_SUNAT: ' . $e->getMessage() . PHP_EOL .
                    'Stack trace: ' . $e->getTraceAsString() . PHP_EOL,
                FILE_APPEND
            );
            error_log("Error en Actualizar_Cliente_SUNAT: " . $e->getMessage());
            return 0;
        } finally {
            conexionBD::cerrar_conexion();
        }
    }

    // ========================================
    // ACTUALIZAR COMPROBANTE
    // ========================================
    public function Actualizar_Comprobante(
        $id_comprobante,
        $tipo_comprobante,
        $serie,
        $correlativo,
        $fecha_emision,
        $moneda,
        $id_cliente,
        $subtotal,
        $total_igv,
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
    ) {
        $c = conexionBD::conexionPDO();

        try {
            // Verificar que el comprobante exista y sea PENDIENTE
            $sql_check = "SELECT estado_sunat, estado_documento 
                      FROM comprobantes 
                      WHERE id_comprobante = ?";
            $query_check = $c->prepare($sql_check);
            $query_check->execute([$id_comprobante]);
            $comprobante = $query_check->fetch(PDO::FETCH_ASSOC);

            if (!$comprobante) {
                throw new Exception('Comprobante no encontrado');
            }

            if ($comprobante['estado_sunat'] !== 'PENDIENTE') {
                throw new Exception('Solo se pueden editar comprobantes PENDIENTES');
            }

            if ($comprobante['estado_documento'] !== 'ACTIVO') {
                throw new Exception('El comprobante no está activo');
            }

            // Obtener información del servicio y rutas
            $sql_servicio = "SELECT 
                            s.nombre AS servicio_nombre,
                            s.costo AS servicio_precio,
                            r1.nombre AS origen_nombre,
                            r2.nombre AS destino_nombre
                        FROM servicios s
                        LEFT JOIN rutas r1 ON r1.idrutas = ?
                        LEFT JOIN rutas r2 ON r2.idrutas = ?
                        WHERE s.id_servicio = ?
                        LIMIT 1";

            $query_servicio = $c->prepare($sql_servicio);
            $query_servicio->execute([$id_origen, $id_destino, $id_servicio]);
            $servicio = $query_servicio->fetch(PDO::FETCH_ASSOC);

            if (!$servicio) {
                throw new Exception('Servicio no encontrado');
            }

            $v_precio_unitario = $servicio['servicio_precio'];
            $v_descripcion_servicio = $servicio['servicio_nombre'] . ' (' .
                $servicio['origen_nombre'] . ' - ' .
                $servicio['destino_nombre'] . ')';

            // Generar número completo
            $numero_completo = $serie . '-' . str_pad($correlativo, 8, '0', STR_PAD_LEFT);

            // Actualizar comprobante
            $sql = "UPDATE comprobantes SET
                tipo_comprobante = ?,
                serie = ?,
                correlativo = ?,
                numero_completo = ?,
                fecha_emision = ?,
                moneda = ?,
                id_cliente = ?,
                subtotal = ?,
                total_gravada = ?,
                total_igv = ?,
                total_impuestos = ?,
                total = ?,
                id_tipo_pago = ?,
                id_servicio = ?,
                id_origen = ?,
                iddestino = ?,
                idconductor = ?,
                observaciones = ?,
                updated_at = NOW()
            WHERE id_comprobante = ? 
            AND estado_sunat = 'PENDIENTE'
            AND estado_documento = 'ACTIVO'";

            $query = $c->prepare($sql);
            $resultado = $query->execute([
                $tipo_comprobante,
                $serie,
                $correlativo,
                $numero_completo,
                $fecha_emision,
                $moneda,
                $id_cliente,
                $subtotal,
                $subtotal,  // total_gravada
                $total_igv,
                $total_igv, // total_impuestos
                $total,
                $id_tipo_pago,
                $id_servicio,
                $id_origen,
                $id_destino,
                $id_conductor,
                $observaciones,
                $id_comprobante
            ]);

            if (!$resultado) {
                throw new Exception('Error al actualizar el comprobante');
            }

            // Actualizar detalle del comprobante
            $sql_detalle = "UPDATE comprobante_detalle SET
                        codigo_producto = ?,
                        descripcion = ?,
                        cantidad = ?,
                        precio_unitario = ?,
                        valor_unitario = ?,
                        valor_venta = ?,
                        igv = ?,
                        total_impuestos_item = ?,
                        total_item = ?,
                        updated_at = NOW()
                    WHERE id_comprobante = ?";

            $query_detalle = $c->prepare($sql_detalle);
            $resultado_detalle = $query_detalle->execute([
                'SERV-' . $id_servicio,
                $v_descripcion_servicio,
                $cantidad,
                $v_precio_unitario,
                ($subtotal / $cantidad),
                $subtotal,
                $total_igv,
                $total_igv,
                $total,
                $id_comprobante
            ]);

            if (!$resultado_detalle) {
                throw new Exception('Error al actualizar el detalle del comprobante');
            }

            return true;
        } catch (Exception $e) {
            error_log("Error en Actualizar_Comprobante: " . $e->getMessage());
            return false;
        } finally {
            conexionBD::cerrar_conexion();
        }
    }
    /**
 * OBTENER DATOS COMPLETOS PARA DECLARACIÓN SUNAT
 * Retorna todos los campos necesarios para la declaración mensual
 */
public function Obtener_Datos_Declaracion_SUNAT($tipo = '', $estado = '', $fecha_desde = '', $fecha_hasta = '')
{
    $c = conexionBD::conexionPDO();
    error_log("PARAMETROS => tipo:$tipo, estado:$estado, desde:$fecha_desde, hasta:$fecha_hasta");

    try {
        $sql = "SELECT 
                -- DATOS DEL COMPROBANTE
                c.id_comprobante,
                c.tipo_comprobante,
                CASE c.tipo_comprobante
                    WHEN '01' THEN 'FACTURA'
                    WHEN '03' THEN 'BOLETA'
                    WHEN '07' THEN 'NOTA DE CRÉDITO'
                    WHEN '08' THEN 'NOTA DE DÉBITO'
                    ELSE 'OTROS'
                END AS tipo_documento_nombre,
                c.serie,
                c.correlativo,
                CONCAT(c.serie, '-', LPAD(c.correlativo, 8, '0')) AS numero_comprobante,
                DATE_FORMAT(c.fecha_emision, '%d/%m/%Y') AS fecha_emision,
                DATE_FORMAT(c.fecha_vencimiento, '%d/%m/%Y') AS fecha_vencimiento,
                c.hora_emision,
                
                -- DATOS DEL CLIENTE
                cl.tipo_documento AS tipo_doc_cliente,
                cl.numero_documento AS numero_doc_cliente,
                cl.razon_social AS cliente_nombre,
                cl.direccion AS cliente_direccion,
                cl.telefono AS cliente_telefono,
                cl.departamento,
                cl.provincia,
                cl.distrito,
                cl.ubigeo,
                
                -- MONEDA Y TIPO DE CAMBIO
                c.moneda,
                c.tipo_cambio,
                
                -- IMPORTES BASE
                FORMAT(IFNULL(c.subtotal, 0), 2) AS subtotal,
                FORMAT(IFNULL(c.total_descuentos, 0), 2) AS total_descuentos,
                FORMAT(IFNULL(c.total_gravada, 0), 2) AS total_gravada,
                FORMAT(IFNULL(c.total_exonerada, 0), 2) AS total_exonerada,
                FORMAT(IFNULL(c.total_inafecta, 0), 2) AS total_inafecta,
                FORMAT(IFNULL(c.total_gratuita, 0), 2) AS total_gratuita,
                
                -- IMPUESTOS
                FORMAT(IFNULL(c.total_igv, 0), 2) AS total_igv,
                FORMAT(IFNULL(c.total_isc, 0), 2) AS total_isc,
                FORMAT(IFNULL(c.total_otros_tributos, 0), 2) AS total_otros_tributos,
                FORMAT(IFNULL(c.total_impuestos, 0), 2) AS total_impuestos,
                FORMAT(IFNULL(c.total, 0), 2) AS total,
                
                -- ESTADO SUNAT
                c.estado_sunat,
                c.estado_documento,
                c.codigo_hash,
                c.hash_cdr,
                c.codigo_respuesta_sunat,
                c.descripcion_respuesta_sunat,
                DATE_FORMAT(c.fecha_envio_sunat, '%d/%m/%Y %H:%i:%s') AS fecha_envio_sunat,
                
                -- DATOS DE LA NOTA (SI APLICA)
                c.id_comprobante_origen,
                c.motivo_nota,
                c.texto_motivo,
                CONCAT(co.serie, '-', LPAD(co.correlativo, 8, '0')) AS comprobante_afectado,
                
                -- REFERENCIAS
                c.orden_compra,
                c.guia_remision,
                c.observaciones,
                
                -- TIPO DE OPERACIÓN Y FORMA DE PAGO
                c.tipo_operacion,
                tp.tipo_pago AS forma_pago,
                
                -- INFORMACIÓN DEL SERVICIO
                s.nombre AS servicio_nombre,
                s.descripcion AS servicio_descripcion,
                r_origen.nombre AS origen,
                r_destino.nombre AS destino,
                CONCAT(cond.conductor_nombre, ' ', cond.conductor_apellidos) AS conductor_nombre,
                
                -- DETALLE DEL SERVICIO
                cd.cantidad,
                FORMAT(IFNULL(cd.precio_unitario, 0), 2) AS precio_unitario,
                FORMAT(IFNULL(cd.valor_unitario, 0), 2) AS valor_unitario,
                FORMAT(IFNULL(cd.valor_venta, 0), 2) AS valor_venta,
                FORMAT(IFNULL(cd.igv, 0), 2) AS igv_detalle,
                FORMAT(IFNULL(cd.total_item, 0), 2) AS total_item,
                cd.descripcion AS descripcion_detalle,
                
                -- ARCHIVOS XML Y CDR
                c.nombre_archivo_xml,
                c.nombre_archivo_cdr,
                
                -- USUARIO Y FECHAS DE REGISTRO
                CONCAT(u.usu_nombre, ' ', u.usu_apellido) AS usuario_registro,
                DATE_FORMAT(c.created_at, '%d/%m/%Y %H:%i:%s') AS fecha_registro,
                DATE_FORMAT(c.updated_at, '%d/%m/%Y %H:%i:%s') AS fecha_actualizacion,
                
                -- DATOS DE ANULACIÓN (SI APLICA)
                c.motivo_anulacion,
                DATE_FORMAT(c.fecha_anulacion, '%d/%m/%Y %H:%i:%s') AS fecha_anulacion,
                ua.usu_nombre AS usuario_anulacion
                
            FROM comprobantes c
            INNER JOIN cliente_sunat cl ON c.id_cliente = cl.id_cliente
            LEFT JOIN tipo_pago tp ON c.id_tipo_pago = tp.id_tipo_pago
            LEFT JOIN servicios s ON c.id_servicio = s.id_servicio
            LEFT JOIN rutas r_origen ON c.id_origen = r_origen.idrutas
            LEFT JOIN rutas r_destino ON c.iddestino = r_destino.idrutas
            LEFT JOIN conductor cond ON c.idconductor = cond.idconductor
            LEFT JOIN comprobante_detalle cd ON c.id_comprobante = cd.id_comprobante
            LEFT JOIN usuario u ON c.id_usuario = u.id_usuario
            LEFT JOIN usuario ua ON c.usuario_anulacion = ua.id_usuario
            LEFT JOIN comprobantes co ON c.id_comprobante_origen = co.id_comprobante
            WHERE 1=1";
        
        $params = array();
        
        // FILTRO: TIPO DE COMPROBANTE
        if ($tipo !== '' && $tipo !== null) {
            $sql .= " AND c.tipo_comprobante = ?";
            $params[] = $tipo;
        }

        // FILTRO: ESTADO SUNAT (CORREGIDO - usar LIKE en lugar de =)
        if ($estado !== '' && $estado !== null) {
            $sql .= " AND UPPER(c.estado_sunat) LIKE ?";
            $params[] = strtoupper($estado) . '%';
        } else {
            // POR DEFECTO: Solo mostrar ENVIADO o ACEPTADO (usando LIKE)
            $sql .= " AND (UPPER(c.estado_sunat) LIKE 'ENVIADO%' OR UPPER(c.estado_sunat) LIKE 'ACEPTADO%')";
        }

        // FILTRO: FECHA DESDE
        if ($fecha_desde !== '' && $fecha_desde !== null) {
            // Convertir formato dd/mm/yyyy a yyyy-mm-dd
            $fecha_desde_partes = explode('/', $fecha_desde);
            if (count($fecha_desde_partes) == 3) {
                $fecha_desde_sql = $fecha_desde_partes[2] . '-' . $fecha_desde_partes[1] . '-' . $fecha_desde_partes[0];
            } else {
                $fecha_desde_sql = date('Y-m-d', strtotime($fecha_desde));
            }
            $sql .= " AND DATE(c.fecha_emision) >= ?";
            $params[] = $fecha_desde_sql;
            error_log("Fecha desde SQL: " . $fecha_desde_sql);
        }

        // FILTRO: FECHA HASTA
        if ($fecha_hasta !== '' && $fecha_hasta !== null) {
            // Convertir formato dd/mm/yyyy a yyyy-mm-dd
            $fecha_hasta_partes = explode('/', $fecha_hasta);
            if (count($fecha_hasta_partes) == 3) {
                $fecha_hasta_sql = $fecha_hasta_partes[2] . '-' . $fecha_hasta_partes[1] . '-' . $fecha_hasta_partes[0];
            } else {
                $fecha_hasta_sql = date('Y-m-d', strtotime($fecha_hasta));
            }
            $sql .= " AND DATE(c.fecha_emision) <= ?";
            $params[] = $fecha_hasta_sql;
            error_log("Fecha hasta SQL: " . $fecha_hasta_sql);
        }
        
        // ORDENAMIENTO
        $sql .= " ORDER BY c.fecha_emision ASC, c.tipo_comprobante ASC, c.serie ASC, c.correlativo ASC";
        
        error_log("SQL FINAL: " . $sql);
        error_log("PARAMS: " . json_encode($params));
        
        $query = $c->prepare($sql);
        
        // BIND DE PARÁMETROS
        foreach ($params as $key => $value) {
            $query->bindValue($key + 1, $value);
        }
        
        $query->execute();
        $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
        
        error_log("REGISTROS ENCONTRADOS: " . count($resultado));
        
        return $resultado;
        
    } catch (Exception $e) {
        error_log("Error en Obtener_Datos_Declaracion_SUNAT: " . $e->getMessage());
        error_log("TRACE: " . $e->getTraceAsString());
        return array();
    } finally {
        conexionBD::cerrar_conexion();
    }
}
}
