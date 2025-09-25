<?php
    require '../../model/model_salidas_diarias.php';
    $MSD = new Modelo_Salidas_Diarias();//Instaciamos
    // DATOS DE LA SALIDA DIARIA
    $conductor = strtoupper(htmlspecialchars($_POST['conductor'],ENT_QUOTES,'UTF-8'));
    $monto = strtoupper(htmlspecialchars($_POST['monto'],ENT_QUOTES,'UTF-8'));
    $fechaHora = strtoupper(htmlspecialchars($_POST['fechaHora'],ENT_QUOTES,'UTF-8'));
    $origen = strtoupper(htmlspecialchars($_POST['origen'],ENT_QUOTES,'UTF-8'));
    $destino = strtoupper(htmlspecialchars($_POST['destino'],ENT_QUOTES,'UTF-8'));
    $observacion = strtoupper(htmlspecialchars($_POST['observacion'],ENT_QUOTES,'UTF-8'));
    $idUsuario = strtoupper(htmlspecialchars($_POST['idUsuario'],ENT_QUOTES,'UTF-8'));
    $totalPasajeros = strtoupper(htmlspecialchars($_POST['totalPasajeros'],ENT_QUOTES,'UTF-8'));
    $totalEncomiendas = strtoupper(htmlspecialchars($_POST['totalEncomiendas'],ENT_QUOTES,'UTF-8'));
    
    $consulta = $MSD->Registrar_Salida_Diaria($conductor,$monto,$fechaHora,$origen,$destino,$observacion,$idUsuario,$totalPasajeros,$totalEncomiendas);
    echo $consulta;



?>