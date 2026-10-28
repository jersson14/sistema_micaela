<?php
/**
 * Integración con Nubefact (PSE + OSE) por API JSON.
 *
 * Se activa sola si existe el archivo .env.nubefact en la raíz del proyecto
 * con NUBEFACT_RUTA y NUBEFACT_TOKEN (ver .env.nubefact.ejemplo).
 * Sin ese archivo, el sistema sigue enviando directo a SUNAT con Greenter.
 *
 * Nubefact arma, firma (con su certificado) y envía el comprobante;
 * el sistema solo manda los datos en JSON.
 */

// =============================================
// CONFIGURACIÓN
// =============================================

/** Valores de .env.nubefact + variables de entorno (estas tienen prioridad). */
function nubefactValores()
{
    static $valores = null;
    if ($valores !== null) {
        return $valores;
    }

    $valores = [];
    $archivo = __DIR__ . '/../../.env.nubefact';
    if (is_file($archivo)) {
        foreach (file($archivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $linea) {
            $linea = trim($linea);
            if ($linea === '' || $linea[0] === '#' || strpos($linea, '=') === false) {
                continue;
            }
            [$clave, $valor] = array_map('trim', explode('=', $linea, 2));
            $valores[$clave] = trim($valor, "\"'");
        }
    }

    // Las variables de entorno (Docker, pruebas) tienen prioridad sobre el archivo
    foreach (['NUBEFACT_RUTA', 'NUBEFACT_TOKEN', 'NUBEFACT_ACTIVO', 'NUBEFACT_MODO', 'NUBEFACT_SERIES'] as $clave) {
        if (getenv($clave) !== false && getenv($clave) !== '') {
            $valores[$clave] = getenv($clave);
        }
    }
    return $valores;
}

/**
 * Salida de emergencia: enviar directo a SUNAT con Greenter SOLO si alguien lo pidió
 * a propósito con NUBEFACT_ACTIVO=0 (requiere el certificado vigente).
 * Si .env.nubefact simplemente falta, NO se envía directo: el comprobante queda pendiente.
 */
function nubefactSunatDirectoPorEmergencia()
{
    return (nubefactValores()['NUBEFACT_ACTIVO'] ?? '1') === '0';
}

function nubefactConfig()
{
    static $config = null;
    if ($config !== null) {
        return $config ?: null;
    }

    $valores = nubefactValores();
    $activo = ($valores['NUBEFACT_ACTIVO'] ?? '1') !== '0';
    if (!$activo || empty($valores['NUBEFACT_RUTA']) || empty($valores['NUBEFACT_TOKEN'])) {
        $config = false;
        return null;
    }

    // NUBEFACT_SERIES=FPP1:FFF1,BPP1:BBB1 (solo si la cuenta DEMO exige otras series)
    $series = [];
    foreach (array_filter(array_map('trim', explode(',', $valores['NUBEFACT_SERIES'] ?? ''))) as $par) {
        if (strpos($par, ':') !== false) {
            [$local, $remota] = array_map('trim', explode(':', $par, 2));
            $series[strtoupper($local)] = strtoupper($remota);
        }
    }

    $config = [
        'ruta'   => $valores['NUBEFACT_RUTA'],
        'token'  => $valores['NUBEFACT_TOKEN'],
        'modo'   => strtoupper($valores['NUBEFACT_MODO'] ?? 'PRODUCCION'),
        'series' => $series,
    ];
    return $config;
}

function nubefactActivo()
{
    return nubefactConfig() !== null;
}

function nubefactSerie($serie)
{
    $config = nubefactConfig();
    $serie = strtoupper(trim($serie));
    return $config['series'][$serie] ?? $serie;
}

function nubefactEsDemo()
{
    $config = nubefactConfig();
    return $config !== null && $config['modo'] === 'DEMO';
}

/**
 * Número a enviar a Nubefact.
 * PRODUCCIÓN: el correlativo real del comprobante.
 * DEMO: la cuenta de pruebas solo acepta números cercanos al último que tiene registrado,
 * así que a cada comprobante se le asigna un número de prueba y se recuerda en
 * greenter/tmp/nubefact_demo.json para poder consultarlo o anularlo después.
 */
function nubefactNumero($comprobante)
{
    if (!nubefactEsDemo()) {
        return (int)$comprobante['correlativo'];
    }
    return nubefactDemo(function (&$estado) use ($comprobante) {
        $id = (string)$comprobante['id_comprobante'];
        if (isset($estado['asignados'][$id])) {
            return (int)$estado['asignados'][$id];
        }
        $serie = nubefactSerie($comprobante['serie']);
        $numero = (int)($estado['ultimo'][$serie] ?? 0) + 1;
        $estado['ultimo'][$serie] = $numero;
        $estado['asignados'][$id] = $numero;
        return $numero;
    });
}

/** DEMO: la cuenta informó cuál es su último número; se reasigna el del comprobante. */
function nubefactDemoReasignar($comprobante, $ultimoRegistrado)
{
    nubefactDemo(function (&$estado) use ($comprobante, $ultimoRegistrado) {
        $serie = nubefactSerie($comprobante['serie']);
        $estado['ultimo'][$serie] = max((int)$ultimoRegistrado, (int)($estado['ultimo'][$serie] ?? 0));
        unset($estado['asignados'][(string)$comprobante['id_comprobante']]);
    });
}

/** Lee y guarda greenter/tmp/nubefact_demo.json con candado. */
function nubefactDemo(callable $operacion)
{
    $dir = __DIR__ . '/../tmp';
    if (!is_dir($dir)) mkdir($dir, 0777, true);
    $archivo = $dir . '/nubefact_demo.json';
    $fp = fopen($archivo, 'c+');
    flock($fp, LOCK_EX);
    $contenido = stream_get_contents($fp);
    $estado = json_decode($contenido ?: '', true) ?: ['ultimo' => [], 'asignados' => []];
    $resultado = $operacion($estado);
    ftruncate($fp, 0);
    rewind($fp);
    fwrite($fp, json_encode($estado, JSON_PRETTY_PRINT));
    flock($fp, LOCK_UN);
    fclose($fp);
    return $resultado;
}

// =============================================
// LLAMADAS HTTP
// =============================================

/**
 * POST a la API. Retorna ['http' => int, 'data' => array|null, 'error_red' => string|null]
 */
function nubefactPost(array $payload)
{
    $config = nubefactConfig();
    $ch = curl_init($config['ruta']);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTPHEADER => [
            'Authorization: Token token="' . $config['token'] . '"',
            'Content-Type: application/json',
        ],
        CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
    ]);
    $cuerpo = curl_exec($ch);
    $errorRed = $cuerpo === false ? curl_error($ch) : null;
    $http = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $data = $cuerpo !== false ? json_decode($cuerpo, true) : null;
    return ['http' => $http, 'data' => is_array($data) ? $data : null, 'error_red' => $errorRed, 'crudo' => $cuerpo];
}

