<?php
require_once 'model_conexion.php';

class Modelo_Reportes extends conexionBD
{
    // ============================================================
    // 1. FACTURAS ARCHIVADAS (ANULADAS)
    // ============================================================
    public function Listar_Facturas_Archivadas($tipo = '', $fecha_desde = '', $fecha_hasta = '')
    {
        $c = conexionBD::conexionPDO();
        
        $sql = "SELECT 
                    c.id_comprobante,
                    c.tipo_comprobante,
                    c.serie,
                    c.correlativo,
                    CONCAT(c.serie, '-', LPAD(c.correlativo, 8, '0')) AS numero_comprobante,
                    c.fecha_emision,
                    c.total,
                    c.estado_sunat,
                    c.motivo_anulacion,
                    COALESCE(c.texto_motivo, c.observaciones, '') AS texto_motivo,
                    c.fecha_anulacion,
                    cl.razon_social,
                    cl.numero_documento,
                    CONCAT(u.usu_nombre, ' ', u.usu_apellido) AS usuario_nombre
                FROM comprobantes c
                INNER JOIN cliente_sunat cl ON c.id_cliente = cl.id_cliente
                LEFT JOIN usuario u ON c.usuario_anulacion = u.id_usuario
                WHERE c.estado_documento = 'ANULADO'";
        
        $params = [];
        
        if (!empty($tipo)) {
            $sql .= " AND c.tipo_comprobante = ?";
            $params[] = $tipo;
        }
        
        if (!empty($fecha_desde) && !empty($fecha_hasta)) {
            $sql .= " AND DATE(c.fecha_anulacion) BETWEEN ? AND ?";
            $params[] = $fecha_desde;
            $params[] = $fecha_hasta;
        }
        
        $sql .= " ORDER BY c.fecha_anulacion DESC";
        
        $query = $c->prepare($sql);
        
        foreach ($params as $key => $value) {
            $query->bindValue($key + 1, $value);
        }
        
        $query->execute();
        $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
        
        conexionBD::cerrar_conexion();
        return $resultado;
    }
    
    // ============================================================
    // 2. REPORTE INGRESOS VS GASTOS
    // ============================================================
    public function Reporte_Ingresos_Gastos($fecha_desde, $fecha_hasta)
    {
        $c = conexionBD::conexionPDO();
        
        try {
            // Total de ingresos (comprobantes aceptados)
            $sql_ingresos = "SELECT IFNULL(SUM(total), 0) AS total_ingresos
                            FROM comprobantes
                            WHERE estado_documento = 'ACTIVO'
                            AND estado_sunat IN ('ACEPTADO', 'ENVIADO')
                            AND tipo_comprobante IN ('01', '03')
                            AND DATE(fecha_emision) BETWEEN ? AND ?";
            
            $query_ingresos = $c->prepare($sql_ingresos);
            $query_ingresos->execute([$fecha_desde, $fecha_hasta]);
            $ingresos = $query_ingresos->fetch(PDO::FETCH_ASSOC);
            
            // Total de gastos
            $sql_gastos = "SELECT IFNULL(SUM(monto), 0) AS total_gastos
                          FROM gastos
                          WHERE estado = 'VALIDO'
                          AND DATE(created_at) BETWEEN ? AND ?";
            
            $query_gastos = $c->prepare($sql_gastos);
            $query_gastos->execute([$fecha_desde, $fecha_hasta]);
            $gastos = $query_gastos->fetch(PDO::FETCH_ASSOC);
            
            // Calcular balance
            $total_ingresos = floatval($ingresos['total_ingresos']);
            $total_gastos = floatval($gastos['total_gastos']);
            $balance = $total_ingresos - $total_gastos;
            
            // Detalle por día (CORREGIDO)
            $sql_detalle = "SELECT 
                                DATE(fecha) AS fecha,
                                SUM(CASE WHEN tipo = 'INGRESO' THEN monto ELSE 0 END) AS ingreso_dia,
                                SUM(CASE WHEN tipo = 'GASTO' THEN monto ELSE 0 END) AS gasto_dia
                            FROM (
                                SELECT DATE(fecha_emision) AS fecha, total AS monto, 'INGRESO' AS tipo
                                FROM comprobantes
                                WHERE estado_documento = 'ACTIVO'
                                AND estado_sunat IN ('ACEPTADO', 'ENVIADO')
                                AND tipo_comprobante IN ('01', '03')
                                AND DATE(fecha_emision) BETWEEN ? AND ?
                                
                                UNION ALL
                                
                                SELECT DATE(created_at) AS fecha, monto, 'GASTO' AS tipo
                                FROM gastos
                                WHERE estado = 'VALIDO'
                                AND DATE(created_at) BETWEEN ? AND ?
                            ) AS movimientos
                            GROUP BY DATE(fecha)
                            ORDER BY fecha DESC";
            
            $query_detalle = $c->prepare($sql_detalle);
            $query_detalle->execute([$fecha_desde, $fecha_hasta, $fecha_desde, $fecha_hasta]);
            $detalle_diario = $query_detalle->fetchAll(PDO::FETCH_ASSOC);
            
            // Log para debugging
            error_log("Reporte Ingresos vs Gastos - Detalle diario: " . count($detalle_diario) . " registros");
            
            return [
                'total_ingresos' => $total_ingresos,
                'total_gastos' => $total_gastos,
                'balance' => $balance,
                'detalle_diario' => $detalle_diario
            ];
            
        } catch (Exception $e) {
            error_log("Error en Reporte_Ingresos_Gastos: " . $e->getMessage());
            return [
                'total_ingresos' => 0,
                'total_gastos' => 0,
                'balance' => 0,
                'detalle_diario' => []
            ];
        } finally {
            conexionBD::cerrar_conexion();
        }
    }
    
