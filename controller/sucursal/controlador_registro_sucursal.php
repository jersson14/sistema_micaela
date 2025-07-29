<?php
    require '../../model/model_sucursal.php';
    $MSU = new Modelo_Sucursal();//Instaciamos
    $sucur = strtoupper(htmlspecialchars($_POST['sucur'],ENT_QUOTES,'UTF-8'));
    $tele1 = strtoupper(htmlspecialchars($_POST['tele1'],ENT_QUOTES,'UTF-8'));
    $tele2 = strtoupper(htmlspecialchars($_POST['tele2'],ENT_QUOTES,'UTF-8'));
    $direc = strtoupper(htmlspecialchars($_POST['direc'],ENT_QUOTES,'UTF-8'));
    $desc = strtoupper(htmlspecialchars($_POST['desc'],ENT_QUOTES,'UTF-8'));

    $consulta = $MSU->Registrar_Sucursal($sucur,$tele1,$tele2,$direc,$desc);
    echo $consulta;



?>