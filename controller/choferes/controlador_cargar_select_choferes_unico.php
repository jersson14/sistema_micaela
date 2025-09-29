<?php
    require '../../model/model_choferes.php';
    $MCH = new Modelo_Choferes();//Instaciamos
    $dni = htmlspecialchars($_POST['dni'],ENT_QUOTES,'UTF-8');
    $consulta = $MCH->Cargar_Select_Choferes_Unico($dni);
        echo json_encode($consulta);
    
 
?>
