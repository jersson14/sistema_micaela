<?php
    require '../../model/model_practicas.php';
    $MPR = new Modelo_Practicas();//Instaciamos
    $consulta = $MPR->Listar_Practicas();
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
