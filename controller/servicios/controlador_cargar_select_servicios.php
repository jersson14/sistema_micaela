<?php
    require '../../model/model_servicios.php';
    $MSE = new Modelo_Servicios();//Instaciamos
    $consulta = $MSE->Cargar_Select_Servicios();
    echo json_encode($consulta);
 
?>