function nubefactDescargar($url)
{
    if (empty($url)) {
        return null;
    }
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 30,
    ]);
    $contenido = curl_exec($ch);
    $http = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ($contenido !== false && $http === 200 && $contenido !== '') ? $contenido : null;
}

// =============================================
// ARMADO DEL JSON
// =============================================

function nubefactTipoComprobante($tipo)
{
    return ['01' => 1, '03' => 2, '07' => 3, '08' => 4][$tipo] ?? null;
}

function nubefactTipoDocCliente($tipo)
{
    $tipo = trim((string)$tipo);
    $validos = ['6', '1', '4', '7', 'A', 'B', '0'];
    if ($tipo === '' || $tipo === '-' || $tipo === '00') {
        return '-';
    }
    return in_array($tipo, $validos, true) ? $tipo : '-';
}

/** Afectación IGV de SUNAT (catálogo 07) -> tipo_de_igv de Nubefact */
function nubefactTipoIgv($afectacion)
{
    return ['10' => 1, '20' => 8, '30' => 9][trim((string)$afectacion)] ?? 1;
}

/**
 * Ítems del comprobante con la misma lógica que factura_bd.php:
 * se usan los detalles si cuadran con el encabezado; si no, un ítem con los totales.
 */
function nubefactItems($pdo, $comprobante)
{
    $total = (float)$comprobante['total'];
    $base = $comprobante['total_gravada'] > 0 ? (float)$comprobante['total_gravada'] : round($total / 1.18, 2);
    $igv = $comprobante['total_igv'] > 0 ? (float)$comprobante['total_igv'] : round($total - $base, 2);

    $stmt = $pdo->prepare("SELECT * FROM comprobante_detalle WHERE id_comprobante = ? ORDER BY orden_item ASC");
    $stmt->execute([$comprobante['id_comprobante']]);
    $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $items = [];
    if (!empty($detalles)) {
        $sumaValor = array_sum(array_column($detalles, 'valor_venta'));
        $sumaIgv = array_sum(array_column($detalles, 'igv'));
        if (abs($sumaValor - $base) <= 0.10 && abs($sumaIgv - $igv) <= 0.10) {
            foreach ($detalles as $det) {
                $cantidad = (float)$det['cantidad'] ?: 1;
                $subtotal = round((float)$det['valor_venta'], 2);
                $igvItem = round((float)$det['igv'], 2);
                $items[] = [
                    'unidad_de_medida' => !empty($det['unidad_medida']) ? $det['unidad_medida'] : 'NIU',
                    'codigo' => !empty($det['codigo_producto']) ? $det['codigo_producto'] : 'P001',
                    'descripcion' => $det['descripcion'],
                    'cantidad' => $cantidad,
                    'valor_unitario' => round((float)$det['valor_unitario'], 10),
                    'precio_unitario' => round(($subtotal + $igvItem) / $cantidad, 10),
                    'descuento' => '',
                    'subtotal' => $subtotal,
                    'tipo_de_igv' => nubefactTipoIgv($det['afectacion_igv'] ?? '10'),
                    'igv' => $igvItem,
                    'total' => round($subtotal + $igvItem, 2),
                    'anticipo_regularizacion' => false,
                    'anticipo_documento_serie' => '',
                    'anticipo_documento_numero' => '',
                ];
            }
        }
    }

    if (empty($items)) {
        $items[] = [
            'unidad_de_medida' => 'NIU',
            'codigo' => !empty($detalles[0]['codigo_producto']) ? $detalles[0]['codigo_producto'] : 'P001',
            'descripcion' => !empty($detalles[0]['descripcion']) ? $detalles[0]['descripcion'] : 'SERVICIO DE TRANSPORTE',
            'cantidad' => 1,
            'valor_unitario' => $base,
            'precio_unitario' => $total,
            'descuento' => '',
            'subtotal' => $base,
            'tipo_de_igv' => 1,
            'igv' => $igv,
            'total' => $total,
            'anticipo_regularizacion' => false,
            'anticipo_documento_serie' => '',
            'anticipo_documento_numero' => '',
        ];
    }

    return $items;
}

