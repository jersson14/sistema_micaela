<?php
use Greenter\Ws\Services\SunatEndpoints;
use Greenter\See;

require __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../model/model_conexion.php';

function getSee($pdo = null) {
    // Timeout de socket: evita que SOAP se cuelgue indefinidamente cuando SUNAT tarda
    @ini_set('default_socket_timeout', 15);

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

    // Cliente SOAP con trace para registrar la respuesta HTTP cruda de SUNAT cuando falla un envío
    $wsClient = new \Greenter\Ws\Services\SoapClient('', [
        'trace' => true,
        'stream_context' => stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ],
        ]),
    ]);
    $prop = new \ReflectionProperty(See::class, 'wsClient');
    $prop->setAccessible(true);
    $prop->setValue($see, $wsClient);
    $GLOBALS['greenter_ws_client'] = $wsClient;

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
    echo "🔑 Usuario SOL configurado: {$usuario}\n";
    echo "📦 Certificado: {$certPath}\n\n";

    // ============================
    //  FIX: ENVIAR SOLO EL USUARIO
    // ============================
    $see->setClaveSOL($ruc, $usuario, $clave);

    return $see;
}

function getConnection() {
    try {
        $conexionBD = new conexionBD();
        $pdo = $conexionBD->conexionPDO();
        
        if ($pdo === null) {
            throw new Exception("No se pudo establecer conexión a la base de datos");
        }
        
        return $pdo;
    } catch (Exception $e) {
        error_log("Error de conexión Greenter: " . $e->getMessage());
        die("❌ Error de conexión a la base de datos: " . $e->getMessage() . "\n");
    }
}
