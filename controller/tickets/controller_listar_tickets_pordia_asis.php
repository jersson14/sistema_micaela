<?php
require_once '../../model/model_tickets.php';
$MT = new Modelo_Tickets();
    $ori = htmlspecialchars($_POST['ori'],ENT_QUOTES,'UTF-8');

    $consulta = $MT->Listar_ticket_pordia_asis($ori);
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
