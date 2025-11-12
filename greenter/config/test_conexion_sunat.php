<?php
require __DIR__ . '/../../vendor/autoload.php';

use Greenter\See;
use Greenter\Ws\Services\SunatEndpoints;

$ruc = '20603540647';
$usuarioSol = 'JERSSON5';
$claveSol = 'Jer2025*';
$certPath = __DIR__ . '/../certificados/certificado_produccion.pem';

try {
    if (!file_exists($certPath)) {
        throw new Exception("❌ No se encontró el certificado en: $certPath");
    }

    // Instancia de See
    $see = new See();
    $see->setCertificate(file_get_contents($certPath));
    
    // Usuario completo: RUC + Usuario SOL
    $usuarioCompleto = $ruc . $usuarioSol;
    $see->setClaveSOL($ruc, $usuarioCompleto, $claveSol);
    $see->setService(SunatEndpoints::FE_PRODUCCION);

    echo "✅ Configuración establecida correctamente\n";
    echo "RUC: $ruc\n";
    echo "Usuario SOL: $usuarioCompleto\n";
    echo "Endpoint: " . SunatEndpoints::FE_PRODUCCION . "\n";
    
    // Para probar la conexión real, necesitas enviar un documento
    // No existe un método directo de "ping" o "getStatus" en Greenter v5

} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}