<?php
    require_once '../../utilitario/requiere_sesion.php';
    require '../../model/model_encomiendas.php';
    $MEN = new Modelo_Encomiendas();//Instaciamos
    $ori = htmlspecialchars($_POST['ori'],ENT_QUOTES,'UTF-8');
    $des = htmlspecialchars($_POST['des'],ENT_QUOTES,'UTF-8');
    $esta = htmlspecialchars($_POST['esta'],ENT_QUOTES,'UTF-8');

    $consulta = $MEN->Listar_encomienda_ruta_estado($ori,$des,$esta);
    if($consulta){
        echo json_encode($consulta);
    }else{
        echo '{"data": []}'; // sin filas: la tabla muestra "no hay registros"
    }
?>
