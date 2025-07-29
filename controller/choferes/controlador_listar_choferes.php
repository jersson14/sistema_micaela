<?php
    require '../../model/model_choferes.php';
    $MCH = new Modelo_Choferes();//Instaciamos
    $consulta = $MCH->Listar_Choferes();
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
