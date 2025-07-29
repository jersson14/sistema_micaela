<?php
require '../../model/model_facturas.php';

$MFA = new Modelo_Facturas(); // Instanciamos el modelo

// Capturar y sanitizar datos enviados por POST
$idfactu = strtoupper(htmlspecialchars($_POST['idfactu'], ENT_QUOTES, 'UTF-8'));
$total = strtoupper(htmlspecialchars($_POST['total'], ENT_QUOTES, 'UTF-8'));
$facturaactu = htmlspecialchars($_POST['facturaactu'], ENT_QUOTES, 'UTF-8');
$nombrefactura = strtoupper(htmlspecialchars($_POST['nombrefactura'], ENT_QUOTES, 'UTF-8'));
$notacreactu = htmlspecialchars($_POST['notacreactu'], ENT_QUOTES, 'UTF-8');
$nombrenotacre = strtoupper(htmlspecialchars($_POST['nombrenotacre'], ENT_QUOTES, 'UTF-8'));
$fecha = strtoupper(htmlspecialchars($_POST['fecha'], ENT_QUOTES, 'UTF-8'));
$idusu = htmlspecialchars($_POST['idusu'], ENT_QUOTES, 'UTF-8');

// Directorios correctos
$directorio_facturas = "controller/facturas/filefacturas/";
$directorio_notacredito = "controller/facturas/filenotacredito/";

$ruta_factura = $facturaactu;
$ruta_notacre = $notacreactu;

// Validar si se subió un nuevo archivo de factura
if (!empty($_FILES['facturaObj']['name'])) {
    $ruta_factura = $directorio_facturas . basename($nombrefactura);
    if (move_uploaded_file($_FILES['facturaObj']['tmp_name'], "../../" . $ruta_factura)) {
        if (!empty($facturaactu) && file_exists("../../" . $facturaactu) && is_file("../../" . $facturaactu)) {
            unlink("../../" . $facturaactu); // Eliminar factura anterior
        }
    } else {
        $ruta_factura = $facturaactu; // Mantener la factura actual si la nueva falla
    }
}

// Validar si se subió un nuevo archivo de nota de crédito
if (!empty($_FILES['notacreObj']['name'])) {
    $ruta_notacre = $directorio_notacredito . basename($nombrenotacre);
    if (move_uploaded_file($_FILES['notacreObj']['tmp_name'], "../../" . $ruta_notacre)) {
        if (!empty($notacreactu) && file_exists("../../" . $notacreactu) && is_file("../../" . $notacreactu)) {
            unlink("../../" . $notacreactu); // Eliminar nota de crédito anterior
        }
    } else {
        $ruta_notacre = $notacreactu; // Mantener la nota de crédito actual si la nueva falla
    }
}

// Llamar al método de modificación en el modelo
$consulta = $MFA->Modificar_Factura($idfactu, $total, $ruta_factura, $ruta_notacre, $fecha, $idusu);

echo $consulta;
?>