    // ============================================================
    // 3. REPORTE SERVICIOS PRESTADOS
    // ============================================================
    public function Reporte_Servicios_Prestados($fecha_desde, $fecha_hasta)
    {
        $c = conexionBD::conexionPDO();
        
        try {
            $sql = "SELECT 
                        s.id_servicio,
                        s.nombre,
                        s.costo,
                        COUNT(c.id_comprobante) AS cantidad_vendida,
                        SUM(c.total) AS total_vendido
                    FROM servicios s
                    LEFT JOIN comprobantes c ON s.id_servicio = c.id_servicio
                        AND c.estado_documento = 'ACTIVO'
                        AND c.estado_sunat IN ('ACEPTADO', 'ENVIADO')
                        AND DATE(c.fecha_emision) BETWEEN ? AND ?
                    WHERE s.estado = 'ACTIVO'
                    GROUP BY s.id_servicio, s.nombre, s.costo
                    HAVING cantidad_vendida > 0
                    ORDER BY total_vendido DESC";
            
            $query = $c->prepare($sql);
            $query->execute([$fecha_desde, $fecha_hasta]);
            
            return $query->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Error en Reporte_Servicios_Prestados: " . $e->getMessage());
            return [];
        } finally {
            conexionBD::cerrar_conexion();
        }
    }
    
    // ============================================================
    // 4. REPORTE SALIDAS DIARIAS
    // ============================================================
    public function Reporte_Salidas_Diarias($fecha_desde, $fecha_hasta, $estado = '')
    {
        $c = conexionBD::conexionPDO();
        
        $sql = "SELECT 
                    sd.id_salidas_diarias,
                    sd.salida_nro,
                    sd.fecha_hora,
                    sd.monto,
                    sd.estado,
                    sd.total_pasajeros,
                    sd.total_encomiendas,
                    sd.observacion,
                    CONCAT(ch.nombres_apellidos) AS conductor,
                    ch.placa_vehiculo,
                    ro.nombre AS origen,
                    rd.nombre AS destino,
                    CONCAT(u.usu_nombre, ' ', u.usu_apellido) AS usuario_registro
                FROM salidas_diarias sd
                INNER JOIN chofer ch ON sd.id_conductor = ch.id_chofer
                LEFT JOIN rutas ro ON sd.id_origen = ro.idrutas
                LEFT JOIN rutas rd ON sd.id_destino = rd.idrutas
                LEFT JOIN usuario u ON sd.id_usuario = u.id_usuario
                WHERE DATE(sd.fecha_hora) BETWEEN ? AND ?";
        
        $params = [$fecha_desde, $fecha_hasta];
        
        if (!empty($estado)) {
            $sql .= " AND sd.estado = ?";
            $params[] = $estado;
        }
        
        $sql .= " ORDER BY sd.fecha_hora DESC";
        
        $query = $c->prepare($sql);
        $query->execute($params);
        
        $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
        
        conexionBD::cerrar_conexion();
        return $resultado;
    }
    
