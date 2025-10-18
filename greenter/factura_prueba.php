<?php
require __DIR__ . '/../vendor/autoload.php';  // ✅ ruta correcta

use Greenter\Model\Client\Client;
use Greenter\Model\Company\Company;
use Greenter\Model\Company\Address;
use Greenter\Model\Sale\FormaPagos\FormaPagoContado;
use Greenter\Model\Sale\Invoice;
use Greenter\Model\Sale\SaleDetail;
use Greenter\Model\Sale\Legend;

// Configuración SUNAT
$see = require __DIR__ . '/config/config_greenter.php';

// === CLIENTE ===
$client = (new Client())
    ->setTipoDoc('6')
    ->setNumDoc('20000000001')
    ->setRznSocial('CLIENTE DE PRUEBA');

// === DIRECCIÓN EMISOR ===
$address = (new Address())
    ->setUbigueo('150101')
    ->setDepartamento('LIMA')
    ->setProvincia('LIMA')
    ->setDistrito('LIMA')
    ->setUrbanizacion('-')
    ->setDireccion('Av. Villa Nueva 221')
    ->setCodLocal('0000');

// === EMPRESA EMISORA ===
$company = (new Company())
    ->setRuc('20123456789')
    ->setRazonSocial('GREEN SAC')
    ->setNombreComercial('GREEN')
    ->setAddress($address);

// === FACTURA ===
$invoice = (new Invoice())
    ->setUblVersion('2.1')
    ->setTipoOperacion('0101')
    ->setTipoDoc('01')
    ->setSerie('F001')
    ->setCorrelativo('1')
    ->setFechaEmision(new DateTime('now', new DateTimeZone('America/Lima')))
    ->setFormaPago(new FormaPagoContado())
    ->setTipoMoneda('PEN')
    ->setCompany($company)
    ->setClient($client)
    ->setMtoOperGravadas(100.00)
    ->setMtoIGV(18.00)
    ->setTotalImpuestos(18.00)
    ->setValorVenta(100.00)
    ->setSubTotal(118.00)
    ->setMtoImpVenta(118.00);

// === DETALLE ===
$item = (new SaleDetail())
    ->setCodProducto('P001')
    ->setUnidad('NIU')
    ->setCantidad(2)
    ->setDescripcion('SERVICIO DE TRANSPORTE')
    ->setMtoValorUnitario(50.00)
    ->setMtoBaseIgv(100.00)
    ->setPorcentajeIgv(18.00)
    ->setIgv(18.00)
    ->setTipAfeIgv('10')
    ->setTotalImpuestos(18.00)
    ->setMtoValorVenta(100.00)
    ->setMtoPrecioUnitario(59.00);

// === LEYENDA ===
$legend = (new Legend())
    ->setCode('1000')
    ->setValue('SON CIENTO DIECIOCHO CON 00/100 SOLES');

$invoice->setDetails([$item])
        ->setLegends([$legend]);

// === ENVÍO A SUNAT ===
$result = $see->send($invoice);

// === GUARDAR XML Y CDR ===
$xmlPath = __DIR__ . '/xml/' . $invoice->getName() . '.xml';
$cdrPath = __DIR__ . '/cdr/R-' . $invoice->getName() . '.zip';

file_put_contents($xmlPath, $see->getFactory()->getLastXml());

if (!$result->isSuccess()) {
    echo "❌ Error al enviar a SUNAT:" . PHP_EOL;
    echo $result->getError()->getCode() . ' - ' . $result->getError()->getMessage();
    exit();
}

file_put_contents($cdrPath, $result->getCdrZip());

// === LECTURA DEL CDR ===
$cdr = $result->getCdrResponse();
$code = (int)$cdr->getCode();

if ($code === 0) {
    echo "✅ ESTADO: ACEPTADA" . PHP_EOL;
    echo $cdr->getDescription() . PHP_EOL;
} elseif ($code >= 2000 && $code <= 3999) {
    echo "❌ ESTADO: RECHAZADA" . PHP_EOL;
    echo $cdr->getDescription() . PHP_EOL;
} else {
    echo "⚠️ ESTADO: EXCEPCIÓN" . PHP_EOL;
    echo $cdr->getDescription() . PHP_EOL;
}
