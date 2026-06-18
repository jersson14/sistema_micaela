<?php
setlocale(LC_TIME, 'es_ES.UTF-8');
date_default_timezone_set('America/Lima');

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../conexion.php';

$id_encomienda = (int)$_GET['id'];

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
        <table class="header-table">
            <tr>
                <td class="empresa-cell">
                    <img src="../../../img/logito.png" alt="Logo">
                </td>
                
                <td class="info-cell">
                    <div class="salidas-box">SALIDAS DIARIAS</div>
                    <div class="numero-boleta">N° '.str_pad($boleta_nro, 6, "0", STR_PAD_LEFT).'</div>
                    
                    <div class="contacto-info">
                        <span class="direccion">■ PROLONG. HUANCAVELICA S/N</span> 
                        <span class="lugar-badge">ABANCAY</span><br>
                        <span class="telefono">☎ 983 152 885</span><br>
                        <span class="direccion">■ ALAMEDA PACHACUTEC</span> 
                        <span class="lugar-badge">CUSCO</span><br>
                        <span class="telefono">☎ 983 152 886</span>
                    </div>
                </td>
            </tr>
        </table>

        <table class="info-basica">
            <tr>
                <td style="width: 25%;">FECHA: '.$fecha_formateada.'</td>
                <td style="width: 25%;">HORA: '.$hora_formateada.'</td>
                <td style="width: 25%;">ORIG.: '.strtoupper($nombre_origen).'</td>
                <td style="width: 25%;">DEST.: '.strtoupper($nombre_destino).'</td>
            </tr>
        </table>

        <table class="contenido-table">             
            <tr>                 
                <td style="width: 50%;">                     
                    <span class="campo-label">Conductor:</span>                     
                    <div class="campo-input">'.strtoupper($conductor_nombre).'</div>                 
                </td>                 
                <td style="width: 50%;">                     
                    <span class="campo-label">Para:</span>                     
                    <div class="campo-input">'.strtoupper($receptor_nombre).'</div>                 
                </td>             
            </tr>             
            <tr>                 
                <td style="width: 50%;">                     
                    <span class="campo-label">Cel:</span>                     
                    <div class="campo-input">'.$conductor_celular.'</div>                 
                </td>                 
                <td style="width: 50%;">                     
                    <span class="campo-label">Cel:</span>                     
                    <div class="campo-input">'.$receptor_celular.'</div>                 
                </td>             
            </tr>             
            <tr>                              
                <td rowspan="3" style="width: 50%; vertical-align: top;">                     
                    <span class="campo-label">Descripción:</span>                     
                    <div class="campo-input descripcion-input">'.strtoupper($descripcion).'</div>                 
                </td>                 
                <td style="width: 50%;">                     
                    <span class="campo-label">DNI:</span>                     
                    <div class="campo-input">'.$receptor_doc.'</div>                 
                </td>             
            </tr>             
            <tr>                 
                <td style="width: 50%;">                     
                    <span class="campo-label">De parte:</span>                     
                    <div class="campo-input">'.strtoupper($emisor_nombre).'</div>                 
                </td>             
            </tr>             
            <tr>                 
                <td style="width: 50%;">                     
                    <span class="campo-label">Cel:</span>                     
                    <div class="campo-input">'.$emisor_celular.'</div>                 
                </td>             
            </tr>         
        </table>

        <div class="advertencia-section">
            SERVICIO PRIVADO CON RECOJO A DOMICILIO<br>
            <span class="advertencia-destacado">* SOLO 15 DÍAS SE GUARDA LAS ENCOMIENDAS</span><br>
            <b>PARA RECOJO MOSTRAR FOTO DE LA BOLETA</b>
        </div>

        <table class="pagos-table">
            <tr>
                <td style="width: 33.33%;">
                    <span class="pago-label" style="color: #0b6afaff;">Pago S/.</span>
                    <div class="pago-valor">'.number_format($pago, 2).'</div>
                </td>
                
                <td style="width: 33.33%;">
                    <span class="pago-label" style="color: #ff0000;">Por Pagar S/.</span>
                    <div class="pago-valor">'.number_format($por_pagar, 2).'</div>
                </td>
                
                <td style="width: 33.33%;">
                    <span class="pago-label" style="color: #ff0000;">A Domicilio S/.</span>
                    <div class="pago-valor">'.number_format($a_domicilio, 2).'</div>
                </td>
            </tr>
        </table>
    </div>';
}

// Hoja A4 VERTICAL con 2 boletas horizontales arriba
$mpdf = new \Mpdf\Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4',  // A4 Vertical
    'margin_left' => 10,
    'margin_right' => 10,
    'margin_top' => 10,
    'margin_bottom' => 10,
    'default_font' => 'Arial'
]);

