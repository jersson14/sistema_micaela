<?php
require __DIR__ . '/config_greenter.php';

$see = getSee();

echo "✅ Conexión establecida con configuración correcta.<br>";

// Mostrar endpoint en función del modo (de tu BD)
$pdo = new PDO('mysql:host=127.0.0.1;port=3307;dbname=micaela;charset=utf8mb4', 'root', '');
$stmt = $pdo->query("SELECT modo_prueba FROM empresa LIMIT 1");
$modo = (int)$stmt->fetchColumn();

if ($modo === 1) {
    echo "🛰️ Endpoint activo: SUNAT BETA (Pruebas)<br>";
} else {
    echo "🛰️ Endpoint activo: SUNAT PRODUCCIÓN<br>";
}

// Verificar el certificado
$certPath = __DIR__ . '/../certificados/certificado_produccion.pem';
if (file_exists($certPath)) {
    echo "🔐 Certificado encontrado: " . realpath($certPath) . "<br>";
    echo "📦 Tamaño: " . filesize($certPath) . " bytes<br>";

    // Verificar si contiene clave privada
    $contenido = file_get_contents($certPath);
    if (strpos($contenido, 'PRIVATE KEY') !== false) {
        echo "🔑 Contiene clave privada (OK)<br>";
    } else {
        echo "⚠️ No se encontró clave privada dentro del certificado<br>";
    }
} else {
    echo "❌ Certificado no encontrado.<br>";
}
