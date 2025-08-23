<?php
    require '../../model/model_rutas.php';
    $MRU = new Modelo_Rutas();//Instaciamos
    $consulta = $MRU->Listar_Rutas();
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
