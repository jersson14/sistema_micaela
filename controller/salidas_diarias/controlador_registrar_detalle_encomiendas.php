<?php     
require '../../model/model_salidas_diarias.php';      

$MSD = new Modelo_Salidas_Diarias(); // Instanciar

// Recibir y limpiar los datos
$idSalida = isset($_POST['idSalida']) ? htmlspecialchars($_POST['idSalida'], ENT_QUOTES, 'UTF-8') : '';
$encomiendas = isset($_POST['encomiendas']) ? $_POST['encomiendas'] : ''; // No aplicar htmlspecialchars al JSON

// Debug: mostrar los datos recibidos
// echo "ID Salida: " . $idSalida . "\n";
// echo "Encomiendas JSON: " . $encomiendas . "\n";

// Convertir los datos en un array
$array_encomiendas = json_decode($encomiendas, true);

// Debug: verificar si el JSON se decodificó correctamente
if (json_last_error() !== JSON_ERROR_NONE) {
    echo "Error al decodificar JSON: " . json_last_error_msg();
    exit;
}

// Validar que los datos sean correctos
if (empty($idSalida) || empty($array_encomiendas) || !is_array($array_encomiendas)) {
    echo "Error: Datos incompletos. ID: '$idSalida', Encomiendas: " . (is_array($array_encomiendas) ? count($array_encomiendas) : 'no es array');
    exit; 
}

// Insertar cada encomienda con sus respectivos datos
$errores = 0;
foreach ($array_encomiendas as $index => $idEncomienda) {
    // Validar que el ID de encomienda sea válido
    if (empty($idEncomienda) || !is_numeric($idEncomienda)) {
        echo "Error: ID de encomienda inválido en posición " . ($index + 1) . ": " . $idEncomienda;
        exit;
    }
    
    $consulta = $MSD->Registrar_detalle_encomiendas($idSalida, intval($idEncomienda));
    
    if (!$consulta) {
        $errores++;
        echo "Error al registrar la encomienda: " . $idEncomienda;
        exit;     
    } 
}  

if ($errores == 0) {
    echo 1; // Confirmación de éxito
} else {
    echo "Se registraron las encomiendas con $errores errores";
}
?>