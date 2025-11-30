<?php
setlocale(LC_TIME, 'es_ES.UTF-8');
date_default_timezone_set('America/Lima');

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../conexion.php';

use Mpdf\Mpdf;
use Mpdf\QrCode\QrCode;
use Mpdf\QrCode\Output;

// Validación mejorada de ID
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id <= 0) {
    die("Error: ID inválido");
}

// Query con prepared statement para seguridad
$query = "
SELECT 
    c.id_comprobante, c.tipo_comprobante, c.serie, c.correlativo,
    CONCAT(c.serie, '-', LPAD(c.correlativo, 8, '0')) AS numero_comprobante,
    c.fecha_emision, c.hora_emision, c.total, c.total_gravada, c.total_igv,
    c.moneda, c.estado_sunat, c.estado_documento, c.observaciones,
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
    ch.marca_vehiculo AS chofer_marca, ch.placa_vehiculo AS chofer_placa
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
WHERE c.id_comprobante = :id";

$stmt = $conexion->prepare($query);
$stmt->execute(['id' => $id]);

if ($stmt->rowCount() === 0) {
    die("Comprobante no encontrado");
}
$row = $stmt->fetch(PDO::FETCH_ASSOC);

// Función helper para escapar HTML
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

// FORMATEAR DATOS
$meses = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
$fechaObj = new DateTime($row['fecha_emision']);
$fechaFormato = $fechaObj->format('d').' '.$meses[(int)$fechaObj->format('m')-1].' '.$fechaObj->format('Y');
$hora = !empty($row['hora_emision']) ? date('h:i A', strtotime($row['hora_emision'])) : '';

// Formateo de montos
$total = number_format($row['total'], 2);
$total_gravada = number_format($row['total_gravada'], 2);
$total_igv = number_format($row['total_igv'], 2);

// Tipo de documento y comprobante
$tipoDocCliente = ($row['cliente_tipo_doc'] == '6') ? 'RUC' : 'DNI';
$tipoCompNombre = ($row['tipo_comprobante'] == '01') ? 'FACTURA' : 'BOLETA';

// GENERAR QR
$cadenaQR = "{$row['empresa_ruc']}|{$row['tipo_comprobante']}|{$row['serie']}|{$row['correlativo']}|{$row['total_igv']}|{$row['total']}|{$row['fecha_emision']}|{$row['cliente_tipo_doc']}|{$row['cliente_doc']}|";
$qrCode = new QrCode($cadenaQR);
$output = new Output\Png();
$qrImage = 'data:image/png;base64,' . base64_encode($output->output($qrCode, 150));

// Validación de logo
$logoPath = (!empty($row['empresa_logo']) && file_exists(__DIR__."/../../../img/".$row['empresa_logo']))
    ? "../../../img/".$row['empresa_logo'] 
    : "../../../img/logito.png";

// ESTADO SUNAT - Lógica mejorada
$estadoSunat = strtoupper(trim($row['estado_sunat']));
$estados = [
    'PENDIENTE' => [
        'clase' => 'pendiente',
        'icono' => '⚠',
        'mensaje' => 'BORRADOR - NO ENVIADO',
        'mostrarQR' => false,
        'pie' => '<div class="footer-draft">Sin validez tributaria</div>'
    ],
    'ACEPTADO' => [
        'clase' => 'aceptado',
        'icono' => '✓',
        'mensaje' => 'ACEPTADO POR SUNAT',
        'mostrarQR' => true,
        'pie' => '<div class="footer-oficial">Representación impresa del CPE<br>Verificar en www.sunat.gob.pe</div>'
    ],
    'ENVIADO' => [
        'clase' => 'aceptado',
        'icono' => '✓',
        'mensaje' => 'ACEPTADO POR SUNAT',
        'mostrarQR' => true,
        'pie' => '<div class="footer-oficial">Representación impresa del CPE<br>Verificar en www.sunat.gob.pe</div>'
    ]
];

$estadoConfig = $estados[$estadoSunat] ?? [
    'clase' => 'rechazado',
    'icono' => '✖',
    'mensaje' => 'RECHAZADO',
    'mostrarQR' => false,
    'pie' => '<div class="footer-draft">Sin validez tributaria</div>'
];

