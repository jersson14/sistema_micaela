<?php
    require '../../model/model_tipo_pago.php';
    $MTP = new Modelo_TipoPago();//Instaciamos
    $consulta = $MTP->Cargar_Select_Tipopago();
    echo json_encode($consulta);
 
?>
