
<?php
require_once 'model_conexion.php';

class Modelo_Comprobantes extends conexionBD{
    
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
    public function Obtener_Correlativo($serie, $tipo_comprobante) {
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
   public function Listar_Comprobantes($estado = '', $fecha_desde = '', $fecha_hasta = '') {
    $c = conexionBD::conexionPDO();
    
    $sql = "SELECT 
                c.id_comprobante,
                c.tipo_comprobante,
                c.serie,
                c.correlativo,
                CONCAT(c.serie, '-', LPAD(c.correlativo, 8, '0')) as numero_comprobante,
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
                s.nombre as servicio_nombre,
                r_origen.nombre as origen,
                r_destino.nombre as destino,
                CONCAT(u.usu_nombre, ' ', u.usu_apellido) as usuario_nombre,
                c.created_at
            FROM comprobantes c
            INNER JOIN cliente_sunat cl ON c.id_cliente = cl.id_cliente
            LEFT JOIN tipo_pago tp ON c.id_tipo_pago = tp.id_tipo_pago
            LEFT JOIN servicios s ON c.id_servicio = s.id_servicio
            LEFT JOIN rutas r_origen ON c.id_origen = r_origen.idrutas
            LEFT JOIN rutas r_destino ON c.iddestino = r_destino.idrutas
            LEFT JOIN usuario u ON c.id_usuario = u.id_usuario
            WHERE 1=1";
    
    $params = array();
    
    if (!empty($estado)) {
        $sql .= " AND c.estado_sunat = ?";
        $params[] = $estado;
    }
    
    if (!empty($fecha_desde) && !empty($fecha_hasta)) {
        $sql .= " AND c.fecha_emision BETWEEN ? AND ?";
        $params[] = $fecha_desde;
        $params[] = $fecha_hasta;
    }
    
    $sql .= " ORDER BY c.id_comprobante DESC";
    
    $query = $c->prepare($sql);
    
    foreach ($params as $key => $value) {
        $query->bindValue($key + 1, $value);
    }
    
    $query->execute();
    $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
    
    return $resultado;
    
    conexionBD::cerrar_conexion();
}
    
    // ========================================
    // OBTENER COMPROBANTE POR ID
    // ========================================
public function Obtener_Comprobante($id_comprobante) {
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
            WHERE c.id_comprobante = ?";

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
public function Verificar_Estado_SUNAT($id_comprobante) {
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
public function Actualizar_Estado_SUNAT($id_comprobante, $estado) {
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
    public function Anular_Comprobante($id_comprobante, $motivo, $usuario) {
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
    public function ObtenerResumenEnvios() {
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
    public function ListarPendientesEnvio($tipo = '', $fecha_desde = '', $fecha_hasta = '') {
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
    public function ListarHistorialEnvios($tipo = '', $estado = '', $fecha_desde = '', $fecha_hasta = '') {
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
public function ObtenerRespuestaSunat($id_comprobante) {
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


}
?>