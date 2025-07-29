<?php
require '../../model/model_facturas.php';
$MFA = new Modelo_Facturas(); // Instanciamos el modelo

if (!isset($_POST['componentes'], $_POST['total'], $_POST['idusu'], $_POST['id_Fac'])) {
    echo json_encode(['status' => 0, 'message' => 'Faltan datos en la solicitud']);
    exit;
}

$detalle_practicas = json_decode($_POST['componentes'], true);
$total = floatval($_POST['total']);
$idusu = intval($_POST['idusu']);
$id_Fac = intval($_POST['id_Fac']);

$exito = true;
$registros_nuevos = false;

// **Actualizar siempre la factura**, aunque `componentes` esté vacío
$resultado_factura = $MFA->Modificar_Factura_Solo_Total_Usuario($total, $idusu,$id_Fac);

foreach ($detalle_practicas as $detalle) {
    if (empty($detalle['id_factura']) || empty($detalle['id_practica']) || !isset($detalle['subtotal'])) {
        $exito = false;
        break;
    }

    $resultado = $MFA->Modificar_Detalle_facturas(
        $detalle['id_factura'],
        $detalle['id_practica'],
        floatval($detalle['subtotal']),
        $total,
        $idusu
    );

    if ($resultado == 1) {
        $registros_nuevos = true;
    } elseif ($resultado === 0) {
    } else {
        $exito = false;
        break;
    }
}

if ($exito) {
    if ($registros_nuevos || $resultado_factura) {
        echo json_encode(['status' => 1, 'message' => 'Factura actualizada con éxito']);
    } else {
        echo json_encode(['status' => 2, 'message' => 'No hubo cambios en la factura']);
    }
} else {
    echo json_encode(['status' => 0, 'message' => 'Error en la modificación']);
}

?>
