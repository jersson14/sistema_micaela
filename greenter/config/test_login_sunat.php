<?php
require __DIR__ . '/../../vendor/autoload.php';

use Greenter\See;
use Greenter\Ws\Services\SunatEndpoints;

$see = new See();

$see->setCertificate(file_get_contents(__DIR__ . '/../certificados/certificado_produccion.pem'));

$see->setClaveSOL(
    '20603540647',
    '20603540647JERSSON5',
    'Jer2025*'
);

$see->setService(SunatEndpoints::FE_PRODUCCION);

echo "✔️ Objeto SEE configurado correctamente.\n";
echo "Endpoint activo: ".SunatEndpoints::FE_PRODUCCION."\n";
