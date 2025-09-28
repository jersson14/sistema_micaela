<?php
    require '../../model/model_encomiendas.php';
    $MEN = new Modelo_Encomiendas();//Instaciamos
    $usu = htmlspecialchars($_POST['usu'],ENT_QUOTES,'UTF-8');
    $des = htmlspecialchars($_POST['des'],ENT_QUOTES,'UTF-8');

    $consulta = $MEN->Listar_todas_encomienda_por_dia_env($usu,$des);
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
