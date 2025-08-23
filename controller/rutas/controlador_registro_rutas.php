<?php
    require '../../model/model_rutas.php';
    $MRU = new Modelo_Rutas();//Instaciamos
    $nom = strtoupper(htmlspecialchars($_POST['nom'],ENT_QUOTES,'UTF-8'));
    $desc = strtoupper(htmlspecialchars($_POST['desc'],ENT_QUOTES,'UTF-8'));

    $consulta = $MRU->Registrar_Ruta($nom,$desc);
    echo $consulta;



?>