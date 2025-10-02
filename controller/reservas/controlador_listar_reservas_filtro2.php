<?php
    require '../../model/model_reservas.php';
    $MRE = new Modelo_Reservas();//Instaciamos
    $fedes = htmlspecialchars($_POST['fedes'],ENT_QUOTES,'UTF-8');
    $fehas = htmlspecialchars($_POST['fehas'],ENT_QUOTES,'UTF-8');
    $usu = htmlspecialchars($_POST['usu'],ENT_QUOTES,'UTF-8');

    $consulta = $MRE->Listar_reservas_fecha_usuario($fedes,$fehas,$usu);
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
