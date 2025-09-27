<?php
    require '../../model/model_ingresos.php';
    $MIN = new Modelo_Ingresos();//Instaciamos
    $indica = htmlspecialchars($_POST['indica'],ENT_QUOTES,'UTF-8');
    $fechainicio = htmlspecialchars($_POST['fechainicio'],ENT_QUOTES,'UTF-8');
    $fechafin = htmlspecialchars($_POST['fechafin'],ENT_QUOTES,'UTF-8');

    $consulta = $MIN->Listar_ingresos_fechas($indica,$fechainicio,$fechafin);
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