    // ============================================================
    // 5. REPORTE DE CLIENTES (USANDO TABLA 'clientes')
    // ============================================================
    public function Reporte_Clientes($tipo_filtro = 'todos', $fecha_desde = '', $fecha_hasta = '')
    {
        $c = conexionBD::conexionPDO();
        
        try {
            $sql = "SELECT 
                        cl.id_cliente,
                        cl.tipo_documento,
                        cl.nro_documento,
                        cl.nombre_completo,
                        COALESCE(cl.celular, '') AS celular,
                        COALESCE(cl.email, '') AS email,
                        COALESCE(cl.procedencia, '') AS procedencia,
                        COALESCE(cl.total_viajes, 0) AS total_viajes,
                        cl.ultimo_viaje,
                        COUNT(DISTINCT c.id_comprobante) AS comprobantes_emitidos,
                        COALESCE(SUM(c.total), 0) AS total_gastado
                    FROM clientes cl
                    LEFT JOIN cliente_sunat cs ON cl.nro_documento = cs.numero_documento
                    LEFT JOIN comprobantes c ON cs.id_cliente = c.id_cliente
                        AND c.estado_documento = 'ACTIVO'
                        AND c.estado_sunat IN ('ACEPTADO', 'ENVIADO')";
            
            $params = [];
            $where_clauses = [];
            
            if (!empty($fecha_desde) && !empty($fecha_hasta)) {
                $where_clauses[] = "DATE(c.fecha_emision) BETWEEN ? AND ?";
                $params[] = $fecha_desde;
                $params[] = $fecha_hasta;
            }
            
            if (!empty($where_clauses)) {
                $sql .= " WHERE " . implode(' AND ', $where_clauses);
            }
            
            $sql .= " GROUP BY cl.id_cliente, cl.tipo_documento, cl.nro_documento, 
                      cl.nombre_completo, cl.celular, cl.email, cl.procedencia, 
                      cl.total_viajes, cl.ultimo_viaje";
            
            // Filtros adicionales después del GROUP BY
            switch ($tipo_filtro) {
                case 'frecuentes':
                    $sql .= " HAVING total_viajes >= 5";
                    break;
                case 'nuevos':
                    $sql .= " HAVING total_viajes <= 2";
                    break;
                case 'inactivos':
                    $sql .= " HAVING ultimo_viaje IS NOT NULL AND DATEDIFF(CURDATE(), ultimo_viaje) > 30";
                    break;
            }
            
            $sql .= " ORDER BY total_gastado DESC";
            
            $query = $c->prepare($sql);
            $query->execute($params);
            
            $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
            
            return $resultado;
            
        } catch (Exception $e) {
            error_log("Error en Reporte_Clientes: " . $e->getMessage());
            
            // Query de respaldo sin joins complejos
            try {
                $sql_simple = "SELECT 
                                id_cliente,
                                tipo_documento,
                                nro_documento,
                                nombre_completo,
                                COALESCE(celular, '') AS celular,
                                COALESCE(email, '') AS email,
                                COALESCE(procedencia, '') AS procedencia,
                                COALESCE(total_viajes, 0) AS total_viajes,
                                ultimo_viaje,
                                0 AS comprobantes_emitidos,
                                0.00 AS total_gastado
                            FROM clientes
                            ORDER BY nombre_completo";
                
                $query = $c->prepare($sql_simple);
                $query->execute();
                
                return $query->fetchAll(PDO::FETCH_ASSOC);
                
            } catch (Exception $e2) {
                error_log("Error en query simple de Reporte_Clientes: " . $e2->getMessage());
                return [];
            }
        } finally {
            conexionBD::cerrar_conexion();
        }
    }
    
