<?php
setlocale(LC_TIME, 'es_ES.UTF-8');
date_default_timezone_set('America/Lima');

require_once __DIR__ . '/../vendor/autoload.php';
require_once '../conexion.php';
$mysqli->set_charset("utf8");

use Mpdf\Mpdf;
use Mpdf\QrCode\QrCode;
use Mpdf\QrCode\Output;

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) die("Error: ID inválido");

$query = "
SELECT 
    c.id_comprobante, c.tipo_comprobante, c.serie, c.correlativo,
    CONCAT(c.serie, '-', LPAD(c.correlativo, 8, '0')) AS numero_comprobante,
    c.fecha_emision, c.hora_emision, c.total, c.total_gravada, c.total_igv,
    c.moneda, c.estado_sunat, c.estado_documento, c.observaciones,
    c.motivo_nota,
    cl.razon_social AS cliente_nombre, cl.numero_documento AS cliente_doc,
    cl.direccion AS cliente_direccion, cl.tipo_documento AS cliente_tipo_doc,
    tp.tipo_pago, s.nombre AS servicio_nombre,
    r_origen.nombre AS origen, r_destino.nombre AS destino,
    CONCAT(u.usu_nombre, ' ', u.usu_apellido) AS usuario_nombre,
    su.sucrusal AS sucursal_nombre, e.nombre AS empresa_nombre,
    e.razon_social AS empresa_razon_social, e.ruc AS empresa_ruc,
    e.direccion AS empresa_direccion, e.telefono AS empresa_telefono,
    e.logo AS empresa_logo,
    ch.nombres_apellidos AS chofer_nombre, ch.nro_doc AS chofer_doc,
    ch.marca_vehiculo AS chofer_marca, ch.placa_vehiculo AS chofer_placa,
    c.id_comprobante_origen,
    c_origen.tipo_comprobante AS tipo_comprobante_origen,
    c_origen.serie AS serie_origen,
    c_origen.correlativo AS correlativo_origen,
    CONCAT(c_origen.serie, '-', LPAD(c_origen.correlativo, 8, '0')) AS numero_comprobante_origen,
    c_origen.fecha_emision AS fecha_emision_origen
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
LEFT JOIN comprobantes c_origen ON c.id_comprobante_origen = c_origen.id_comprobante
WHERE c.id_comprobante = '$id'";

$result = $mysqli->query($query);
if ($result->num_rows === 0) die("Comprobante no encontrado");
$row = $result->fetch_assoc();

// DETECTAR SI ES NOTA DE CRÉDITO O DÉBITO
$esNota = in_array($row['tipo_comprobante'], ['07', '08']);
$tipoNota = ($row['tipo_comprobante'] == '07') ? 'CRÉDITO' : 'DÉBITO';

// FORMATEAR DATOS
$meses = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
$fechaObj = new DateTime($row['fecha_emision']);
$fechaFormato = $fechaObj->format('d').' '.$meses[(int)$fechaObj->format('m')-1].' '.$fechaObj->format('Y');
$hora = !empty($row['hora_emision']) ? date('h:i A', strtotime($row['hora_emision'])) : '';

$total = number_format($row['total'], 2);
$total_gravada = number_format($row['total_gravada'], 2);
$total_igv = number_format($row['total_igv'], 2);

$tipoDocCliente = ($row['cliente_tipo_doc'] == '6') ? 'RUC' : 'DNI';

// NOMBRE DEL DOCUMENTO
if ($esNota) {
    $tipoCompNombre = "NOTA DE {$tipoNota} ELECTRÓNICA";
} else {
    $tipoCompNombre = ($row['tipo_comprobante'] == '01') ? 'FACTURA' : 'BOLETA';
}

// NOMBRE DEL DOCUMENTO AFECTADO (para notas)
$docAfectadoNombre = '';
if ($esNota && !empty($row['tipo_comprobante_origen'])) {
    $docAfectadoNombre = ($row['tipo_comprobante_origen'] == '01') ? 'FACTURA' : 'BOLETA';
}

// GENERAR QR
$cadenaQR = "{$row['empresa_ruc']}|{$row['tipo_comprobante']}|{$row['serie']}|{$row['correlativo']}|{$row['total_igv']}|{$row['total']}|{$row['fecha_emision']}|{$row['cliente_tipo_doc']}|{$row['cliente_doc']}|";
$qrCode = new QrCode($cadenaQR);
$output = new Output\Png();
$qrImage = 'data:image/png;base64,' . base64_encode($output->output($qrCode, 150));

