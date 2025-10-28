
<?php
require_once 'model_conexion.php';

class Modelo_Comprobantes extends conexionBD
{

    // ========================================
    // REGISTRAR O ACTUALIZAR CLIENTE
    // ========================================
    public function Registrar_Cliente_SUNAT(
        $tipo_documento,
        $numero_documento,
        $razon_social,
        $direccion,
        $telefono,
        $departamento,
        $provincia,
        $distrito,
        $ubigeo
    ) {
        $c = conexionBD::conexionPDO();
        $id_cliente = 0;

        try {
            // 1️⃣ Preparamos el procedimiento
            $sql = "CALL SP_REGISTRAR_CLIENTE_SUNAT(?, ?, ?, ?, ?, ?, ?, ?, ?, @p_id_cliente)";
            $query = $c->prepare($sql);
            $query->bindParam(1, $tipo_documento);
            $query->bindParam(2, $numero_documento);
            $query->bindParam(3, $razon_social);
            $query->bindParam(4, $direccion);
            $query->bindParam(5, $telefono);
            $query->bindParam(6, $departamento);
            $query->bindParam(7, $provincia);
            $query->bindParam(8, $distrito);
            $query->bindParam(9, $ubigeo);

            // 2️⃣ Ejecutamos
            $query->execute();
            $query->closeCursor(); // ⚠️ NECESARIO para liberar el resultado del CALL

            // 3️⃣ Consultamos el parámetro OUT
            $stmt = $c->query("SELECT @p_id_cliente AS id_cliente");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result && isset($result['id_cliente']) && $result['id_cliente'] > 0) {
                $id_cliente = (int)$result['id_cliente'];
            }
        } catch (Exception $e) {
            file_put_contents('error_clientes.log', '[' . date('Y-m-d H:i:s') . '] ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
            $id_cliente = 0;
        }

        conexionBD::cerrar_conexion();
        return $id_cliente;
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
    public function Actualizar_Estado_SUNAT($id_comprobante, $estado)
    {
        $c = conexionBD::conexionPDO();
        $sql = "UPDATE comprobantes SET estado_sunat = ? WHERE id_comprobante = ?";
        $query = $c->prepare($sql);
        $query->bindParam(1, $estado);
        $query->bindParam(2, $id_comprobante);
        $query->execute();
        conexionBD::cerrar_conexion();
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
        file_put_contents('error_comunicacion_baja.log', 
            '[' . date('Y-m-d H:i:s') . '] ' . $e->getMessage() . PHP_EOL, 
            FILE_APPEND);
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
public function Obtener_Datos_Basicos_Comprobante($id_comprobante)
{
    $c = conexionBD::conexionPDO();
    
    try {
        $sql = "SELECT fecha_emision, serie, correlativo 
                FROM comprobantes 
                WHERE id_comprobante = ?";
        
        $query = $c->prepare($sql);
        $query->execute([$id_comprobante]);
        
        return $query->fetch(PDO::FETCH_ASSOC);
        
    } catch (Exception $e) {
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
}
?>