<?php
    require '../../model/model_facturas.php';
    $MFA = new Modelo_Facturas();//Instaciamos
    $fechaini = htmlspecialchars($_POST['fechaini'],ENT_QUOTES,'UTF-8');
    $fechafin = htmlspecialchars($_POST['fechafin'],ENT_QUOTES,'UTF-8');
    $usu = htmlspecialchars($_POST['usu'],ENT_QUOTES,'UTF-8');

    $consulta = $MFA->Listar_facturas_fecha_usu($fechaini,$fechafin,$usu);
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