$logoPath = (!empty($row['empresa_logo']) && file_exists(__DIR__."/../../../img/".$row['empresa_logo']))
    ? "../../../img/".$row['empresa_logo'] : "../../../img/logito.png";

// ESTADO SUNAT
$estadoSunat = strtoupper(trim($row['estado_sunat']));
$mostrarQR = false;
$claseMensaje = $iconoEstado = $mensajeEstado = $pieTicket = '';

if ($estadoSunat === 'PENDIENTE') {
    $claseMensaje = 'pendiente';
    $iconoEstado = '⚠';
    $mensajeEstado = 'BORRADOR - NO ENVIADO';
    $pieTicket = '<div class="footer-draft">Sin validez tributaria</div>';
} elseif ($estadoSunat === 'ACEPTADO' || $estadoSunat === 'ENVIADO') {
    $mostrarQR = true;
    $claseMensaje = 'aceptado';
    $iconoEstado = '✓';
    $mensajeEstado = 'ACEPTADO POR SUNAT';
    $pieTicket = '<div class="footer-oficial">Representación impresa del CPE<br>Verificar en www.sunat.gob.pe</div>';
} else {
    $claseMensaje = 'rechazado';
    $iconoEstado = '✖';
    $mensajeEstado = 'RECHAZADO';
    $pieTicket = '<div class="footer-draft">Sin validez tributaria</div>';
}

// SECCIÓN DE DOCUMENTO AFECTADO (solo para notas)
$seccionAfectado = '';
if ($esNota && !empty($row['numero_comprobante_origen'])) {
    $fechaOrigenObj = new DateTime($row['fecha_emision_origen']);
    $fechaOrigenFormato = $fechaOrigenObj->format('d').' '.$meses[(int)$fechaOrigenObj->format('m')-1].' '.$fechaOrigenObj->format('Y');
    
    $seccionAfectado = '
    <div class="sec doc-afectado">
        <div class="sec-tit">DOCUMENTO AFECTADO</div>
        <div class="lin"><div class="lin-lab">TIPO:</div><div class="lin-val">'.$docAfectadoNombre.'</div></div>
        <div class="lin"><div class="lin-lab">NÚMERO:</div><div class="lin-val">'.$row['numero_comprobante_origen'].'</div></div>
        <div class="lin"><div class="lin-lab">FECHA:</div><div class="lin-val">'.$fechaOrigenFormato.'</div></div>
    </div>';
}

// SECCIÓN DE MOTIVO (solo para notas)
$seccionMotivo = '';
if ($esNota && !empty($row['motivo_nota'])) {
    $seccionMotivo = '
    <div class="sec motivo-nota">
        <div class="sec-tit">MOTIVO</div>
        <div style="font-size:6px; padding:2px; text-align:justify;">'.$row['motivo_nota'].'</div>
    </div>';
}

