<?php
    require '../../model/model_sucursal.php';
    $MSU = new Modelo_Sucursal();//Instaciamos
    $consulta = $MSU->Listar_Sucursal();
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
