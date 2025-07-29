<?php
    require '../../model/model_sucursal.php';
    $MSU = new Modelo_Sucursal();//Instaciamos
    $id = strtoupper(htmlspecialchars($_POST['id'],ENT_QUOTES,'UTF-8'));

    $consulta = $MSU->Eliminar_Sucursal($id);
    echo $consulta;



?>