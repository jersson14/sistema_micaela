<?php
    /**
     * Registra una salida diaria con sus pasajeros y encomiendas en una sola petición.
     * Todo o nada: se valida antes de guardar y, si algo falla a mitad de camino,
     * la salida se elimina (libera sus encomiendas y anula el ingreso).
     * Responde el id de la salida, o el motivo del rechazo.
     */
    require_once '../../utilitario/requiere_sesion.php';
    require '../../model/model_salidas_diarias.php';
    require '../../model/model_estados_encomienda.php';
    $MSD = new Modelo_Salidas_Diarias();//Instaciamos

    $conductor = (int)($_POST['conductor'] ?? 0);
    $origen = (int)($_POST['origen'] ?? 0);
    $destino = (int)($_POST['destino'] ?? 0);
    $monto = $_POST['monto'] ?? '';
    $fechaHora = strtoupper(htmlspecialchars($_POST['fechaHora'] ?? '',ENT_QUOTES,'UTF-8'));
    $observacion = strtoupper(htmlspecialchars($_POST['observacion'] ?? '',ENT_QUOTES,'UTF-8'));
    $pasajeros = json_decode($_POST['pasajeros'] ?? '[]', true);
    $encomiendas = json_decode($_POST['encomiendas'] ?? '[]', true);

    // ---- Validaciones antes de guardar ----
    if (!$conductor || !$origen || !$destino || trim($fechaHora) === '') {
        echo 'Complete conductor, origen, destino y fecha';
        exit;
    }
    if ($origen === $destino) {
        echo 'El origen y el destino no pueden ser iguales';
        exit;
    }
    if (!is_numeric($monto) || (float)$monto < 0) {
        echo 'El monto de la salida no es válido';
        exit;
    }
    if (!is_array($pasajeros) || !is_array($encomiendas)) {
        echo 'Datos de pasajeros o encomiendas no válidos';
        exit;
    }
    foreach ($pasajeros as $i => $p) {
        if (empty($p['tipo_documento']) || empty($p['documento']) || empty($p['nombres'])) {
            echo 'Faltan datos del pasajero ' . ($i + 1);
            exit;
        }
    }
    $encomiendas = array_values(array_unique(array_filter(array_map('intval', $encomiendas))));
    $filas = $MSD->Encomiendas_Para_Salida($encomiendas);
    if (count($filas) !== count($encomiendas)) {
        echo 'Alguna de las encomiendas seleccionadas ya no existe';
        exit;
    }
    foreach ($filas as $e) {
        if ($e['estado_encomienda'] !== 'PENDIENTE') {
            echo "La encomienda {$e['boleta_nro']} ya no está PENDIENTE (está {$e['estado_encomienda']})";
            exit;
        }
        if ((int)$e['id_conductor'] !== $conductor || (int)$e['id_origen'] !== $origen || (int)$e['id_destino'] !== $destino) {
            echo "La encomienda {$e['boleta_nro']} no es de este conductor o de esta ruta";
            exit;
        }
    }

    // ---- Guardar ----
    try {
        $idSalida = $MSD->Registrar_Salida_Diaria($conductor,(float)$monto,$fechaHora,$origen,$destino,$observacion,$ID_USUARIO_SESION,count($pasajeros),count($encomiendas));
    } catch (PDOException $e) {
        echo Reglas_Estado_Encomienda::Mensaje_Error($e);
        exit;
    }
    if (!$idSalida) {
        echo 'No se pudo registrar la salida diaria';
        exit;
    }

    try {
        foreach ($pasajeros as $p) {
            $edad = (isset($p['edad']) && is_numeric($p['edad'])) ? (int)$p['edad'] : null;
            $MSD->Registrar_detalle_pasajeros(
                $idSalida,
                htmlspecialchars($p['tipo_documento'], ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($p['documento'], ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($p['nombres'], ENT_QUOTES, 'UTF-8'),
                $edad,
                htmlspecialchars($p['celular'] ?? '', ENT_QUOTES, 'UTF-8')
            );
        }
        foreach ($encomiendas as $idEncomienda) {
            $MSD->Registrar_detalle_encomiendas($idSalida, $idEncomienda);
        }
    } catch (PDOException $e) {
        // Deshacer: la salida queda ELIMINADA, sus encomiendas vuelven a PENDIENTE
        $motivo = Reglas_Estado_Encomienda::Mensaje_Error($e);
        try {
            $MSD->Eliminar_Salida_diaria($idSalida, $ID_USUARIO_SESION);
        } catch (PDOException $e2) {
            error_log('No se pudo deshacer la salida ' . $idSalida . ': ' . $e2->getMessage());
        }
        echo 'No se registró la salida: ' . $motivo;
        exit;
    }

    echo $idSalida;
?>
