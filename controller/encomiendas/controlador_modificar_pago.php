<?php
    require '../../model/model_encomiendas.php';
    $MEN = new Modelo_Encomiendas();//Instaciamos
    $id = strtoupper(htmlspecialchars($_POST['id'],ENT_QUOTES,'UTF-8'));
    $nuevo_estado = strtoupper(htmlspecialchars($_POST['nuevo_estado'],ENT_QUOTES,'UTF-8'));
    $pago_anti = strtoupper(htmlspecialchars($_POST['pago_anti'],ENT_QUOTES,'UTF-8'));
    $pago_nuevo = strtoupper(htmlspecialchars($_POST['pago_nuevo'],ENT_QUOTES,'UTF-8'));
    $idusu = strtoupper(htmlspecialchars($_POST['idusu'],ENT_QUOTES,'UTF-8'));


    $consulta = $MEN->Modificar_Pago($id,$nuevo_estado,$pago_anti,$pago_nuevo,$idusu);
    echo $consulta;



?>