// HTML COMPACTO
$html = '
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:"Courier New",monospace; font-size:7px; color:#000; line-height:1.1; }
.ticket { width:72mm; margin:0 auto; padding:3px; }
.header { text-align:center; margin-bottom:3px; padding-bottom:2px; border-bottom:2px solid #000; }
.logo img { width:40px; margin-bottom:2px; }
.emp-nom { font-size:9px; font-weight:bold; margin:1px 0; }
.emp-info { font-size:6px; margin:0; }
.comp-box { border:2px solid #000; padding:2px; margin:3px 0; text-align:center; background:#f0f0f0; }
.comp-box.nota { background:#fff3cd; border-color:#856404; }
.comp-tipo { font-size:8px; font-weight:bold; }
.comp-num { font-size:10px; font-weight:bold; letter-spacing:0.5px; }
.fecha { text-align:center; margin:2px 0; font-size:6px; font-weight:bold; }
.sec { margin:2px 0; padding:2px 0; border-top:1px dashed #000; }
.sec-tit { font-size:7px; font-weight:bold; background:#000; color:#fff; padding:1px 2px; margin-bottom:1px; }
.doc-afectado .sec-tit { background:#d9534f; }
.motivo-nota .sec-tit { background:#5bc0de; }
.lin { display:flex; justify-content:space-between; margin:1px 0; font-size:6px; }
.lin-lab { font-weight:bold; width:35%; }
.lin-val { width:65%; text-align:right; }
.tots { margin:3px 0; padding:2px; background:#f5f5f5; border:1px solid #000; }
.tot-lin { display:flex; justify-content:space-between; margin:1px 0; font-size:7px; }
.tot-fin { border-top:1px solid #000; padding-top:1px; margin-top:1px; font-weight:bold; font-size:9px; }
.qr { text-align:center; margin:3px 0; padding:2px; }
.qr img { width:60px; height:60px; }
.qr-txt { font-size:5px; margin-top:1px; }
.est { text-align:center; padding:2px; margin:2px 0; font-weight:bold; font-size:7px; border:1px solid #000; }
.pendiente { background:#fff3cd; color:#856404; }
.aceptado { background:#d4edda; color:#155724; }
.rechazado { background:#f8d7da; color:#721c24; }
.footer-oficial { text-align:center; font-size:5px; margin-top:3px; padding-top:2px; border-top:1px solid #000; line-height:1.2; }
.footer-draft { text-align:center; font-size:6px; margin-top:2px; padding:2px; background:#ffe5e5; border:1px solid #f00; color:#f00; font-weight:bold; }
.sep { border-top:1px dashed #000; margin:2px 0; }
</style>

<div class="ticket">
<div class="header">
<div class="logo"><img src="'.$logoPath.'"></div>
<div class="emp-nom">'.$row['empresa_nombre'].'</div>
<div class="emp-info">RUC: '.$row['empresa_ruc'].'</div>
<div class="emp-info">'.$row['empresa_direccion'].'</div>
<div class="emp-info">Telf: '.$row['empresa_telefono'].'</div>
</div>

<div class="comp-box '.($esNota ? 'nota' : '').'">
<div class="comp-tipo">'.$tipoCompNombre.'</div>
<div class="comp-num">'.$row['numero_comprobante'].'</div>
</div>

<div class="fecha">'.$fechaFormato.' | '.$hora.'</div>
<div class="sep"></div>

<div class="sec">
<div class="sec-tit">CLIENTE</div>
<div class="lin"><div class="lin-lab">'.$tipoDocCliente.':</div><div class="lin-val">'.$row['cliente_doc'].'</div></div>
<div class="lin"><div class="lin-lab">NOMBRE:</div><div class="lin-val">'.$row['cliente_nombre'].'</div></div>
</div>

'.$seccionAfectado.'

'.$seccionMotivo.'

<div class="sec">
<div class="sec-tit">VIAJE</div>
<div class="lin"><div class="lin-lab">ORIGEN:</div><div class="lin-val">'.$row['origen'].'</div></div>
<div class="lin"><div class="lin-lab">DESTINO:</div><div class="lin-val">'.$row['destino'].'</div></div>
'.(!empty($row['servicio_nombre']) ? '<div class="lin"><div class="lin-lab">SERVICIO:</div><div class="lin-val">'.$row['servicio_nombre'].'</div></div>' : '').'
</div>

'.(!empty($row['chofer_nombre']) ? '
<div class="sec">
<div class="sec-tit">CONDUCTOR</div>
<div class="lin"><div class="lin-lab">NOMBRE:</div><div class="lin-val">'.$row['chofer_nombre'].'</div></div>
<div class="lin"><div class="lin-lab">VEHÍCULO:</div><div class="lin-val">'.$row['chofer_marca'].' - '.$row['chofer_placa'].'</div></div>
</div>' : '').'

<div class="sep"></div>

<div class="tots">
<div class="tot-lin"><span>OP. GRAVADA:</span><span>S/ '.$total_gravada.'</span></div>
<div class="tot-lin"><span>IGV (18%):</span><span>S/ '.$total_igv.'</span></div>
<div class="tot-lin tot-fin"><span>TOTAL '.($esNota ? 'A DESCONTAR' : '').':</span><span>S/ '.$total.'</span></div>
</div>

<div style="text-align:center; margin:2px 0; font-size:7px; font-weight:bold;">PAGO: '.$row['tipo_pago'].'</div>
<div class="sep"></div>

<div class="est '.$claseMensaje.'">'.$iconoEstado.' '.$mensajeEstado.'</div>

'.($mostrarQR ? '<div class="qr"><img src="'.$qrImage.'"><div class="qr-txt">CONSULTA SUNAT</div></div>' : '').'

<div style="text-align:center; font-size:5px; margin:2px 0;">Atendido por: <b>'.$row['usuario_nombre'].'</b></div>
'.$pieTicket.'
</div>';

$mpdf = new Mpdf([
    'mode' => 'utf-8',
    'format' => [80, 200],
    'margin_left' => 0,
    'margin_right' => 0,
    'margin_top' => 0,
    'margin_bottom' => 0,
    'default_font_size' => 7,
    'default_font' => 'dejavusanscondensed'
]);

$mpdf->WriteHTML($html);
$mpdf->Output('Ticket_'.$row['numero_comprobante'].'.pdf', 'I');
?>