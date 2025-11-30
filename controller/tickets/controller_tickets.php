<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once '../../model/model_tickets.php';
$MT = new Modelo_Tickets();

$accion = isset($_POST['accion']) ? $_POST['accion'] : '';

if ($accion == 'OBTENER_CORRELATIVO') {
    try {
        $correlativo = $MT->Obtener_Correlativo_Ticket();
        echo json_encode(array('correlativo' => $correlativo));
    } catch (Exception $e) {
        echo json_encode(array('error' => $e->getMessage()));
    }
}

elseif ($accion == 'REGISTRAR_TICKET') {
    date_default_timezone_set('America/Lima');

    try {
        $fecha = !empty($_POST['fecha']) ? date('Y-m-d', strtotime($_POST['fecha'])) : date('Y-m-d');
        $dni_pasajero = $_POST['dni_pasajero'];
        $nombre_pasajero = strtoupper($_POST['nombre_pasajero']);
        $idservicio = intval($_POST['idservicio']);
        $idorigen = intval($_POST['idorigen']);
        $iddestino = intval($_POST['iddestino']);
        $gravada = floatval($_POST['gravada']);
        $igv = floatval($_POST['igv']);
        $total = floatval($_POST['total']);
        $usuario_crea = isset($_POST['usuario_crea']) ? intval($_POST['usuario_crea']) : 0;

        if ($usuario_crea <= 0) {
            echo json_encode(array('status' => 'error', 'message' => 'No se ha identificado el usuario en la sesión.'));
            exit;
        }

        if (empty($dni_pasajero) || empty($nombre_pasajero)) {
            echo json_encode(array('status' => 'error', 'message' => 'Faltan datos del pasajero'));
            exit;
        }

        if (empty($idservicio) || empty($idorigen) || empty($iddestino)) {
            echo json_encode(array('status' => 'error', 'message' => 'Faltan datos del viaje'));
            exit;
        }

        $idcliente = $MT->Registrar_Cliente_Si_No_Existe($dni_pasajero, $nombre_pasajero);

        if (!$idcliente) {
            echo json_encode(array('status' => 'error', 'message' => 'Error al registrar el cliente'));
            exit;
        }

        $correlativo = $MT->Obtener_Correlativo_Ticket();
        $numero_ticket = 'TKT' . $correlativo;

        $id_ticket = $MT->Registrar_Ticket(
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
        );

        if ($id_ticket) {
            echo json_encode(array(
                'status' => 'success',
                'message' => 'Ticket registrado correctamente',
                'id_ticket' => $id_ticket,
                'numero_ticket' => $numero_ticket
            ));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Error al registrar el ticket'));
        }
    } catch (Exception $e) {
        echo json_encode(array('status' => 'error', 'message' => 'Error: ' . $e->getMessage()));
    }
}

elseif ($accion == 'LISTAR_TICKETS') {
    try {
        $tickets = $MT->Listar_Tickets();
        echo json_encode($tickets);
    } catch (Exception $e) {
        echo json_encode(array('error' => $e->getMessage()));
    }
}

elseif ($accion == 'OBTENER_TICKET') {
    try {
        $id = intval($_POST['id']);
        $ticket = $MT->Obtener_Ticket_Por_Id($id);
        echo json_encode($ticket);
    } catch (Exception $e) {
        echo json_encode(array('error' => $e->getMessage()));
    }
}

elseif ($accion == 'BUSCAR_CLIENTE_DNI') {
    try {
        $dni = $_POST['dni'];
        $cliente = $MT->Buscar_Cliente_Por_DNI($dni);
        echo json_encode($cliente ? $cliente : array());
    } catch (Exception $e) {
        echo json_encode(array('error' => $e->getMessage()));
    }
}

elseif ($accion == 'LISTAR_SERVICIOS') {
    try {
        $servicios = $MT->Listar_Servicios();
        echo json_encode($servicios);
    } catch (Exception $e) {
        echo json_encode(array('error' => $e->getMessage()));
    }
}

elseif ($accion == 'LISTAR_RUTAS') {
    try {
        $rutas = $MT->Listar_Rutas();
        echo json_encode($rutas);
    } catch (Exception $e) {
        echo json_encode(array('error' => $e->getMessage()));
    }
}

else {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array('status' => 'error', 'message' => 'Acción no válida'));
}