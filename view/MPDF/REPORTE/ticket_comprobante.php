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
    c.fecha_emision,c.observaciones, c.hora_emision, c.total, c.total_gravada, c.total_igv,
    c.moneda, c.estado_sunat, c.estado_documento, 
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
WHERE c.id_comprobante = :id";

$stmt = $conexion->prepare($query);
$stmt->execute(['id' => $id]);

if ($stmt->rowCount() === 0) {
    die("Comprobante no encontrado");
}
$row = $stmt->fetch(PDO::FETCH_ASSOC);

// CONSULTAR DETALLE DEL COMPROBANTE
$queryDetalle = "
SELECT 
    orden_item,
    descripcion,
    unidad_medida,
    cantidad,
    precio_unitario,
    valor_unitario,
    descuento,
    igv,
    total_item
FROM comprobante_detalle
WHERE id_comprobante = :id
ORDER BY orden_item ASC";

$stmtDetalle = $conexion->prepare($queryDetalle);
$stmtDetalle->execute(['id' => $id]);
$detalles = $stmtDetalle->fetchAll(PDO::FETCH_ASSOC);

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

// CONSTRUIR HTML DE DETALLE DE ITEMS - MEJORADO PARA CLIENTES
$htmlDetalle = '';
if (count($detalles) > 0) {
    $htmlDetalle = '<div class="sec"><div class="sec-tit">DETALLE DE PRODUCTOS/SERVICIOS</div>';
    
    foreach ($detalles as $detalle) {
        // Formatear cantidad sin decimales innecesarios
        $cantidadNum = floatval($detalle['cantidad']);
        $cantidad = ($cantidadNum == floor($cantidadNum)) 
            ? number_format($cantidadNum, 0) 
            : number_format($cantidadNum, 2);
        
        $precioUnitario = number_format($detalle['precio_unitario'], 2);
        $totalItem = number_format($detalle['total_item'], 2);
        $descuento = floatval($detalle['descuento']);
        
        $htmlDetalle .= '<div class="item">';
        
        // Descripción del producto/servicio
        $htmlDetalle .= '<div class="item-desc">'.e($detalle['descripcion']).'</div>';
        
        // Información de cantidad y precio detallada
        $htmlDetalle .= '<div class="item-cantidad">';
        $htmlDetalle .= '<span class="cant-label">Cantidad:</span> ';
        $htmlDetalle .= '<span class="cant-num">'.e($cantidad).' '.e($detalle['unidad_medida']).'</span>';
        $htmlDetalle .= '</div>';
        
        $htmlDetalle .= '<div class="item-precio">';
        $htmlDetalle .= '<span>Precio Unit.: '.e($row['moneda']).' '.e($precioUnitario).'</span>';
        $htmlDetalle .= '</div>';
        
        // Subtotal del item
        $htmlDetalle .= '<div class="item-subtotal">';
        $htmlDetalle .= '<span>Subtotal:</span>';
        $htmlDetalle .= '<span class="item-total">'.e($row['moneda']).' '.e($totalItem).'</span>';
        $htmlDetalle .= '</div>';
        
        if ($descuento > 0) {
            $htmlDetalle .= '<div class="item-desc-txt">* Descuento aplicado: '.e($row['moneda']).' '.number_format($descuento, 2).'</div>';
        }
        
        $htmlDetalle .= '</div>';
    }
    
    $htmlDetalle .= '</div>';
}

