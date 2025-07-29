<?php
    require '../../model/model_facturas.php';
    $MFA = new Modelo_Facturas();//Instaciamos
    $consulta = $MFA->Listar_Facturas_todo_archivado();
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
