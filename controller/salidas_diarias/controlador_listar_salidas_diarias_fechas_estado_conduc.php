
<?php
    require '../../model/model_salidas_diarias.php';
    $MSD = new Modelo_Salidas_Diarias();//Instaciamos
    $fedes = htmlspecialchars($_POST['fedes'],ENT_QUOTES,'UTF-8');
    $fehas = htmlspecialchars($_POST['fehas'],ENT_QUOTES,'UTF-8');
    $estado = htmlspecialchars($_POST['estado'],ENT_QUOTES,'UTF-8');
    $usu = htmlspecialchars($_POST['usu'],ENT_QUOTES,'UTF-8');

    $consulta = $MSD->Listar_salida_fecha_estado_condu($fedes,$fehas,$estado,$usu);
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