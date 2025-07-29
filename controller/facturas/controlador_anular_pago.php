<?php
    require '../../model/model_facturas.php';
    $MFA = new Modelo_Facturas();//Instaciamos
    $id = strtoupper(htmlspecialchars($_POST['id'],ENT_QUOTES,'UTF-8'));
    $idusu = strtoupper(htmlspecialchars($_POST['idusu'],ENT_QUOTES,'UTF-8'));
    $motivo_anulacion = strtoupper(htmlspecialchars($_POST['motivo_anulacion'],ENT_QUOTES,'UTF-8'));
    $monto_anulado = strtoupper(htmlspecialchars($_POST['monto_anulado'],ENT_QUOTES,'UTF-8'));


    $consulta = $MFA->Anular_pago($id,$idusu,$motivo_anulacion,$monto_anulado);
    echo $consulta;



?>