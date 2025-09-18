<?php
setlocale(LC_TIME, 'es_ES.UTF-8');
date_default_timezone_set('America/Lima');

require_once __DIR__ . '/../vendor/autoload.php';
require_once '../conexion.php';
$mysqli->set_charset("utf8");

$id_encomienda = $mysqli->real_escape_string($_GET['id']);

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
WHERE encomiendas.id_encomienda = '$id_encomienda'";

$resultado = $mysqli->query($query);

$html = '';

if ($resultado->num_rows > 0) {
    $row = $resultado->fetch_assoc();

    // Datos principales
    $boleta_nro = $row['boleta_nro'];
    $fecha_formateada = $row['fecha_formateada'];
    $hora_formateada = $row['hora_formateada'];
    $descripcion = $row['descripcion'];
    $nombre_origen = $row['nombre_origen'];
    $nombre_destino = $row['nombre_destino'];
    $pago = $row['pago'];
    $por_pagar = $row['por_pagar'];
    $a_domicilio = $row['a_domicilio'];
    
    // Datos del emisor
    $emisor_nombre = $row['nombre_emisor'];
    $emisor_doc = $row['nro_doc_emisor'];
    $emisor_celular = $row['celular_emisor'];
    
    // Datos del receptor
    $receptor_nombre = $row['nombre_receptor'];
    $receptor_doc = $row['nro_doc_receptor'];
    $receptor_celular = $row['celular_receptor'];
    
    // Datos del conductor
    $conductor_nombre = $row['nombres_apellidos'];
    $conductor_celular = $row['celular_chofer'];

    $html = '
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.2;
        }
        
        .boleta {
            width: 100%;
            height: 100%;
            border: 2px solid #000;
            border-radius: 8px;
        }
        
        .header {
            height: 45px;
            border-bottom: 1px solid #000;
            position: relative;
        }
        
        .header-left {
            position: absolute;
            left: 5px;
            top: 5px;
            width: 45%;
        }
        
        .empresa-transportes {
            font-size: 8px;
            font-weight: bold;
            background: #000;
            color: white;
            padding: 2px 4px;
            display: inline-block;
        }
        
        .logo-section {
            margin-top: 2px;
        }
        
        .tours-text {
            font-size: 18px;
            font-weight: bold;
            display: inline;
        }
        
        .micaela-text {
            font-size: 18px;
            font-weight: bold;
            background: #ff0000;
            color: white;
            padding: 2px 8px;
            display: inline;
            margin-left: 2px;
        }
        
        .slogan {
            font-size: 7px;
            font-style: italic;
            margin-top: 1px;
        }
        
        .header-right {
            position: absolute;
            right: 5px;
            top: 5px;
            width: 52%;
            text-align: right;
        }
        
        .salidas-diarias {
            background: #000;
            color: white;
            font-size: 8px;
            font-weight: bold;
            padding: 2px 8px;
            display: inline-block;
            margin-bottom: 2px;
        }
        
        .numero-boleta {
            font-size: 14px;
            font-weight: bold;
            color: #ff0000;
            margin-bottom: 2px;
        }
        
        .contacto-info {
            font-size: 7px;
            line-height: 1.1;
        }
        
        .contacto-lugar {
            color: white;
            background: #ff0000;
            padding: 1px 3px;
            font-weight: bold;
            display: inline;
        }
        
        .info-superior {
            height: 20px;
            border-bottom: 1px solid #000;
            padding: 4px;
            display: table;
            width: 100%;
        }
        
        .info-cell {
            display: table-cell;
            width: 25%;
            font-size: 9px;
            font-weight: bold;
            vertical-align: middle;
        }
        
        .contenido-principal {
            height: 120px;
            padding: 5px;
            display: table;
            width: 100%;
        }
        
        .columna-izquierda {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 5px;
        }
        
        .columna-derecha {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-left: 5px;
        }
        
        .campo {
            margin-bottom: 8px;
        }
        
        .etiqueta {
            color: #ff0000;
            font-weight: bold;
            font-size: 8px;
            display: block;
            margin-bottom: 1px;
        }
        
        .valor {
            border: 1px solid #000;
            padding: 3px;
            min-height: 16px;
            font-size: 9px;
            background: white;
        }
        
        .valor-alto {
            min-height: 45px;
        }
        
        .advertencia {
            height: 25px;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 3px;
            text-align: center;
            font-size: 7px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .texto-rojo {
            color: #ff0000;
            font-weight: bold;
        }
        
        .footer-pagos {
            height: 25px;
            border-top: 1px solid #000;
            display: table;
            width: 100%;
        }
        
        .celda-pago {
            display: table-cell;
            text-align: center;
            border-right: 1px solid #000;
            vertical-align: middle;
            padding: 2px;
        }
        
        .celda-pago:last-child {
            border-right: none;
        }
        
        .etiqueta-pago {
            color: #ff0000;
            font-weight: bold;
            font-size: 8px;
            display: block;
        }
        
        .valor-pago {
            font-size: 10px;
            font-weight: bold;
        }
    </style>

    <div class="boleta">
        <!-- HEADER -->
        <div class="header">
            <div class="header-left">
                <div class="empresa-transportes">EMPRESA DE TRANSPORTES</div>
                <div class="logo-section">
                    <span class="tours-text">TOURS</span>
                    <span class="micaela-text">MICAELA</span>
                </div>
                <div class="slogan">LLEGAMOS A TU FELICIDAD</div>
            </div>
            
            <div class="header-right">
                <div class="salidas-diarias">SALIDAS DIARIAS</div>
                <div class="numero-boleta">N° ' . str_pad($boleta_nro, 6, "0", STR_PAD_LEFT) . '</div>
                
                <div class="contacto-info">
                    <div>🏠 PROLONGACIÓN HUANCAVELICA S/N <span class="contacto-lugar">ABANCAY:</span></div>
                    <div style="margin-left: 10px;">📞 983 152 885</div>
                    <div style="margin-top: 1px;">🏠 ALAMEDA PACHACUTEC (Frente al C.C. Confraternidad) <span class="contacto-lugar">CUSCO:</span></div>
                    <div style="margin-left: 10px;">📞 983 152 886</div>
                </div>
            </div>
        </div>

        <!-- INFO SUPERIOR -->
        <div class="info-superior">
            <div class="info-cell">FECHA: ' . $fecha_formateada . '</div>
            <div class="info-cell">HORA: ' . $hora_formateada . '</div>
            <div class="info-cell">ORIG.: ' . mb_strtoupper($nombre_origen, 'UTF-8') . '</div>
            <div class="info-cell">DEST.: ' . mb_strtoupper($nombre_destino, 'UTF-8') . '</div>
        </div>

        <!-- CONTENIDO PRINCIPAL -->
        <div class="contenido-principal">
            <div class="columna-izquierda">
                <div class="campo">
                    <span class="etiqueta">CONDUCTOR:</span>
                    <div class="valor">' . mb_strtoupper($conductor_nombre, 'UTF-8') . '</div>
                </div>
                
                <div class="campo">
                    <span class="etiqueta">CEL:</span>
                    <div class="valor">' . $conductor_celular . '</div>
                </div>
                
                <div class="campo">
                    <span class="etiqueta">DESCRIPCIÓN:</span>
                    <div class="valor valor-alto">' . mb_strtoupper($descripcion, 'UTF-8') . '</div>
                </div>
            </div>
            
            <div class="columna-derecha">
                <div class="campo">
                    <span class="etiqueta">PARA:</span>
                    <div class="valor">' . mb_strtoupper($receptor_nombre, 'UTF-8') . '</div>
                </div>
                
                <div class="campo">
                    <span class="etiqueta">CEL:</span>
                    <div class="valor">' . $receptor_celular . '</div>
                </div>
                
                <div class="campo">
                    <span class="etiqueta">DNI:</span>
                    <div class="valor">' . $receptor_doc . '</div>
                </div>
                
                <div class="campo">
                    <span class="etiqueta">DE PARTE:</span>
                    <div class="valor">' . mb_strtoupper($emisor_nombre, 'UTF-8') . '</div>
                </div>
                
                <div class="campo">
                    <span class="etiqueta">CEL:</span>
                    <div class="valor">' . $emisor_celular . '</div>
                </div>
            </div>
        </div>

        <!-- ADVERTENCIA -->
        <div class="advertencia">
            <div>BRINDAMOS SERVICIO PRIVADO CON RECOJO A DOMICILIO</div>
            <div class="texto-rojo">SOLO 15 DÍAS SE GUARDA LAS ENCOMIENDAS NO NOS HACEMOS RESPONSABLES DE PERDIDA.</div>
        </div>

        <!-- FOOTER DE PAGOS -->
        <div class="footer-pagos">
            <div class="celda-pago">
                <span class="etiqueta-pago">PAGO S/.</span>
                <div class="valor-pago">' . number_format($pago, 2) . '</div>
            </div>
            <div class="celda-pago">
                <span class="etiqueta-pago">POR PAGAR S/.</span>
                <div class="valor-pago">' . number_format($por_pagar, 2) . '</div>
            </div>
            <div class="celda-pago">
                <span class="etiqueta-pago">A DOMICILIO S/.</span>
                <div class="valor-pago">' . ($a_domicilio == 'SI' ? 'SÍ' : 'NO') . '</div>
            </div>
        </div>
    </div>';

} else {
    $html = '<div style="text-align: center; padding: 50px; color: #e74c3c;">
                <h3>Encomienda no encontrada</h3>
                <p>Verifique el ID de la encomienda.</p>
             </div>';
}

// Configuración del PDF - Medidas exactas: 10.5 cm x 21.5 cm
$mpdf = new \Mpdf\Mpdf([
    'mode' => 'utf-8',
    'format' => [105, 215], // 10.5cm x 21.5cm en mm
    'orientation' => 'P', // Portrait
    'margin_left' => 3,
    'margin_right' => 3,
    'margin_top' => 3,
    'margin_bottom' => 3,
    'default_font' => 'Arial'
]);

$mpdf->WriteHTML($html);
$mpdf->Output('boleta_encomienda_' . str_pad($boleta_nro, 6, "0", STR_PAD_LEFT) . '.pdf', 'I');
?>