    // ============================================================
    // 6. REPORTE DE CHOFERES (USANDO TABLA 'choferes')
    // ============================================================
    public function Reporte_Choferes($estado = '', $fecha_desde = '', $fecha_hasta = '')
    {
        $c = conexionBD::conexionPDO();
        
        try {
            $sql = "SELECT 
                        ch.id_chofer,
                        ch.tipo_documen,
                        ch.nro_doc,
                        ch.nombres_apellidos,
                        COALESCE(ch.celular, '') AS celular,
                        COALESCE(ch.marca_vehiculo, '') AS marca_vehiculo,
                        COALESCE(ch.placa_vehiculo, '') AS placa_vehiculo,
                        COALESCE(ch.nro_licencia, '') AS nro_licencia,
                        ch.fecha_vencimiento_licencia,
                        ch.estado,
                        COUNT(DISTINCT sd.id_salidas_diarias) AS total_salidas,
                        COUNT(DISTINCT c.id_comprobante) AS total_comprobantes,
                        COALESCE(SUM(c.total), 0) AS total_facturado
                    FROM choferes ch
                    LEFT JOIN salidas_diarias sd ON ch.id_chofer = sd.id_conductor";
            
            $where_parts = [];
            $params = [];
            
            if (!empty($fecha_desde) && !empty($fecha_hasta)) {
                $where_parts[] = "DATE(sd.fecha_hora) BETWEEN ? AND ?";
                $params[] = $fecha_desde;
                $params[] = $fecha_hasta;
            }
            
            if (!empty($where_parts)) {
                $sql .= " AND " . implode(' AND ', $where_parts);
            }
            
            $sql .= " LEFT JOIN comprobantes c ON ch.id_chofer = c.idconductor
                        AND c.estado_documento = 'ACTIVO'
                        AND c.estado_sunat IN ('ACEPTADO', 'ENVIADO')";
            
            if (!empty($fecha_desde) && !empty($fecha_hasta)) {
                $sql .= " AND DATE(c.fecha_emision) BETWEEN ? AND ?";
                $params[] = $fecha_desde;
                $params[] = $fecha_hasta;
            }
            
            $where_conditions = [];
            if (!empty($estado)) {
                $where_conditions[] = "ch.estado = ?";
                $params[] = $estado;
            }
            
            if (!empty($where_conditions)) {
                $sql .= " WHERE " . implode(' AND ', $where_conditions);
            }
            
            $sql .= " GROUP BY ch.id_chofer, ch.tipo_documen, ch.nro_doc, ch.nombres_apellidos, 
                      ch.celular, ch.marca_vehiculo, ch.placa_vehiculo, ch.nro_licencia, 
                      ch.fecha_vencimiento_licencia, ch.estado
                      ORDER BY total_salidas DESC";
            
            $query = $c->prepare($sql);
            $query->execute($params);
            
            $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
            
            return $resultado;
            
        } catch (Exception $e) {
            error_log("Error en Reporte_Choferes: " . $e->getMessage());
            
            // Query de respaldo sin joins
            try {
                $sql_simple = "SELECT 
                                id_chofer,
                                tipo_documen,
                                nro_doc,
                                nombres_apellidos,
                                COALESCE(celular, '') AS celular,
                                COALESCE(marca_vehiculo, '') AS marca_vehiculo,
                                COALESCE(placa_vehiculo, '') AS placa_vehiculo,
                                COALESCE(nro_licencia, '') AS nro_licencia,
                                fecha_vencimiento_licencia,
                                estado,
                                0 AS total_salidas,
                                0 AS total_comprobantes,
                                0.00 AS total_facturado
                            FROM choferes";
                
                if (!empty($estado)) {
                    $sql_simple .= " WHERE estado = ?";
                    $params = [$estado];
                } else {
                    $params = [];
                }
                
                $sql_simple .= " ORDER BY nombres_apellidos";
                
                $query = $c->prepare($sql_simple);
                $query->execute($params);
                
                return $query->fetchAll(PDO::FETCH_ASSOC);
                
            } catch (Exception $e2) {
                error_log("Error en query simple de Reporte_Choferes: " . $e2->getMessage());
                return [];
            }
        } finally {
            conexionBD::cerrar_conexion();
        }
    }
    
