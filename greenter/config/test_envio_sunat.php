<?php
require __DIR__ . '/../../vendor/autoload.php';

use Greenter\See;
use Greenter\Ws\Services\SunatEndpoints;
use Greenter\Model\Document\Voided;

// === CONFIG ===
$ruc = '20603540647';
$usuario = 'JERSSON5';
$clave = 'Jer2025*';
$certificado = __DIR__ . '/../certificados/certificado_produccion.pem';

// === VALIDAR CERT ===
if (!file_exists($certificado)) {
    die("❌ No existe el certificado en: $certificado");
}

// === CONFIGURAR SEE ===
$see = new See();
$see->setCertificate(file_get_contents($certificado));
$see->setClaveSOL($ruc, $ruc . $usuario, $clave);
$see->setService(SunatEndpoints::FE_PRODUCCION);

echo "📡 Enviando documento de prueba a SUNAT...\n";

// === DOCUMENTO DE ENVÍO (RA vacía) ===
$voided = new Voided();
$voided->setId('RA-' . date('Ymd') . '-001');
$voided->setFecComunicacion(new DateTime());
$voided->setFecGeneracion(new DateTime());
$voided->setDetails([]);

// === ENVÍO ===
$res = $see->send($voided);

// === RESPUESTA ===
if ($res->isSuccess()) {
    echo "🟩 Login correcto. SUNAT aceptó el envío.\n";
    echo "Ticket: " . $res->getTicket() . "\n";
} else {
    echo "🟥 Error al validar login con SUNAT:\n";
    echo $res->getError()->getMessage() . "\n";
}
