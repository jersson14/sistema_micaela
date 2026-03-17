<?php
require '../../utilitario/jwt_config.php';
require '../../utilitario/JWTHelper.php';
require '../../model/model_usuario.php';

header('Content-Type: application/json');

// Obtener refresh token
$refreshToken = $_POST['refresh_token'] ?? null;

if (!$refreshToken) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Refresh token no proporcionado'
    ]);
    exit;
}

// Validar refresh token
$decoded = JWTHelper::validateToken($refreshToken);

if (!$decoded || !isset($decoded->type) || $decoded->type !== 'refresh') {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Refresh token inválido o expirado'
    ]);
    exit;
}

// Obtener datos actualizados del usuario
$MU = new Modelo_Usuario();
$userId = $decoded->data->id_usuario;

// Aquí podrías hacer una consulta para obtener datos actualizados
// Por ahora, generamos un nuevo token con los datos del refresh token
$userData = array(
    'id_usuario' => $decoded->data->id_usuario,
    'usuario' => $decoded->data->usuario
);

// Generar nuevo access token
$newAccessToken = JWTHelper::generateToken($userData, JWT_ACCESS_TOKEN_EXPIRATION);

echo json_encode([
    'success' => true,
    'tokens' => [
        'access_token' => $newAccessToken,
        'expires_in' => JWT_ACCESS_TOKEN_EXPIRATION * 3600
    ]
]);
