<?php
require_once '../../model/model_tickets.php';
$MT = new Modelo_Tickets();
    $fechaini = htmlspecialchars($_POST['fechaini'],ENT_QUOTES,'UTF-8');
    $fechafin = htmlspecialchars($_POST['fechafin'],ENT_QUOTES,'UTF-8');
    $esta = htmlspecialchars($_POST['esta'],ENT_QUOTES,'UTF-8');
    $ori = htmlspecialchars($_POST['ori'],ENT_QUOTES,'UTF-8');

    $consulta = $MT->Listar_nota_fecha_ori_asis($fechaini,$fechafin,$esta,$ori);
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
