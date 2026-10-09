<?php
/**
 * Funciones de envío a SUNAT compartidas por factura_bd.php (y sus pruebas).
 */

use Greenter\Ws\Services\SoapClient;
use Greenter\Ws\Services\ConsultCdrService;
use Greenter\Ws\Services\SunatEndpoints;

/**
 * SUNAT responde 1033 cuando el número ya fue registrado. Pasa cuando un envío
 * anterior sí llegó pero la respuesta se perdió (timeout, conexión cortada).
 */
function esComprobanteYaRegistrado($codigo, $mensaje)
{
    return trim((string)$codigo) === '1033'
        || strpos(strtolower((string)$mensaje), 'registrado previamente') !== false;
}

/**
 * Consulta en SUNAT la constancia (CDR) de un comprobante ya registrado y verifica
 * que corresponda al mismo cliente.
 * Retorna ['estado' => ACEPTADO | RECHAZADO | NO_DISPONIBLE, 'cdrZip', 'codigo', 'mensaje']
 */
function recuperarCdrSunat($empresa, $comprobante)
{
    $ruc = trim($empresa['ruc']);

    try {
        $ws = new SoapClient(SunatEndpoints::FE_CONSULTA_CDR . '?wsdl');
        $ws->setCredentials($ruc . trim($empresa['usuario_sol']), trim($empresa['clave_sol']));
        $service = new ConsultCdrService();
        $service->setClient($ws);
        $resultado = $service->getStatusCdr(
            $ruc,
            $comprobante['tipo_comprobante'],
            $comprobante['serie'],
            (int)$comprobante['correlativo']
        );
    } catch (Throwable $e) {
        return ['estado' => 'NO_DISPONIBLE', 'mensaje' => $e->getMessage()];
    }

    if (!$resultado->isSuccess() || !$resultado->getCdrZip()) {
        $err = $resultado->getError();
        $detalle = $err ? trim($err->getCode() . ' ' . $err->getMessage()) : (string)$resultado->getMessage();
        return ['estado' => 'NO_DISPONIBLE', 'mensaje' => 'No se pudo obtener la constancia: ' . $detalle];
    }

    // Leer el XML del CDR para comparar el receptor
    $xmlCdr = '';
    $tmp = tempnam(sys_get_temp_dir(), 'cdr');
    file_put_contents($tmp, $resultado->getCdrZip());
    $zip = new ZipArchive();
    if ($zip->open($tmp) === true) {
        for ($i = 0; $i < $zip->numFiles; $i++) {
            if (strtolower(substr($zip->getNameIndex($i), -4)) === '.xml') {
                $xmlCdr = (string)$zip->getFromIndex($i);
                break;
            }
        }
        $zip->close();
    }
    @unlink($tmp);

    $receptorEsperado = trim($comprobante['tipo_documento']) . '-' . trim($comprobante['numero_documento']);
    if (preg_match('/<cac:RecipientParty>.*?<cbc:ID>([^<]+)<\/cbc:ID>/s', $xmlCdr, $m)
        && trim($m[1]) !== $receptorEsperado) {
        return [
            'estado' => 'RECHAZADO',
            'mensaje' => "El número ya está registrado en SUNAT para otro cliente ({$m[1]}); este comprobante es para {$receptorEsperado}"
        ];
    }

    $cdr = $resultado->getCdrResponse();
    $codigoCdr = $cdr ? (int)$cdr->getCode() : 0;
    $descripcionCdr = $cdr ? (string)$cdr->getDescription() : 'Constancia recuperada de SUNAT';

    // 0 = aceptado; 4000+ = aceptado con observaciones; 2000-3999 = rechazado
    if ($codigoCdr !== 0 && $codigoCdr < 4000) {
        return ['estado' => 'RECHAZADO', 'codigo' => (string)$codigoCdr, 'mensaje' => $descripcionCdr];
    }

    return [
        'estado' => 'ACEPTADO',
        'cdrZip' => $resultado->getCdrZip(),
        'codigo' => (string)$codigoCdr,
        'mensaje' => $descripcionCdr,
    ];
}

function guardarAceptado($pdo, $comprobante, $xml, $cdrZip, $descripcion, $codigo)
{
    preg_match('/<ds:DigestValue>(.*?)<\/ds:DigestValue>/', $xml, $matches);
    $hash = $matches[1] ?? 'NO_HASH';

    $nombreCdr = 'R-' . $comprobante['serie'] . '-' . str_pad($comprobante['correlativo'], 8, '0', STR_PAD_LEFT) . '.zip';
    if ($cdrZip) {
        $cdrPath = __DIR__ . '/cdr/';
        if (!is_dir($cdrPath)) mkdir($cdrPath, 0777, true);
        file_put_contents($cdrPath . $nombreCdr, $cdrZip);
    }

    $upd = $pdo->prepare("UPDATE comprobantes
                          SET estado_sunat='ACEPTADO',
                              codigo_hash=?,
                              descripcion_respuesta_sunat=?,
                              codigo_respuesta_sunat=?,
                              nombre_archivo_cdr=?,
                              fecha_envio_sunat=NOW()
                          WHERE id_comprobante=?");
    $upd->execute([
        $hash,
        $descripcion,
        $codigo,
        $cdrZip ? $nombreCdr : null,
        $comprobante['id_comprobante']
    ]);

    echo "\n✅ ACEPTADO POR SUNAT\n";
    echo "   Descripción: {$descripcion}\n";
    echo "   Código: {$codigo}\n";
    echo "   Hash: {$hash}\n";
}

function guardarError($pdo, $comprobante, $estado, $codigo, $descripcion)
{
    $upd = $pdo->prepare("UPDATE comprobantes
                          SET estado_sunat=?,
                              codigo_respuesta_sunat=?,
                              descripcion_respuesta_sunat=?,
                              fecha_envio_sunat=NOW()
                          WHERE id_comprobante=?");
    $upd->execute([$estado, $codigo, $descripcion, $comprobante['id_comprobante']]);
}
