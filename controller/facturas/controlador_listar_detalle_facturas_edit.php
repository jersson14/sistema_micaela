<?php
require '../../model/model_facturas.php';
    $MFA = new Modelo_Facturas(); // Instanciamos el modelo
    $id = htmlspecialchars($_POST['id'], ENT_QUOTES, 'UTF-8');
    $consulta = $MFA->Listar_detalle_factura_edit($id);

    if ($consulta) {
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
