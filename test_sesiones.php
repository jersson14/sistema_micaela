<?php
/**
 * Script de prueba para verificar configuración de sesiones
 * Acceder a: http://tu-dominio.com/test_sesiones.php
 */

// Incluir configuración de sesiones
require_once 'utilitario/session_config.php';

// Información de configuración
$info = array(
    'PHP Version' => phpversion(),
    'Session Status' => session_status() === PHP_SESSION_ACTIVE ? 'ACTIVA' : 'INACTIVA',
    'Session ID' => session_id(),
    'Session Save Path' => session_save_path(),
    'Session Name' => session_name(),
    'Session Cookie Params' => session_get_cookie_params(),
    'Session GC MaxLifetime' => ini_get('session.gc_maxlifetime'),
    'Session Cookie Lifetime' => ini_get('session.cookie_lifetime'),
    'Writable Session Path' => is_writable(session_save_path()) ? 'SI' : 'NO',
    'Session Path Exists' => is_dir(session_save_path()) ? 'SI' : 'NO',
);

// Probar escritura de sesión
$_SESSION['test_time'] = time();
$_SESSION['test_data'] = 'Prueba de sesión';

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test de Sesiones - Tours Micaela</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #023D77;
            border-bottom: 3px solid #023D77;
            padding-bottom: 10px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .info-table th, .info-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .info-table th {
            background: #023D77;
            color: white;
            font-weight: bold;
        }
        .info-table tr:hover {
            background: #f5f5f5;
        }
        .status {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
        }
        .status.ok {
            background: #4CAF50;
            color: white;
        }
        .status.error {
            background: #f44336;
            color: white;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .success {
            background: #d4edda;
            border: 1px solid #28a745;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        pre {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Test de Configuración de Sesiones</h1>
        
        <?php if (session_status() === PHP_SESSION_ACTIVE): ?>
            <div class="success">
                ✅ <strong>Sesión activa correctamente</strong>
            </div>
        <?php else: ?>
            <div class="warning">
                ⚠️ <strong>Advertencia:</strong> La sesión no está activa
            </div>
        <?php endif; ?>
        
        <?php if (!is_writable(session_save_path())): ?>
            <div class="warning">
                ⚠️ <strong>Advertencia:</strong> El directorio de sesiones no tiene permisos de escritura.<br>
                Ejecuta: <code>chmod 700 <?php echo session_save_path(); ?></code>
            </div>
        <?php endif; ?>
        
        <h2>Información de Configuración</h2>
        <table class="info-table">
            <thead>
                <tr>
                    <th>Parámetro</th>
                    <th>Valor</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($info as $key => $value): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($key); ?></strong></td>
                        <td>
                            <?php 
                            if (is_array($value)) {
                                echo '<pre>' . print_r($value, true) . '</pre>';
                            } else {
                                echo htmlspecialchars($value);
                                
                                // Mostrar estado
                                if ($key === 'Session Status' && $value === 'ACTIVA') {
                                    echo ' <span class="status ok">OK</span>';
                                } elseif ($key === 'Session Status' && $value === 'INACTIVA') {
                                    echo ' <span class="status error">ERROR</span>';
                                } elseif ($key === 'Writable Session Path' && $value === 'SI') {
                                    echo ' <span class="status ok">OK</span>';
                                } elseif ($key === 'Writable Session Path' && $value === 'NO') {
                                    echo ' <span class="status error">ERROR</span>';
                                }
                            }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <h2>Variables de Sesión Actuales</h2>
        <pre><?php print_r($_SESSION); ?></pre>
        
        <h2>Recomendaciones</h2>
        <ul>
            <li>Verifica que el directorio de sesiones tenga permisos 700</li>
            <li>Asegúrate de que PHP tenga permisos para escribir en el directorio</li>
            <li>Si usas HTTPS, cambia <code>session.cookie_secure</code> a 1</li>
            <li>Verifica que no haya espacios o saltos de línea antes de <code>&lt;?php</code></li>
        </ul>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; text-align: center; color: #666;">
            <p>Tours Micaela - Sistema de Gestión</p>
            <p><small>Generado: <?php echo date('Y-m-d H:i:s'); ?></small></p>
        </div>
    </div>
</body>
</html>
