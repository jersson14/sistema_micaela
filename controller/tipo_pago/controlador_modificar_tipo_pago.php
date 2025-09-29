<?php
    require '../../model/model_tipo_pago.php';
    $MTP = new Modelo_TipoPago();//Instaciamos
    $id = strtoupper(htmlspecialchars($_POST['id'],ENT_QUOTES,'UTF-8'));
    $tipopa = strtoupper(htmlspecialchars($_POST['tipopa'],ENT_QUOTES,'UTF-8'));
    $desc = strtoupper(htmlspecialchars($_POST['desc'],ENT_QUOTES,'UTF-8'));
    $esta = strtoupper(htmlspecialchars($_POST['esta'],ENT_QUOTES,'UTF-8'));

    $consulta = $MTP->Modificar_TipoPago($id,$tipopa,$desc,$esta);
    echo $consulta;



?>