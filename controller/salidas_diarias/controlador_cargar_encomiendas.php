<?php
    require '../../model/model_salidas_diarias.php';
    $MSD = new Modelo_Salidas_Diarias();//Instaciamos
    $id_conductor = htmlspecialchars($_POST['id_conductor'], ENT_QUOTES, 'UTF-8');
    $id_origen = htmlspecialchars($_POST['id_origen'], ENT_QUOTES, 'UTF-8');
    $id_destino = htmlspecialchars($_POST['id_destino'], ENT_QUOTES, 'UTF-8');

    $consulta = $MSD->Listar_Encomiendas($id_conductor, $id_origen, $id_destino);
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
