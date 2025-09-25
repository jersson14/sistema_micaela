<?php     
require '../../model/model_salidas_diarias.php';      

$MSD = new Modelo_Salidas_Diarias();//Instanciamos  

// Recibir y limpiar los datos
$idSalida = isset($_POST['idSalida']) ? htmlspecialchars($_POST['idSalida'], ENT_QUOTES, 'UTF-8') : '';
$pasajeros = isset($_POST['pasajeros']) ? $_POST['pasajeros'] : ''; // No aplicar htmlspecialchars al JSON

// Debug: mostrar los datos recibidos (puedes comentar esto después)
// echo "ID Salida: " . $idSalida . "\n";
// echo "Pasajeros JSON: " . $pasajeros . "\n";

// Convertimos los datos en un array
$array_pasajeros = json_decode($pasajeros, true);

// Debug: verificar si el JSON se decodificó correctamente
if (json_last_error() !== JSON_ERROR_NONE) {
    echo "Error al decodificar JSON: " . json_last_error_msg();
    exit;
}

// Validar que los datos sean correctos
if (empty($idSalida) || empty($array_pasajeros) || !is_array($array_pasajeros)) {
    echo "Error: Datos incompletos. ID: '$idSalida', Pasajeros: " . (is_array($array_pasajeros) ? count($array_pasajeros) : 'no es array');
    exit; 
}

// Insertar cada pasajero con sus respectivos datos
foreach ($array_pasajeros as $index => $pasajero) {
    // Validar que cada pasajero tenga los campos requeridos
    if (!isset($pasajero['tipo_documento']) || !isset($pasajero['documento']) || !isset($pasajero['nombres'])) {
        echo "Error: Datos incompletos en el pasajero " . ($index + 1);
        exit;
    }
    
    $tipo_documento = htmlspecialchars($pasajero['tipo_documento'], ENT_QUOTES, 'UTF-8');
    $documento = htmlspecialchars($pasajero['documento'], ENT_QUOTES, 'UTF-8');
    $nombres = htmlspecialchars($pasajero['nombres'], ENT_QUOTES, 'UTF-8');
    
    // Manejar la edad (puede ser "N/A")
    $edad = isset($pasajero['edad']) && $pasajero['edad'] !== 'N/A' && is_numeric($pasajero['edad']) 
            ? intval($pasajero['edad']) 
            : null; // o 0 si prefieres
    
    $celular = isset($pasajero['celular']) ? htmlspecialchars($pasajero['celular'], ENT_QUOTES, 'UTF-8') : '';
     
    $consulta = $MSD->Registrar_detalle_pasajeros($idSalida, $tipo_documento, $documento, $nombres, $edad, $celular);
    
    if (!$consulta) {
        echo "Error al registrar el pasajero: " . $documento;
        exit;     
    } 
}  

echo 1; // Confirmación de éxito 
?>