// HTML CON ESPACIOS REDUCIDOS Y MEJOR DETALLE
$html = '
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:"Courier New",monospace; font-size:11px; color:#000; line-height:1.2; }
.ticket { width:72mm; padding:1.5mm; }
.header { text-align:center; margin-bottom:1mm; padding-bottom:1mm; border-bottom:2px solid #000; }
.logo img { width:35px; margin-bottom:0.5mm; }
.emp-nom { font-size:13px; font-weight:bold; margin:0.3mm 0; }
.emp-info { font-size:9px; margin:0.1mm 0; line-height:1.1; }
.comp-box { border:2px solid #000; padding:1.5mm; margin:1mm 0; text-align:center; background:#f0f0f0; }
.comp-tipo { font-size:11px; font-weight:bold; }
.comp-num { font-size:14px; font-weight:bold; letter-spacing:0.5px; margin-top:0.3mm; }
.fecha { text-align:center; margin:0.8mm 0; font-size:10px; font-weight:bold; }
.sec { margin:1mm 0; padding:0.8mm 0; border-top:1px dashed #000; }
.sec-tit { font-size:10px; font-weight:bold; background:#000; color:#fff; padding:0.8mm 1.5mm; margin-bottom:0.8mm; }
.fila { font-size:10px; margin:0.3mm 0; line-height:1.2; }
.fila b { font-weight:bold; }

/* ESTILOS MEJORADOS PARA DETALLE DE ITEMS */
.item { margin:1mm 0; padding:1mm; background:#f9f9f9; border:1px solid #ddd; border-radius:2px; }
.item-desc { font-size:11px; font-weight:bold; margin-bottom:0.5mm; color:#000; border-bottom:1px dotted #999; padding-bottom:0.3mm; }
.item-cantidad { font-size:10px; margin:0.4mm 0; background:#fff; padding:0.5mm; border-left:3px solid #4CAF50; }
.cant-label { font-weight:bold; color:#4CAF50; }
.cant-num { font-weight:bold; font-size:11px; color:#000; }
.item-precio { font-size:9px; margin:0.3mm 0; padding:0.3mm 0.5mm; }
.item-subtotal { display:flex; justify-content:space-between; font-size:10px; margin:0.5mm 0; padding-top:0.5mm; border-top:1px dotted #ccc; font-weight:bold; }
.item-total { font-weight:bold; font-size:11px; color:#000; }
.item-desc-txt { font-size:8px; color:#d32f2f; font-style:italic; margin-top:0.3mm; padding:0.3mm; background:#ffebee; }

.tots { margin:1mm 0; padding:1.5mm; background:#f5f5f5; border:1px solid #000; }
.tot-lin { display:flex; justify-content:space-between; margin:0.3mm 0; font-size:10px; }
.tot-fin { border-top:1px solid #000; padding-top:0.8mm; margin-top:0.8mm; font-weight:bold; font-size:12px; }
.qr { text-align:center; margin:1mm 0; }
.qr img { width:55px; height:55px; }
.qr-txt { font-size:8px; margin-top:0.3mm; }
.est { text-align:center; padding:1mm; margin:1mm 0; font-weight:bold; font-size:10px; border:1px solid #000; }
.pendiente { background:#fff3cd; color:#856404; }
.aceptado { background:#d4edda; color:#155724; }
.rechazado { background:#f8d7da; color:#721c24; }
.footer-oficial { text-align:center; font-size:8px; margin-top:1mm; padding-top:0.8mm; border-top:1px solid #000; line-height:1.1; }
.footer-draft { text-align:center; font-size:9px; margin-top:1mm; padding:0.8mm; background:#ffe5e5; border:1px solid #f00; color:#f00; font-weight:bold; }
.sep { border-top:1px dashed #000; margin:1mm 0; }
.obs { font-size:9px; padding:0.8mm; margin:1mm 0; background:#fff3cd; border-left:3px solid #ff9800; line-height:1.2; }
.pago-info { text-align:center; margin:1mm 0; font-size:10px; font-weight:bold; padding:1mm; background:#e3f2fd; border:1px solid #2196F3; }
.usuario-info { text-align:center; font-size:8px; margin:1mm 0; }
</style>

<div class="ticket">
<div class="header">
<div class="logo"><img src="'.e($logoPath).'"></div>
<div class="emp-nom">'.e($row['empresa_nombre']).'</div>
<div class="emp-info">RUC: '.e($row['empresa_ruc']).'</div>
<div class="emp-info">'.e($row['empresa_direccion']).'</div>
<div class="emp-info">Telf: '.e($row['empresa_telefono']).' - +51983152886</div>
<div class="emp-info">Quejas: +51968110220 - ALEXANDER SERRANO</div>
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
<div class="fila"><b>OBSERVACIONES:</b> '.e($row['observaciones']).'</div>

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
<div class="fila"><b>CELULAR:</b> '.e($row['chofer_celular']).'</div>
<div class="fila"><b>VEHÍCULO:</b> '.e($row['chofer_marca']).' - '.e($row['chofer_placa']).'</div>
</div>' : '').'

'.$htmlDetalle.'

<div class="sep"></div>

<div class="tots">
<div class="tot-lin"><span>OP. GRAVADA:</span><span>'.e($row['moneda']).' '.e($total_gravada).'</span></div>
<div class="tot-lin"><span>IGV (18%):</span><span>'.e($row['moneda']).' '.e($total_igv).'</span></div>
<div class="tot-lin tot-fin"><span>TOTAL A PAGAR:</span><span>'.e($row['moneda']).' '.e($total).'</span></div>
</div>


'.((!empty($row['observaciones'])) ? '<div class="obs"><b>Observaciones:</b> '.e($row['observaciones']).'</div>' : '').'

<div class="sep"></div>

<div class="est '.$estadoConfig['clase'].'">'.$estadoConfig['icono'].' '.$estadoConfig['mensaje'].'</div>

'.($estadoConfig['mostrarQR'] ? '<div class="qr"><img src="'.$qrImage.'"><div class="qr-txt">CONSULTA EN SUNAT</div></div>' : '').'

<div class="usuario-info">Atendido por: <b>'.e($row['usuario_nombre']).'</b></div>
'.$estadoConfig['pie'].'
</div>';

// Generar PDF con altura ajustada
try {
    $mpdf = new Mpdf([
        'mode' => 'utf-8',
        'format' => [80, 270],
        'margin_left' => 3,
        'margin_right' => 3,
        'margin_top' => 1,
        'margin_bottom' => 1,
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