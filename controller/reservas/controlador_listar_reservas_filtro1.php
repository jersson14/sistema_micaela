<?php
    require '../../model/model_reservas.php';
    $MRE = new Modelo_Reservas();//Instaciamos
    $ori = htmlspecialchars($_POST['ori'],ENT_QUOTES,'UTF-8');
    $des = htmlspecialchars($_POST['des'],ENT_QUOTES,'UTF-8');
    $esta = htmlspecialchars($_POST['esta'],ENT_QUOTES,'UTF-8');

    $consulta = $MRE->Listar_reservas_ruta_estado($ori,$des,$esta);
    if($consulta){
        echo json_encode($consulta);
    }else{
        echo '{
            "sEcho": 1,
            "iTotalRecords": "0",
            "iTotalDisplayRecords": "0",
            "aaData": []
        }';
    }
?>
