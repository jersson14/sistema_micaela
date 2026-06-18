<?php
    require '../../model/model_servicios.php';
    $MSE = new Modelo_Servicios();//Instaciamos
    $id = htmlspecialchars($_POST['id'],ENT_QUOTES,'UTF-8');
    $consulta = $MSE->Cargar_Traermonto($id);
    echo json_encode($consulta);
 
?>
