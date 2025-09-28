<?php
    require '../../model/model_encomiendas.php';
    $MEN = new Modelo_Encomiendas();//Instaciamos
    $usu = htmlspecialchars($_POST['usu'],ENT_QUOTES,'UTF-8');
    $des = htmlspecialchars($_POST['des'],ENT_QUOTES,'UTF-8');
    $fedes = htmlspecialchars($_POST['fedes'],ENT_QUOTES,'UTF-8');
    $fehas = htmlspecialchars($_POST['fehas'],ENT_QUOTES,'UTF-8');
    $esta = htmlspecialchars($_POST['esta'],ENT_QUOTES,'UTF-8');

    $consulta = $MEN->Listar_todas_encomienda_por_fechas_estado_env($usu,$des,$fedes,$fehas,$esta);
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
