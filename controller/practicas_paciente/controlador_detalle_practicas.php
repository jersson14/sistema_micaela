<?php
require '../../model/model_practicas_paciente.php';

$MPP = new Modelo_Practicas_Paciente(); // Instancia del modelo

// Recibir y limpiar los datos
$id = htmlspecialchars($_POST['id'], ENT_QUOTES, 'UTF-8');
$practicas = htmlspecialchars($_POST['practicas'], ENT_QUOTES, 'UTF-8');
$precio = htmlspecialchars($_POST['precio'], ENT_QUOTES, 'UTF-8');
$cantidad = htmlspecialchars($_POST['cantidad'], ENT_QUOTES, 'UTF-8');
$subtotal = htmlspecialchars($_POST['subtotal'], ENT_QUOTES, 'UTF-8');

// Convertimos los datos en arrays
$array_practicas = explode(",", $practicas);
$array_precio = explode(",", $precio);
$array_cantidad = explode(",", $cantidad);
$array_subtotal = explode(",", $subtotal);

// Validar que todos los arrays tengan la misma cantidad de elementos
if (count($array_practicas) !== count($array_precio) || count($array_practicas) !== count($array_cantidad) || count($array_practicas) !== count($array_subtotal)) {
    echo "Error: La cantidad de datos no coincide.";
    exit;
}

// Insertar cada práctica con su respectivo precio, cantidad y subtotal
for ($i = 0; $i < count($array_practicas); $i++) {
    $consulta = $MPP->Registrar_detalle_practicas($id, $array_practicas[$i], $array_precio[$i], $array_cantidad[$i], $array_subtotal[$i]);
    if (!$consulta) {
        echo "Error al registrar la práctica: " . $array_practicas[$i];
        exit;
    }
}

echo 1; // Confirmación de éxito
?>
