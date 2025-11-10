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

    $see = new See();

    // ✅ Ruta correcta del certificado .pem
    $certPath = __DIR__ . '/../certificados/certificado_produccion.pem';
    if (!file_exists($certPath)) {
        die("❌ No se encontró el certificado en: $certPath");
    }

    $see->setCertificate(file_get_contents($certPath));

    // ✅ Configurar endpoint según el modo
    $estado = $empresa['modo_prueba'] ?? 1;
    if ($estado == 1) {
        $see->setService(SunatEndpoints::FE_BETA);
    } else {
        $see->setService(SunatEndpoints::FE_PRODUCCION);
    }

    // ✅ Configurar credenciales SOL desde la BD
    $ruc     = $empresa['ruc'];
    $usuario = $empresa['usuario_sol'];
    $clave   = $empresa['clave_sol'];

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
