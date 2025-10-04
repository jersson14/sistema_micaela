<?php
    require '../../model/model_reservas.php';
    $MRE = new Modelo_Reservas();//Instaciamos
    $ori = htmlspecialchars($_POST['ori'],ENT_QUOTES,'UTF-8');
    $des = htmlspecialchars($_POST['des'],ENT_QUOTES,'UTF-8');
    $consulta = $MRE->Cargar_Reservas($ori,$des);
    echo json_encode($consulta);
 
?>
