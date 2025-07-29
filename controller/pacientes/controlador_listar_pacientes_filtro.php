<?php
    require '../../model/model_paciente.php';
    $MPA = new Modelo_Paciente();//Instaciamos
    $obra = htmlspecialchars($_POST['obra'],ENT_QUOTES,'UTF-8');
    $fechaini = htmlspecialchars($_POST['fechaini'],ENT_QUOTES,'UTF-8');
    $fechafin = htmlspecialchars($_POST['fechafin'],ENT_QUOTES,'UTF-8');

    $consulta = $MPA->Listar_paciente_filtro($obra,$fechaini,$fechafin);
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
