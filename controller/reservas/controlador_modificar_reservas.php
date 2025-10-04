<?php
    require '../../model/model_reservas.php';
    $MRE = new Modelo_Reservas();//Instaciamos
    $idreserva = strtoupper(htmlspecialchars($_POST['idreserva'],ENT_QUOTES,'UTF-8'));
    $tipodocemi = strtoupper(htmlspecialchars($_POST['tipodocemi'],ENT_QUOTES,'UTF-8'));
    $documento = strtoupper(htmlspecialchars($_POST['documento'],ENT_QUOTES,'UTF-8'));
    $nomemi = strtoupper(htmlspecialchars($_POST['nomemi'],ENT_QUOTES,'UTF-8'));
    $celemi = strtoupper(htmlspecialchars($_POST['celemi'],ENT_QUOTES,'UTF-8'));
    $fechare = strtoupper(htmlspecialchars($_POST['fechare'],ENT_QUOTES,'UTF-8'));
    $fechavia = strtoupper(htmlspecialchars($_POST['fechavia'],ENT_QUOTES,'UTF-8'));
    $ori = strtoupper(htmlspecialchars($_POST['ori'],ENT_QUOTES,'UTF-8'));
    $des = strtoupper(htmlspecialchars($_POST['des'],ENT_QUOTES,'UTF-8'));
    $monto = strtoupper(htmlspecialchars($_POST['monto'],ENT_QUOTES,'UTF-8'));
    $obser = strtoupper(htmlspecialchars($_POST['obser'],ENT_QUOTES,'UTF-8'));
    $idusu = strtoupper(htmlspecialchars($_POST['idusu'],ENT_QUOTES,'UTF-8'));


    $consulta = $MRE->Modificar_Reserva($idreserva,$tipodocemi,$documento,$nomemi,$celemi,$fechare,$fechavia,$ori,$des,$monto,$obser,$idusu);
    echo $consulta;



?>