// HTML CON FUENTES MÁS GRANDES Y MEJOR LEGIBILIDAD
$html = '
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:"Courier New",monospace; font-size:11px; color:#000; line-height:1.3; }
.ticket { width:72mm; padding:2mm; }
.header { text-align:center; margin-bottom:2mm; padding-bottom:2mm; border-bottom:2px solid #000; }
.logo img { width:45px; margin-bottom:1mm; }
.emp-nom { font-size:13px; font-weight:bold; margin:0.5mm 0; }
.emp-info { font-size:9px; margin:0.2mm 0; line-height:1.2; }
.comp-box { border:2px solid #000; padding:2mm; margin:2mm 0; text-align:center; background:#f0f0f0; }
.comp-tipo { font-size:11px; font-weight:bold; }
.comp-num { font-size:14px; font-weight:bold; letter-spacing:0.5px; margin-top:0.5mm; }
.fecha { text-align:center; margin:1.5mm 0; font-size:10px; font-weight:bold; }
.sec { margin:1.5mm 0; padding:1mm 0; border-top:1px dashed #000; }
.sec-tit { font-size:10px; font-weight:bold; background:#000; color:#fff; padding:1mm 2mm; margin-bottom:1mm; }
.fila { font-size:10px; margin:0.5mm 0; line-height:1.3; }
.fila b { font-weight:bold; }
.tots { margin:2mm 0; padding:2mm; background:#f5f5f5; border:1px solid #000; }
.tot-lin { display:flex; justify-content:space-between; margin:0.5mm 0; font-size:10px; }
.tot-fin { border-top:1px solid #000; padding-top:1mm; margin-top:1mm; font-weight:bold; font-size:12px; }
.qr { text-align:center; margin:2mm 0; }
.qr img { width:65px; height:65px; }
.qr-txt { font-size:8px; margin-top:0.5mm; }
.est { text-align:center; padding:1.5mm; margin:1.5mm 0; font-weight:bold; font-size:10px; border:1px solid #000; }
.pendiente { background:#fff3cd; color:#856404; }
.aceptado { background:#d4edda; color:#155724; }
.rechazado { background:#f8d7da; color:#721c24; }
.footer-oficial { text-align:center; font-size:8px; margin-top:1.5mm; padding-top:1mm; border-top:1px solid #000; line-height:1.2; }
.footer-draft { text-align:center; font-size:9px; margin-top:1.5mm; padding:1mm; background:#ffe5e5; border:1px solid #f00; color:#f00; font-weight:bold; }
.sep { border-top:1px dashed #000; margin:1.5mm 0; }
.obs { font-size:9px; padding:1mm; margin:1.5mm 0; background:#f9f9f9; border-left:2px solid #666; line-height:1.3; }
.pago-info { text-align:center; margin:1.5mm 0; font-size:10px; font-weight:bold; }
.usuario-info { text-align:center; font-size:8px; margin:1.5mm 0; }
</style>

<div class="ticket">
<div class="header">
<div class="logo"><img src="'.e($logoPath).'"></div>
<div class="emp-nom">'.e($row['empresa_nombre']).'</div>
<div class="emp-info">RUC: '.e($row['empresa_ruc']).'</div>
<div class="emp-info">'.e($row['empresa_direccion']).'</div>
<div class="emp-info">Telf: '.e($row['empresa_telefono']).' - +51983152886</div>
<div class="emp-info">Quejas: +51968110220 - AlEXANDER SERRANO</div>
</div>

<div class="comp-box">
<div class="comp-tipo">'.e($tipoCompNombre).' ELECTRÓNICA</div>
<div class="comp-num">'.e($row['numero_comprobante']).'</div>
</div>

<div class="fecha">'.e($fechaFormato).' | '.e($hora).'</div>
<div class="sep"></div>

<div class="sec">
<div class="sec-tit">CLIENTE</div>
<div class="fila"><b>'.e($tipoDocCliente).':</b> '.e($row['cliente_doc']).'</div>
<div class="fila"><b>NOMBRE:</b> '.e($row['cliente_nombre']).'</div>
'.((!empty($row['cliente_direccion'])) ? '<div class="fila"><b>DIRECCIÓN:</b> '.e($row['cliente_direccion']).'</div>' : '').'
</div>

<div class="sec">
<div class="sec-tit">VIAJE</div>
<div class="fila"><b>ORIGEN:</b> '.e($row['origen']).'</div>
<div class="fila"><b>DESTINO:</b> '.e($row['destino']).'</div>
'.((!empty($row['servicio_nombre'])) ? '<div class="fila"><b>SERVICIO:</b> '.e($row['servicio_nombre']).'</div>' : '').'
</div>

'.((!empty($row['chofer_nombre'])) ? '
<div class="sec">
<div class="sec-tit">CONDUCTOR</div>
<div class="fila"><b>NOMBRE:</b> '.e($row['chofer_nombre']).'</div>
<div class="fila"><b>DNI:</b> '.e($row['chofer_doc']).'</div>
<div class="fila"><b>VEHÍCULO:</b> '.e($row['chofer_marca']).' - '.e($row['chofer_placa']).'</div>
</div>' : '').'

<div class="sep"></div>

<div class="tots">
<div class="tot-lin"><span>OP. GRAVADA:</span><span>'.e($row['moneda']).' '.e($total_gravada).'</span></div>
<div class="tot-lin"><span>IGV (18%):</span><span>'.e($row['moneda']).' '.e($total_igv).'</span></div>
<div class="tot-lin tot-fin"><span>TOTAL:</span><span>'.e($row['moneda']).' '.e($total).'</span></div>
</div>

<div class="pago-info">PAGO: '.e($row['tipo_pago']).'</div>

'.((!empty($row['observaciones'])) ? '<div class="obs"><b>Obs:</b> '.e($row['observaciones']).'</div>' : '').'

<div class="sep"></div>

<div class="est '.$estadoConfig['clase'].'">'.$estadoConfig['icono'].' '.$estadoConfig['mensaje'].'</div>

'.($estadoConfig['mostrarQR'] ? '<div class="qr"><img src="'.$qrImage.'"><div class="qr-txt">CONSULTA SUNAT</div></div>' : '').'

<div class="usuario-info">Atendido por: <b>'.e($row['usuario_nombre']).'</b></div>
'.$estadoConfig['pie'].'
</div>';

// Generar PDF con fuentes más grandes
try {
    $mpdf = new Mpdf([
        'mode' => 'utf-8',
        'format' => [80, 262],
        'margin_left' => 4,
        'margin_right' => 4,
        'margin_top' => 2,
        'margin_bottom' => 2,
        'default_font_size' => 11,
        'default_font' => 'dejavusanscondensed'
    ]);

    $mpdf->WriteHTML($html);
    $mpdf->Output('Ticket_'.preg_replace('/[^A-Za-z0-9\-]/', '_', $row['numero_comprobante']).'.pdf', 'I');
} catch (Exception $e) {
    error_log("Error generando PDF: " . $e->getMessage());
    die("Error al generar el ticket. Por favor, contacte al administrador.");
}
?>