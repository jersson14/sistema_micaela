<?php
// Evitar cualquier salida antes de los headers
ob_clean();

$serie = isset($_GET['serie']) ? basename($_GET['serie']) : '';
$correlativo = isset($_GET['correlativo']) ? basename($_GET['correlativo']) : '';
$correlativo = str_pad($correlativo, 8, '0', STR_PAD_LEFT); // ← agregar esta línea

if (empty($serie) || empty($correlativo)) {
    die("Parámetros inválidos");
}

$archivo = __DIR__ . "/xml/" . $serie . "-" . $correlativo . ".xml";

if (!file_exists($archivo)) {
    die("Archivo no encontrado: " . $archivo);
}

// Limpiar cualquier buffer de salida
while (ob_get_level()) {
    ob_end_clean();
}

header('Content-Description: File Transfer');
header('Content-Type: application/xml');
header('Content-Disposition: attachment; filename="' . $serie . '-' . $correlativo . '.xml"');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($archivo));
readfile($archivo);