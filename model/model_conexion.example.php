<?php
/**
 * Archivo de ejemplo de conexión a la base de datos
 * 
 * INSTRUCCIONES:
 * 1. Copia este archivo como "model_conexion.php"
 * 2. Configura tus credenciales reales
 * 3. NO subas model_conexion.php a GitHub (está en .gitignore)
 */

date_default_timezone_set('America/Lima');

class conexionBD {
    private $host;
    private $usuario;
    private $contrasena;
    private $bdName;
    private $puerto;
    private $conexion;

    public function __construct() {
        // CONFIGURACIÓN DE BASE DE DATOS
        // Cambia estos valores según tu entorno
        $this->host = "localhost";           // o "db" para Docker
        $this->usuario = "tu_usuario";       // Usuario de MySQL
        $this->contrasena = "tu_contraseña"; // Contraseña de MySQL
        $this->bdName = "micaela";           // Nombre de la base de datos
        $this->puerto = 3307;                // Puerto de MySQL (3307 o 3306)
    }

    public function conectar() {
        try {
            $this->conexion = new PDO(
                "mysql:host={$this->host};port={$this->puerto};dbname={$this->bdName};charset=utf8mb4",
                $this->usuario,
                $this->contrasena,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
            return $this->conexion;
        } catch (PDOException $e) {
            error_log("Error de conexión: " . $e->getMessage());
            die("Error al conectar con la base de datos. Verifica tu configuración.");
        }
    }

    public function desconectar() {
        $this->conexion = null;
    }
}
