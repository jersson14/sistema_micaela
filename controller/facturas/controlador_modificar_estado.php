<?php
    require '../../model/model_facturas.php';
    $MFA = new Modelo_Facturas();//Instaciamos
    $id = strtoupper(htmlspecialchars($_POST['id'],ENT_QUOTES,'UTF-8'));
    $esta = strtoupper(htmlspecialchars($_POST['esta'],ENT_QUOTES,'UTF-8'));
    $motivo = strtoupper(htmlspecialchars($_POST['motivo'],ENT_QUOTES,'UTF-8'));
    $idusu = strtoupper(htmlspecialchars($_POST['idusu'],ENT_QUOTES,'UTF-8'));


    $consulta = $MFA->Modificar_Estado($id,$esta,$motivo,$idusu);
    echo $consulta;



?>