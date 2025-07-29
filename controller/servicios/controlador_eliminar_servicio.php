<?php
    require '../../model/model_servicios.php';
    $MSE = new Modelo_Servicios();//Instaciamos
    $id = strtoupper(htmlspecialchars($_POST['id'],ENT_QUOTES,'UTF-8'));

    $consulta = $MSE->Eliminar_Servicios($id);
    echo $consulta;



?>