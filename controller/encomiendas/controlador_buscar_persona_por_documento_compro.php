<?php
require '../../model/model_encomiendas.php';
$MEN = new Modelo_Encomiendas(); // Instanciamos

$numero_documento = strtoupper(htmlspecialchars($_POST['numero_documento'], ENT_QUOTES, 'UTF-8'));

$consulta = $MEN->Buscar_persona_por_documento_compro($numero_documento); // ✅ corregido

if (isset($consulta["data"]) && count($consulta["data"]) > 0) {
    echo json_encode(["data" => $consulta["data"]]);
} else {
    echo json_encode(["data" => []]);
}
?>
