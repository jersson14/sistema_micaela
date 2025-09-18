<?php
    require '../../model/model_encomiendas.php';
    $MEN = new Modelo_Encomiendas();//Instaciamos
    $consulta = $MEN->Cargar_Usuarios();
    echo json_encode($consulta);
 
?>
