<?php
use Greenter\Ws\Services\SunatEndpoints;
use Greenter\See;

require __DIR__ . '/../../vendor/autoload.php';

function getSee($pdo = null) {
    // Si no se pasa una conexión PDO, crearla
    if ($pdo === null) {
        $pdo = getConnection();
    }

    // Obtener datos de la empresa desde la BD
    $stmt = $pdo->query("SELECT * FROM empresa LIMIT 1");
    $empresa = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$empresa) {
        die("❌ No se encontró información de la empresa en la base de datos");
    }

    // DEBUG: Descomentar para ver los campos disponibles
    // echo "<pre>"; print_r($empresa); echo "</pre>"; die();

    $see = new See();

    // Ruta del certificado fija
    $certPath = __DIR__ . '/../../certificate.pem';
    if (!file_exists($certPath)) {
        die("❌ No se encontró el certificado en: $certPath");
    }

    $see->setCertificate(file_get_contents($certPath));

    // Configurar endpoint según ambiente (producción/beta)
    $estado = $empresa['estado'] ?? 1;
    
    if ($estado == 1) { // 1 = Beta/Testing
        $see->setService(SunatEndpoints::FE_BETA);
    } else { // 0 = Producción
        $see->setService(SunatEndpoints::FE_PRODUCCION);
    }

    // Configurar credenciales SOL desde la BD
    $ruc = $empresa['ruc'];
    $usuario = $empresa['usuario_sol'];  // Campo correcto de tu BD
    $clave = $empresa['clave_sol'];      // Campo correcto de tu BD

    // Validar que los campos existan
    if (empty($ruc) || empty($usuario) || empty($clave)) {
        die("❌ Faltan datos de configuración SOL en la base de datos");
    }

    $see->setClaveSOL($ruc, $usuario, $clave);

    return $see;
}

function getConnection() {
    try {
        $pdo = new PDO(
            'mysql:host=127.0.0.1;port=3307;dbname=micaela;charset=utf8mb4',
            'root',
            '',
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        die("❌ Error de conexión a la base de datos: " . $e->getMessage());
    }
}