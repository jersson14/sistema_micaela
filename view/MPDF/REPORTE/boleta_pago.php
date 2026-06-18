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

$boleta_html = '';
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

    $boleta_html = '
    <div class="boleta">
        <!-- Header -->
        <table class="header-table">
            <tr>
                <td class="empresa-cell">
                    <img src="../../../img/logito.png" alt="Logo" width="240px" height="120px">
                </td>
                
                <td class="info-cell">
                    <div class="salidas-box">SALIDAS DIARIAS</div>
                    <div class="numero-boleta">N° '.str_pad($boleta_nro, 6, "0", STR_PAD_LEFT).'</div>
                    
                    <div class="contacto-info">
                        <span class="direccion">■ <strong>PROLONGACIÓN HUANCAVELICA S/N</strong></span> 
                        <span class="lugar-badge">ABANCAY:</span><br>
                        <span class="telefono">☎ <strong>983 152 885</strong></span><br>
                        <span class="direccion">■ <strong>ALAMEDA PACHACUTEC (Frente al C.C. Confraternidad)</strong></span> 
                        <span class="lugar-badge">CUSCO:</span><br>
                        <span class="telefono">☎ <strong>983 152 886</strong></span>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Información básica -->
        <table class="info-basica">
            <tr>
                <td class="info-item" style="width: 25%;">FECHA: '.$fecha_formateada.'</td>
                <td class="info-item" style="width: 25%;">HORA: '.$hora_formateada.'</td>
                <td class="info-item" style="width: 25%;">ORIG.: '.strtoupper($nombre_origen).'</td>
                <td class="info-item" style="width: 25%;">DEST.: '.strtoupper($nombre_destino).'</td>
            </tr>
        </table>

        <!-- Contenido principal -->
        <table class="contenido-table">             
            <tr>                 
                <td style="width: 50%;">                     
                    <span style="font-weight: bold; color: #ff0000; font-size: 10px; text-transform: uppercase;">Conductor:</span>                     
                    <div class="campo-input">'.strtoupper($conductor_nombre).'</div>                 
                </td>                 
                <td style="width: 50%;">                     
                    <span style="font-weight: bold; color: #ff0000; font-size: 10px; text-transform: uppercase;">Para:</span>                     
                    <div class="campo-input">'.strtoupper($receptor_nombre).'</div>                 
                </td>             
            </tr>             
            <tr>                 
                <td style="width: 50%;">                     
                    <span style="font-weight: bold; color: #ff0000; font-size: 10px; text-transform: uppercase;">Cel:</span>                     
                    <div class="campo-input">'.$conductor_celular.'</div>                 
                </td>                 
                <td style="width: 50%;">                     
                    <span style="font-weight: bold; color: #ff0000; font-size: 10px; text-transform: uppercase;">Cel:</span>                     
                    <div class="campo-input">'.$receptor_celular.'</div>                 
                </td>             
            </tr>             
            <tr>                              
                <td rowspan="3" style="width: 50%; vertical-align: top;">                     
                    <span style="font-weight: bold; color: #ff0000; font-size: 10px; text-transform: uppercase;">Descripción:</span>                     
                    <div class="campo-input descripcion-input">'.strtoupper($descripcion).'</div>                 
                </td>                 
                <td style="width: 50%;">                     
                    <span style="font-weight: bold; color: #ff0000; font-size: 10px; text-transform: uppercase;">DNI:</span>                     
                    <div class="campo-input">'.$receptor_doc.'</div>                 
                </td>             
            </tr>             
            <tr>                 
                <td style="width: 50%;">                     
                    <span style="font-weight: bold; color: #ff0000; font-size: 10px; text-transform: uppercase;">De parte:</span>                     
                    <div class="campo-input">'.strtoupper($emisor_nombre).'</div>                 
                </td>             
            </tr>             
            <tr>                 
                <td style="width: 50%;">                     
                    <span style="font-weight: bold; color: #ff0000; font-size: 10px; text-transform: uppercase;">Cel:</span>                     
                    <div class="campo-input">'.$emisor_celular.'</div>                 
                </td>             
            </tr>         
        </table>

        <!-- Advertencia -->
        <div class="advertencia-section">
            BRINDAMOS SERVICIO PRIVADO CON RECOJO A DOMICILIO<br>
            <span class="advertencia-destacado">* SOLO 15 DÍAS SE GUARDA LAS ENCOMIENDAS NO NOS HACEMOS RESPONSABLES DE PÉRDIDA.</span><br>
            <b>OJO: PARA EL RECOJO MOSTRAR FOTO DE LA BOLETA</b>
        </div>

        <!-- Footer pagos -->
        <table class="pagos-table">
            <tr>
                <td class="pago-cell">
                    <span style="font-weight: bold; color: #1a28e9ff; font-size: 10px; text-transform: uppercase; display: block; margin-bottom: 3px;">Pago S/.</span>
                    <div class="pago-valor">'.number_format($pago, 2).'</div>
                </td>
                
                <td class="pago-cell">
                    <span style="font-weight: bold; color: #ff0000; font-size: 10px; text-transform: uppercase; display: block; margin-bottom: 3px;">Por Pagar S/.</span>
                    <div class="pago-valor">'.number_format($por_pagar, 2).'</div>
                </td>
                
                <td class="pago-cell">
                    <span style="font-weight: bold; color: #ff0000; font-size: 10px; text-transform: uppercase; display: block; margin-bottom: 3px;">A Domicilio S/.</span>
                    <div class="pago-valor">'.number_format($a_domicilio, 2).'</div>
                </td>
            </tr>
        </table>
    </div>';
}

