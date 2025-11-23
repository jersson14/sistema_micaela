<?php
setlocale(LC_TIME, 'es_ES.UTF-8');
date_default_timezone_set('America/Lima');

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../conexion.php';

use Mpdf\Mpdf;
use Mpdf\QrCode\QrCode;
use Mpdf\QrCode\Output;

// =========================================================
// VALIDAR PARÁMETROS
// =========================================================
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    die("Error: ID de comprobante inválido.");
}

// =========================================================
// CONSULTA PRINCIPAL
// =========================================================
$query = "
SELECT 
    c.id_comprobante,
    c.tipo_comprobante,
    c.serie,
    c.correlativo,
    CONCAT(c.serie, '-', LPAD(c.correlativo, 8, '0')) AS numero_comprobante,
    c.fecha_emision,
    c.hora_emision,
    c.total,
    c.total_gravada,
    c.total_igv,
    c.moneda,
    c.estado_sunat,
    c.descripcion_respuesta_sunat,
    cl.razon_social AS cliente_nombre,
    cl.numero_documento AS cliente_doc,
    cl.direccion AS cliente_direccion,
    tp.tipo_pago,
    s.nombre AS servicio_nombre,
    r_origen.nombre AS origen,
    r_destino.nombre AS destino,
    CONCAT(u.usu_nombre, ' ', u.usu_apellido) AS usuario_nombre,
    su.sucrusal AS sucursal_nombre,
    su.direccion AS sucursal_direccion,
    su.telefono1 AS sucursal_telefono,
    e.nombre AS empresa_nombre,
    e.razon_social AS empresa_razon_social,
    e.ruc AS empresa_ruc,
    e.direccion AS empresa_direccion,
    e.telefono AS empresa_telefono,
    e.logo AS empresa_logo,
    ch.nombres_apellidos AS chofer_nombre,
    ch.nro_doc AS chofer_doc,
    ch.marca_vehiculo AS chofer_marca,
    ch.placa_vehiculo AS chofer_placa,
    ch.celular AS chofer_celular
FROM comprobantes c
INNER JOIN cliente_sunat cl ON c.id_cliente = cl.id_cliente
LEFT JOIN tipo_pago tp ON c.id_tipo_pago = tp.id_tipo_pago
LEFT JOIN servicios s ON c.id_servicio = s.id_servicio
LEFT JOIN rutas r_origen ON c.id_origen = r_origen.idrutas
LEFT JOIN rutas r_destino ON c.iddestino = r_destino.idrutas
LEFT JOIN usuario u ON c.id_usuario = u.id_usuario
LEFT JOIN sucursales su ON u.id_sucursal = su.id_sucursal
LEFT JOIN empresa e ON su.id_empresa = e.id_empresa
LEFT JOIN choferes ch ON c.idconductor = ch.id_chofer
WHERE c.id_comprobante = :id
";

$stmt = $conexion->prepare($query);
$stmt->execute(['id' => $id]);
if ($stmt->rowCount() === 0) {
    die("No se encontró el comprobante solicitado.");
}

$row = $stmt->fetch(PDO::FETCH_ASSOC);

// =========================================================
// VALIDACIÓN SEGÚN ESTADO SUNAT
// =========================================================
$estadoSunat = strtoupper(trim($row['estado_sunat']));

if ($estadoSunat !== 'ACEPTADO') {
    echo "<div style='
        font-family: Arial;
        text-align: center;
        margin-top: 100px;
        color: #444;
    '>
        <h2>📄 Comprobante en estado: <span style='color: red;'>$estadoSunat</span></h2>
        <p>No se puede generar la representación impresa mientras no sea <b>ACEPTADO</b> por SUNAT.</p>
        <p>Respuesta de SUNAT: <i>{$row['descripcion_respuesta_sunat']}</i></p>
        <p><a href='javascript:window.close();' style='color: #007BFF;'>Cerrar</a></p>
    </div>";
    exit;
}

// =========================================================
// FORMATEAR DATOS
// =========================================================
$fecha = date('d/m/Y', strtotime($row['fecha_emision']));
$hora = $row['hora_emision'];
$total = number_format($row['total'], 2);
$total_gravada = number_format($row['total_gravada'], 2);
$total_igv = number_format($row['total_igv'], 2);

// =========================================================
// GENERAR CÓDIGO QR
// =========================================================
$cadenaQR = "{$row['empresa_ruc']}|{$row['tipo_comprobante']}|{$row['serie']}|{$row['correlativo']}|{$row['total_igv']}|{$row['total']}|{$row['fecha_emision']}|{$row['cliente_doc']}|";
$qrCode = new QrCode($cadenaQR);
$output = new Output\Png();
$qrImage = 'data:image/png;base64,' . base64_encode($output->output($qrCode, 150));

