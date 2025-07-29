<?php
require '../../model/model_facturas.php';
$MFA = new Modelo_Facturas(); // Instanciamos el modelo

// DATOS DE FACTURA
$nrofact = strtoupper(htmlspecialchars($_POST['nrofact'], ENT_QUOTES, 'UTF-8'));
$total = strtoupper(htmlspecialchars($_POST['total'], ENT_QUOTES, 'UTF-8'));
$nombrefactura = htmlspecialchars($_POST['nombrefactura'], ENT_QUOTES, 'UTF-8');
$nombrenotacre = htmlspecialchars($_POST['nombrenotacre'], ENT_QUOTES, 'UTF-8');

// DATOS DEL USUARIO
$fecha = htmlspecialchars($_POST['fecha'], ENT_QUOTES, 'UTF-8');
$idusu = htmlspecialchars($_POST['idusu'], ENT_QUOTES, 'UTF-8');

// Definir rutas de almacenamiento
$rutaFactura = 'controller/facturas/filefacturas/' . $nombrefactura;
$rutaNotacre = 'controller/facturas/filenotacredito/' . $nombrenotacre;

// Registrar en la base de datos
$consulta = $MFA->Registrar_Factura($nrofact, $total, $rutaFactura, $rutaNotacre, $fecha, $idusu);

if ($consulta) {
    // Subir archivo de factura si existe
    if ($nombrefactura!="" ) {
        move_uploaded_file($_FILES['factura']['tmp_name'], "filefacturas/" . $nombrefactura);
    }

    // Subir archivo de nota de crédito si existe
    if ($nombrenotacre!="" ) {
        move_uploaded_file($_FILES['notacre']['tmp_name'], "filenotacredito/" . $nombrenotacre);
    }

    echo $consulta;
}
?>
