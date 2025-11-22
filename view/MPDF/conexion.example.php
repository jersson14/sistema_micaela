<?php
/**
 * Archivo de ejemplo de conexión para mPDF
 * 
 * INSTRUCCIONES:
 * 1. Copia este archivo como "conexion.php"
 * 2. Configura tus credenciales reales
 * 3. NO subas conexion.php a GitHub (está en .gitignore)
 */

date_default_timezone_set('America/Lima');

// CONFIGURACIÓN DE BASE DE DATOS
// Cambia estos valores según tu entorno
$host = "localhost";           // o "db" para Docker
$usuario = "tu_usuario";       // Usuario de MySQL
$contrasena = "tu_contraseña"; // Contraseña de MySQL
$bdName = "micaela";           // Nombre de la base de datos
$puerto = 3307;                // Puerto de MySQL (3307 o 3306)

try {
    $conexion = new PDO(
        "mysql:host={$host};port={$puerto};dbname={$bdName};charset=utf8mb4",
        $usuario,
        $contrasena,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    error_log("Error de conexión mPDF: " . $e->getMessage());
    die("Error al conectar con la base de datos.");
}
