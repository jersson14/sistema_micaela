<?php
    require '../../model/model_practicas_paciente.php';
    $MPP = new Modelo_Practicas_Paciente();//Instaciamos
    $obra = htmlspecialchars($_POST['obra'],ENT_QUOTES,'UTF-8');

    $consulta = $MPP->Listar_practicas_paciente_filtro($obra);
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
