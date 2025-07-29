<?php
    require '../../model/model_sucursal.php';
    $MSU = new Modelo_Sucursal();//Instaciamos
    $id = strtoupper(htmlspecialchars($_POST['id'],ENT_QUOTES,'UTF-8'));
    $sucu = strtoupper(htmlspecialchars($_POST['sucu'],ENT_QUOTES,'UTF-8'));
    $tele1 = strtoupper(htmlspecialchars($_POST['tele1'],ENT_QUOTES,'UTF-8'));
    $tele2 = strtoupper(htmlspecialchars($_POST['tele2'],ENT_QUOTES,'UTF-8'));
    $direc = strtoupper(htmlspecialchars($_POST['direc'],ENT_QUOTES,'UTF-8'));
    $desc = strtoupper(htmlspecialchars($_POST['desc'],ENT_QUOTES,'UTF-8'));
    $esta = strtoupper(htmlspecialchars($_POST['esta'],ENT_QUOTES,'UTF-8'));

    $consulta = $MSU->Modificar_Sucursal($id,$sucu,$tele1,$tele2,$direc,$desc,$esta);
    echo $consulta;



?>