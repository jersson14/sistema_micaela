<?php
    require '../../model/model_practicas_paciente.php';
    $MPP = new Modelo_Practicas_Paciente();//Instaciamos
    $idusu = htmlspecialchars($_POST['idusu'],ENT_QUOTES,'UTF-8');

    $consulta = $MPP->Listar_Practicas_paciente_diario_medico($idusu);

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
