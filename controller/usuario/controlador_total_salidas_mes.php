<?php
    require '../../model/model_usuario.php';

    $MUSU= new Modelo_Usuario();//Instaciamos
    $consulta = $MUSU->listar_total_salidas_mes();
    echo json_encode($consulta);

?>