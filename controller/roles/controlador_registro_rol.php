<?php
    require '../../model/model_roles.php';
    $MRO = new Modelo_Roles();//Instaciamos
    $rol = strtoupper(htmlspecialchars($_POST['rol'],ENT_QUOTES,'UTF-8'));
    $descripcion = strtoupper(htmlspecialchars($_POST['descripcion'],ENT_QUOTES,'UTF-8'));
    $estado = strtoupper(htmlspecialchars($_POST['estado'],ENT_QUOTES,'UTF-8'));
    $fecha = strtoupper(htmlspecialchars($_POST['fecha'],ENT_QUOTES,'UTF-8'));

    $consulta = $MRO->Registrar_Rol($rol,$descripcion,$estado,$fecha);
    echo $consulta;



?>