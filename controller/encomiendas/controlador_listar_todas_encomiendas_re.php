<?php
    require_once '../../utilitario/requiere_sesion.php';
    require '../../model/model_encomiendas.php';
    $MEN = new Modelo_Encomiendas();//Instaciamos
    $des = htmlspecialchars($_POST['des'],ENT_QUOTES,'UTF-8');

    $consulta = $MEN->Listar_todas_encomienda_asis($des);
    if($consulta){
        echo json_encode($consulta);
    }else{
        echo '{"data": []}'; // sin filas: la tabla muestra "no hay registros"
    }
?>
