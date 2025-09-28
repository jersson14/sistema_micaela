<?php
    require '../../model/model_encomiendas.php';
    $MEN = new Modelo_Encomiendas();//Instaciamos
    $des = htmlspecialchars($_POST['des'],ENT_QUOTES,'UTF-8');

    $consulta = $MEN->Listar_todas_encomienda_por_dia_asis($des);
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
