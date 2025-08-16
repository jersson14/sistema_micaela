<?php
    require '../../model/model_choferes.php';
    $MCH = new Modelo_Choferes();
    $id = strtoupper(htmlspecialchars($_POST['id'],ENT_QUOTES,'UTF-8'));

    $consulta = $MCH->Eliminar_Chofer($id);
    echo $consulta;



?>