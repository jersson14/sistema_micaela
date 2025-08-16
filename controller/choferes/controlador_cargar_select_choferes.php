<?php
    require '../../model/model_choferes.php';
    $MCH = new Modelo_Choferes();//Instaciamos
    $consulta = $MCH->Cargar_Select_Choferes();
    echo json_encode($consulta);
 
?>
