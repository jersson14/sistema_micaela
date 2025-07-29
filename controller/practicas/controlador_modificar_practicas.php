<?php
    require '../../model/model_practicas.php';
    $MPR = new Modelo_Practicas();
    $id = strtoupper(htmlspecialchars($_POST['id'],ENT_QUOTES,'UTF-8'));
    $code = strtoupper(htmlspecialchars($_POST['code'],ENT_QUOTES,'UTF-8'));
    $pract = strtoupper(htmlspecialchars($_POST['pract'],ENT_QUOTES,'UTF-8'));
    $valor = strtoupper(htmlspecialchars($_POST['valor'],ENT_QUOTES,'UTF-8'));
    $obra = strtoupper(htmlspecialchars($_POST['obra'],ENT_QUOTES,'UTF-8'));
    $status = strtoupper(htmlspecialchars($_POST['status'],ENT_QUOTES,'UTF-8'));
    $idusu = strtoupper(htmlspecialchars($_POST['idusu'],ENT_QUOTES,'UTF-8'));

    $consulta = $MPR->Modificar_Practicas($id,$code,$pract,$valor,$obra,$status,$idusu);
    echo $consulta;



?>