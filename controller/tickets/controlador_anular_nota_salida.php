<?php
require_once '../../model/model_tickets.php';
$MT = new Modelo_Tickets();
    $id = strtoupper(htmlspecialchars($_POST['id'],ENT_QUOTES,'UTF-8'));
    $motivo = strtoupper(htmlspecialchars($_POST['motivo'],ENT_QUOTES,'UTF-8'));

    $consulta = $MT->Anular_nota_salida($id,$motivo);
    echo $consulta;



?>