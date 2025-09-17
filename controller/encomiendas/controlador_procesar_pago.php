<?php
    require '../../model/model_encomiendas.php';
    $MEN = new Modelo_Encomiendas();//Instaciamos
    $id = strtoupper(htmlspecialchars($_POST['id'],ENT_QUOTES,'UTF-8'));
    $nuevo_estado = strtoupper(htmlspecialchars($_POST['nuevo_estado'],ENT_QUOTES,'UTF-8'));
    $saldo_pendiente = strtoupper(htmlspecialchars($_POST['saldo_pendiente'],ENT_QUOTES,'UTF-8'));
    $monto_recibido = strtoupper(htmlspecialchars($_POST['monto_recibido'],ENT_QUOTES,'UTF-8'));
    $idusu = strtoupper(htmlspecialchars($_POST['idusu'],ENT_QUOTES,'UTF-8'));


    $consulta = $MEN->Realizar_Pago($id,$nuevo_estado,$saldo_pendiente,$monto_recibido,$idusu);
    echo $consulta;



?>