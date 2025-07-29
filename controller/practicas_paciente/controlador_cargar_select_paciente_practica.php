<?php
    require '../../model/model_practicas_paciente.php';
    $MPP = new Modelo_Practicas_Paciente();//Instaciamos
    $id = htmlspecialchars($_POST['id'],ENT_QUOTES,'UTF-8');
    $consulta = $MPP->Cargar_Practicaspaciente($id);
    echo json_encode($consulta);
 
?>
