<?php
    require '../../model/model_servicios.php';
    $MSE = new Modelo_Servicios();//Instaciamos
    $consulta = $MSE->Listar_Servicios();
    if($consulta){
        echo json_encode($consulta);
    }else{
        echo '{
            "sEcho": 1,
            "iTotalRecords": "0",
            "iTotalDisplayRecords": "0",
            "aaData": []
        }';
    }
?>
