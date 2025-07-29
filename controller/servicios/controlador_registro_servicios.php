<?php
    require '../../model/model_servicios.php';
    $MSE = new Modelo_Servicios();//Instaciamos
    $serv = strtoupper(htmlspecialchars($_POST['serv'],ENT_QUOTES,'UTF-8'));
    $cost = strtoupper(htmlspecialchars($_POST['cost'],ENT_QUOTES,'UTF-8'));
    $desc = strtoupper(htmlspecialchars($_POST['desc'],ENT_QUOTES,'UTF-8'));
    $idusu = strtoupper(htmlspecialchars($_POST['idusu'],ENT_QUOTES,'UTF-8'));

    $consulta = $MSE->Registrar_Servicios($serv,$cost,$desc,$idusu);
    echo $consulta;



?>