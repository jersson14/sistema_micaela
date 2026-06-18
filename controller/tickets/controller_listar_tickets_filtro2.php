<?php
require_once '../../model/model_tickets.php';
$MT = new Modelo_Tickets();
    $fedes = htmlspecialchars($_POST['fedes'],ENT_QUOTES,'UTF-8');
    $fehas = htmlspecialchars($_POST['fehas'],ENT_QUOTES,'UTF-8');
    $usu = htmlspecialchars($_POST['usu'],ENT_QUOTES,'UTF-8');

    $consulta = $MT->Listar_nota_fecha_usuario($fedes,$fehas,$usu);
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
