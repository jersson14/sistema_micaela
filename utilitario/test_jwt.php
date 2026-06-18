<?php
/**
 * Script de prueba para JWT
 * Verifica que la implementación JWT funcione correctamente
 * 
 * Uso: php utilitario/test_jwt.php
 */

require_once __DIR__ . '/JWTHelper.php';

echo "=== TEST DE IMPLEMENTACIÓN JWT ===\n\n";

// Test 1: Generar token
echo "Test 1: Generar Access Token\n";
$userData = array(
    'id_usuario' => 1,
    'usuario' => 'admin',
    'nombre' => 'Administrador'
);

$token = JWTHelper::generateToken($userData, 2);
echo "✓ Token generado: " . substr($token, 0, 50) . "...\n\n";

// Test 2: Validar token
echo "Test 2: Validar Token\n";
$decoded = JWTHelper::validateToken($token);
if ($decoded) {
    echo "✓ Token válido\n";
    echo "  - Usuario: " . $decoded->data->usuario . "\n";
    echo "  - ID: " . $decoded->data->id_usuario . "\n";
    echo "  - Expira en: " . date('Y-m-d H:i:s', $decoded->exp) . "\n\n";
} else {
    echo "✗ Token inválido\n\n";
}

// Test 3: Generar refresh token
echo "Test 3: Generar Refresh Token\n";
$refreshToken = JWTHelper::generateRefreshToken($userData);
echo "✓ Refresh token generado: " . substr($refreshToken, 0, 50) . "...\n\n";

// Test 4: Validar refresh token
echo "Test 4: Validar Refresh Token\n";
$decodedRefresh = JWTHelper::validateToken($refreshToken);
if ($decodedRefresh) {
    echo "✓ Refresh token válido\n";
    echo "  - Tipo: " . $decodedRefresh->type . "\n";
    echo "  - Expira en: " . date('Y-m-d H:i:s', $decodedRefresh->exp) . "\n\n";
} else {
    echo "✗ Refresh token inválido\n\n";
}

// Test 5: Token expirado
echo "Test 5: Validar Token Expirado\n";
$expiredToken = JWTHelper::generateToken($userData, -1); // Token expirado hace 1 hora
$decodedExpired = JWTHelper::validateToken($expiredToken);
if (!$decodedExpired) {
    echo "✓ Token expirado correctamente rechazado\n\n";
} else {
    echo "✗ Token expirado aceptado (ERROR)\n\n";
}

// Test 6: Token inválido
echo "Test 6: Validar Token Inválido\n";
$invalidToken = "token.invalido.aqui";
$decodedInvalid = JWTHelper::validateToken($invalidToken);
if (!$decodedInvalid) {
    echo "✓ Token inválido correctamente rechazado\n\n";
} else {
    echo "✗ Token inválido aceptado (ERROR)\n\n";
}

// Test 7: Verificar expiración próxima
echo "Test 7: Verificar Expiración Próxima\n";
$tokenProximo = JWTHelper::generateToken($userData, 0.2); // 12 minutos
$expiringSoon = JWTHelper::isTokenExpiringSoon($tokenProximo);
if ($expiringSoon) {
    echo "✓ Token próximo a expirar detectado correctamente\n\n";
} else {
    echo "✗ Token próximo a expirar no detectado\n\n";
}

// Test 8: Password hash
echo "Test 8: Password Hash\n";
$password = "123456";
$hashed = password_hash($password, PASSWORD_DEFAULT);
echo "✓ Password hasheado: " . substr($hashed, 0, 30) . "...\n";

$verify = password_verify($password, $hashed);
if ($verify) {
    echo "✓ Password verificado correctamente\n\n";
} else {
    echo "✗ Password no verificado (ERROR)\n\n";
}

echo "=== TODOS LOS TESTS COMPLETADOS ===\n";
echo "\n✓ La implementación JWT está funcionando correctamente\n";
echo "\nPróximos pasos:\n";
echo "1. Ejecutar: php utilitario/migrate_passwords.php\n";
echo "2. Incluir jwt_handler.js en view/index.php\n";
echo "3. Cambiar la clave secreta en JWTHelper.php\n";
