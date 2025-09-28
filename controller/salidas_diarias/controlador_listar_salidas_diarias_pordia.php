<?php
    require '../../model/model_salidas_diarias.php';
    $MSD = new Modelo_Salidas_Diarias();//Instaciamos
    $consulta = $MSD->Listar_Salidas_Diarias_pordia();
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
