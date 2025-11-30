<?php
require_once 'model_conexion.php';

class Modelo_Tickets extends conexionBD
{
    public function Obtener_Correlativo_Ticket()
    {
        try {
            $c = conexionBD::conexionPDO();
            $sql = "SELECT COALESCE(MAX(CAST(SUBSTRING(numero_ticket, 4) AS UNSIGNED)), 0) + 1 AS correlativo
                    FROM tickets_viaje";
            $query = $c->prepare($sql);
            $query->execute();
            $resultado = $query->fetch(PDO::FETCH_ASSOC);
            return str_pad($resultado['correlativo'], 8, '0', STR_PAD_LEFT);
        } catch (PDOException $e) {
            error_log("Error en Obtener_Correlativo_Ticket: " . $e->getMessage());
            return str_pad(1, 8, '0', STR_PAD_LEFT);
        }
    }

    public function Registrar_Cliente_Si_No_Existe($dni, $nombre_completo)
    {
        try {
            $c = conexionBD::conexionPDO();

            // Buscar cliente existente
            $sql_buscar = "SELECT id_cliente FROM cliente WHERE nro_documento = ?";
            $query = $c->prepare($sql_buscar);
            $query->execute([$dni]);
            $cliente = $query->fetch(PDO::FETCH_ASSOC);

            if ($cliente) {
                return $cliente['id_cliente'];
            }

            // Insertar nuevo cliente
            $sql_insertar = "INSERT INTO cliente (
                                tipo_documento, nro_documento, nombre_completo,
                                procedencia, celular, direccion, email,
                                total_viajes, ultimo_viaje, edad, created_at, updated_at
                            ) VALUES (
                                'DNI', ?, ?,
                                '', '', '', '',
                                0, NULL, 0, NOW(), NOW()
                            )";
            $query_insert = $c->prepare($sql_insertar);
            $resultado = $query_insert->execute([$dni, $nombre_completo]);

            if ($resultado) {
                return $c->lastInsertId();
            }

            return false;
        } catch (PDOException $e) {
            error_log("Error en Registrar_Cliente_Si_No_Existe: " . $e->getMessage());
            return false;
        }
    }

    public function Registrar_Ticket(
        $numero_ticket,
        $fecha,
        $idcliente,
        $idservicio,
        $idorigen,
        $iddestino,
        $gravada,
        $igv,
        $total,
        $usuario_crea
    ) {
        try {
            $c = conexionBD::conexionPDO();
            $sql = "INSERT INTO tickets_viaje (
                        numero_ticket, fecha, idcliente, idservicio, 
                        idorigen, iddestino, gravada, igv, total, usuario_crea
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $query = $c->prepare($sql);
            $resultado = $query->execute([
                $numero_ticket,
                $fecha,
                $idcliente,
                $idservicio,
                $idorigen,
                $iddestino,
                $gravada,
                $igv,
                $total,
                $usuario_crea
            ]);

            if ($resultado) {
                return $c->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error en Registrar_Ticket: " . $e->getMessage());
            return false;
        }
    }

    public function Listar_Tickets()
    {
        try {
            $c = conexionBD::conexionPDO();
            $sql = "SELECT
                        t.id,
                        t.numero_ticket,
                        t.fecha,
                        c.nombre_completo as cliente,
                        c.nro_documento as cliente_dni,
                        s.nombre as servicio_nombre,
                        ro.nombre as origen,
                        rd.nombre as destino,
                        t.gravada,
                        t.igv,
                        t.total,
                        CONCAT(u.usu_nombre, ' ', u.usu_apellido) as usuario,
                        t.created_at
                    FROM tickets_viaje t
                    INNER JOIN cliente c ON t.idcliente = c.id_cliente
                    INNER JOIN servicios s ON t.idservicio = s.id_servicio
                    INNER JOIN rutas ro ON t.idorigen = ro.idrutas
                    INNER JOIN rutas rd ON t.iddestino = rd.idrutas
                    LEFT JOIN usuario u ON t.usuario_crea = u.id_usuario
                    ORDER BY t.id DESC";
            $query = $c->prepare($sql);
            $query->execute();
            $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
            return $resultado;
        } catch (PDOException $e) {
            error_log("Error en Listar_Tickets: " . $e->getMessage());
            return array();
        }
    }

    public function Obtener_Ticket_Por_Id($id)
    {
        try {
            $c = conexionBD::conexionPDO();
            $sql = "SELECT
                        t.*,
                        c.nombre_completo as cliente_nombre,
                        c.nro_documento as cliente_dni,
                        c.celular as cliente_celular,
                        s.nombre as servicio_nombre,
                        s.costo as servicio_precio,
                        ro.nombre as origen_nombre,
                        rd.nombre as destino_nombre,
                        CONCAT(u.usu_nombre, ' ', u.usu_apellido) as usuario_nombre
                    FROM tickets_viaje t
                    INNER JOIN cliente c ON t.idcliente = c.id_cliente
                    INNER JOIN servicios s ON t.idservicio = s.id_servicio
                    INNER JOIN rutas ro ON t.idorigen = ro.idrutas
                    INNER JOIN rutas rd ON t.iddestino = rd.idrutas
                    LEFT JOIN usuario u ON t.usuario_crea = u.id_usuario
                    WHERE t.id = ?";
            $query = $c->prepare($sql);
            $query->execute([$id]);
            $resultado = $query->fetch(PDO::FETCH_ASSOC);
            return $resultado;
        } catch (PDOException $e) {
            error_log("Error en Obtener_Ticket_Por_Id: " . $e->getMessage());
            return false;
        }
    }

    public function Buscar_Cliente_Por_DNI($dni)
    {
        try {
            $c = conexionBD::conexionPDO();
            $sql = "SELECT DISTINCT
                        c.id_cliente,
                        c.nro_documento,
                        c.nombre_completo,
                        c.celular,
                        c.direccion
                    FROM cliente c
                    WHERE c.nro_documento = ?";
            $query = $c->prepare($sql);
            $query->execute([$dni]);
            $resultado = $query->fetch(PDO::FETCH_ASSOC);
            return $resultado;
        } catch (PDOException $e) {
            error_log("Error en Buscar_Cliente_Por_DNI: " . $e->getMessage());
            return false;
        }
    }

    public function Listar_Servicios()
    {
        try {
            $c = conexionBD::conexionPDO();
            $sql = "SELECT id_servicio, nombre, costo
                    FROM servicios
                    WHERE estado = 'ACTIVO'
                    ORDER BY nombre ASC";
            $query = $c->prepare($sql);
            $query->execute();
            $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
            return $resultado;
        } catch (PDOException $e) {
            error_log("Error en Listar_Servicios: " . $e->getMessage());
            return array();
        }
    }

    public function Listar_Rutas()
    {
        try {
            $c = conexionBD::conexionPDO();
            $sql = "SELECT idrutas, nombre
                    FROM rutas
                    WHERE estado = 'ACTIVO'
                    ORDER BY nombre ASC";
            $query = $c->prepare($sql);
            $query->execute();
            $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
            return $resultado;
        } catch (PDOException $e) {
            error_log("Error en Listar_Rutas: " . $e->getMessage());
            return array();
        }
    }
}