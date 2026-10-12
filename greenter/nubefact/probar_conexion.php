<?php
/**
 * Prueba la conexión con Nubefact SIN emitir comprobantes.
 * Consulta un comprobante que no existe: si responde "no existe", la RUTA y el TOKEN están bien.
 *
 * Uso: php greenter/nubefact/probar_conexion.php
 */
require_once __DIR__ . '/nubefact_api.php';

$config = nubefactConfig();
if ($config === null) {
    exit("❌ Nubefact no está activo: falta .env.nubefact, o NUBEFACT_RUTA / NUBEFACT_TOKEN, o NUBEFACT_ACTIVO=0.\n");
}

echo "Modo: {$config['modo']}\n";
echo "Ruta: " . preg_replace('#/[^/]{8,}$#', '/****', $config['ruta']) . "\n";
echo "Token: " . substr($config['token'], 0, 4) . str_repeat('*', 12) . "\n\n";

$r = nubefactPost([
    'operacion' => 'consultar_comprobante',
    'tipo_de_comprobante' => 1,
    'serie' => nubefactSerie('FPP1'),
    'numero' => 99999999,
]);

if ($r['data'] === null) {
    exit("❌ Sin respuesta válida de Nubefact. " . ($r['error_red'] ?: "HTTP {$r['http']}: " . substr((string)$r['crudo'], 0, 200)) . "\n");
}

$errores = $r['data']['errors'] ?? null;
$texto = is_array($errores) ? json_encode($errores, JSON_UNESCAPED_UNICODE) : (string)$errores;
echo "HTTP {$r['http']} | Respuesta: " . ($texto !== '' ? $texto : json_encode($r['data'], JSON_UNESCAPED_UNICODE)) . "\n\n";

if ($errores && stripos($texto, 'existe') !== false) {
    echo "✅ CONEXIÓN CORRECTA: la ruta y el token funcionan (el comprobante de prueba no existe, como se esperaba).\n";
} elseif ($errores && nubefactErrorEsTemporal($r['data']['codigo'] ?? null, $texto)) {
    echo "❌ Revisa la RUTA y el TOKEN en .env.nubefact.\n";
} else {
    echo "⚠️ Respuesta no esperada. Revisa el mensaje de arriba.\n";
}
