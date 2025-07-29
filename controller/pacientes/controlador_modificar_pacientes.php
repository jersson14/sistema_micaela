<?php
    require '../../model/model_paciente.php';
    $MPA = new Modelo_Paciente();//Instaciamos
    $id = strtoupper(htmlspecialchars($_POST['id'],ENT_QUOTES,'UTF-8'));
    $dni = strtoupper(htmlspecialchars($_POST['dni'],ENT_QUOTES,'UTF-8'));
    $nom = strtoupper(htmlspecialchars($_POST['nom'],ENT_QUOTES,'UTF-8'));
    $epell = strtoupper(htmlspecialchars($_POST['epell'],ENT_QUOTES,'UTF-8'));
    $direc = strtoupper(htmlspecialchars($_POST['direc'],ENT_QUOTES,'UTF-8'));
    $local = strtoupper(htmlspecialchars($_POST['local'],ENT_QUOTES,'UTF-8'));
    $tele = strtoupper(htmlspecialchars($_POST['tele'],ENT_QUOTES,'UTF-8'));
    $obra = htmlspecialchars($_POST['obra'],ENT_QUOTES,'UTF-8');
    $idusu = strtoupper(htmlspecialchars($_POST['idusu'],ENT_QUOTES,'UTF-8'));


    $consulta = $MPA->Modificar_Paciente($id,$dni,$nom,$epell,$direc,$local,$tele,$obra,$idusu);
    echo $consulta;



?>