<?php
use Greenter\Ws\Services\SunatEndpoints;
use Greenter\See;

require __DIR__ . '/../../vendor/autoload.php';

function getSee() {
    $see = new See();

    // Ruta del certificado
    $certPath = __DIR__ . '/../../certificate.pem';
    if (!file_exists($certPath)) {
        die("❌ No se encontró el certificado en: $certPath");
    }

    $see->setCertificate(file_get_contents($certPath));
    $see->setService(SunatEndpoints::FE_BETA);
    $see->setClaveSOL('20000000001', 'MODDATOS', 'moddatos');

    return $see;
}
