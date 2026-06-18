<?php
    require '../../model/model_usuario.php';

    $MUSU= new Modelo_Usuario();//Instaciamos
    $consulta = $MUSU->listar_total_notas_debito();
    echo json_encode($consulta);

?>