<?php
/**
 * Configuración de conexión para Docker
 * Este archivo usa variables de entorno para conectarse a la BD
 */
class conexionBD {
    private $pdo;

    public function conexionPDO() {
        // Leer variables de entorno o usar valores por defecto
        $host = getenv('DB_HOST') ?: 'db';
        $usuario = getenv('DB_USER') ?: 'micaela_user';
        $contrasena = getenv('DB_PASSWORD') ?: 'micaela_pass_2024';
        $bdName = getenv('DB_NAME') ?: 'micaela';
        $puerto = getenv('DB_PORT') ?: '3306';

        try {
            $this->pdo = new PDO("mysql:host=$host;port=$puerto;dbname=$bdName", $usuario, $contrasena);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->exec("set names utf8");
            return $this->pdo;
        } catch (PDOException $e) {
            echo 'Falló la conexión: ' . $e->getMessage();
            return null;
        }
    }

    public function cerrar_conexion() {
        $this->pdo = null;
    }
}
?>