$html = '
<style>
    body { 
        font-family: Arial, sans-serif; 
        margin: 0;
        padding: 0;
    }
    
    .contenedor-boletas {
        width: 100%;
    }
    
    .fila-boletas {
        width: 100%;
        display: table;
        table-layout: fixed;
        margin-bottom: 5mm;
    }
    
    .columna-boleta {
        display: table-cell;
        width: 50%;
        padding: 0 2mm;
        vertical-align: top;
    }
    
    .columna-boleta:first-child {
        padding-left: 0;
    }
    
    .columna-boleta:last-child {
        padding-right: 0;
    }
    
    .boleta { 
        border: 2px solid #000; 
        border-radius: 4px;
        width: 100%;
        background: #fff;
        overflow: hidden;
    }
    
    table {
        border-collapse: collapse;
        width: 100%;
        font-size: 7px;
    }
    
    td {
        vertical-align: top;
        box-sizing: border-box;
    }
    
    .header-table {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-bottom: 1.5px solid #000;
    }
    
    .header-table td {
        border-right: 1.5px solid #000;
    }
    
    .header-table td:last-child {
        border-right: none;
    }
    
    .empresa-cell {
        width: 50%;
        padding: 3px;
        text-align: center;
    }
    
    .empresa-cell img {
        width: 100%;
        max-width: 80px;
        height: auto;
    }
    
    .info-cell {
        width: 50%;
        padding: 4px;
        text-align: right;
    }
    
    .salidas-box { 
        background: #000;
        color: #fff; 
        padding: 2px 6px; 
        font-weight: bold; 
        font-size: 6px;
        margin-bottom: 3px;
        border-radius: 8px;
        text-align: center;
        display: inline-block;
    }
    
    .numero-boleta { 
        color: #ff0000; 
        font-weight: bold; 
        font-size: 11px;
        text-align: right;
        margin-bottom: 3px;
    }
    
    .contacto-info { 
        font-size: 5.5px; 
        line-height: 1.3;
        text-align: right;
    }
    
    .contacto-info .direccion {
        font-weight: bold;
    }
    
    .contacto-info .telefono {
        font-weight: bold;
        color: #ff0000;
    }
    
    .lugar-badge {
        background: #ff0000;
        color: #fff; 
        padding: 1px 3px; 
        font-weight: bold;
        border-radius: 6px;
        font-size: 5px;
    }
    
    .info-basica {
        background: linear-gradient(135deg, #f1f3f4 0%, #e8eaed 100%);
        border-bottom: 1.5px solid #000;
    }
    
    .info-basica td {
        border-right: 1.5px solid #000;
        padding: 3px 4px;
        font-size: 6.5px;
        font-weight: bold;
        text-align: center;
    }
    
    .info-basica td:last-child {
        border-right: none;
    }
    
    .contenido-table td {
        border-right: 1.5px solid #000;
        border-bottom: 1.5px solid #000;
        padding: 3px 4px;
        background: #fafafa;
        font-size: 6.5px;
    }
    
    .contenido-table tr:last-child td {
        border-bottom: none;
    }
    
    .contenido-table td:last-child {
        border-right: none;
    }
    
    .campo-label {
        font-weight: bold;
        color: #ff0000;
        font-size: 6.5px;
        display: block;
        margin-bottom: 1px;
        text-transform: uppercase;
    }
    
    .campo-input {
        border: 1px solid #dee2e6;
        min-height: 12px;
        padding: 2px 3px;
        font-size: 6.5px;
        background: #fff;
        border-radius: 2px;
    }
    
    .descripcion-input {
        min-height: 28px !important;
    }
    
    .advertencia-section {
        background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
        border-top: 1.5px solid #000;
        border-bottom: 1.5px solid #000;
        text-align: center;
        padding: 3px;
        font-size: 5px;
        line-height: 1.2;
    }
    
    .advertencia-destacado {
        color: #dc3545;
        font-weight: bold;
    }
    
    .pagos-table {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-top: 1.5px solid #000;
    }
    
    .pagos-table td {
        border-right: 1.5px solid #000;
        padding: 4px 3px;
        text-align: center;
    }
    
    .pagos-table td:last-child {
        border-right: none;
    }
    
    .pago-label {
        font-weight: bold;
        font-size: 6.5px;
        margin-bottom: 2px;
        text-transform: uppercase;
        display: block;
    }
    
    .pago-valor {
        font-weight: bold;
        font-size: 8px;
        color: #000;
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 2px;
        padding: 2px 3px;
    }
</style>

<div class="contenedor-boletas">
    <div class="fila-boletas">
        <div class="columna-boleta">
            '.$boleta_html.'
        </div>
        <div class="columna-boleta">
            '.$boleta_html.'
        </div>
    </div>
</div>
';

$mpdf->WriteHTML($html);
$mpdf->Output('boleta_'.$boleta_nro.'.pdf', 'I');