<?php
require '../../model/model_practicas.php';
$MPR = new Modelo_Practicas();
$id = htmlspecialchars($_POST['id'], ENT_QUOTES, 'UTF-8');

$consulta = $MPR->Listar_Historial_practicas($id);

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
