<?php
require '../../model/model_facturas.php';
$MFA = new Modelo_Facturas(); // Instanciamos el modelo
$id = strtoupper(htmlspecialchars($_POST['id'],ENT_QUOTES,'UTF-8'));

    $consulta = $MFA->Eliminar_detalle_factura_unico($id);
    echo $consulta;



?>