<?php
    /**
     * Edita una salida diaria en una sola petición: datos de la salida, pasajeros
     * nuevos y encomiendas que se quitan. Se valida todo antes de guardar; si algo
     * falla a mitad de camino se deshace lo hecho (vuelven a subirse las encomiendas
     * quitadas y se quitan los pasajeros agregados). Responde el id de la salida o
     * el motivo del rechazo.
     */
    require_once '../../utilitario/requiere_sesion.php';
    require '../../model/model_salidas_diarias.php';
    require '../../model/model_estados_encomienda.php';
    $MSD = new Modelo_Salidas_Diarias();//Instaciamos

    $idSalida = (int)($_POST['idSalida'] ?? 0);
    $monto = $_POST['monto'] ?? '';
    $fechaactualizar = strtoupper(htmlspecialchars($_POST['fechaactualizar'] ?? '',ENT_QUOTES,'UTF-8'));
    $observacion = strtoupper(htmlspecialchars($_POST['observacion'] ?? '',ENT_QUOTES,'UTF-8'));
    $pasajeros = json_decode($_POST['pasajeros'] ?? '[]', true);   // solo los agregados en esta edición
    $quitar = json_decode($_POST['quitar'] ?? '[]', true);         // encomiendas que se desmarcaron

    // ---- Validaciones antes de guardar ----
    if ($MSD->Estado_Salida($idSalida) !== 'EN TRANSITO') {
        echo 'Solo se puede editar una salida EN TRANSITO';
        exit;
    }
    if (!is_numeric($monto) || (float)$monto < 0 || trim($fechaactualizar) === '') {
        echo 'Revise el monto y la fecha de la salida';
        exit;
    }
    if (!is_array($pasajeros) || !is_array($quitar)) {
        echo 'Datos de pasajeros o encomiendas no válidos';
        exit;
    }
    $yaEstan = $MSD->Pasajeros_Salida($idSalida);
    $nuevos = [];
    foreach ($pasajeros as $i => $p) {
        if (empty($p['tipo_documento']) || empty($p['documento']) || empty($p['nombres'])) {
            echo 'Faltan datos del pasajero nuevo ' . ($i + 1);
            exit;
        }
        $clave = htmlspecialchars($p['tipo_documento'], ENT_QUOTES, 'UTF-8') . '|' . htmlspecialchars($p['documento'], ENT_QUOTES, 'UTF-8');
        if (isset($yaEstan[$clave]) || isset($nuevos[$clave])) {
            echo 'El pasajero ' . $p['documento'] . ' ya está en esta salida';
            exit;
        }
        $nuevos[$clave] = $p;
    }
    $activas = $MSD->Encomiendas_Activas_Salida($idSalida);
    $quitar = array_values(array_unique(array_filter(array_map('intval', $quitar))));
    foreach ($quitar as $idEncomienda) {
        if (!in_array($idEncomienda, $activas, true)) {
            echo "La encomienda $idEncomienda no está en esta salida";
            exit;
        }
    }

    // ---- Guardar (con deshacer si algo falla) ----
    $quitadas = [];
    $agregados = [];
    try {
        foreach ($nuevos as $clave => $p) {
            $celular = (isset($p['celular']) && $p['celular'] !== 'N/A') ? htmlspecialchars($p['celular'], ENT_QUOTES, 'UTF-8') : null;
            $edad = (isset($p['edad']) && is_numeric($p['edad'])) ? (int)$p['edad'] : null;
            $MSD->Modificar_detalle_pasajeros(
                $idSalida,
                htmlspecialchars($p['tipo_documento'], ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($p['documento'], ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($p['nombres'], ENT_QUOTES, 'UTF-8'),
                $edad,
                $celular
            );
            $agregados[] = $clave;
        }
        foreach ($quitar as $idEncomienda) {
            $MSD->Modificar_detalle_encomiendas($idSalida, $idEncomienda);
            $quitadas[] = $idEncomienda;
        }
        // Al final, con los totales que quedaron en la BD
        $totalPasajeros = count($MSD->Pasajeros_Salida($idSalida));
        $MSD->Modificar_Salida_Diaria($idSalida,(float)$monto,$fechaactualizar,$observacion,$ID_USUARIO_SESION,$totalPasajeros,0);
    } catch (PDOException $e) {
        $motivo = Reglas_Estado_Encomienda::Mensaje_Error($e);
        try {
            foreach ($quitadas as $idEncomienda) {
                $MSD->Registrar_detalle_encomiendas($idSalida, $idEncomienda);
            }
            $filas = $MSD->Pasajeros_Salida($idSalida);
            foreach ($agregados as $clave) {
                if (isset($filas[$clave])) {
                    $MSD->Eliminar_Cliente_Salida_diaria($filas[$clave]);
                }
            }
        } catch (PDOException $e2) {
            error_log('No se pudo deshacer la edición de la salida ' . $idSalida . ': ' . $e2->getMessage());
        }
        echo 'No se guardaron los cambios: ' . $motivo;
        exit;
    }

    echo $idSalida;
?>
