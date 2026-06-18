<?php
setlocale(LC_TIME, 'es_ES.UTF-8');
date_default_timezone_set('America/Lima');

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../conexion.php';

use Mpdf\Mpdf;

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id <= 0) {
    die("Error: ID inválido");
}

$query = "
SELECT 
    tv.id, tv.numero_ticket, tv.fecha, tv.total, tv.gravada, tv.igv, tv.estado,
    c.tipo_documento, c.nro_documento, c.nombre_completo, c.celular,
    s.nombre AS servicio, 
    r_origen.nombre AS origen, 
    r_destino.nombre AS destino,
    CONCAT(u.usu_nombre, ' ', u.usu_apellido) AS usuario_nombre,
    su.sucrusal AS sucursal_nombre, 
    e.nombre AS empresa_nombre,
    e.razon_social AS empresa_razon_social, 
    e.ruc AS empresa_ruc,
    e.direccion AS empresa_direccion, 
    e.telefono AS empresa_telefono
FROM tickets_viaje tv
INNER JOIN clientes c ON tv.idcliente = c.id_cliente
INNER JOIN servicios s ON tv.idservicio = s.id_servicio
LEFT JOIN rutas r_origen ON tv.idorigen = r_origen.idrutas
LEFT JOIN rutas r_destino ON tv.iddestino = r_destino.idrutas
LEFT JOIN usuario u ON tv.usuario_crea = u.id_usuario
LEFT JOIN sucursales su ON u.id_sucursal = su.id_sucursal
LEFT JOIN empresa e ON su.id_empresa = e.id_empresa
WHERE tv.id = :id";

$stmt = $conexion->prepare($query);
$stmt->execute(['id' => $id]);

if ($stmt->rowCount() === 0) {
    die("Ticket no encontrado");
}
$row = $stmt->fetch(PDO::FETCH_ASSOC);

function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

$meses = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
$fechaObj = new DateTime($row['fecha']);
$fechaFormato = $fechaObj->format('d').' '.$meses[(int)$fechaObj->format('m')-1].' '.$fechaObj->format('Y');

$total = number_format($row['total'], 2);
$gravada = number_format($row['gravada'], 2);
$igv = number_format($row['igv'], 2);

$tipoDoc = $row['tipo_documento'];

$html = '
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:"Courier New",monospace; font-size:10px; color:#000; line-height:1.3; }
.ticket { width:72mm; padding:2mm; }
.header { text-align:center; margin-bottom:2mm; padding-bottom:2mm; border-bottom:2px solid #000; }
.emp-nom { font-size:16px; font-weight:bold; margin:0.5mm 0; }
.emp-info { font-size:9px; margin:0.2mm 0; line-height:1.2; }
.aviso { font-size:10px; font-weight:bold; margin:0.5mm 0; }
.ticket-box { border:2px solid #000; padding:2mm; margin:2mm 0; text-align:center; background:#f0f0f0; }
.ticket-num { font-size:13px; font-weight:bold; letter-spacing:0.5px; }
.fecha { text-align:center; margin:1.5mm 0; font-size:10px; font-weight:bold; }
.sec { margin:1.5mm 0; padding:1mm 0; border-top:1px dashed #000; }
.sec-tit { font-size:10px; font-weight:bold; background:#000; color:#fff; padding:1mm 2mm; margin-bottom:1mm; }
.fila { font-size:10px; margin:0.5mm 0; line-height:1.3; }
.fila b { font-weight:bold; }
.tots { margin:2mm 0; padding:2mm; background:#f5f5f5; border:1px solid #000; }
.tot-lin { display:flex; justify-content:space-between; margin:0.5mm 0; font-size:10px; }
.tot-fin { border-top:1px solid #000; padding-top:1mm; margin-top:1mm; font-weight:bold; font-size:11px; }
.sep { border-top:1px dashed #000; margin:1.5mm 0; }
.footer { text-align:center; font-size:8px; margin-top:2mm; padding-top:1mm; border-top:1px solid #000; }
</style>

<div class="ticket">
<div class="header">
<div class="emp-nom">'.e($row['empresa_nombre']).'</div>
<div class="emp-info">RUC: '.e($row['empresa_ruc']).'</div>
<div class="emp-info">'.e($row['empresa_direccion']).'</div>
<div class="emp-info">Telf: '.e($row['empresa_telefono']).' - +51983152886</div>
<div class="emp-info">Quejas: +51968110220 - AlEXANDER SERRANO</div>
<div class="aviso">ESTE NO ES UN COMPROBANTE DE PAGO VALIDO</div>

</div>

<div class="ticket-box">
<div class="ticket-num">TICKET N° '.e($row['numero_ticket']).'</div>
</div>

<div class="fecha">'.e($fechaFormato).'</div>
<div class="sep"></div>

<div class="sec">
<div class="sec-tit">CLIENTE</div>
<div class="fila"><b>'.e($tipoDoc).':</b> '.e($row['nro_documento']).'</div>
<div class="fila"><b>NOMBRE:</b> '.e($row['nombre_completo']).'</div>
'.((!empty($row['celular'])) ? '<div class="fila"><b>CELULAR:</b> '.e($row['celular']).'</div>' : '').'
</div>

<div class="sec">
<div class="sec-tit">VIAJE</div>
<div class="fila"><b>ORIGEN:</b> '.e($row['origen']).'</div>
<div class="fila"><b>DESTINO:</b> '.e($row['destino']).'</div>
<div class="fila"><b>SERVICIO:</b> '.e($row['servicio']).'</div>
</div>

<div class="sep"></div>

<div class="tots">
<div class="tot-lin"><span>BASE GRAVADA:</span><span>S/ '.e($gravada).'</span></div>
<div class="tot-lin"><span>IGV (18%):</span><span>S/ '.e($igv).'</span></div>
<div class="tot-lin tot-fin"><span>TOTAL:</span><span>S/ '.e($total).'</span></div>
</div>

<div class="footer">Atendido por: <b>'.e($row['usuario_nombre']).'</b><br>Gracias por su preferencia</div>
</div>';

try {
    $mpdf = new Mpdf([
        'mode' => 'utf-8',
        'format' => [80, 200],
        'margin_left' => 4,
        'margin_right' => 4,
        'margin_top' => 2,
        'margin_bottom' => 2,
        'default_font_size' => 10,
        'default_font' => 'dejavusanscondensed'
    ]);

    $mpdf->WriteHTML($html);
    $mpdf->Output('Ticket_'.preg_replace('/[^A-Za-z0-9\-]/', '_', $row['numero_ticket']).'.pdf', 'I');
} catch (Exception $e) {
    error_log("Error generando PDF: " . $e->getMessage());
    die("Error al generar el ticket. Por favor, contacte al administrador.");
}
?>