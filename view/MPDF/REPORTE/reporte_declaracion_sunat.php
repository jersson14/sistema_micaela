<?php
// ============================================================
// ARCHIVO: view/MPDF/REPORTE/reporte_declaracion_sunat.php
// REPORTE PROFESIONAL PARA DECLARACIONES SUNAT
// ============================================================

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../conexion.php';

use Mpdf\Mpdf;

try {
    // ============================================================
    // 1. OBTENER PARÁMETROS
    // ============================================================
    $tipo = isset($_GET['tipo']) ? $_GET['tipo'] : '';
    $estado = isset($_GET['estado']) ? $_GET['estado'] : '';
    $fecha_desde = isset($_GET['fecha_desde']) ? $_GET['fecha_desde'] : '';
    $fecha_hasta = isset($_GET['fecha_hasta']) ? $_GET['fecha_hasta'] : '';

    error_log("=== REPORTE SUNAT ===");
    error_log("Tipo: $tipo | Estado: $estado | Desde: $fecha_desde | Hasta: $fecha_hasta");

    // ============================================================
    // 2. CONSTRUIR QUERY
    // ============================================================
    $sql = "SELECT 
                c.id_comprobante,
                c.tipo_comprobante,
                CASE c.tipo_comprobante
                    WHEN '01' THEN 'FACTURA'
                    WHEN '03' THEN 'BOLETA'
                    WHEN '07' THEN 'N/C'
                    WHEN '08' THEN 'N/D'
                    ELSE 'OTROS'
                END AS tipo_documento_nombre,
                c.serie,
                c.correlativo,
                CONCAT(c.serie, '-', LPAD(c.correlativo, 8, '0')) AS numero_comprobante,
                DATE_FORMAT(c.fecha_emision, '%d/%m/%Y') AS fecha_emision,
                c.moneda,
                cl.tipo_documento AS tipo_doc_cliente,
                cl.numero_documento AS numero_doc_cliente,
                cl.razon_social AS cliente_nombre,
                FORMAT(IFNULL(c.total_gravada, 0), 2) AS total_gravada,
                FORMAT(IFNULL(c.total_exonerada, 0), 2) AS total_exonerada,
                FORMAT(IFNULL(c.total_inafecta, 0), 2) AS total_inafecta,
                FORMAT(IFNULL(c.total_igv, 0), 2) AS total_igv,
                FORMAT(IFNULL(c.total, 0), 2) AS total,
                c.estado_sunat,
                SUBSTRING(c.hash_cdr, 1, 10) AS hash_cdr
            FROM comprobantes c
            INNER JOIN cliente_sunat cl ON c.id_cliente = cl.id_cliente
            WHERE 1=1";

    $params = array();

    if (!empty($tipo)) {
        $sql .= " AND c.tipo_comprobante = ?";
        $params[] = $tipo;
    }

    if (!empty($estado)) {
        $sql .= " AND UPPER(c.estado_sunat) LIKE ?";
        $params[] = strtoupper($estado) . '%';
    } else {
        $sql .= " AND (UPPER(c.estado_sunat) LIKE 'ENVIADO%' OR UPPER(c.estado_sunat) LIKE 'ACEPTADO%')";
    }

    if (!empty($fecha_desde)) {
        $partes = explode('/', $fecha_desde);
        if (count($partes) == 3) {
            $fecha_desde_sql = $partes[2] . '-' . $partes[1] . '-' . $partes[0];
            $sql .= " AND DATE(c.fecha_emision) >= ?";
            $params[] = $fecha_desde_sql;
        }
    }

    if (!empty($fecha_hasta)) {
        $partes = explode('/', $fecha_hasta);
        if (count($partes) == 3) {
            $fecha_hasta_sql = $partes[2] . '-' . $partes[1] . '-' . $partes[0];
            $sql .= " AND DATE(c.fecha_emision) <= ?";
            $params[] = $fecha_hasta_sql;
        }
    }

    $sql .= " ORDER BY c.fecha_emision ASC, c.serie ASC, c.correlativo ASC";

    error_log("SQL: " . $sql);

    // ============================================================
    // 3. EJECUTAR QUERY
    // ============================================================
    $stmt = $conexion->prepare($sql);
    
    foreach ($params as $key => $value) {
        $stmt->bindValue($key + 1, $value);
    }
    
    $stmt->execute();
    $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    error_log("Registros encontrados: " . count($datos));

    if (empty($datos)) {
        throw new Exception("No se encontraron registros para los filtros seleccionados");
    }

    // ============================================================
    // 4. CONFIGURAR PDF
    // ============================================================
    $mpdf = new Mpdf([
        'mode' => 'utf-8',
        'format' => 'A4-L',
        'margin_left' => 12,
        'margin_right' => 12,
        'margin_top' => 45,
        'margin_bottom' => 25,
        'margin_header' => 12,
        'margin_footer' => 12
    ]);

    // DATOS EMPRESA
    $empresa_nombre = "ETTOM S.A.";
    $empresa_ruc = "20603540647";
    $empresa_direccion = "PRO. HUANCAVELICA S/N - Abancay - Apurímac, Perú";

    // Determinar periodo
    $periodo_texto = "";
    if (!empty($fecha_desde) && !empty($fecha_hasta)) {
        $periodo_texto = "PERÍODO: " . strtoupper($fecha_desde) . " AL " . strtoupper($fecha_hasta);
        
        // Calcular mes/año para el título
        $partes_desde = explode('/', $fecha_desde);
        if (count($partes_desde) == 3) {
            $meses = ['', 'ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 
                      'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE'];
            $mes = (int)$partes_desde[1];
            $anio = $partes_desde[2];
            $periodo_texto = strtoupper($meses[$mes]) . " " . $anio;
        }
    }

    // ============================================================
    // 5. HEADER PROFESIONAL
    // ============================================================
    $header = '
    <table width="100%" style="border-bottom: 3px solid #1a5490;">
        <tr>

            <td width="50%" style="text-align:left; vertical-align:middle; padding-left:15px;">
                <h2 style="color:#1a5490; margin:0; font-size:18px; font-weight:bold;">' . $empresa_nombre . '</h2>
                <p style="margin:2px 0; font-size:10px; color:#666;">RUC: ' . $empresa_ruc . '</p>
                <p style="margin:2px 0; font-size:9px; color:#666;">' . $empresa_direccion . '</p>
            </td>
            <td width="35%" style="text-align:right; vertical-align:middle;">
                <div style="background:linear-gradient(135deg, #1a5490 0%, #2980b9 100%); 
                            padding:12px 15px; border-radius:8px; color:white;">
                    <h3 style="margin:0; font-size:14px; font-weight:bold;">REPORTE TRIBUTARIO</h3>
                    <p style="margin:3px 0 0 0; font-size:10px; opacity:0.9;">Declaración SUNAT</p>
                    <p style="margin:2px 0 0 0; font-size:8px; opacity:0.8;">' . date('d/m/Y H:i') . '</p>
                </div>
            </td>
        </tr>
    </table>
    
    <div style="background:#f8f9fa; padding:8px 15px; margin-top:8px; border-left:4px solid #28a745; border-radius:4px;">
        <table width="100%">
            <tr>
                <td width="70%" style="font-size:11px; color:#333; font-weight:bold;">
                    ' . $periodo_texto . '
                </td>
                <td width="30%" style="text-align:right; font-size:9px; color:#666;">
                    Total Registros: <strong style="color:#1a5490;">' . count($datos) . '</strong>
                </td>
            </tr>
        </table>
    </div>';

    // ============================================================
    // 6. FOOTER PROFESIONAL
    // ============================================================
    $footer = '
    <div style="border-top: 2px solid #1a5490; padding-top:8px; margin-top:10px;">
        <table width="100%" style="font-size:8px; color:#666;">
            <tr>
                <td width="33%" style="text-align:left;">
                    <strong style="color:#1a5490;">Sistema de Facturación Electrónica</strong><br>
                    Generado: {DATE d/m/Y H:i}
                </td>
                <td width="34%" style="text-align:center;">
                    <strong style="color:#e74c3c;">DOCUMENTO AUXILIAR - NO VÁLIDO PARA DECLARAR</strong><br>
                    Declaración oficial en: <strong>www.sunat.gob.pe</strong>
                </td>
                <td width="33%" style="text-align:right;">
                    <strong>Página {PAGENO} de {nbpg}</strong><br>
                    ' . $empresa_ruc . '
                </td>
            </tr>
        </table>
    </div>';

    $mpdf->SetHTMLHeader($header);
    $mpdf->SetHTMLFooter($footer);

    // ============================================================
    // 7. ESTILOS PROFESIONALES
    // ============================================================
    $html = '
    <style>
        body { 
            font-family: "Helvetica", Arial, sans-serif; 
            font-size: 8px; 
            color: #2c3e50;
        }
        
        .info-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .info-box h3 {
            margin: 0 0 5px 0;
            font-size: 12px;
            font-weight: bold;
        }
        
        .info-box p {
            margin: 2px 0;
            font-size: 9px;
            opacity: 0.95;
        }
        
        table.data { 
            border-collapse: collapse; 
            width: 100%; 
            margin-bottom: 15px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.12);
        }
        
        table.data th { 
            background: linear-gradient(to bottom, #34495e 0%, #2c3e50 100%);
            color: white; 
            padding: 8px 5px; 
            font-size: 7px; 
            text-align: center;
            border: 1px solid #2c3e50;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        table.data td { 
            padding: 6px 4px; 
            border: 1px solid #dfe6e9; 
            font-size: 7.5px;
            background: white;
        }
        
        table.data tr:nth-child(even) td { 
            background-color: #f8f9fa; 
        }
        
        table.data tr:hover td {
            background-color: #e3f2fd;
        }
        
        .text-right { text-align: right; font-family: "Courier New", monospace; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 6.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .badge-success { 
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            border: 1px solid #11998e;
        }
        
        .badge-warning { 
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border: 1px solid #f5576c;
        }
        
        .totales { 
            background: linear-gradient(to right, #1a5490 0%, #2980b9 100%);
            color: white;
            font-weight: bold; 
            font-size: 8px;
        }
        
        .totales td {
            border-color: #1a5490 !important;
            padding: 8px 5px !important;
        }
        
        .resumen-box {
            background: white;
            border: 2px solid #1a5490;
            border-radius: 8px;
            padding: 12px;
            margin-top: 20px;
        }
        
        .resumen-box h4 {
            color: #1a5490;
            margin: 0 0 10px 0;
            font-size: 11px;
            padding-bottom: 5px;
            border-bottom: 2px solid #1a5490;
        }
        
        table.resumen {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        
        table.resumen th {
            background: #34495e;
            color: white;
            padding: 6px;
            font-size: 7.5px;
            text-align: left;
        }
        
        table.resumen td {
            padding: 5px 6px;
            border: 1px solid #dfe6e9;
            font-size: 7.5px;
        }
        
        table.resumen tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        .nota-importante {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 10px 12px;
            margin-top: 15px;
            border-radius: 4px;
            font-size: 8px;
            line-height: 1.4;
        }
        
        .nota-importante strong {
            color: #856404;
            font-size: 9px;
        }
        
        .tipo-factura { color: #e74c3c; font-weight: bold; }
        .tipo-boleta { color: #3498db; font-weight: bold; }
        .tipo-nota { color: #9b59b6; font-weight: bold; }
    </style>';

    // ============================================================
    // 8. CONTENIDO PRINCIPAL
    // ============================================================
    
    $html .= '
    <table class="data">
        <thead>
            <tr>
                <th width="3%">N°</th>
                <th width="5%">TIPO</th>
                <th width="11%">NÚMERO</th>
                <th width="7%">FECHA</th>
                <th width="4%">T.D</th>
                <th width="9%">NRO. DOCUMENTO</th>
                <th width="18%">RAZÓN SOCIAL / CLIENTE</th>
                <th width="3%">M</th>
                <th width="7%">GRAVADA</th>
                <th width="6%">EXON.</th>
                <th width="6%">INAF.</th>
                <th width="6%">IGV</th>
                <th width="7%">TOTAL</th>
                <th width="8%">ESTADO</th>
            </tr>
        </thead>
        <tbody>';

    // VARIABLES TOTALES
    $contador = 0;
    $total_gravada_sum = 0;
    $total_exonerada_sum = 0;
    $total_inafecta_sum = 0;
    $total_igv_sum = 0;
    $total_general_sum = 0;
    
    // Contadores por tipo
    $resumen_tipos = array();

    // GENERAR FILAS
    foreach ($datos as $row) {
        $contador++;
        
        // ACUMULAR TOTALES
        $gravada = floatval(str_replace(',', '', $row['total_gravada']));
        $exonerada = floatval(str_replace(',', '', $row['total_exonerada']));
        $inafecta = floatval(str_replace(',', '', $row['total_inafecta']));
        $igv = floatval(str_replace(',', '', $row['total_igv']));
        $total = floatval(str_replace(',', '', $row['total']));
        
        $total_gravada_sum += $gravada;
        $total_exonerada_sum += $exonerada;
        $total_inafecta_sum += $inafecta;
        $total_igv_sum += $igv;
        $total_general_sum += $total;
        
        // Resumen por tipo
        $tipo_doc = $row['tipo_documento_nombre'];
        if (!isset($resumen_tipos[$tipo_doc])) {
            $resumen_tipos[$tipo_doc] = array('cantidad' => 0, 'total' => 0);
        }
        $resumen_tipos[$tipo_doc]['cantidad']++;
        $resumen_tipos[$tipo_doc]['total'] += $total;
        
        // ESTADO
        
        $estado_sunat = strtoupper(trim($row['estado_sunat']));

        if ($estado_sunat === 'ACEPTADO' || $estado_sunat === 'ENVIADO') {
            $estado_badge = '<span class="badge" style="background:#27ae60; color:white; padding:3px 6px; border-radius:4px;">ACEPTADO</span>';
        } 
        elseif ($estado_sunat === 'RECHAZADO' || $estado_sunat === 'ANULADO' || $estado_sunat === 'OBSERVADO') {
            $estado_badge = '<span class="badge" style="background:#e74c3c; color:white; padding:3px 6px; border-radius:4px;">' . $estado_sunat . '</span>';
        } 
        else {
            // Cualquier otro estado se marca como pendiente
            $estado_badge = '<span class="badge" style="background:#f1c40f; color:#2c3e50; padding:3px 6px; border-radius:4px;">PENDIENTE</span>';
        }

        // Color por tipo de documento
        $tipo_clase = '';
        switch($row['tipo_comprobante']) {
            case '01':
                $tipo_clase = 'tipo-factura';
                break;
            case '03':
                $tipo_clase = 'tipo-boleta';
                break;
            default:
                $tipo_clase = 'tipo-nota';
                break;
        }
        
        $html .= '
            <tr>
                <td class="text-center" style="color:#95a5a6; font-weight:bold;">' . $contador . '</td>
                <td class="text-center ' . $tipo_clase . '">' . htmlspecialchars($tipo_doc) . '</td>
                <td class="text-center" style="font-family:Courier New; font-weight:bold; color:#2c3e50;">' . 
                    htmlspecialchars($row['numero_comprobante']) . '</td>
                <td class="text-center">' . htmlspecialchars($row['fecha_emision']) . '</td>
                <td class="text-center" style="font-weight:bold;">' . htmlspecialchars($row['tipo_doc_cliente']) . '</td>
                <td class="text-left" style="font-family:Courier New;">' . htmlspecialchars($row['numero_doc_cliente']) . '</td>
                <td class="text-left" style="color:#2c3e50;">' . htmlspecialchars(substr($row['cliente_nombre'], 0, 35)) . '</td>
                <td class="text-center" style="font-weight:bold; color:#7f8c8d;">' . htmlspecialchars($row['moneda']) . '</td>
                <td class="text-right">' . number_format($gravada, 2) . '</td>
                <td class="text-right">' . number_format($exonerada, 2) . '</td>
                <td class="text-right">' . number_format($inafecta, 2) . '</td>
                <td class="text-right" style="color:#27ae60; font-weight:bold;">' . number_format($igv, 2) . '</td>
                <td class="text-right" style="font-weight:bold; color:#2c3e50;">' . number_format($total, 2) . '</td>
                <td class="text-center">' . $estado_badge . '</td>
            </tr>';
    }

    // FILA TOTALES
    $html .= '
        <tr class="totales">
            <td colspan="8" class="text-right" style="letter-spacing:0.5px;">TOTALES GENERALES:</td>
            <td class="text-right">' . number_format($total_gravada_sum, 2) . '</td>
            <td class="text-right">' . number_format($total_exonerada_sum, 2) . '</td>
            <td class="text-right">' . number_format($total_inafecta_sum, 2) . '</td>
            <td class="text-right">' . number_format($total_igv_sum, 2) . '</td>
            <td class="text-right">' . number_format($total_general_sum, 2) . '</td>
            <td></td>
        </tr>
    </tbody>
    </table>';

    // ============================================================
    // 9. RESUMEN POR TIPO DE COMPROBANTE
    // ============================================================
    $html .= '
    <div class="resumen-box">
        <h4>📊 RESUMEN POR TIPO DE COMPROBANTE</h4>
        <table class="resumen">
            <tr>
                <th width="50%">TIPO DE DOCUMENTO</th>
                <th width="20%" style="text-align:center;">CANTIDAD</th>
                <th width="30%" style="text-align:right;">TOTAL (S/)</th>
            </tr>';

    foreach ($resumen_tipos as $tipo => $datos_tipo) {
        $porcentaje = ($total_general_sum > 0) ? ($datos_tipo['total'] / $total_general_sum * 100) : 0;
        
        $html .= '
            <tr>
                <td style="font-weight:bold;">' . htmlspecialchars($tipo) . '</td>
                <td style="text-align:center; font-weight:bold; color:#2c3e50;">' . $datos_tipo['cantidad'] . '</td>
                <td style="text-align:right; font-family:Courier New; font-weight:bold; color:#27ae60;">
                    ' . number_format($datos_tipo['total'], 2) . ' 
                    <span style="color:#95a5a6; font-size:6.5px;">(' . number_format($porcentaje, 1) . '%)</span>
                </td>
            </tr>';
    }

    $html .= '
            <tr style="background:#34495e; color:white; font-weight:bold;">
                <td>TOTAL</td>
                <td style="text-align:center;">' . $contador . '</td>
                <td style="text-align:right;">' . number_format($total_general_sum, 2) . '</td>
            </tr>
        </table>
    </div>';

    // ============================================================
    // 10. NOTA IMPORTANTE
    // ============================================================
    $html .= '
    <div class="nota-importante">
        <strong>⚠️ IMPORTANTE - LEER ANTES DE DECLARAR:</strong><br>
        • Este reporte es un <strong>documento auxiliar</strong> para facilitar su declaración mensual.<br>
        • Verifique que todos los comprobantes tengan estado <strong>"ACEPTADO"</strong> en SUNAT antes de declarar.<br>
        • La declaración oficial debe realizarse en <strong>www.sunat.gob.pe</strong> mediante el formulario correspondiente.<br>
        • Conserve los archivos <strong>XML y CDR</strong> de cada comprobante por el plazo legal establecido.<br>
        • En caso de discrepancias, prevalece la información registrada en los sistemas de SUNAT.
    </div>';

    // ============================================================
    // 11. GENERAR PDF
    // ============================================================
    $mpdf->WriteHTML($html);
    $mpdf->Output('Declaracion_SUNAT_' . date('Ymd_His') . '.pdf', 'I');

} catch (Exception $e) {
    error_log("ERROR REPORTE: " . $e->getMessage());
    
    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>Error en Reporte</title>
        <style>
            * { margin:0; padding:0; box-sizing:border-box; }
            body { 
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif; 
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }
            .error-container {
                background: white;
                padding: 40px;
                border-radius: 16px;
                box-shadow: 0 20px 60px rgba(0,0,0,0.3);
                max-width: 600px;
                width: 100%;
            }
            .error-icon {
                width: 80px;
                height: 80px;
                background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 20px;
                font-size: 40px;
                color: white;
            }
            h3 { 
                color: #2c3e50; 
                margin-bottom: 20px; 
                text-align: center;
                font-size: 24px;
            }
            .error-detail {
                background: #f8f9fa;
                padding: 15px;
                border-radius: 8px;
                margin: 10px 0;
                border-left: 4px solid #e74c3c;
            }
            .error-detail strong {
                color: #e74c3c;
                display: block;
                margin-bottom: 5px;
            }
            .error-detail p {
                color: #555;
                font-size: 14px;
                line-height: 1.6;
            }
            .back-button {
                display: block;
                width: 100%;
                padding: 12px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                text-align: center;
                text-decoration: none;
                border-radius: 8px;
                margin-top: 20px;
                font-weight: bold;
                transition: transform 0.2s;
            }
            .back-button:hover {
                transform: translateY(-2px);
            }
        </style>
    </head>
    <body>
        <div class='error-container'>
            <div class='error-icon'>⚠️</div>
            <h3>Error al generar el reporte</h3>
            <div class='error-detail'>
                <strong>Mensaje de error:</strong>
                <p>" . htmlspecialchars($e->getMessage()) . "</p>
            </div>
            <div class='error-detail'>
                <strong>Ubicación:</strong>
                <p>Archivo: " . htmlspecialchars(basename($e->getFile())) . " | Línea: " . $e->getLine() . "</p>
            </div>
            <a href='javascript:history.back()' class='back-button'>← Volver e intentar nuevamente</a>
        </div>
    </body>
    </html>";
}
?>