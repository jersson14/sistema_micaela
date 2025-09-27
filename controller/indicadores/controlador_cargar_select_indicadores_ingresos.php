<?php
    require '../../model/model_indicadores.php';
    $MI = new Modelo_Indicadores();//Instaciamos
    $consulta = $MI->Cargar_Select_Indicadores_ingresos();
    echo json_encode($consulta);
 
?>
