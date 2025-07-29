<?php
require '../../model/model_facturas.php';
$MFA = new Modelo_Facturas();//Instaciamos
    $id_obra = htmlspecialchars($_POST['id_obra'], ENT_QUOTES, 'UTF-8');

    $consulta = $MFA->Listar_practocaticas_por_obra($id_obra);
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
