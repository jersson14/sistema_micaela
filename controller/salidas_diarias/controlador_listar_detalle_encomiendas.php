<?php
    require_once '../../utilitario/requiere_sesion.php';
    require '../../model/model_salidas_diarias.php';
    $MSD = new Modelo_Salidas_Diarias();//Instaciamos
    $id = htmlspecialchars($_POST['id'], ENT_QUOTES, 'UTF-8');

    $consulta = $MSD->Listar_detalle_salida_encomiendas($id);
    if($consulta){
        echo json_encode($consulta);
    }else{
        echo '{"data": []}'; // sin filas: la tabla muestra "no hay registros"
    }
?>