    // ============================================================
    // 7. REPORTE ESTADO SUNAT
    // ============================================================
    public function Reporte_Estado_SUNAT($estado = '', $fecha_desde = '', $fecha_hasta = '')
    {
        $c = conexionBD::conexionPDO();
        
        $sql = "SELECT 
                    c.id_comprobante,
                    c.tipo_comprobante,
                    CONCAT(c.serie, '-', LPAD(c.correlativo, 8, '0')) AS numero_comprobante,
                    c.fecha_emision,
                    c.fecha_envio_sunat,
                    c.estado_sunat,
                    c.codigo_respuesta_sunat,
                    c.descripcion_respuesta_sunat,
                    c.total,
                    cl.razon_social,
                    cl.numero_documento,
                    CONCAT(u.usu_nombre, ' ', u.usu_apellido) AS usuario_emisor
                FROM comprobantes c
                INNER JOIN cliente_sunat cl ON c.id_cliente = cl.id_cliente
                LEFT JOIN usuario u ON c.id_usuario = u.id_usuario
                WHERE c.estado_documento = 'ACTIVO'";
        
        $params = [];
        
        if (!empty($estado)) {
            $sql .= " AND c.estado_sunat = ?";
            $params[] = $estado;
        }
        
        if (!empty($fecha_desde) && !empty($fecha_hasta)) {
            $sql .= " AND DATE(c.fecha_envio_sunat) BETWEEN ? AND ?";
            $params[] = $fecha_desde;
            $params[] = $fecha_hasta;
        }
        
        $sql .= " ORDER BY c.fecha_envio_sunat DESC";
        
        $query = $c->prepare($sql);
        $query->execute($params);
        
        $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
        
        conexionBD::cerrar_conexion();
        return $resultado;
    }
    
    // ============================================================
    // 8. ESTADÍSTICAS GENERALES DASHBOARD
    // ============================================================
    public function Obtener_Estadisticas_Dashboard()
    {
        $c = conexionBD::conexionPDO();
        
        try {
            // Comprobantes del mes actual
            $sql_comprobantes = "SELECT 
                                    COUNT(*) AS total,
                                    SUM(total) AS monto_total
                                FROM comprobantes
                                WHERE MONTH(fecha_emision) = MONTH(CURDATE())
                                AND YEAR(fecha_emision) = YEAR(CURDATE())
                                AND estado_documento = 'ACTIVO'
                                AND estado_sunat IN ('ACEPTADO', 'ENVIADO')";
            
            $comprobantes = $c->query($sql_comprobantes)->fetch(PDO::FETCH_ASSOC);
            
            // Pendientes de envío
            $sql_pendientes = "SELECT COUNT(*) AS total
                              FROM comprobantes
                              WHERE estado_sunat = 'PENDIENTE'
                              AND estado_documento = 'ACTIVO'";
            
            $pendientes = $c->query($sql_pendientes)->fetch(PDO::FETCH_ASSOC);
            
            // Clientes registrados
            $sql_clientes = "SELECT COUNT(*) AS total FROM cliente";
            $clientes = $c->query($sql_clientes)->fetch(PDO::FETCH_ASSOC);
            
            // Salidas de hoy
            $sql_salidas = "SELECT COUNT(*) AS total
                           FROM salidas_diarias
                           WHERE DATE(fecha_hora) = CURDATE()
                           AND estado != 'ELIMINADO'";
            
            $salidas = $c->query($sql_salidas)->fetch(PDO::FETCH_ASSOC);
            
            return [
                'comprobantes_mes' => $comprobantes['total'] ?? 0,
                'monto_mes' => $comprobantes['monto_total'] ?? 0,
                'pendientes_sunat' => $pendientes['total'] ?? 0,
                'total_clientes' => $clientes['total'] ?? 0,
                'salidas_hoy' => $salidas['total'] ?? 0
            ];
            
        } catch (Exception $e) {
            error_log("Error en Obtener_Estadisticas_Dashboard: " . $e->getMessage());
            return [];
        } finally {
            conexionBD::cerrar_conexion();
        }
    }
    
