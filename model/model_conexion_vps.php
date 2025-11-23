<?php
// ============================================
// CONEXIÓN PARA VPS CON DOCKER
// ============================================
class conexionBD {
    private $pdo;

    public function conexionPDO() {
        // Configuración para Docker en VPS
        $host = getenv('DB_HOST') ?: 'db';  // Nombre del servicio en docker-compose
        $usuario = getenv('DB_USER') ?: 'micaela_user';
        $contrasena = getenv('DB_PASSWORD') ?: 'micaela_pass_2024_VPS';
        $bdName = getenv('DB_NAME') ?: 'micaela';
        $puerto = getenv('DB_PORT') ?: 3306;  // Puerto interno de Docker

        try {
            $this->pdo = new PDO("mysql:host=$host;port=$puerto;dbname=$bdName;charset=utf8mb4", $usuario, $contrasena);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Forzar collation para evitar conflictos
            $this->pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
            $this->pdo->exec("SET CHARACTER SET utf8mb4");
            $this->pdo->exec("SET collation_connection = utf8mb4_unicode_ci");
            return $this->pdo;
        } catch (PDOException $e) {
            error_log('Error de conexión BD: ' . $e->getMessage());
            echo 'Error al conectar con la base de datos. Por favor, contacte al administrador.';
            return null;
        }
    }

    public function cerrar_conexion() {
        $this->pdo = null;
    }
}
?>
