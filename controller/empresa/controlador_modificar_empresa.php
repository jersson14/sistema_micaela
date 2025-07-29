<?php
    require '../../model/model_empresa.php';
    $ME = new Modelo_Empresa();//Instaciamos
    $id = strtoupper(htmlspecialchars($_POST['id'],ENT_QUOTES,'UTF-8'));
    $nom = strtoupper(htmlspecialchars($_POST['nom'],ENT_QUOTES,'UTF-8'));
    $raz = strtoupper(htmlspecialchars($_POST['raz'],ENT_QUOTES,'UTF-8'));
    $nomco = strtoupper(htmlspecialchars($_POST['nomco'],ENT_QUOTES,'UTF-8'));
    $tipo_doc = strtoupper(htmlspecialchars($_POST['tipo_doc'],ENT_QUOTES,'UTF-8'));
    $nro_doc = strtoupper(htmlspecialchars($_POST['nro_doc'],ENT_QUOTES,'UTF-8'));
    $email = strtoupper(htmlspecialchars($_POST['email'],ENT_QUOTES,'UTF-8'));
    $codi = strtoupper(htmlspecialchars($_POST['codi'],ENT_QUOTES,'UTF-8'));
    $tele = strtoupper(htmlspecialchars($_POST['tele'],ENT_QUOTES,'UTF-8'));
    $dire = strtoupper(htmlspecialchars($_POST['dire'],ENT_QUOTES,'UTF-8'));
    $ubi = strtoupper(htmlspecialchars($_POST['ubi'],ENT_QUOTES,'UTF-8'));
    $urb = strtoupper(htmlspecialchars($_POST['urb'],ENT_QUOTES,'UTF-8'));
    $dis = strtoupper(htmlspecialchars($_POST['dis'],ENT_QUOTES,'UTF-8'));
    $pro = strtoupper(htmlspecialchars($_POST['pro'],ENT_QUOTES,'UTF-8'));
    $dep = strtoupper(htmlspecialchars($_POST['dep'],ENT_QUOTES,'UTF-8'));
    $codpa = strtoupper(htmlspecialchars($_POST['codpa'],ENT_QUOTES,'UTF-8'));
    $ususol = strtoupper(htmlspecialchars($_POST['ususol'],ENT_QUOTES,'UTF-8'));
    $passol = strtoupper(htmlspecialchars($_POST['passol'],ENT_QUOTES,'UTF-8'));


    $consulta = $ME->Modificar_Empresa(
        $id,
        $nom,
        $raz,
        $nomco,
        $tipo_doc,
        $nro_doc,
        $email,
        $codi,
        $tele,
        $dire,
        $ubi,
        $urb,
        $dis,
        $pro,
        $dep,
        $codpa,
        $ususol,
        $passol
    );
    echo $consulta;



?>