<?php
    require '../../model/model_tipo_pago.php';
    $MTP = new Modelo_TipoPago();//Instaciamos

    $tipopa = strtoupper(htmlspecialchars($_POST['tipopa'],ENT_QUOTES,'UTF-8'));
    $desc = strtoupper(htmlspecialchars($_POST['desc'],ENT_QUOTES,'UTF-8'));

    $consulta = $MTP->Registrar_Tipopago($tipopa,$desc);
    echo $consulta;



?>