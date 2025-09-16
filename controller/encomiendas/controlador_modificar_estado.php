<?php
    require '../../model/model_encomiendas.php';
    $MEN = new Modelo_Encomiendas();//Instaciamos
    $id = strtoupper(htmlspecialchars($_POST['id'],ENT_QUOTES,'UTF-8'));
    $nuevo_estado = strtoupper(htmlspecialchars($_POST['nuevo_estado'],ENT_QUOTES,'UTF-8'));
    $observacion = strtoupper(htmlspecialchars($_POST['observacion'],ENT_QUOTES,'UTF-8'));
    $anula = strtoupper(htmlspecialchars($_POST['anula'],ENT_QUOTES,'UTF-8'));
    $idusu = strtoupper(htmlspecialchars($_POST['idusu'],ENT_QUOTES,'UTF-8'));


    $consulta = $MEN->Modificar_Estado($id,$nuevo_estado,$observacion,$anula,$idusu);
    echo $consulta;



?>