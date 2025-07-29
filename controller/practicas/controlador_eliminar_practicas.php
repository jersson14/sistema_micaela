<?php
    require '../../model/model_practicas.php';
    $MPR = new Modelo_Practicas();//Instaciamos
    $id = strtoupper(htmlspecialchars($_POST['id'],ENT_QUOTES,'UTF-8'));

    $consulta = $MPR->Eliminar_Practicas($id);
    echo $consulta;



?>