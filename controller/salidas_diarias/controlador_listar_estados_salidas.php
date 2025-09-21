<?php
    require '../../model/model_salidas_diarias.php';
    $MSD = new Modelo_Salidas_Diarias();//Instaciamos
    $id = htmlspecialchars($_POST['id'], ENT_QUOTES, 'UTF-8');

    $consulta = $MSD->Listar_Historial_Estado_Salida($id);

    if ($consulta && isset($consulta['data'])) {
        echo json_encode($consulta); // ✅ Devuelve "data" correctamente
    } else {
        echo json_encode([
            "sEcho" => 1,
            "iTotalRecords" => 0,
            "iTotalDisplayRecords" => 0,
            "data" => [] // 🔴 Corrige "aaData" por "data"
        ]);
    }
