<?php
    require '../../model/model_obras_sociales.php';
    $MOS = new Modelo_Obras_Sociales();//Instaciamos
    $consulta = $MOS->Cargar_Select_Obras_Sociales();
    echo json_encode($consulta);
 
?>
