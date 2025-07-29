<?php
require '../../model/model_practicas_paciente.php';
$MPP = new Modelo_Practicas_Paciente();

// Verificar si id2 existe en $_POST
if (!isset($_POST['id2'])) {
    die(json_encode(['error' => 'No se recibió el parámetro id2']));
}

$id2 = htmlspecialchars($_POST['id2'], ENT_QUOTES, 'UTF-8');

// Log para debug
error_log("ID2 recibido: " . $id2);

$consulta = $MPP->Cargar_Practicaspaciente2($id2);
echo json_encode($consulta);
 
?>
