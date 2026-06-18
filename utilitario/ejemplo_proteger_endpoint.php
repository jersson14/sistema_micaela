<?php
/**
 * EJEMPLO: Cómo proteger un endpoint con JWT
 * 
 * Copia este código al inicio de tus controladores para protegerlos
 */

// Opción 1: Protección simple (redirige al login si no está autenticado)
require_once __DIR__ . '/AuthMiddleware.php';
AuthMiddleware::requireAuth();

// Tu código del controlador aquí...
// El usuario está autenticado si llega a este punto


// Opción 2: Protección con verificación manual
require_once __DIR__ . '/AuthMiddleware.php';

if (!AuthMiddleware::checkAuth()) {
    header('Content-Type: application/json');
    http_response_code(401);
    echo json_encode([
        'error' => true,
        'message' => 'No autenticado'
    ]);
    exit;
}

// Tu código del controlador aquí...


// Opción 3: Obtener datos del usuario actual
require_once __DIR__ . '/AuthMiddleware.php';
AuthMiddleware::requireAuth();

$usuario = AuthMiddleware::getCurrentUser();
$id_usuario = $usuario['id_usuario'];
$nombre = $usuario['nombre'];

// Usar los datos del usuario...


// Opción 4: Para APIs que solo usan JWT (sin sesiones)
require_once __DIR__ . '/JWTHelper.php';

$token = JWTHelper::getTokenFromHeader();

if (!$token) {
    header('Content-Type: application/json');
    http_response_code(401);
    echo json_encode(['error' => 'Token no proporcionado']);
    exit;
}

$decoded = JWTHelper::validateToken($token);

if (!$decoded) {
    header('Content-Type: application/json');
    http_response_code(401);
    echo json_encode(['error' => 'Token inválido o expirado']);
    exit;
}

// Acceder a los datos del usuario
$userData = $decoded->data;
$id_usuario = $userData->id_usuario;
