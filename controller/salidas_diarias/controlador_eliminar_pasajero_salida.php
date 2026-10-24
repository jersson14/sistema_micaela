<?php
    require_once '../../utilitario/requiere_sesion.php';
    require '../../model/model_salidas_diarias.php';
    $MSD = new Modelo_Salidas_Diarias();//Instaciamos
    $id_pasajero = (int)($_POST['id_pasajero'] ?? 0);

    // Solo se quitan pasajeros de una salida que sigue EN TRANSITO
    if ($MSD->Estado_Salida_De_Pasajero($id_pasajero) !== 'EN TRANSITO') {
        echo 'Solo se pueden quitar pasajeros de una salida EN TRANSITO';
        exit;
    }

    $consulta = $MSD->Eliminar_Cliente_Salida_diaria($id_pasajero);
    echo $consulta;
?>
