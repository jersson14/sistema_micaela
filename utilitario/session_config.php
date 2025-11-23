<?php
/**
 * Configuración de sesiones para VPS
 * Este archivo debe ser incluido en todos los archivos que usen sesiones
 */

// Configuración de sesión para producción
ini_set('session.gc_maxlifetime', 7200); // 2 horas
ini_set('session.cookie_lifetime', 7200); // 2 horas
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
} else if (time() - $_SESSION['CREATED'] > 1800) {
    // Regenerar cada 30 minutos
    session_regenerate_id(true);
    $_SESSION['CREATED'] = time();
}
?>
