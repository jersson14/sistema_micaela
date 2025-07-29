<?php
    require '../../model/model_paciente.php';
    $MPA = new Modelo_Paciente();//Instaciamos
    $consulta = $MPA->Listar_Pacientes();
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
