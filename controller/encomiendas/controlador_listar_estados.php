<?php
    require '../../model/model_encomiendas.php';
    $MEN = new Modelo_Encomiendas();//Instaciamos
    $id = htmlspecialchars($_POST['id'], ENT_QUOTES, 'UTF-8');

    $consulta = $MEN->Listar_Historial_Estado($id);

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
