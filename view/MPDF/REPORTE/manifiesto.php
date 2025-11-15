<?php
setlocale(LC_TIME, 'es_ES.UTF-8');
date_default_timezone_set('America/Lima');

require_once __DIR__ . '/../vendor/autoload.php';
require_once '../conexion.php';
$mysqli->set_charset("utf8");

$id_salida = $mysqli->real_escape_string($_GET['id']);

// Consulta para obtener datos de la salida y conductor
$query_salida = "SELECT
    s.id_salidas_diarias, 
    s.salida_nro, 
    s.id_conductor, 
    s.monto, 
    s.fecha_hora, 
    DATE_FORMAT(s.fecha_hora, \"%d-%m-%Y\") AS fecha_formateada_salida,
    DATE_FORMAT(s.fecha_hora, \"%H:%i:%s\") AS hora_formateada_salida, 
    s.id_origen, 
    s.id_destino, 
    s.total_pasajeros, 
    s.total_encomiendas, 
    s.created_at, 
    DATE_FORMAT(s.created_at, \"%d-%m-%Y - %H:%i:%s\") AS fecha_formateada_creado, 
    s.updated_at, 
    DATE_FORMAT(s.updated_at, \"%d-%m-%Y - %H:%i:%s\") AS fecha_formateada_actualizado, 
    s.observacion, 
    s.id_usuario, 
    s.estado, 
    r_origen.nombre AS origen_nombre, 
    r_origen.descripcion AS origen_descripcion, 
    r_destino.nombre AS destino_nombre, 
    r_destino.descripcion AS destino_descripcion, 
    c.tipo_documen, 
    c.nro_doc, 
    c.nombres_apellidos, 
    c.celular, 
    u.dni_usuario, 
    CONCAT(u.usu_nombre, ' ', u.usu_apellido) AS usuario_nombre_completo, 
    c.placa_vehiculo, 
    c.nro_licencia, 
    c.marca_vehiculo
FROM
    salidas_diarias AS s
    INNER JOIN
    rutas AS r_origen
    ON 
        s.id_origen = r_origen.idrutas
    INNER JOIN
    rutas AS r_destino
    ON 
        s.id_destino = r_destino.idrutas
    INNER JOIN
    choferes AS c
    ON 
        s.id_conductor = c.id_chofer
    INNER JOIN
    usuario AS u
    ON 
        s.id_usuario = u.id_usuario   
WHERE s.id_salidas_diarias = '$id_salida'";

// Consulta para obtener los pasajeros
$query_pasajeros = "SELECT
    salida_cliente.id_cliente_salida, 
    salida_cliente.idsalida, 
    salida_cliente.idcliente, 
    salida_cliente.created_at, 
    salida_cliente.updated_at, 
    salida_cliente.observacion, 
    clientes.tipo_documento, 
    clientes.nro_documento, 
    clientes.nombre_completo, 
    clientes.procedencia, 
    clientes.edad, 
    clientes.celular, 
    clientes.direccion
FROM
    salidas_diarias
    INNER JOIN
    salida_cliente
    ON 
        salida_cliente.idsalida = salidas_diarias.id_salidas_diarias
    INNER JOIN
    clientes
    ON 
        clientes.id_cliente = salida_cliente.idcliente
WHERE salida_cliente.idsalida = '$id_salida'
ORDER BY salida_cliente.id_cliente_salida";

$resultado_salida = $mysqli->query($query_salida);
$resultado_pasajeros = $mysqli->query($query_pasajeros);

$html = '';
if ($resultado_salida->num_rows > 0) {
    $salida = $resultado_salida->fetch_assoc();
    
    // Datos de la salida
    $salida_nro = $salida['salida_nro'];
    $fecha_salida = $salida['fecha_formateada_salida'];
    $hora_salida = $salida['hora_formateada_salida'];
    $origen = $salida['origen_nombre'];
    $destino = $salida['destino_nombre'];
    $conductor_nombre = $salida['nombres_apellidos'];
    $conductor_doc = $salida['nro_doc'];
    $conductor_licencia = $salida['nro_licencia'];
    $placa_vehiculo = $salida['placa_vehiculo'];
    $marca_vehiculo = $salida['marca_vehiculo'];
    $conductor_celular = $salida['celular'];
    
    // Obtener lista de pasajeros
    $pasajeros = array();
    $contador = 1;
    while($pasajero = $resultado_pasajeros->fetch_assoc()) {
        $pasajeros[] = array(
            'numero' => $contador,
            'nombre' => $pasajero['nombre_completo'],
            'dni' => $pasajero['nro_documento'],
            'edad' => $pasajero['edad'],
            'telefono' => $pasajero['celular']
        );
        $contador++;
    }
    
    // Completar hasta 6 filas vacías si hay menos pasajeros
    while($contador <= 6) {
        $pasajeros[] = array(
            'numero' => $contador,
            'nombre' => '',
            'dni' => '',
            'edad' => '',
            'telefono' => ''
        );
        $contador++;
    }

    $html = '
    <style>
        @import url("https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css");

        body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            padding: 0; 
        }

        .page-container {
            width: 100%;
            padding: 8mm 10mm 0 10mm;
            box-sizing: border-box;
        }

        .manifiesto { 
            border: 3px solid #000;
            border-radius: 10px;
            width: 100%;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            overflow: hidden;
            padding: 10px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
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
        
        /* Header */
        .header-table {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-bottom: 2px solid #000;
            border-collapse: collapse;
            width: 100%;
        }
        
        .header-left {
            width: 50%;
            padding: 10px;
            border-right: 2px solid #000;
            text-align: center;
        }
        
        .empresa-titulo { 
            font-weight: bold; 
            font-size: 10px; 
            color: #000;
            margin-bottom: 4px;
            letter-spacing: 0.5px;
        }
        
        .logo-container {
            margin: 4px 0;
        }
        
        .logo-autos {
            color: #000;
            padding: 4px 8px;
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 2px;
            display: inline-block;
        }
        
        .ruta-text {
            font-size: 10px;
            font-weight: bold;
            color: #333;
        }
        
        .header-right {
            width: 50%;
            padding: 10px;
            text-align: center;
        }
        
        .manifiesto-title {
            background: #000;
            color: #fff;
            padding: 4px 12px;
            border-radius: 15px;
            font-weight: bold;
            font-size: 10px;
            margin-bottom: 4px;
            display: inline-block;
        }
        
        .numero-manifiesto {
            font-size: 22px;
            font-weight: bold;
            color: #ff0000;
            margin-bottom: 4px;
            letter-spacing: 1px;
        }
        
        .direcciones-info {
            font-size: 7.5px;
            line-height: 1.4;
            text-align: left;
            padding: 0 5px;
        }
        
        .lugar-info {
            font-weight: bold;
            margin-bottom: 2px;
            font-size: 7.5px;
        }
        
        /* Información básica */
        .info-basica {
            border-bottom: 2px solid #000;
            border-collapse: collapse;
            width: 100%;
        }
        
        .info-basica td {
            border-right: 2px solid #000;
        }
        
        .info-basica td:last-child {
            border-right: 2px solid #000;
        }
        
        .info-item {
            font-weight: bold;
            font-size: 9px;
            padding: 6px 8px;
        }
        
        .info-label {
            color: #666;
            font-size: 8px;
        }
        
        .info-value {
            color: #000;
            font-size: 9px;
        }
        
        /* Tabla de pasajeros */
        .pasajeros-table {
            border-collapse: collapse;
            width: 100%;
            font-size: 9px;
            flex: 1;
        }
        
        .pasajeros-header {
            background: #000;
            color: #fff;
            font-weight: bold;
            text-align: center;
            padding: 6px 3px;
            border: 2px solid #000;
            font-size: 9px;
        }
        
        .pasajeros-row {
            border-bottom: 1px solid #000;
            height: 24px;
        }
        
        .pasajeros-cell {
            border-right: 1px solid #000;
            padding: 5px 3px;
            text-align: center;
            vertical-align: middle;
            font-size: 8.5px;
        }
        
        .pasajeros-cell:last-child {
            border-right: 2px solid #000;
        }
        
        .numero-cell {
            width: 8%;
            background: #f0f0f0;
            font-weight: bold;
        }
        
        .nombre-cell {
            width: 50%;
            text-align: left;
            padding-left: 8px;
        }
        
        .dni-cell {
            width: 18%;
        }
        
        .edad-cell {
            width: 10%;
        }
        
        .telefono-cell {
            width: 14%;
        }
        
        /* Footer */
        .footer-table {
            border-top: 2px solid #000;
            border-collapse: collapse;
            width: 100%;
            background: #f8f9fa;
        }
        
        .footer-table td:first-child {
            border-right: 2px solid #000;
        }
        
        .footer-table td:last-child {
            border-right: none;
        }
        
        .firma-cell {
            text-align: center;
            padding: 18px 10px 8px 10px;
            height: 65px;
            vertical-align: bottom;
        }
        
        .firma-label {
            font-weight: bold;
            font-size: 8.5px;
            color: #333;
            border-top: 2px solid #000;
            padding-top: 3px;
            display: inline-block;
            width: 85%;
        }
    </style>
    
    <div class="page-container">
        <div class="manifiesto">
            <!-- Header -->
            <table class="header-table">
                <tr>
                    <td class="header-left">
                        <div class="empresa-titulo">EMPRESA DE TRANSPORTES</div>
                        <div class="logo-container">
                            <img src="../../../img/logito.png" alt="Logo Empresa" style="max-height: 50px; margin: 2px 0;">
                            <div class="logo-autos">AUTOS</div>
                            <div class="ruta-text">ABANCAY - CUSCO</div>
                        </div>
                    </td>
                    
                    <td class="header-right">
                        <div class="manifiesto-title">MANIFIESTO DE PASAJEROS</div>
                        <div class="numero-manifiesto">N° S-S-'.str_pad($salida_nro, 7, "0", STR_PAD_LEFT).'</div>
                        
                        <div class="direcciones-info">
                            <div class="lugar-info">ABANCAY: PROLONGACIÓN HUANCAVELICA S/N</div>
                            <div class="lugar-info">CUSCO: ALAMEDA PACHACUTEC (Frente al Centro Comercial Confraternidad)</div>
                        </div>
                    </td>
                </tr>
            </table>

            <!-- Información básica -->
            <table class="info-basica">
                <tr>
                    <td class="info-item" style="width: 50%; text-align: center; border-right: 2px solid #000;">
                        <span class="info-label">Origen:</span> <span class="info-value">'.strtoupper($origen).'</span>
                    </td>
                    <td class="info-item" style="width: 50%; text-align: center;">
                        <span class="info-label">Destino:</span> <span class="info-value">'.strtoupper($destino).'</span>
                    </td>
                </tr>
                <tr>
                    <td class="info-item" style="width: 25%; text-align: center; border-right: 2px solid #000;">
                        <span class="info-label">Placa de Vehículo:</span> <span class="info-value">'.$placa_vehiculo.'</span>
                    </td>
                    <td class="info-item" style="width: 25%; text-align: center; border-right: 2px solid #000;">
                        <span class="info-label">Marca:</span> <span class="info-value">'.$marca_vehiculo.'</span>
                    </td>
                    <td class="info-item" style="width: 25%; text-align: center; border-right: 2px solid #000;">
                        <span class="info-label">Fecha:</span> <span class="info-value">'.$fecha_salida.'</span>
                    </td>
                    <td class="info-item" style="width: 25%; text-align: center;">
                        <span class="info-label">Hora:</span> <span class="info-value">'.$hora_salida.'</span>
                    </td>
                </tr>
                <tr>
                    <td class="info-item" style="width: 50%; text-align: center; border-right: 2px solid #000;">
                        <span class="info-label">Conductor:</span> <span class="info-value">'.strtoupper($conductor_nombre).'</span>
                    </td>
                    <td class="info-item" style="width: 50%; text-align: center;">
                        <span class="info-label">N° Licencia:</span> <span class="info-value">'.$conductor_licencia.'</span>
                    </td>
                </tr>
            </table>

            <!-- Tabla de pasajeros -->
            <table class="pasajeros-table">
                <tr>
                    <td class="pasajeros-header numero-cell">N°</td>
                    <td class="pasajeros-header nombre-cell">NOMBRES Y APELLIDOS</td>
                    <td class="pasajeros-header dni-cell">DNI</td>
                    <td class="pasajeros-header edad-cell">EDAD</td>
                    <td class="pasajeros-header telefono-cell">TELÉFONO</td>
                </tr>';
            
    // Agregar filas de pasajeros
    foreach($pasajeros as $pasajero) {
        $html .= '
                <tr class="pasajeros-row">
                    <td class="pasajeros-cell numero-cell">'.$pasajero['numero'].'</td>
                    <td class="pasajeros-cell nombre-cell">'.strtoupper($pasajero['nombre']).'</td>
                    <td class="pasajeros-cell dni-cell">'.$pasajero['dni'].'</td>
                    <td class="pasajeros-cell edad-cell">'.$pasajero['edad'].'</td>
                    <td class="pasajeros-cell telefono-cell">'.$pasajero['telefono'].'</td>
                </tr>';
    }
            
    $html .= '
            </table>

            <!-- Footer con firmas -->
            <table class="footer-table">
                <tr>
                    <td class="firma-cell" style="width: 50%;">___________________________________________
                        <div class=""><b>FIRMA CHOFER</b></div>
                    </td>
                    
                    <td class="firma-cell" style="width: 50%;">___________________________________________
                        <div class=""><b>ENCARGADO</b></div>
                    </td>
                </tr>
            </table>
        </div>
    </div>';
}

$mpdf = new \Mpdf\Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4',
    'margin_left' => 0,
    'margin_right' => 0,
    'margin_top' => 0,
    'margin_bottom' => 0,
    'default_font' => 'Arial'
]);
$mpdf->WriteHTML($html);
$mpdf->Output('manifiesto_'.$salida_nro.'.pdf', 'I');
?>