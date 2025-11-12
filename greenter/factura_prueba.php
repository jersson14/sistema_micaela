<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/config/config_greenter.php'; // ✅ carga tu configuración Greenter

use Greenter\Model\Client\Client;
use Greenter\Model\Company\Company;
use Greenter\Model\Company\Address;
use Greenter\Model\Sale\FormaPagos\FormaPagoContado;
use Greenter\Model\Sale\Invoice;
use Greenter\Model\Sale\SaleDetail;
use Greenter\Model\Sale\Legend;

// =============================================
// ✅ 1️⃣ Inicializar conexión con SUNAT
// =============================================
$see = getSee();
echo "✅ Conexión inicializada correctamente.\n";

// =============================================
// ✅ 2️⃣ Cliente de prueba
// =============================================
$client = (new Client())
    ->setTipoDoc('1') // DNI
    ->setNumDoc('12345678')
    ->setRznSocial('CLIENTE DE PRUEBA');

// =============================================
// ✅ 3️⃣ Datos del emisor (tu empresa real)
// =============================================
$address = (new Address())
    ->setUbigueo('150101')
    ->setDepartamento('LIMA')
    ->setProvincia('LIMA')
    ->setDistrito('LIMA')
    ->setDireccion('JR. FICTICIO 123');

$company = (new Company())
    ->setRuc('20603540647')
    ->setRazonSocial('ETIOM S.A.')
    ->setNombreComercial('ETIOM S.A.')
    ->setAddress($address);

// =============================================
// ✅ 4️⃣ Crear factura simple de S/ 1.00
// =============================================
$invoice = (new Invoice())
    ->setUblVersion('2.1')
    ->setTipoOperacion('0101')
    ->setTipoDoc('01')
    ->setSerie('FPP1')
    ->setCorrelativo('98902') // ⚠️ cambia si ya lo usaste
    ->setFechaEmision(new DateTime('now', new DateTimeZone('America/Lima')))
    ->setFormaPago(new FormaPagoContado())
    ->setTipoMoneda('PEN')
    ->setCompany($company)
    ->setClient($client)
    ->setMtoOperGravadas(0.85)
    ->setMtoIGV(0.15)
    ->setTotalImpuestos(0.15)
    ->setValorVenta(0.85)
    ->setSubTotal(1.00)
    ->setMtoImpVenta(1.00);

// =============================================
// ✅ 5️⃣ Detalle del producto
// =============================================
$item = (new SaleDetail())
    ->setCodProducto('P001')
    ->setUnidad('NIU')
    ->setCantidad(1)
    ->setDescripcion('VENTA DE PRUEBA 1 SOL')
    ->setMtoValorUnitario(0.85)
    ->setMtoBaseIgv(0.85)
    ->setPorcentajeIgv(18.00)
    ->setIgv(0.15)
    ->setTipAfeIgv('10')
    ->setTotalImpuestos(0.15)
    ->setMtoValorVenta(0.85)
    ->setMtoPrecioUnitario(1.00);

$invoice->setDetails([$item]);

// =============================================
// ✅ 6️⃣ Leyenda (total en letras)
// =============================================
$legend = (new Legend())
    ->setCode('1000')
    ->setValue('SON UN CON 00/100 SOLES');
$invoice->setLegends([$legend]);

// =============================================
// ✅ 7️⃣ Enviar a SUNAT
// =============================================
echo "🚀 Enviando factura de prueba a SUNAT...\n";
$result = $see->send($invoice);

// =============================================
// ✅ 8️⃣ Guardar XML y CDR
// =============================================
$xmlPath = __DIR__ . '/xml/';
$cdrPath = __DIR__ . '/cdr/';
if (!is_dir($xmlPath)) mkdir($xmlPath, 0777, true);
if (!is_dir($cdrPath)) mkdir($cdrPath, 0777, true);

file_put_contents($xmlPath . $invoice->getName() . '.xml', $see->getFactory()->getLastXml());

if (!$result->isSuccess()) {
    echo "❌ Error al enviar a SUNAT:" . PHP_EOL;
    echo $result->getError()->getCode() . ' - ' . $result->getError()->getMessage() . PHP_EOL;
    exit();
}

file_put_contents($cdrPath . 'R-' . $invoice->getName() . '.zip', $result->getCdrZip());

$cdr = $result->getCdrResponse();
$code = (int)$cdr->getCode();

if ($code === 0) {
    echo "✅ FACTURA ACEPTADA POR SUNAT" . PHP_EOL;
    echo "Descripción: " . $cdr->getDescription() . PHP_EOL;
} elseif ($code >= 2000 && $code <= 3999) {
    echo "❌ FACTURA RECHAZADA" . PHP_EOL;
    echo "Descripción: " . $cdr->getDescription() . PHP_EOL;
} else {
    echo "⚠️ OBSERVACIÓN O ADVERTENCIA" . PHP_EOL;
    echo "Descripción: " . $cdr->getDescription() . PHP_EOL;
}
