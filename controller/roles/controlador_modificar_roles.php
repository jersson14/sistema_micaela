<?php
    require '../../model/model_roles.php';
    $MRO = new Modelo_Roles();//Instaciamos
    $id = strtoupper(htmlspecialchars($_POST['id'],ENT_QUOTES,'UTF-8'));
    $rol = strtoupper(htmlspecialchars($_POST['rol'],ENT_QUOTES,'UTF-8'));
    $descripcion = strtoupper(htmlspecialchars($_POST['descripcion'],ENT_QUOTES,'UTF-8'));
    $estado = strtoupper(htmlspecialchars($_POST['estado'],ENT_QUOTES,'UTF-8'));

    $consulta = $MRO->Modificar_Rol($id,$rol,$descripcion,$estado);
    echo $consulta;



?>