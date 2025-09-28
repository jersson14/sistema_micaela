<?php
    require '../../model/model_encomiendas.php';
    $MEN = new Modelo_Encomiendas();//Instaciamos
    $consulta = $MEN->Listar_Encomiendas_pordia();
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
