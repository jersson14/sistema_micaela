<?php
    require '../../model/model_rutas.php';
    $MRU = new Modelo_Rutas();//Instaciamos
    $id = strtoupper(htmlspecialchars($_POST['id'],ENT_QUOTES,'UTF-8'));

    $consulta = $MRU->Eliminar_Ruta($id);
    echo $consulta;



?>