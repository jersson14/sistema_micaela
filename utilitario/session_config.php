<?php
/**
 * Configuración de sesiones para VPS
 * Este archivo debe ser incluido en todos los archivos que usen sesiones
 */

if (!defined('SESSION_TIMEOUT_SECONDS')) {
    // Sesión activa por 1 hora
    define('SESSION_TIMEOUT_SECONDS', 3600);
}

if (!defined('SESSION_REGENERATE_SECONDS')) {
    // Regenerar ID cada 30 minutos por seguridad
    define('SESSION_REGENERATE_SECONDS', 1800);
}

// Configuración de sesión para producción
ini_set('session.gc_maxlifetime', SESSION_TIMEOUT_SECONDS);
ini_set('session.cookie_lifetime', SESSION_TIMEOUT_SECONDS);
ini_set('session.use_strict_mode', 1); // Modo estricto
ini_set('session.cookie_httponly', 1); // Protección XSS
ini_set('session.cookie_secure', 0); // Cambiar a 1 si usas HTTPS
ini_set('session.use_only_cookies', 1); // Solo cookies
ini_set('session.cookie_samesite', 'Lax'); // Protección CSRF

// Configuración de ruta de sesiones (importante para VPS compartidos)
$session_path = sys_get_temp_dir() . '/php_sessions';
if (!is_dir($session_path)) {
    @mkdir($session_path, 0700, true);
}
ini_set('session.save_path', $session_path);

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Regenerar ID de sesión periódicamente para seguridad
if (!isset($_SESSION['CREATED'])) {
    $_SESSION['CREATED'] = time();
} else if (time() - $_SESSION['CREATED'] > SESSION_REGENERATE_SECONDS) {
    session_regenerate_id(true);
    $_SESSION['CREATED'] = time();
}
?>
