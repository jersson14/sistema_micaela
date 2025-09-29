<?php
    require '../../model/model_tipo_pago.php';
    $MTP = new Modelo_TipoPago();//Instaciamos
    $consulta = $MTP->Listar_Sucursal();
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
