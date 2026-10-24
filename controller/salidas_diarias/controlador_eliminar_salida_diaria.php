<?php
    require '../../utilitario/requiere_sesion.php';
    require '../../model/model_salidas_diarias.php';
    require '../../model/model_estados_encomienda.php';
    $MSD = new Modelo_Salidas_Diarias();//Instaciamos
    $id = (int)($_POST['id'] ?? 0);

    // Solo EN TRANSITO; libera sus encomiendas y anula el ingreso (lo valida el procedimiento)
    try {
        echo $MSD->Eliminar_Salida_diaria($id,$ID_USUARIO_SESION);
    } catch (PDOException $e) {
        echo Reglas_Estado_Encomienda::Mensaje_Error($e);
    }
?>
