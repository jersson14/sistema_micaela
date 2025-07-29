<?php
    require '../../model/model_servicios.php';
    $MSE = new Modelo_Servicios();//Instaciamos
    $id = strtoupper(htmlspecialchars($_POST['id'],ENT_QUOTES,'UTF-8'));
    $serv = strtoupper(htmlspecialchars($_POST['serv'],ENT_QUOTES,'UTF-8'));
    $cost = strtoupper(htmlspecialchars($_POST['cost'],ENT_QUOTES,'UTF-8'));
    $desc = strtoupper(htmlspecialchars($_POST['desc'],ENT_QUOTES,'UTF-8'));
    $estado = strtoupper(htmlspecialchars($_POST['estado'],ENT_QUOTES,'UTF-8'));
    $idusu = strtoupper(htmlspecialchars($_POST['idusu'],ENT_QUOTES,'UTF-8'));


    $consulta = $MSE->Modificar_Servicios($id,$serv,$cost,$desc,$estado,$idusu);
    echo $consulta;



?>