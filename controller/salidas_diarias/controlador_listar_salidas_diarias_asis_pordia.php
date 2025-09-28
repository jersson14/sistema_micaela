<?php
    require '../../model/model_salidas_diarias.php';
    $MSD = new Modelo_Salidas_Diarias();//Instaciamos
    $ori = htmlspecialchars($_POST['ori'], ENT_QUOTES, 'UTF-8');
    $usu = htmlspecialchars($_POST['usu'], ENT_QUOTES, 'UTF-8');

    $consulta = $MSD->Listar_Historial_Estado_Salida_Asis_pordia($ori, $usu);

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
