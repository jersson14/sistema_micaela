<?php
    require '../../model/model_rutas.php';
    $MRU = new Modelo_Rutas();//Instaciamos
    $consulta = $MRU->Cargar_Select_Rutas();
    echo json_encode($consulta);
 
?>
