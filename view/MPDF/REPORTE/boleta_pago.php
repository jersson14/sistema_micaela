<?php
setlocale(LC_TIME, 'es_ES.UTF-8');
date_default_timezone_set('America/Lima');

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../conexion.php';

$id_encomienda = (int)$_GET['id']; // Sanitizar como entero

// Consulta directa con SELECT
$query = "SELECT
    emisor.id_cliente AS id_emisor, 
    emisor.tipo_documento AS tipo_doc_emisor, 
    emisor.nro_documento AS nro_doc_emisor, 
    emisor.nombre_completo AS nombre_emisor, 
    emisor.celular AS celular_emisor, 
    emisor.direccion AS direccion_emisor, 
    receptor.id_cliente AS id_receptor, 
    receptor.tipo_documento AS tipo_doc_receptor, 
    receptor.nro_documento AS nro_doc_receptor, 
    receptor.nombre_completo AS nombre_receptor, 
    receptor.celular AS celular_receptor, 
    receptor.direccion AS direccion_receptor, 
    choferes.id_chofer, 
    choferes.tipo_documen, 
    choferes.nro_doc, 
    choferes.nombres_apellidos, 
    choferes.celular AS celular_chofer, 
    usuario.id_usuario, 
    usuario.dni_usuario, 
    usuario.usu_nombre, 
    usuario.usu_apellido, 
    encomiendas.id_encomienda, 
    encomiendas.boleta_nro, 
    encomiendas.id_conductor, 
    encomiendas.fecha_hora, 
    DATE_FORMAT(encomiendas.fecha_hora, \"%d-%m-%Y\") AS fecha_formateada, 
    DATE_FORMAT(encomiendas.fecha_hora, \"%H:%i:%s\") AS hora_formateada, 
    encomiendas.descripcion, 
    encomiendas.id_cliente_emisor, 
    encomiendas.id_cliente_receptor, 
    encomiendas.pago, 
    encomiendas.por_pagar, 
    encomiendas.a_domicilio, 
    encomiendas.id_usuario, 
    encomiendas.observacion, 
    encomiendas.estado_pago, 
    encomiendas.estado_encomienda, 
    encomiendas.motivo_anulacion, 
    encomiendas.fecha_anulacion, 
    encomiendas.created_at, 
    DATE_FORMAT(encomiendas.created_at, \"%d-%m-%Y - %H:%i:%s\") AS fecha_formateada2, 
    encomiendas.updated_at, 
    DATE_FORMAT(encomiendas.updated_at, \"%d-%m-%Y - %H:%i:%s\") AS fecha_formateada3, 
    encomiendas.id_origen, 
    encomiendas.id_destino, 
    rutas_origen.nombre AS nombre_origen,
    rutas_destino.nombre AS nombre_destino
FROM
    encomiendas
INNER JOIN clientes AS emisor
    ON encomiendas.id_cliente_emisor = emisor.id_cliente
INNER JOIN clientes AS receptor
    ON encomiendas.id_cliente_receptor = receptor.id_cliente
INNER JOIN usuario
    ON encomiendas.id_usuario = usuario.id_usuario
INNER JOIN choferes
    ON encomiendas.id_conductor = choferes.id_chofer
INNER JOIN rutas AS rutas_origen
    ON rutas_origen.idrutas = encomiendas.id_origen
INNER JOIN rutas AS rutas_destino
    ON rutas_destino.idrutas = encomiendas.id_destino
WHERE encomiendas.id_encomienda = :id_encomienda";

$stmt = $conexion->prepare($query);
$stmt->execute(['id_encomienda' => $id_encomienda]);

$html = '';
if ($stmt->rowCount() > 0) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $boleta_nro = $row['boleta_nro'];
    $fecha_formateada = $row['fecha_formateada'];
    $hora_formateada = $row['hora_formateada'];
    $descripcion = $row['descripcion'];
    $nombre_origen = $row['nombre_origen'];
    $nombre_destino = $row['nombre_destino'];
    $pago = $row['pago'];
    $por_pagar = $row['por_pagar'];
    $a_domicilio = $row['a_domicilio'];

    $emisor_nombre = $row['nombre_emisor'];
    $emisor_doc = $row['nro_doc_emisor'];
    $emisor_celular = $row['celular_emisor'];

    $receptor_nombre = $row['nombre_receptor'];
    $receptor_doc = $row['nro_doc_receptor'];
    $receptor_celular = $row['celular_receptor'];

    $conductor_nombre = $row['nombres_apellidos'];
    $conductor_celular = $row['celular_chofer'];

    $html = '
    <style>
        @import url(\'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css\');
        
        body { 
            font-family: Arial, sans-serif; 
            margin: 0;
            padding: 0;
        }
        
        .boleta { 
            border: 3px solid #000; 
            border-radius: 8px;
            width: 100%;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            overflow: hidden;
            position: relative;
        }
        
        /* Estilos generales para tablas */
        table {
            border-collapse: collapse;
            width: 100%;
            font-size: 10px;
        }
        
        td {
            vertical-align: top;
            box-sizing: border-box;
        }
        
        /* Header mejorado */
        .header-table {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-bottom: 2px solid #000;
            border-collapse: collapse;
            width: 100%;
        }
        
        .header-table td {
            border-right: 2px solid #000;
        }
        
        .header-table td:last-child {
            border-right: none;
        }
        
        .empresa-cell {
            width: 50%;
            padding: 6px;
            border-right: 2px solid #000;
            text-align: center;
        }
        
        .empresa-titulo { 
            font-weight: bold; 
            font-size: 11px; 
            color: #fff;
            margin-bottom: 4px;
            letter-spacing: 0.5px;
            text-shadow: 2px 0 0 #000, -2px 0 0 #000, 0 2px 0 #000, 0 -2px 0 #000, 1px 1px 0 #000, -1px -1px 0 #000, 1px -1px 0 #000, -1px 1px 0 #000;
        }
        
        .logo-text { 
            font-size: 18px; 
            font-weight: bold; 
            margin-bottom: 4px;
        }
        
        .tours-text {
            color: #000;
            text-shadow: 2px 0 0 #fff, -2px 0 0 #fff, 0 2px 0 #fff, 0 -2px 0 #fff, 1px 1px 0 #fff, -1px -1px 0 #fff, 1px -1px 0 #fff, -1px 1px 0 #fff;
        }
        
        .logo-micaela {              
            background: linear-gradient(135deg, #ff0000, #cc0000);             
            color: #fff;              
            padding: 4px 10px;             
            border-radius: 4px;             
            box-shadow: 0 2px 4px rgba(255,0,0,0.5);             
            text-shadow: 2px 0 0 #990000, -2px 0 0 #990000, 0 2px 0 #990000, 0 -2px 0 #990000, 1px 1px 0 #990000, -1px -1px 0 #990000, 1px -1px 0 #990000, -1px 1px 0 #990000;         
        }
        
        .slogan { 
            font-size: 8px; 
            color: #666;
            font-weight: bold;
            font-style: italic;
        }
        
        .info-cell {
            width: 50%;
            padding: 12px;
            text-align: right;
        }
        
        .salidas-box { 
            background: #000;
            color: #fff; 
            padding: 8px 16px; 
            font-weight: bold; 
            font-size: 10px;
            margin-bottom: 8px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 3px 6px rgba(0,0,0,0.4);
            display: inline-block;
        }
        
        .numero-boleta { 
            color: #ff0000; 
            font-weight: bold; 
            font-size: 20px;
            text-align: right;
            margin-bottom: 10px;
            text-shadow: 1px 1px 2px rgba(255,0,0,0.3);
        }
        
        .contacto-info { 
            font-size: 11px; 
            line-height: 1.5;
            text-align: right;
            color: #333;
        }
        
        .contacto-info .direccion {
            font-weight: bold;
            color: #000;
        }
        
        .contacto-info .telefono {
            font-weight: bold;
            color: #ff0000;
        }
        
        .lugar-badge {
            background: #ff0000;
            color: #fff; 
            padding: 4px 8px; 
            font-weight: bold;
            border-radius: 15px;
            box-shadow: 0 2px 4px rgba(255,0,0,0.3);
            display: inline-block;
        }
        
        /* Info básica mejorada */
        .info-basica {
            background: linear-gradient(135deg, #f1f3f4 0%, #e8eaed 100%);
            border-bottom: 2px solid #000;
            border-collapse: collapse;
            width: 100%;
        }
        
        .info-basica td {
            border-right: 2px solid #000;
        }
        
        .info-basica td:last-child {
            border-right: none;
        }
        
        .info-item {
            padding: 8px 10px;
            font-size: 10px;
            font-weight: bold;
            border-right: 2px solid #000;
            text-align: center;
            color: #333;
        }
        
        .info-item:last-child {
            border-right: none;
        }
        
        /* Contenido principal mejorado */
        .contenido-table {
            min-height: 150px;
            border-collapse: collapse;
            width: 100%;
        }
        
        .contenido-table td {
            border-right: 2px solid #000;
            border-bottom: 2px solid #000;
            padding: 6px 10px;
            background: #fafafa;
            vertical-align: top;
        }
        
        .contenido-table tr:last-child td {
            border-bottom: none;
        }
        
        .contenido-table td:last-child {
            border-right: none;
        }
        
        .campo-label {
            font-weight: bold !important;
            color: #ff0000 !important;
            font-size: 11px;
            display: block;
            margin-bottom: 3px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .campo-input {
            border: 2px solid #dee2e6;
            min-height: 20px;
            padding: 6px 8px;
            font-size: 10px;
            background: #fff;
            border-radius: 3px;
            color: #333;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .descripcion-input {
            min-height: 60px !important;
        }
        
        /* Advertencia mejorada */
        .advertencia-section {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            text-align: center;
            padding: 8px;
            font-size: 8px;
            line-height: 1.3;
        }
        
        .advertencia-destacado {
            color: #dc3545;
            font-weight: bold;
            text-shadow: 1px 1px 2px rgba(220,53,69,0.2);
        }
        
        /* Footer pagos mejorado - SIN CELESTE */
        .pagos-table {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-top: 2px solid #000;
            border-collapse: collapse;
            width: 100%;
        }
        
        .pagos-table td {
            border-right: 3px solid #000;
        }
        
        .pagos-table td:last-child {
            border-right: 3px solid #000;
        }
        
        .pago-cell {
            width: 33.33%;
            border-right: 2px solid #000;
            padding: 12px 8px;
            text-align: center;
        }
        
        .pago-cell:last-child {
            border-right: none;
        }
        
        .pago-label {
            display: block;
            font-weight: bold;
            color: #dc3545;
            font-size: 11px;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .pago-valor {
            font-weight: bold;
            font-size: 14px;
            color: #000;
            background: #fff;
            border: 2px solid #dee2e6;
            border-radius: 4px;
            padding: 4px 8px;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
        }
    </style>
    
    <div class="boleta">
        <!-- Header mejorado -->
        <table class="header-table">
            <tr>
                <td class="empresa-cell">
                    <img src="../../../img/logito.png" alt="Logo" width="270px" height="140px" style="margin-bottom: 3px;">
                    
                </td>
                
                <td class="info-cell">
                    <div class="salidas-box">SALIDAS DIARIAS</div>
                    <div class="numero-boleta">N° '.str_pad($boleta_nro, 6, "0", STR_PAD_LEFT).'</div>
                    
                    <div class="contacto-info">
                        <span class="direccion">■ <strong>PROLONGACIÓN HUANCAVELICA S/N</strong></span> 
                        <span class="lugar-badge">ABANCAY:</span><br>
                        <span class="telefono">☎ <strong>983 152 885</strong></span><br><br>
                        <span class="direccion">■ <strong>ALAMEDA PACHACUTEC (Frente al C.C. Confraternidad)</strong></span> 
                        <span class="lugar-badge">CUSCO:</span><br>
                        <span class="telefono">☎ <strong>983 152 886</strong></span>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Información básica mejorada -->
        <table class="info-basica">
            <tr>
                <td class="info-item" style="width: 25%;">FECHA: '.$fecha_formateada.'</td>
                <td class="info-item" style="width: 25%;">HORA: '.$hora_formateada.'</td>
                <td class="info-item" style="width: 25%;">ORIG.: '.strtoupper($nombre_origen).'</td>
                <td class="info-item" style="width: 25%;">DEST.: '.strtoupper($nombre_destino).'</td>
            </tr>
        </table>

        <!-- Contenido principal con divisiones -->
        <table class="contenido-table">             
            <tr>                 
                <td style="width: 50%;">                     
                    <span style="font-weight: bold; color: #ff0000; font-size: 11px; text-transform: uppercase;">Conductor:</span>                     
                    <div class="campo-input">'.strtoupper($conductor_nombre).'</div>                 
                </td>                 
                <td style="width: 50%;">                     
                    <span style="font-weight: bold; color: #ff0000; font-size: 11px; text-transform: uppercase;">Para:</span>                     
                    <div class="campo-input">'.strtoupper($receptor_nombre).'</div>                 
                </td>             
            </tr>             
            <tr>                 
                <td style="width: 50%;">                     
                    <span style="font-weight: bold; color: #ff0000; font-size: 11px; text-transform: uppercase;">Cel:</span>                     
                    <div class="campo-input">'.$conductor_celular.'</div>                 
                </td>                 
                <td style="width: 50%;">                     
                    <span style="font-weight: bold; color: #ff0000; font-size: 11px; text-transform: uppercase;">Cel:</span>                     
                    <div class="campo-input">'.$receptor_celular.'</div>                 
                </td>             
            </tr>             
            <tr>                              
                <td rowspan="3" style="width: 50%; vertical-align: top;">                     
                    <span style="font-weight: bold; color: #ff0000; font-size: 11px; text-transform: uppercase;">Descripción:</span>                     
                    <div class="campo-input descripcion-input">'.strtoupper($descripcion).'</div>                 
                </td>                 
                <td style="width: 50%;">                     
                    <span style="font-weight: bold; color: #ff0000; font-size: 11px; text-transform: uppercase;">DNI:</span>                     
                    <div class="campo-input">'.$receptor_doc.'</div>                 
                </td>             
            </tr>             
            <tr>                 
                <td style="width: 50%;">                     
                    <span style="font-weight: bold; color: #ff0000; font-size: 11px; text-transform: uppercase;">De parte:</span>                     
                    <div class="campo-input">'.strtoupper($emisor_nombre).'</div>                 
                </td>             
            </tr>             
            <tr>                 
                <td style="width: 50%;">                     
                    <span style="font-weight: bold; color: #ff0000; font-size: 11px; text-transform: uppercase;">Cel:</span>                     
                    <div class="campo-input">'.$emisor_celular.'</div>                 
                </td>             
            </tr>         
        </table>

        <!-- Advertencia mejorada -->
        <div class="advertencia-section">
            BRINDAMOS SERVICIO PRIVADO CON RECOJO A DOMICILIO<br>
            <span class="advertencia-destacado">* SOLO 15 DÍAS SE GUARDA LAS ENCOMIENDAS NO NOS HACEMOS RESPONSABLES DE PÉRDIDA.</span><br>
            <b>OJO: PARA EL RECOJO MOSTRAR FOTO DE LA BOLETA</b><br>
        </div>

        <!-- Footer pagos sin celeste -->
        <table class="pagos-table">
            <tr>
                <td class="pago-cell">
                    <span style="font-weight: bold; color: #ff0000; font-size: 11px; text-transform: uppercase; display: block; margin-bottom: 4px;">Pago S/.</span>
                    <div class="pago-valor">'.number_format($pago, 2).'</div>
                </td>
                
                <td class="pago-cell">
                    <span style="font-weight: bold; color: #ff0000; font-size: 11px; text-transform: uppercase; display: block; margin-bottom: 4px;">Por Pagar S/.</span>
                    <div class="pago-valor">'.number_format($por_pagar, 2).'</div>
                </td>
                
                <td class="pago-cell">
                    <span style="font-weight: bold; color: #ff0000; font-size: 11px; text-transform: uppercase; display: block; margin-bottom: 4px;">A Domicilio S/.</span>
                    <div class="pago-valor">'.($a_domicilio == "SI" ? "SÍ" : "NO").'</div>
                </td>
            </tr>
        </table>
    </div>';
}
$mpdf = new \Mpdf\Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4',
    'margin_left' => 10,
    'margin_right' => 10,
    'margin_top' => 10,
    'margin_bottom' => 10,
    'default_font' => 'Arial'
]);

$html = '
<div style="height: 50%; display: flex; justify-content: center; align-items: center; flex-direction: column;">
    <div class="boleta" style="max-width: 148mm; width: 100%; margin: 0 auto;">
        '.$html.'
    </div>
</div>
';

$mpdf->WriteHTML($html);
$mpdf->Output('boleta_'.$boleta_nro.'.pdf', 'I');