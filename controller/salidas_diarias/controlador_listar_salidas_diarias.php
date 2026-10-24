<?php
    require_once '../../utilitario/requiere_sesion.php';
    require '../../model/model_salidas_diarias.php';
    $MSD = new Modelo_Salidas_Diarias();//Instaciamos
    $consulta = $MSD->Listar_Salidas_Diarias();
    if($consulta){
        echo json_encode($consulta);
    }else{
        echo '{"data": []}'; // sin filas: la tabla muestra "no hay registros"
    }
?>
