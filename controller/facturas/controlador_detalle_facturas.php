<?php
require '../../model/model_facturas.php';

$MFA = new Modelo_Facturas(); // Instanciamos el modelo

// Recibir y limpiar los datos
$id = htmlspecialchars($_POST['id'], ENT_QUOTES, 'UTF-8');
$practicas_paciente = htmlspecialchars($_POST['practicas_paciente'], ENT_QUOTES, 'UTF-8');
$subtotal = htmlspecialchars($_POST['subtotal'], ENT_QUOTES, 'UTF-8');

// Convertimos los datos en arrays
$array_practicas_paciente = explode(",", $practicas_paciente);
$array_subtotal = explode(",", $subtotal);

// Validar que ambos arrays tengan la misma cantidad de elementos
if (count($array_practicas_paciente) !== count($array_subtotal)) {
    echo "Error: La cantidad de prácticas y subtotales no coincide.";
    exit;
}

// Insertar cada práctica con su respectivo subtotal
for ($i = 0; $i < count($array_practicas_paciente); $i++) { 
    $consulta = $MFA->Registrar_detalle_facturas($id, $array_practicas_paciente[$i], $array_subtotal[$i]);
    if (!$consulta) {
        echo "Error al registrar la práctica: " . $array_practicas_paciente[$i];
        exit;
    }
}

echo 1; // Confirmación de éxito
?>
