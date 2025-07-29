<?php
    require '../../model/model_paciente.php';
    $MPA = new Modelo_Paciente();//Instaciamos
    $id = strtoupper(htmlspecialchars($_POST['id'],ENT_QUOTES,'UTF-8'));

    $consulta = $MPA->Eliminar_Pacientes($id);
    echo $consulta;



?>