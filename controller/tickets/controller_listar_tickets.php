<?php
require_once '../../model/model_tickets.php';
$MT = new Modelo_Tickets();
    $consulta = $MT->Listar_Tickets();
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
