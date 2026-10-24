<?php
/**
 * Para controladores que solo se usan dentro del sistema: corta la petición
 * si no hay sesión iniciada. El usuario que registra las acciones se toma de
 * la sesión ($ID_USUARIO_SESION), no del formulario.
 */
require_once __DIR__ . '/session_config.php';

if (!isset($_SESSION['S_ID']) || empty($_SESSION['S_ID'])) {
    http_response_code(401);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'Sesión expirada. Vuelva a iniciar sesión.', 'data' => []]);
    exit;
}

$ID_USUARIO_SESION = (int)$_SESSION['S_ID'];
