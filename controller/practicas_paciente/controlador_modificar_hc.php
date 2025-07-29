<?php
    require '../../model/model_practicas_paciente.php';
    $MPP = new Modelo_Practicas_Paciente();//Instaciamos
    $id = htmlspecialchars($_POST['id'],ENT_QUOTES,'UTF-8');
    $nombrearchivo = htmlspecialchars($_POST['nombrearchivo'],ENT_QUOTES,'UTF-8');
    $archivoactual = htmlspecialchars($_POST['archivoactual'],ENT_QUOTES,'UTF-8');

    if(empty($nombrearchivo)){
        $ruta = 'controller/practicas_paciente/filepracticas/';
    }else{
        $ruta = 'controller/practicas_paciente/filepracticas/'.$nombrearchivo;
    }

    $consulta = $MPP->Modificar_archivo_HC($id,$ruta);
    echo $consulta;
    if ($consulta==1) {
        if(!empty($nombrearchivo)){
            if(move_uploaded_file($_FILES['archivoobj']['tmp_name'],"filepracticas/".$nombrearchivo));
            unlink('../../'.$archivoactual);
        }
    }
?>