<?php
    require '../../model/model_salidas_diarias.php';
    $MSD = new Modelo_Salidas_Diarias();//Instaciamos
    $id = strtoupper(htmlspecialchars($_POST['id'],ENT_QUOTES,'UTF-8'));
    $idusu = strtoupper(htmlspecialchars($_POST['idusu'],ENT_QUOTES,'UTF-8'));
    $observacion = strtoupper(htmlspecialchars($_POST['observacion'],ENT_QUOTES,'UTF-8'));


    $consulta = $MSD->Modificar_Salida_Estatus_Incompleto($id,$idusu,$observacion);
    echo $consulta;



?>