function nubefactArmarComprobante($pdo, $comprobante)
{
    $tipo = $comprobante['tipo_comprobante'];
    $items = nubefactItems($pdo, $comprobante);

    $totales = [1 => 0.0, 8 => 0.0, 9 => 0.0];
    $totalIgv = 0.0;
    foreach ($items as $item) {
        $totales[$item['tipo_de_igv']] = ($totales[$item['tipo_de_igv']] ?? 0) + $item['subtotal'];
        $totalIgv += $item['igv'];
    }

    $payload = [
        'operacion' => 'generar_comprobante',
        'tipo_de_comprobante' => nubefactTipoComprobante($tipo),
        'serie' => nubefactSerie($comprobante['serie']),
        'numero' => nubefactNumero($comprobante),
        'sunat_transaction' => 1,
        'cliente_tipo_de_documento' => nubefactTipoDocCliente($comprobante['tipo_documento']),
        'cliente_numero_de_documento' => trim((string)$comprobante['numero_documento']) ?: '-',
        'cliente_denominacion' => $comprobante['razon_social'],
        'cliente_direccion' => $comprobante['direccion'] ?? '',
        'cliente_email' => '',
        'cliente_email_1' => '',
        'cliente_email_2' => '',
        'fecha_de_emision' => date('d-m-Y', strtotime($comprobante['fecha_emision'])),
        'fecha_de_vencimiento' => '',
        'moneda' => 1,
        'tipo_de_cambio' => '',
        'porcentaje_de_igv' => 18.00,
        'descuento_global' => '',
        'total_descuento' => '',
        'total_anticipo' => '',
        'total_gravada' => round($totales[1], 2) ?: '',
        'total_inafecta' => round($totales[9], 2) ?: '',
        'total_exonerada' => round($totales[8], 2) ?: '',
        'total_igv' => round($totalIgv, 2),
        'total_gratuita' => '',
        'total_otros_cargos' => '',
        'total' => round((float)$comprobante['total'], 2),
        'percepcion_tipo' => '',
        'percepcion_base_imponible' => '',
        'total_percepcion' => '',
        'total_incluido_percepcion' => '',
        'detraccion' => false,
        'observaciones' => '',
        'documento_que_se_modifica_tipo' => '',
        'documento_que_se_modifica_serie' => '',
        'documento_que_se_modifica_numero' => '',
        'tipo_de_nota_de_credito' => '',
        'tipo_de_nota_de_debito' => '',
        'enviar_automaticamente_a_la_sunat' => true,
        'enviar_automaticamente_al_cliente' => false,
        'codigo_unico' => '',
        'condiciones_de_pago' => '',
        'medio_de_pago' => '',
        'placa_vehiculo' => '',
        'orden_compra_servicio' => '',
        'tabla_personalizada_codigo' => '',
        'formato_de_pdf' => '',
        'items' => $items,
    ];

    // Notas de crédito y débito: documento que se modifica
    if (in_array($tipo, ['07', '08'], true)) {
        if (empty($comprobante['id_comprobante_origen'])) {
            throw new RuntimeException('La nota no tiene documento de referencia (id_comprobante_origen vacío).');
        }
        $stmt = $pdo->prepare("SELECT id_comprobante, tipo_comprobante, serie, correlativo FROM comprobantes WHERE id_comprobante = ?");
        $stmt->execute([$comprobante['id_comprobante_origen']]);
        $ref = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$ref || !in_array($ref['tipo_comprobante'], ['01', '03'], true)) {
            throw new RuntimeException('No se encontró una factura o boleta válida como documento de referencia.');
        }
        $payload['documento_que_se_modifica_tipo'] = nubefactTipoComprobante($ref['tipo_comprobante']);
        $payload['documento_que_se_modifica_serie'] = nubefactSerie($ref['serie']);
        $payload['documento_que_se_modifica_numero'] = nubefactNumero($ref);
        if ($tipo === '07') {
            $payload['tipo_de_nota_de_credito'] = 1; // Anulación de la operación
        } else {
            $payload['tipo_de_nota_de_debito'] = 2;  // Aumento en el valor
        }
        $payload['observaciones'] = mb_substr(trim((string)($comprobante['motivo_nota'] ?? '')), 0, 250);
    }

    return $payload;
}

