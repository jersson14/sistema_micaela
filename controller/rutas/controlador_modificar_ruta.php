<?php
    require '../../model/model_rutas.php';
    $MRU = new Modelo_Rutas();//Instaciamos
    $id = strtoupper(htmlspecialchars($_POST['id'],ENT_QUOTES,'UTF-8'));
    $nom = strtoupper(htmlspecialchars($_POST['nom'],ENT_QUOTES,'UTF-8'));
    $desc = strtoupper(htmlspecialchars($_POST['desc'],ENT_QUOTES,'UTF-8'));
    $estado = strtoupper(htmlspecialchars($_POST['estado'],ENT_QUOTES,'UTF-8'));


    $consulta = $MRU->Modificar_Ruta($id,$nom,$desc,$estado);
    echo $consulta;



?>