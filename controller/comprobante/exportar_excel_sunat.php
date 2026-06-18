<?php
require_once __DIR__ . '/../../model/model_conexion.php';

// ===============================================================
// FUNCION PARA CONVERTIR FECHA dd/mm/yyyy → yyyy-mm-dd
// ===============================================================
function convertirFecha($fecha) {
    if (!$fecha) return null;
    $p = explode('/', $fecha);

    if (count($p) !== 3) return null;

    return "$p[2]-$p[1]-$p[0]";
}

// ===============================================================
// PARÁMETROS
// ===============================================================
$tipo        = $_GET['tipo'] ?? '';
$fecha_desde = $_GET['fecha_desde'] ?? '';
$fecha_hasta = $_GET['fecha_hasta'] ?? '';

$estado = "ACEPTADO"; // SOLO aceptados

$fechaDesdeSQL = convertirFecha($fecha_desde);
$fechaHastaSQL = convertirFecha($fecha_hasta);

// ===============================================================
// CONEXIÓN
// ===============================================================
$cn = new conexionBD();
$conexion = $cn->conexionPDO();

// ===============================================================
// CONSULTA SQL
// ===============================================================
$sql = "SELECT 
    DATE_FORMAT(c.fecha_emision, '%d/%m/%Y') AS FECHA_EMISION,
    CASE c.tipo_comprobante 
        WHEN '01' THEN 'FACTURA'
        WHEN '03' THEN 'BOLETA'
        WHEN '07' THEN 'NOTA DE CRÉDITO'
        WHEN '08' THEN 'NOTA DE DÉBITO'
        ELSE 'OTROS' 
    END AS TIPO_DOCUMENTO,
    CONCAT(c.serie, '-', LPAD(c.correlativo, 8, '0')) AS NUMERO,
    cl.razon_social AS CLIENTE,
    cl.numero_documento AS DOCUMENTO,
    c.total_gravada AS BASE,
    c.total_igv AS IGV,
    c.total AS TOTAL,
    c.estado_sunat AS ESTADO_SUNAT
FROM comprobantes c
INNER JOIN cliente_sunat cl ON c.id_cliente = cl.id_cliente
WHERE UPPER(c.estado_sunat) = 'ACEPTADO'";

$params = [];

// Filtros
if ($tipo !== '') {
    $sql .= " AND c.tipo_comprobante = ?";
    $params[] = $tipo;
}

if ($fechaDesdeSQL !== null) {
    $sql .= " AND DATE(c.fecha_emision) >= ?";
    $params[] = $fechaDesdeSQL;
}

if ($fechaHastaSQL !== null) {
    $sql .= " AND DATE(c.fecha_emision) <= ?";
    $params[] = $fechaHastaSQL;
}

// Ejecutar consulta
$query = $conexion->prepare($sql);
foreach ($params as $i => $v) {
    $query->bindValue($i + 1, $v);
}
$query->execute();
$datos = $query->fetchAll(PDO::FETCH_ASSOC);

// ===============================================================
// EXPORTAR A EXCEL
// ===============================================================
$nombreArchivo = "declaracion_sunat_" . date("Ymd_His") . ".xls";

header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=$nombreArchivo");
header("Pragma: no-cache");
header("Expires: 0");

// ===============================================================
// DISEÑO PROFESIONAL
// ===============================================================
echo "
<style>
    table {
        border-collapse: collapse;
        font-family: Arial, sans-serif;
        font-size: 13px;
        width: 100%;
    }

    th {
        background: #0154A0;
        color: #ffffff;
        padding: 8px;
        font-weight: bold;
        border: 1px solid #ffffff;
        text-align: center;
        white-space: nowrap;
    }

    td {
        padding: 6px;
        border: 1px solid #d0d0d0;
    }

    tr:nth-child(even) td { background: #f3f6fa; }
    tr:nth-child(odd) td { background: #ffffff; }

    .number { mso-number-format:\"#,##0.00\"; text-align:right; }
    .text { mso-number-format:\"\\@\"; }

    .total-row td {
        background: #FFF7C6;
        font-weight: bold;
        border-top: 2px solid #000000;
        border-bottom: 2px solid #000000;
    }
</style>
";

// ===============================================================
// TABLA
// ===============================================================
echo "<table>";

// ENCABEZADOS
if (!empty($datos)) {
    echo "<tr>";
    foreach (array_keys($datos[0]) as $col) {
        echo "<th>$col</th>";
    }
    echo "</tr>";
}

// VARIABLES DE TOTALES
$total_base  = 0;
$total_igv   = 0;
$total_total = 0;

// FILAS
foreach ($datos as $fila) {

    // USAR LAS COLUMNAS CORRECTAS DEL SELECT
    $base  = floatval($fila['BASE']);
    $igv   = floatval($fila['IGV']);
    $total = floatval($fila['TOTAL']);

    $total_base  += $base;
    $total_igv   += $igv;
    $total_total += $total;

    echo "<tr>";
    foreach ($fila as $clave => $valor) {

        if (in_array($clave, ['BASE','IGV','TOTAL'])) {
            echo "<td class='number'>" . number_format(floatval($valor), 2, '.', '') . "</td>";
        } else {
            echo "<td class='text'>" . htmlspecialchars($valor, ENT_QUOTES, 'UTF-8') . "</td>";
        }

    }
    echo "</tr>";
}

// FILA DE TOTALES
echo "
<tr class='total-row'>
    <td colspan='".(count($datos[0]) - 4)."' style='text-align:right;'>TOTALES:</td>
    <td class='number'>".number_format($total_base, 2, '.', '')."</td>
    <td class='number'>".number_format($total_igv, 2, '.', '')."</td>
    <td class='number'>".number_format($total_total, 2, '.', '')."</td>
</tr>
";

echo "</table>";
?>
