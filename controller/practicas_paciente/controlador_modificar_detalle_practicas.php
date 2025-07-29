<?php
require '../../model/model_practicas_paciente.php';
$MPP = new Modelo_Practicas_Paciente(); // Instanciamos el modelo

// Verificar si se reciben los datos necesarios en la solicitud
if (!isset($_POST['total'], $_POST['idusu'], $_POST['idprac'])) {
    echo json_encode(['status' => 0, 'message' => 'Faltan datos en la solicitud']);
    exit;
}

// Obtener total e id de usuario
$total = floatval($_POST['total']);
$idusu = intval($_POST['idusu']);
$idprac = intval($_POST['idprac']);

$detalle_practicas = isset($_POST['componentes']) ? json_decode($_POST['componentes'], true) : [];

$exito = true; // Bandera de éxito global
$registros_nuevos = false; // Bandera para detectar si algunos registros son nuevos

// **Actualizar siempre el total, incluso si `componentes` está vacío**
$resultado_factura = $MPP->Modificar_Total_Usuario($total, $idusu,$idprac);

if (!empty($detalle_practicas)) {
    foreach ($detalle_practicas as $detalle) {
        // Validar que los datos existan y no estén vacíos
        if (
            empty($detalle['id_practica_general']) ||
            empty($detalle['id_practica']) ||
            !isset($detalle['precio']) ||
            !isset($detalle['cantidad']) ||
            !isset($detalle['subtotal'])
        ) {
            $exito = false;
            break;
        }

        // Llamar al método para modificar el registro de prácticas
        $resultado = $MPP->Modificar_Detalle_practicas(
            $detalle['id_practica_general'],
            $detalle['id_practica'],
            floatval($detalle['precio']),
            intval($detalle['cantidad']),
            floatval($detalle['subtotal']),
            $total,
            $idusu
        );

        if ($resultado == 1) {
            $registros_nuevos = true; // Se detectó al menos un cambio
        } elseif ($resultado !== 0) {
            $exito = false; // Si ocurre un error, marcamos como fallo
            break;
        }
    }
}
if ($exito) {
    if ($registros_nuevos || $resultado_factura) {
        echo json_encode(['status' => 1, 'message' => 'Datos actualizados con éxito']);
    } else {
        echo json_encode(['status' => 2, 'message' => 'No hubo cambios en los datos']);
    }
} else {
    echo json_encode(['status' => 0, 'message' => 'Error en la modificación']);
}
