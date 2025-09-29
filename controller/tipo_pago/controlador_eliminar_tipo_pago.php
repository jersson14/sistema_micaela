<?php
    require '../../model/model_tipo_pago.php';
    $MTP = new Modelo_TipoPago();//Instaciamos
    $id = strtoupper(htmlspecialchars($_POST['id'],ENT_QUOTES,'UTF-8'));

    $consulta = $MTP->Eliminar_Tipo_pago($id);
    echo $consulta;



?>