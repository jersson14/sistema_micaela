<?php     
require '../../model/model_salidas_diarias.php';      

$MSD = new Modelo_Salidas_Diarias(); // Instanciamos  

// Recibir y limpiar los datos
$idSalida   = isset($_POST['idSalida']) ? (int)trim($_POST['idSalida']) : 0;
$pasajeros  = isset($_POST['pasajeros']) ? $_POST['pasajeros'] : ''; // No usar htmlspecialchars en JSON

// Decodificar JSON
$array_pasajeros = json_decode($pasajeros, true);

// Validar JSON
if (json_last_error() !== JSON_ERROR_NONE) {
    echo "Error al decodificar JSON: " . json_last_error_msg();
    exit;
}

// Validar datos obligatorios
if ($idSalida <= 0 || empty($array_pasajeros) || !is_array($array_pasajeros)) {
    echo "Error: Datos incompletos. ID: '$idSalida', Pasajeros: " . (is_array($array_pasajeros) ? count($array_pasajeros) : 'no es array');
    exit; 
}

// Procesar cada pasajero
foreach ($array_pasajeros as $index => $pasajero) {
    if (!isset($pasajero['tipo_documento'], $pasajero['documento'], $pasajero['nombres']) 
    || $pasajero['tipo_documento'] === '' 
    || $pasajero['documento'] === '' 
    || $pasajero['nombres'] === '') {
    echo "Error: Datos incompletos en el pasajero " . ($index + 1);
    exit;
}
    
    $tipo_documento = htmlspecialchars($pasajero['tipo_documento'], ENT_QUOTES, 'UTF-8');
    $documento      = htmlspecialchars($pasajero['documento'], ENT_QUOTES, 'UTF-8');
    $nombres        = htmlspecialchars($pasajero['nombres'], ENT_QUOTES, 'UTF-8');
    
    // Edad: puede ser null
    $edad = (isset($pasajero['edad']) && $pasajero['edad'] !== 'N/A' && is_numeric($pasajero['edad'])) 
              ? (int)$pasajero['edad'] 
              : null;
    
    $celular = isset($pasajero['celular']) ? htmlspecialchars($pasajero['celular'], ENT_QUOTES, 'UTF-8') : null;
     
    $consulta = $MSD->Modificar_detalle_pasajeros($idSalida, $tipo_documento, $documento, $nombres, $edad, $celular);
    
    if (!$consulta) {
        echo "Error al registrar el pasajero: " . $documento;
        exit;     
    } 
}  

echo 1; // Confirmación de éxito 
?>