// =========================================================
// LOGO DE EMPRESA
// =========================================================
$logoPath = (!empty($row['empresa_logo']) && file_exists(__DIR__ . "/../../../img/" . $row['empresa_logo']))
    ? "../../../img/" . $row['empresa_logo']
    : "../../../img/logito.png";

// =========================================================
// HTML PROFESIONAL A4
// =========================================================
$html = '
<style>
body { font-family: Arial, sans-serif; color: #333; font-size: 11px; margin: 0; padding: 0; }
.header-table { width: 100%; border-bottom: 2px solid #007BFF; margin-bottom: 10px; }
.header-table td { vertical-align: top; }
.logo { width: 100px; }
.company-name { font-size: 16px; font-weight: bold; color: #007BFF; }
.section-title { background-color: #007BFF; color: white; font-weight: bold; padding: 3px; margin-top: 10px; border-radius: 3px; font-size: 11px; }
.table { width: 100%; border-collapse: collapse; margin-top: 5px; }
.table td { padding: 4px; vertical-align: top; }
.table-bordered td, .table-bordered th { border: 1px solid #ddd; }
.right { text-align: right; }
.center { text-align: center; }
.bold { font-weight: bold; }
.footer { text-align: center; font-size: 9px; color: #555; margin-top: 10px; }
.qr img { width: 100px; height: 100px; margin-top: 5px; }
</style>

<table class="header-table">
    <tr>
        <td><img src="'.$logoPath.'" class="logo"></td>
        <td class="center">
            <div class="company-name">'.$row['empresa_nombre'].'</div>
            <div>RUC: '.$row['empresa_ruc'].'</div>
            <div>'.$row['empresa_direccion'].'</div>
            <div>Tel: '.$row['empresa_telefono'].'</div>
        </td>
        <td class="center" style="border: 1px solid #007BFF; padding: 4px;">
            <div class="bold" style="font-size: 13px;">'.($row['tipo_comprobante'] == "01" ? "FACTURA" : "BOLETA").' ELECTRÓNICA</div>
            <div style="font-size: 11px;">N° '.$row['numero_comprobante'].'</div>
            <div>Fecha: '.$fecha.' '.$hora.'</div>
        </td>
    </tr>
</table>

<div class="section-title">DATOS DEL CLIENTE</div>
<table class="table">
    <tr><td><b>Razón Social:</b></td><td>'.$row['cliente_nombre'].'</td></tr>
    <tr><td><b>DNI/RUC:</b></td><td>'.$row['cliente_doc'].'</td></tr>
    <tr><td><b>Dirección:</b></td><td>'.$row['cliente_direccion'].'</td></tr>
    <tr><td><b>Forma de Pago:</b></td><td>'.$row['tipo_pago'].'</td></tr>
</table>

<div class="section-title">SERVICIO / TRANSPORTE</div>
<table class="table">
    <tr><td><b>Origen:</b></td><td>'.$row['origen'].'</td></tr>
    <tr><td><b>Destino:</b></td><td>'.$row['destino'].'</td></tr>
    <tr><td><b>Servicio:</b></td><td>'.$row['servicio_nombre'].'</td></tr>
    <tr><td><b>Chofer:</b></td><td>'.$row['chofer_nombre'].' ('.$row['chofer_doc'].')</td></tr>
    <tr><td><b>Vehículo:</b></td><td>'.$row['chofer_marca'].' - '.$row['chofer_placa'].'</td></tr>
</table>

<div class="section-title">RESUMEN DE PAGO</div>
<table class="table table-bordered">
    <tr><th>Concepto</th><th class="right">Monto (S/)</th></tr>
    <tr><td>Operación Gravada</td><td class="right">'.$total_gravada.'</td></tr>
    <tr><td>IGV (18%)</td><td class="right">'.$total_igv.'</td></tr>
    <tr><td class="bold">Total</td><td class="right bold">'.$total.'</td></tr>
</table>

<table width="100%" style="margin-top:10px;">
    <tr>
        <td class="center">
            <div class="qr"><img src="'.$qrImage.'"></div>
            <div style="font-size: 9px;">Código QR - SUNAT</div>
        </td>
    </tr>
</table>

<div class="footer">
    Representación impresa del comprobante electrónico.<br>
    Puede verificarlo en <b>https://e-consulta.sunat.gob.pe</b><br>
    <b>Gracias por su preferencia</b><br>
    © '.date('Y').' '.$row['empresa_nombre'].'
</div>
';

// =========================================================
// GENERAR PDF A4
// =========================================================
$mpdf = new Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4',
    'margin_left' => 8,
    'margin_right' => 8,
    'margin_top' => 8,
    'margin_bottom' => 8,
    'default_font_size' => 10,
    'default_font' => 'Arial'
]);

$mpdf->WriteHTML($html);
$mpdf->Output('comprobante_'.$row['numero_comprobante'].'.pdf', 'I');
?>
