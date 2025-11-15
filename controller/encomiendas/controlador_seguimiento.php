<?php
    require '../../model/model_encomiendas.php';
    
    if(isset($_POST['boleta_nro'])){
        $boleta_nro = $_POST['boleta_nro'];
        
        $MEN = new Modelo_Encomiendas();
        $consulta = $MEN->Buscar_Encomienda_Por_Boleta($boleta_nro);
        
        if($consulta){
            echo json_encode($consulta);
        }else{
            echo json_encode(array("error" => true, "message" => "No se encontró la encomienda"));
        }
    }else{
        echo json_encode(array("error" => true, "message" => "Parámetros inválidos"));
    }
?>
