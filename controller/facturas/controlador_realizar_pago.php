<?php
    require '../../model/model_facturas.php';
    $MFA = new Modelo_Facturas();//Instaciamos
    $id = strtoupper(htmlspecialchars($_POST['id'],ENT_QUOTES,'UTF-8'));
    $pagar = strtoupper(htmlspecialchars($_POST['pagar'],ENT_QUOTES,'UTF-8'));
    $saldo = strtoupper(htmlspecialchars($_POST['saldo'],ENT_QUOTES,'UTF-8'));
    $idusu = strtoupper(htmlspecialchars($_POST['idusu'],ENT_QUOTES,'UTF-8'));


    $consulta = $MFA->Realizar_pago($id,$pagar,$saldo,$idusu);
    echo $consulta;



?>