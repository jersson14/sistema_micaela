<?php
    require '../../model/model_reservas.php';
    $MRE = new Modelo_Reservas();//Instaciamos
    $consulta = $MRE->Listar_Reservas_podia();
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
