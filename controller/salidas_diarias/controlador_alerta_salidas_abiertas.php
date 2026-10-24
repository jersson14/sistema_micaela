<?php
    require_once '../../utilitario/requiere_sesion.php';
    require '../../model/model_salidas_diarias.php';
    header('Content-Type: application/json; charset=utf-8');

    // Solo administrador y asistente cierran salidas
    if (!in_array((string)($_SESSION['S_ROL'] ?? ''), ['1', '2'], true)) {
        echo json_encode(['abiertas' => 0, 'atrasadas' => 0]);
        exit;
    }

    $MSD = new Modelo_Salidas_Diarias();//Instaciamos
    echo json_encode($MSD->Contar_Salidas_Abiertas());
?>
