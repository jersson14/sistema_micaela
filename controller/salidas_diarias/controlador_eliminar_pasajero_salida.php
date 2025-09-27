<?php
    require '../../model/model_salidas_diarias.php';
    $MSD = new Modelo_Salidas_Diarias();//Instaciamos
    $id_pasajero = strtoupper(htmlspecialchars($_POST['id_pasajero'],ENT_QUOTES,'UTF-8'));

    $consulta = $MSD->Eliminar_Cliente_Salida_diaria($id_pasajero);
    echo $consulta;



?>