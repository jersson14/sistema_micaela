<?php
require_once '../../utilitario/session_config.php';

// Destruir todas las variables de sesión
$_SESSION = array();

// Destruir la sesión
session_destroy();

// Limpiar cookie de sesión si existe
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Redirigir al login
header('Location: ../../index.php');
exit;
?>