    // ============================================================
    // 9. GRÁFICA INGRESOS MENSUALES
    // ============================================================
    public function Grafica_Ingresos_Mensuales($anio)
    {
        $c = conexionBD::conexionPDO();
        
        try {
            $sql = "SELECT 
                        MONTH(fecha_emision) AS mes,
                        MONTHNAME(fecha_emision) AS nombre_mes,
                        SUM(total) AS total_mes
                    FROM comprobantes
                    WHERE YEAR(fecha_emision) = ?
                    AND estado_documento = 'ACTIVO'
                    AND estado_sunat IN ('ACEPTADO', 'ENVIADO')
                    GROUP BY MONTH(fecha_emision), MONTHNAME(fecha_emision)
                    ORDER BY mes";
            
            $query = $c->prepare($sql);
            $query->execute([$anio]);
            
            return $query->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Error en Grafica_Ingresos_Mensuales: " . $e->getMessage());
            return [];
        } finally {
            conexionBD::cerrar_conexion();
        }
    }
    
    // ============================================================
    // 10. TOP CLIENTES
    // ============================================================
    public function Top_Clientes($limite = 10, $fecha_desde = '', $fecha_hasta = '')
    {
        $c = conexionBD::conexionPDO();
        
        $sql = "SELECT 
                    cs.razon_social,
                    cs.numero_documento,
                    COUNT(c.id_comprobante) AS total_comprobantes,
                    SUM(c.total) AS total_gastado
                FROM cliente_sunat cs
                INNER JOIN comprobantes c ON cs.id_cliente = c.id_cliente
                WHERE c.estado_documento = 'ACTIVO'
                AND c.estado_sunat IN ('ACEPTADO', 'ENVIADO')";
        
        $params = [];
        
        if (!empty($fecha_desde) && !empty($fecha_hasta)) {
            $sql .= " AND DATE(c.fecha_emision) BETWEEN ? AND ?";
            $params[] = $fecha_desde;
            $params[] = $fecha_hasta;
        }
        
        $sql .= " GROUP BY cs.id_cliente
                  ORDER BY total_gastado DESC
                  LIMIT ?";
        
        $params[] = $limite;
        
        $query = $c->prepare($sql);
        $query->execute($params);
        
        $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
        
        conexionBD::cerrar_conexion();
        return $resultado;
    }
    
    // ============================================================
    // 11. TOP SERVICIOS
    // ============================================================
    public function Top_Servicios($limite = 10, $fecha_desde = '', $fecha_hasta = '')
    {
        $c = conexionBD::conexionPDO();
        
        $sql = "SELECT 
                    s.nombre,
                    s.costo,
                    COUNT(c.id_comprobante) AS veces_vendido,
                    SUM(c.total) AS total_vendido
                FROM servicios s
                INNER JOIN comprobantes c ON s.id_servicio = c.id_servicio
                WHERE c.estado_documento = 'ACTIVO'
                AND c.estado_sunat IN ('ACEPTADO', 'ENVIADO')";
        
        $params = [];
        
        if (!empty($fecha_desde) && !empty($fecha_hasta)) {
            $sql .= " AND DATE(c.fecha_emision) BETWEEN ? AND ?";
            $params[] = $fecha_desde;
            $params[] = $fecha_hasta;
        }
        
        $sql .= " GROUP BY s.id_servicio
                  ORDER BY veces_vendido DESC
                  LIMIT ?";
        
        $params[] = $limite;
        
        $query = $c->prepare($sql);
        $query->execute($params);
        
        $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
        
        conexionBD::cerrar_conexion();
        return $resultado;
    }
}
?>