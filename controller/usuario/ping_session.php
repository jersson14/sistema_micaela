<?php
require_once '../../utilitario/session_config.php';

// Verificar si hay sesión activa
if (isset($_SESSION['S_ID']) && !empty($_SESSION['S_ID'])) {
    // Actualizar tiempo de última actividad
    $_SESSION['LAST_ACTIVITY'] = time();
    
    // Respuesta exitosa
    header('Content-Type: application/json');
    echo json_encode([
        'active' => true,
        'user' => $_SESSION['S_NOMBRE'],
        'role' => $_SESSION['S_NOMBRE_ROL'],
        'time' => time(),
        'last_activity' => $_SESSION['LAST_ACTIVITY']
    ]);
} else {
    // No hay sesión activa
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode([
        'active' => false,
        'message' => 'Sesión no encontrada'
    ]);
}
?>