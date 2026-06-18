<?php
date_default_timezone_set('America/Lima');

class conexionBD {
    private static $host;
    private static $usuario;
    private static $contrasena;
    private static $bdName;
    private static $puerto;
    private static $conexion = null;

    public static function conexionPDO() {
        if (self::$conexion === null) {
            // Configuración desde variables de entorno (Docker) o valores por defecto
            self::$host = getenv('DB_HOST') ?: 'localhost';
            self::$usuario = getenv('DB_USER') ?: 'jersson';
            self::$contrasena = getenv('DB_PASSWORD') ?: 'Miranda1407';
            self::$bdName = getenv('DB_NAME') ?: 'micaela';
            self::$puerto = getenv('DB_PORT') ?: 3306;

            try {
                self::$conexion = new PDO(
                    "mysql:host=" . self::$host . ";port=" . self::$puerto . ";dbname=" . self::$bdName . ";charset=utf8mb4",
                    self::$usuario,
                    self::$contrasena,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false
                    ]
                );
                
                // Configurar timezone
                self::$conexion->exec("SET time_zone = '-05:00';");
                
                // Forzar collation utf8mb4_unicode_ci en TODA la sesión
                self::$conexion->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
                self::$conexion->exec("SET CHARACTER SET utf8mb4");
                self::$conexion->exec("SET collation_connection = utf8mb4_unicode_ci");
                self::$conexion->exec("SET collation_database = utf8mb4_unicode_ci");
                self::$conexion->exec("SET collation_server = utf8mb4_unicode_ci");
                
            } catch (PDOException $e) {
                error_log("Error de conexion: " . $e->getMessage());
                die("Error al conectar con la base de datos.");
            }
        }
        return self::$conexion;
    }

    public static function cerrar_conexion() {
        self::$conexion = null;
    }

    public function conectar() {
        return self::conexionPDO();
    }

    public function desconectar() {
        self::cerrar_conexion();
    }
}
?>
