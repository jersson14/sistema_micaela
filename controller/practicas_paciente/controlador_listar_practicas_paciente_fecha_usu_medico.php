<?php
    require '../../model/model_practicas_paciente.php';
    $MPP = new Modelo_Practicas_Paciente();//Instaciamos
    $fechaini = htmlspecialchars($_POST['fechaini'],ENT_QUOTES,'UTF-8');
    $fechafin = htmlspecialchars($_POST['fechafin'],ENT_QUOTES,'UTF-8');
    $idusu = htmlspecialchars($_POST['idusu'],ENT_QUOTES,'UTF-8');

    $consulta = $MPP->Listar_practicas_paciente_fecha_usu_medico($fechaini,$fechafin,$idusu);
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
