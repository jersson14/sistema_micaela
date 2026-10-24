<?php
    require '../../utilitario/requiere_sesion.php';
    require '../../model/model_salidas_diarias.php';
    require '../../model/model_estados_encomienda.php';
    $MSD = new Modelo_Salidas_Diarias();//Instaciamos
    $id = (int)($_POST['id'] ?? 0);

    // Completar la salida: el procedimiento valida que siga EN TRANSITO
    try {
        echo $MSD->Modificar_Salida_Estatus($id,$ID_USUARIO_SESION);
    } catch (PDOException $e) {
        echo Reglas_Estado_Encomienda::Mensaje_Error($e);
    }
?>