// =============================================
// RESULTADOS
// =============================================

function nubefactNumeroCompleto($comprobante)
{
    return $comprobante['serie'] . '-' . str_pad($comprobante['correlativo'], 8, '0', STR_PAD_LEFT);
}

/** Guarda XML y CDR con los nombres que ya usa el sistema (descargas y ticket). */
function nubefactGuardarArchivos($comprobante, $data)
{
    $numero = nubefactNumeroCompleto($comprobante);
    $nombreCdr = null;

    $xml = nubefactDescargar($data['enlace_del_xml'] ?? null);
    if ($xml !== null) {
        if (substr($xml, 0, 2) === 'PK') {
            $xml = nubefactPrimerXmlDelZip($xml) ?? $xml;
        }
        $dirXml = __DIR__ . '/../xml/';
        if (!is_dir($dirXml)) mkdir($dirXml, 0777, true);
        file_put_contents($dirXml . $numero . '.xml', $xml);
    }

    $cdr = nubefactDescargar($data['enlace_del_cdr'] ?? null);
    if ($cdr !== null) {
        $dirCdr = __DIR__ . '/../cdr/';
        if (!is_dir($dirCdr)) mkdir($dirCdr, 0777, true);
        $nombreCdr = 'R-' . $numero . '.zip';
        if (substr($cdr, 0, 2) === 'PK') {
            file_put_contents($dirCdr . $nombreCdr, $cdr);
        } else {
            // Llegó el XML del CDR sin comprimir: se guarda dentro de un zip como los demás
            $zip = new ZipArchive();
            if ($zip->open($dirCdr . $nombreCdr, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                $zip->addFromString('R-' . $numero . '.xml', $cdr);
                $zip->close();
            }
        }
    }

    return $nombreCdr;
}

function nubefactPrimerXmlDelZip($contenido)
{
    $tmp = tempnam(sys_get_temp_dir(), 'nf');
    file_put_contents($tmp, $contenido);
    $xml = null;
    $zip = new ZipArchive();
    if ($zip->open($tmp) === true) {
        for ($i = 0; $i < $zip->numFiles; $i++) {
            if (strtolower(substr($zip->getNameIndex($i), -4)) === '.xml') {
                $xml = $zip->getFromIndex($i);
                break;
            }
        }
        $zip->close();
    }
    @unlink($tmp);
    return $xml;
}

function nubefactActualizarEstado($pdo, $comprobante, $estado, $codigo, $descripcion, $hash = null, $nombreCdr = null)
{
    $upd = $pdo->prepare("UPDATE comprobantes
                          SET estado_sunat = ?,
                              codigo_respuesta_sunat = ?,
                              descripcion_respuesta_sunat = ?,
                              codigo_hash = COALESCE(?, codigo_hash),
                              nombre_archivo_cdr = COALESCE(?, nombre_archivo_cdr),
                              fecha_envio_sunat = NOW()
                          WHERE id_comprobante = ?");
    $upd->execute([$estado, $codigo, mb_substr((string)$descripcion, 0, 500), $hash, $nombreCdr, $comprobante['id_comprobante']]);
}

/** ¿El error de la API es de configuración o del servicio (reintentar) y no de los datos? */
function nubefactErrorEsTemporal($codigo, $mensaje)
{
    $mensaje = mb_strtolower((string)$mensaje);
    foreach (['token', 'ruta', 'url', 'autoriz', 'suspend', 'mantenimiento', 'intente', 'timeout', 'servicio'] as $patron) {
        if (strpos($mensaje, $patron) !== false) {
            return true;
        }
    }
    return in_array((int)$codigo, [10, 11, 40, 50], true);
}

// =============================================
// ENVÍO DE FACTURAS, BOLETAS Y NOTAS
// =============================================

/**
 * Envía (o consulta, si ya se registró antes) el comprobante en Nubefact,
 * actualiza la BD e imprime la salida con el mismo formato que factura_bd.php
 * para que el controlador y la cola la interpreten igual.
 */
function nubefactEnviarComprobante($pdo, $comprobante)
{
    $config = nubefactConfig();
    $numero = nubefactNumeroCompleto($comprobante);
    echo "🚀 Entorno activo: NUBEFACT (OSE) - {$config['modo']}\n";
    echo "📄 Comprobante: {$numero}\n";

    $tipoNf = nubefactTipoComprobante($comprobante['tipo_comprobante']);
    if ($tipoNf === null) {
        nubefactSalidaRechazo($pdo, $comprobante, 'NF_TIPO', "Tipo de comprobante no soportado: {$comprobante['tipo_comprobante']}");
        return;
    }

    $consulta = [
        'operacion' => 'consultar_comprobante',
        'tipo_de_comprobante' => $tipoNf,
        'serie' => nubefactSerie($comprobante['serie']),
        'numero' => nubefactNumero($comprobante),
    ];

    // Si ya quedó registrado en Nubefact en un intento anterior, solo se consulta su estado
    // NF_PEND: registrado en Nubefact y pendiente de SUNAT (la columna admite máx. 10 caracteres)
    $yaRegistrado = trim((string)$comprobante['codigo_respuesta_sunat']) === 'NF_PEND';
    if ($yaRegistrado) {
        echo "🔎 Ya registrado en Nubefact: consultando estado...\n";
        $respuesta = nubefactPost($consulta);
    } else {
        try {
            $payload = nubefactArmarComprobante($pdo, $comprobante);
        } catch (RuntimeException $e) {
            nubefactSalidaRechazo($pdo, $comprobante, 'NF_DATOS', $e->getMessage());
            return;
        }
        $respuesta = nubefactPost($payload);

        // DEMO: la cuenta de pruebas tiene números ya usados. Si el número existe o la cuenta
        // informa su último número, se pasa al siguiente y se reintenta (nunca se consulta,
        // porque ese número puede ser de otro documento).
        for ($ajuste = 0; nubefactEsDemo() && $ajuste < 10; $ajuste++) {
            $textoErr = nubefactTextoError($respuesta);
            if (preg_match('/registrado en NubeFacT para este documento y serie es el (\d+)/iu', $textoErr, $m)) {
                $ultimo = (int)$m[1];
            } elseif (stripos($textoErr, 'existe') !== false) {
                $ultimo = (int)$payload['numero'];
            } else {
                break;
            }
            echo "🧪 DEMO: el número {$payload['numero']} no está disponible; probando con el siguiente...\n";
            nubefactDemoReasignar($comprobante, $ultimo);
            $payload = nubefactArmarComprobante($pdo, $comprobante);
            $consulta['numero'] = $payload['numero'];
            $respuesta = nubefactPost($payload);
        }

        // PRODUCCIÓN: "ya existe" suele ser un envío anterior cuya respuesta se perdió.
        // Se consulta, pero solo se acepta si el documento registrado es este mismo.
        if (!nubefactEsDemo() && stripos(nubefactTextoError($respuesta), 'existe') !== false) {
            echo "🔎 El comprobante ya existe en Nubefact: consultando y verificando que sea el mismo...\n";
            $respuesta = nubefactPost($consulta);
            $data = $respuesta['data'] ?? null;
            if ($data !== null && empty($data['errors'])) {
                $mismo = nubefactEsMismoDocumento($comprobante, $data);
                if ($mismo === false) {
                    nubefactSalidaRechazo($pdo, $comprobante, 'NF_DUPLIC',
                        'El número ' . nubefactSerie($comprobante['serie']) . '-' . $consulta['numero'] .
                        ' ya está registrado en Nubefact con otro cliente o monto. Revise la numeración de la serie en Nubefact.');
                    return;
                }
                if ($mismo === null) {
                    nubefactSalidaTemporal($pdo, $comprobante, 'NF_VERIF', 'Ya existe en Nubefact; no se pudo verificar que sea el mismo comprobante');
                    return;
                }
            }
        }
    }

    nubefactProcesarRespuesta($pdo, $comprobante, $respuesta);
}

function nubefactTextoError($respuesta)
{
    $errores = $respuesta['data']['errors'] ?? null;
    return is_array($errores) ? json_encode($errores, JSON_UNESCAPED_UNICODE) : (string)$errores;
}

/**
 * ¿El documento registrado en Nubefact (respuesta de consulta) es este comprobante?
 * Compara el documento del cliente y el total en su XML. null = no se pudo verificar.
 */
function nubefactEsMismoDocumento($comprobante, $data)
{
    $xml = nubefactDescargar($data['enlace_del_xml'] ?? null);
    if ($xml === null) {
        return null;
    }
    if (substr($xml, 0, 2) === 'PK') {
        $xml = nubefactPrimerXmlDelZip($xml);
        if ($xml === null) {
            return null;
        }
    }
    $documento = trim((string)$comprobante['numero_documento']);
    $total = number_format((float)$comprobante['total'], 2, '.', '');
    $tieneCliente = $documento === '' || $documento === '-' || strpos($xml, '>' . $documento . '<') !== false;
    $tieneTotal = preg_match('#<cbc:PayableAmount[^>]*>' . preg_quote($total, '#') . '<#', $xml) === 1;
    return $tieneCliente && $tieneTotal;
}

function nubefactProcesarRespuesta($pdo, $comprobante, $respuesta)
{
    // Sin conexión o respuesta ilegible: temporal
    if ($respuesta['data'] === null) {
        $detalle = $respuesta['error_red'] ?: ('HTTP ' . $respuesta['http'] . ' ' . mb_substr((string)$respuesta['crudo'], 0, 150));
        nubefactSalidaTemporal($pdo, $comprobante, 'HTTP', 'No se pudo comunicar con Nubefact: ' . $detalle);
        return;
    }

    $data = $respuesta['data'];

    if (!empty($data['errors'])) {
        $mensaje = is_array($data['errors']) ? json_encode($data['errors'], JSON_UNESCAPED_UNICODE) : (string)$data['errors'];
        $codigo = 'NF_' . ($data['codigo'] ?? 'ERROR');
        if (nubefactErrorEsTemporal($data['codigo'] ?? null, $mensaje)) {
            nubefactSalidaTemporal($pdo, $comprobante, $codigo, $mensaje);
        } else {
            nubefactSalidaRechazo($pdo, $comprobante, $codigo, $mensaje);
        }
        return;
    }

    $codigoSunat = trim((string)($data['sunat_responsecode'] ?? ''));
    $descripcion = trim((string)($data['sunat_description'] ?? ''));

    if (!empty($data['aceptada_por_sunat'])) {
        $nombreCdr = nubefactGuardarArchivos($comprobante, $data);
        $descripcion = $descripcion ?: 'Comprobante aceptado';
        nubefactActualizarEstado($pdo, $comprobante, 'ACEPTADO', $codigoSunat !== '' ? $codigoSunat : '0',
            $descripcion, $data['codigo_hash'] ?? null, $nombreCdr);
        echo "\n✅ ACEPTADO POR SUNAT\n";
        echo "   Descripción: {$descripcion}\n";
        echo "   Código: " . ($codigoSunat !== '' ? $codigoSunat : '0') . "\n";
        echo "   Hash: " . ($data['codigo_hash'] ?? '') . "\n";
        if (!empty($data['enlace'])) {
            echo "   Ver en Nubefact: {$data['enlace']}\n";
        }
        return;
    }

    // Rechazo de SUNAT/OSE por los datos del comprobante (códigos 2000-3999)
    if (ctype_digit($codigoSunat) && (int)$codigoSunat >= 2000 && (int)$codigoSunat < 4000) {
        nubefactSalidaRechazo($pdo, $comprobante, $codigoSunat, $descripcion ?: 'Rechazado por SUNAT');
        return;
    }

    // Registrado en Nubefact, pendiente de la respuesta de SUNAT: se consultará en el siguiente intento
    if (!empty($data['enlace'])) {
        echo "   Ver en Nubefact: {$data['enlace']}\n";
    }
    $detalle = $descripcion ?: (trim((string)($data['sunat_soap_error'] ?? '')) ?: 'Registrado en el OSE; esperando respuesta de SUNAT');
    nubefactSalidaTemporal($pdo, $comprobante, 'NF_PEND', $detalle);
}

function nubefactSalidaTemporal($pdo, $comprobante, $codigo, $mensaje)
{
    nubefactActualizarEstado($pdo, $comprobante, 'PENDIENTE', $codigo, '[TEMPORAL] ' . $mensaje);
    echo "\n⚠️ ERROR TRANSITORIO SUNAT\n";
    echo "Código: {$codigo}\n";
    echo "Mensaje: {$mensaje}\n";
    echo "Acción: Reintentar envío\n";
}

function nubefactSalidaRechazo($pdo, $comprobante, $codigo, $mensaje)
{
    nubefactActualizarEstado($pdo, $comprobante, 'RECHAZADO', $codigo, $mensaje);
    echo "\n❌ ERROR EN EL ENVÍO\n";
    echo "Código: {$codigo}\n";
    echo "Mensaje: {$mensaje}\n";
}

// =============================================
// ANULACIONES (comunicación de baja / reversión)
// =============================================

/** Pide la anulación a Nubefact. Retorna ['ok' => bool, 'ticket' => string|null, 'mensaje' => string] */
function nubefactAnular($comprobante, $motivo)
{
    $respuesta = nubefactPost([
        'operacion' => 'generar_anulacion',
        'tipo_de_comprobante' => nubefactTipoComprobante($comprobante['tipo_comprobante']),
        'serie' => nubefactSerie($comprobante['serie']),
        'numero' => nubefactNumero($comprobante),
        'motivo' => mb_substr($motivo, 0, 100),
        'codigo_unico' => '',
    ]);

    if ($respuesta['data'] === null) {
        return ['ok' => false, 'ticket' => null, 'mensaje' => 'No se pudo comunicar con Nubefact: ' . ($respuesta['error_red'] ?: 'HTTP ' . $respuesta['http'])];
    }
    $data = $respuesta['data'];
    if (!empty($data['errors'])) {
        return ['ok' => false, 'ticket' => null, 'mensaje' => is_array($data['errors']) ? json_encode($data['errors'], JSON_UNESCAPED_UNICODE) : (string)$data['errors']];
    }
    $ticket = trim((string)($data['sunat_ticket_numero'] ?? ''));
    return ['ok' => true, 'ticket' => $ticket, 'mensaje' => trim((string)($data['sunat_description'] ?? '')), 'aceptada' => !empty($data['aceptada_por_sunat'])];
}

/** Consulta una anulación. Retorna ['estado' => ACEPTADO|RECHAZADO|PENDIENTE, 'codigo', 'mensaje'] */
function nubefactConsultarAnulacion($comprobante)
{
    $respuesta = nubefactPost([
        'operacion' => 'consultar_anulacion',
        'tipo_de_comprobante' => nubefactTipoComprobante($comprobante['tipo_comprobante']),
        'serie' => nubefactSerie($comprobante['serie']),
        'numero' => nubefactNumero($comprobante),
    ]);

    $data = $respuesta['data'];
    if ($data === null || !empty($data['errors'])) {
        $mensaje = $data === null ? ($respuesta['error_red'] ?: 'HTTP ' . $respuesta['http']) : (is_array($data['errors']) ? json_encode($data['errors']) : $data['errors']);
        return ['estado' => 'PENDIENTE', 'codigo' => 'NF', 'mensaje' => $mensaje];
    }

    $codigo = trim((string)($data['sunat_responsecode'] ?? ''));
    $mensaje = trim((string)($data['sunat_description'] ?? ''));
    if (!empty($data['aceptada_por_sunat'])) {
        return ['estado' => 'ACEPTADO', 'codigo' => $codigo !== '' ? $codigo : '0', 'mensaje' => $mensaje ?: 'Anulación aceptada'];
    }
    if (ctype_digit($codigo) && (int)$codigo >= 2000 && (int)$codigo < 4000) {
        return ['estado' => 'RECHAZADO', 'codigo' => $codigo, 'mensaje' => $mensaje];
    }
    return ['estado' => 'PENDIENTE', 'codigo' => $codigo, 'mensaje' => $mensaje ?: 'En proceso'];
}
