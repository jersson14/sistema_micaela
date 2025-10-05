<?php
    require '../../model/model_usuario.php';

    $MUSU= new Modelo_Usuario();//Instaciamos
    $ori = strtoupper(htmlspecialchars($_POST['ori'],ENT_QUOTES,'UTF-8'));
    $consulta = $MUSU->listar_total_encomiendas_semana_asis($ori);
    echo json_encode($consulta);

?>