<?php
require '../../model/model_facturas.php';
$MFA = new Modelo_Facturas();//Instaciamos
    $id2 = htmlspecialchars($_POST['id2'],ENT_QUOTES,'UTF-8');
    $consulta = $MFA->Cargar_Practicaspaciente_factura($id2);
    echo json_encode($consulta);
 
?>
