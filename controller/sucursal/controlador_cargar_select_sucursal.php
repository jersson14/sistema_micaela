<?php
    require '../../model/model_sucursal.php';
    $MSU = new Modelo_Sucursal();//Instaciamos
    $consulta = $MSU->Cargar_Select_Sucursal();
    echo json_encode($consulta);
 
?>
