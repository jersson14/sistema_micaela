<?php
    require '../../model/model_practicas_paciente.php';
    $MPP = new Modelo_Practicas_Paciente();//Instaciamos
    $id = htmlspecialchars($_POST['id'], ENT_QUOTES, 'UTF-8');
    $consulta = $MPP->Listar_practicas_apci($id);

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
