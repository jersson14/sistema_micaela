<?php
require '../../model/model_facturas.php';
$MFA = new Modelo_Facturas();//Instaciamos
    $id = htmlspecialchars($_POST['id'],ENT_QUOTES,'UTF-8');
    $consulta = $MFA->Cargar_Traermonto($id);
    echo json_encode($consulta);
 
?>
