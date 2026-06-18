<?php
/**
 * Script de migración de contraseñas a bcrypt
 * Este script actualiza las contraseñas existentes que no estén hasheadas con password_hash
 * 
 * IMPORTANTE: Ejecutar solo UNA VEZ
 * Uso: php utilitario/migrate_passwords.php
 */

require_once __DIR__ . '/../model/model_conexion.php';

echo "=== MIGRACIÓN DE CONTRASEÑAS A BCRYPT ===\n\n";
echo "Este script actualizará las contraseñas que no estén hasheadas con bcrypt.\n";
echo "¿Desea continuar? (s/n): ";

$handle = fopen("php://stdin", "r");
$line = fgets($handle);
if (trim($line) != 's') {
    echo "Operación cancelada.\n";
    exit;
}

try {
    $c = conexionBD::conexionPDO();
    
    // Obtener todos los usuarios
    $sql = "SELECT id_usuario, usu_usuario, usu_contrasenia FROM usuario";
    $query = $c->prepare($sql);
    $query->execute();
    $usuarios = $query->fetchAll(PDO::FETCH_ASSOC);
    
    $total = count($usuarios);
    $actualizados = 0;
    $ya_hasheados = 0;
    
    echo "\nTotal de usuarios encontrados: $total\n\n";
    
    foreach ($usuarios as $usuario) {
        $id = $usuario['id_usuario'];
        $username = $usuario['usu_usuario'];
        $password = $usuario['usu_contrasenia'];
        
        // Verificar si ya está hasheada con password_hash
        // Las contraseñas bcrypt empiezan con $2y$
        if (substr($password, 0, 4) === '$2y$') {
            echo "✓ Usuario '$username' ya tiene contraseña hasheada\n";
            $ya_hasheados++;
            continue;
        }
        
        // Hashear la contraseña
        $nueva_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Actualizar en la base de datos
        $sql_update = "UPDATE usuario SET usu_contrasenia = ? WHERE id_usuario = ?";
        $query_update = $c->prepare($sql_update);
        $query_update->bindParam(1, $nueva_password);
        $query_update->bindParam(2, $id);
        
        if ($query_update->execute()) {
            echo "✓ Usuario '$username' actualizado correctamente\n";
            $actualizados++;
        } else {
            echo "✗ Error al actualizar usuario '$username'\n";
        }
    }
    
    echo "\n=== RESUMEN ===\n";
    echo "Total de usuarios: $total\n";
    echo "Ya hasheados: $ya_hasheados\n";
    echo "Actualizados: $actualizados\n";
    echo "\n¡Migración completada!\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