$mpdf = new \Mpdf\Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4',
    'margin_left' => 20,
    'margin_right' => 20,
    'margin_top' => 20,
    'margin_bottom' => 20,
    'default_font' => 'Arial'
]);

$html = '
<style>
    @import url(\'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css\');
    
    body { 
        font-family: Arial, sans-serif; 
        margin: 0;
        padding: 0;
    }
    
    .boleta { 
        border: 2px solid #000; 
        border-radius: 6px;
        width: 100%;
        background: #fff;
        overflow: hidden;
    }
    
    table {
        border-collapse: collapse;
        width: 100%;
        font-size: 9px;
    }
    
    td {
        vertical-align: top;
        box-sizing: border-box;
    }
    
    .header-table {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-bottom: 2px solid #000;
    }
    
    .header-table td {
        border-right: 2px solid #000;
    }
    
    .header-table td:last-child {
        border-right: none;
    }
    
    .empresa-cell {
        width: 50%;
        padding: 8px;
        text-align: center;
    }
    
    .info-cell {
        width: 50%;
        padding: 12px;
        text-align: right;
    }
    
    .salidas-box { 
        background: #000;
        color: #fff; 
        padding: 5px 12px; 
        font-weight: bold; 
        font-size: 9px;
        margin-bottom: 5px;
        border-radius: 15px;
        text-align: center;
        display: inline-block;
    }
    
    .numero-boleta { 
        color: #ff0000; 
        font-weight: bold; 
        font-size: 18px;
        text-align: right;
        margin-bottom: 8px;
    }
    
    .contacto-info { 
        font-size: 9px; 
        line-height: 1.4;
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
        padding: 2px 7px; 
        font-weight: bold;
        border-radius: 10px;
        display: inline-block;
        font-size: 8px;
    }
    
    .info-basica {
        background: linear-gradient(135deg, #f1f3f4 0%, #e8eaed 100%);
        border-bottom: 2px solid #000;
    }
    
    .info-basica td {
        border-right: 2px solid #000;
    }
    
    .info-basica td:last-child {
        border-right: none;
    }
    
    .info-item {
        padding: 6px 8px;
        font-size: 9px;
        font-weight: bold;
        text-align: center;
        color: #333;
    }
    
    .contenido-table {
        min-height: 120px;
    }
    
    .contenido-table td {
        border-right: 2px solid #000;
        border-bottom: 2px solid #000;
        padding: 6px 8px;
        background: #fafafa;
        vertical-align: top;
    }
    
    .contenido-table tr:last-child td {
        border-bottom: none;
    }
    
    .contenido-table td:last-child {
        border-right: none;
    }
    
    .campo-input {
        border: 1px solid #dee2e6;
        min-height: 18px;
        padding: 5px 6px;
        font-size: 10px;
        background: #fff;
        border-radius: 2px;
        color: #333;
    }
    
    .descripcion-input {
        min-height: 50px !important;
    }
    
    .advertencia-section {
        background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
        border-top: 2px solid #000;
        border-bottom: 2px solid #000;
        text-align: center;
        padding: 6px;
        font-size: 8px;
        line-height: 1.3;
    }
    
    .advertencia-destacado {
        color: #dc3545;
        font-weight: bold;
    }
    
    .pagos-table {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-top: 2px solid #000;
    }
    
    .pagos-table td {
        border-right: 2px solid #000;
    }
    
    .pagos-table td:last-child {
        border-right: none;
    }
    
    .pago-cell {
        width: 33.33%;
        padding: 10px 6px;
        text-align: center;
    }
    
    .pago-valor {
        font-weight: bold;
        font-size: 14px;
        color: #000;
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 3px;
        padding: 4px 6px;
    }
    
    .boleta-container {
        width: 100%;
        margin: 0 auto 10px auto;
    }
    
    .separador {
        border-top: 2px dashed #000;
        margin: 10px 0;
        position: relative;
        text-align: center;
    }
    
    .separador::after {
        content: "✂ CORTAR AQUÍ ✂";
        background: white;
        padding: 0 10px;
        position: relative;
        top: -9px;
        font-weight: bold;
        font-size: 9px;
        color: #666;
    }
</style>

<!-- PRIMERA BOLETA -->
<div class="boleta-container">
    '.$boleta_html.'
</div>

<!-- LÍNEA SEPARADORA -->
<div class="separador"></div>

<!-- SEGUNDA BOLETA -->
<div class="boleta-container">
    '.$boleta_html.'
</div>
';

$mpdf->WriteHTML($html);
$mpdf->Output('boleta_'.$boleta_nro.'.pdf', 'I');