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

    // Ruta del certificado PEM de producción
    $certPath = __DIR__ . '/../certificados/certificado_produccion.pem';
    if (!file_exists($certPath)) {
        die("❌ No se encontró el certificado en: $certPath");
    }

    $see->setCertificate(file_get_contents($certPath));

    // Determinar ambiente
    $estado = isset($empresa['modo_prueba']) ? (int)$empresa['modo_prueba'] : 1;

    if ($estado === 1) {
        echo "🧪 Entorno activo: SUNAT BETA (PRUEBAS)\n";
        $see->setService(SunatEndpoints::FE_BETA);
    } else {
        echo "🚀 Entorno activo: SUNAT PRODUCCIÓN\n";
        $see->setService(SunatEndpoints::FE_PRODUCCION);
    }

    // Configurar credenciales SOL
    $ruc     = trim($empresa['ruc']);
    $usuario = trim($empresa['usuario_sol']);  // Ej: FACTURA1
    $clave   = trim($empresa['clave_sol']);    // Ej: Clave SOL

    if (empty($ruc) || empty($usuario) || empty($clave)) {
        die("❌ Faltan datos de configuración SOL en la base de datos\n");
    }

    // Mostrar depuración
    echo "🔑 RUC: {$ruc}\n";
    echo "🔑 Usuario SOL enviado a SUNAT: {$usuario}\n";
    echo "📦 Certificado: {$certPath}\n\n";

    // ============================
    //  FIX: ENVIAR SOLO EL USUARIO
    // ============================
    $see->setClaveSOL($ruc, $usuario, $clave);

    return $see;
}

function getConnection() {
    // Configuración para VPS con Docker usando variables de entorno
    $host = getenv('DB_HOST') ?: 'db';
    $usuario = getenv('DB_USER') ?: 'micaela_user';
    $contrasena = getenv('DB_PASSWORD') ?: 'micaela_pass_2024_VPS';
    $bdName = getenv('DB_NAME') ?: 'micaela';
    $puerto = getenv('DB_PORT') ?: 3306;
    
    try {
        $pdo = new PDO(
            "mysql:host={$host};port={$puerto};dbname={$bdName};charset=utf8mb4",
            $usuario,
            $contrasena,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
        
        // Forzar collation para evitar conflictos
        $pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("SET CHARACTER SET utf8mb4");
        
        return $pdo;
    } catch (PDOException $e) {
        die("❌ Error de conexión a la base de datos: " . $e->getMessage() . "\n");
    }
}
