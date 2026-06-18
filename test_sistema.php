<?php
/**
 * Script de Pruebas Automatizadas
 * Tours Micaela - Sistema de Facturación
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Colores para terminal
class Colors {
    public static $GREEN = "\033[0;32m";
    public static $RED = "\033[0;31m";
    public static $YELLOW = "\033[1;33m";
    public static $BLUE = "\033[0;34m";
    public static $NC = "\033[0m";
}

class TestRunner {
    private $passed = 0;
    private $failed = 0;
    private $tests = [];

    public function test($name, $callback) {
        echo "\n" . Colors::$BLUE . "🧪 Probando: $name" . Colors::$NC . "\n";
        
        try {
            $result = $callback();
            if ($result) {
                $this->passed++;
                echo Colors::$GREEN . "✓ PASÓ" . Colors::$NC . "\n";
                $this->tests[] = ['name' => $name, 'status' => 'PASÓ'];
            } else {
                $this->failed++;
                echo Colors::$RED . "✗ FALLÓ" . Colors::$NC . "\n";
                $this->tests[] = ['name' => $name, 'status' => 'FALLÓ'];
            }
        } catch (Exception $e) {
            $this->failed++;
            echo Colors::$RED . "✗ ERROR: " . $e->getMessage() . Colors::$NC . "\n";
            $this->tests[] = ['name' => $name, 'status' => 'ERROR: ' . $e->getMessage()];
        }
    }

    public function summary() {
        $total = $this->passed + $this->failed;
        $percentage = $total > 0 ? round(($this->passed / $total) * 100, 2) : 0;
        
        echo "\n" . str_repeat("=", 60) . "\n";
        echo Colors::$BLUE . "📊 RESUMEN DE PRUEBAS" . Colors::$NC . "\n";
        echo str_repeat("=", 60) . "\n";
        echo "Total de pruebas: $total\n";
        echo Colors::$GREEN . "✓ Pasaron: {$this->passed}" . Colors::$NC . "\n";
        echo Colors::$RED . "✗ Fallaron: {$this->failed}" . Colors::$NC . "\n";
        echo "Porcentaje de éxito: $percentage%\n";
        echo str_repeat("=", 60) . "\n";

        if ($this->failed > 0) {
            echo "\n" . Colors::$YELLOW . "⚠️  Pruebas que fallaron:" . Colors::$NC . "\n";
            foreach ($this->tests as $test) {
                if ($test['status'] !== 'PASÓ') {
                    echo "  - {$test['name']}: {$test['status']}\n";
                }
            }
        }

        return $this->failed === 0;
    }
}

// Iniciar pruebas
echo Colors::$BLUE;
echo "╔════════════════════════════════════════════════════════╗\n";
echo "║     PRUEBAS AUTOMATIZADAS - TOURS MICAELA              ║\n";
echo "║     Sistema de Facturación Electrónica                ║\n";
echo "╚════════════════════════════════════════════════════════╝\n";
echo Colors::$NC;

$test = new TestRunner();

// ============================================
// 1. PRUEBAS DE ENTORNO
// ============================================
echo "\n" . Colors::$YELLOW . "═══ 1. PRUEBAS DE ENTORNO ═══" . Colors::$NC . "\n";

$test->test("PHP versión >= 7.4", function() {
    return version_compare(PHP_VERSION, '7.4.0', '>=');
});

$test->test("Extensión PDO instalada", function() {
    return extension_loaded('pdo');
});

$test->test("Extensión PDO MySQL instalada", function() {
    return extension_loaded('pdo_mysql');
});

$test->test("Extensión MySQLi instalada", function() {
    return extension_loaded('mysqli');
});

$test->test("Extensión MBString instalada", function() {
    return extension_loaded('mbstring');
});

$test->test("Extensión GD instalada", function() {
    return extension_loaded('gd');
});

$test->test("Extensión ZIP instalada", function() {
    return extension_loaded('zip');
});

$test->test("Extensión SOAP instalada", function() {
    return extension_loaded('soap');
});

// ============================================
// 2. PRUEBAS DE ARCHIVOS CRÍTICOS
// ============================================
echo "\n" . Colors::$YELLOW . "═══ 2. PRUEBAS DE ARCHIVOS CRÍTICOS ═══" . Colors::$NC . "\n";

$test->test("Archivo index.php existe", function() {
    return file_exists('index.php');
});

$test->test("Archivo view/index.php existe", function() {
    return file_exists('view/index.php');
});

$test->test("Archivo model/model_conexion.php existe", function() {
    return file_exists('model/model_conexion.php');
});

$test->test("Archivo model/model_usuario.php existe", function() {
    return file_exists('model/model_usuario.php');
});

$test->test("Directorio greenter existe", function() {
    return is_dir('greenter');
});

$test->test("Directorio controller existe", function() {
    return is_dir('controller');
});

$test->test("Directorio view existe", function() {
    return is_dir('view');
});

$test->test("Directorio model existe", function() {
    return is_dir('model');
});

// ============================================
// 3. PRUEBAS DE COMPOSER
// ============================================
echo "\n" . Colors::$YELLOW . "═══ 3. PRUEBAS DE COMPOSER ═══" . Colors::$NC . "\n";

$test->test("Archivo composer.json existe", function() {
    return file_exists('composer.json');
});

$test->test("Directorio vendor existe", function() {
    return is_dir('vendor');
});

$test->test("Autoload de Composer existe", function() {
    return file_exists('vendor/autoload.php');
});

$test->test("Librería Greenter instalada", function() {
    return file_exists('vendor/greenter/greenter');
});

$test->test("Librería Firebase JWT instalada", function() {
    return file_exists('vendor/firebase/php-jwt');
});

// ============================================
// 4. PRUEBAS DE JWT
// ============================================
echo "\n" . Colors::$YELLOW . "═══ 4. PRUEBAS DE JWT ═══" . Colors::$NC . "\n";

$test->test("Archivo JWTHelper.php existe", function() {
    return file_exists('utilitario/JWTHelper.php');
});

$test->test("Archivo AuthMiddleware.php existe", function() {
    return file_exists('utilitario/AuthMiddleware.php');
});

$test->test("JWT puede generar tokens", function() {
    require_once 'utilitario/JWTHelper.php';
    $token = JWTHelper::generateToken(['id' => 1, 'usuario' => 'test'], 1);
    return !empty($token);
});

$test->test("JWT puede validar tokens", function() {
    require_once 'utilitario/JWTHelper.php';
    $token = JWTHelper::generateToken(['id' => 1, 'usuario' => 'test'], 1);
    $decoded = JWTHelper::validateToken($token);
    return $decoded !== false && isset($decoded->data);
});

$test->test("JWT rechaza tokens inválidos", function() {
    require_once 'utilitario/JWTHelper.php';
    $decoded = JWTHelper::validateToken('token_invalido_123');
    return $decoded === false;
});

// ============================================
// 5. PRUEBAS DE DIRECTORIOS ESCRIBIBLES
// ============================================
echo "\n" . Colors::$YELLOW . "═══ 5. PRUEBAS DE PERMISOS ═══" . Colors::$NC . "\n";

$test->test("Directorio greenter/xml es escribible", function() {
    if (!is_dir('greenter/xml')) {
        mkdir('greenter/xml', 0755, true);
    }
    return is_writable('greenter/xml');
});

$test->test("Directorio greenter/cdr es escribible", function() {
    if (!is_dir('greenter/cdr')) {
        mkdir('greenter/cdr', 0755, true);
    }
    return is_writable('greenter/cdr');
});

$test->test("Directorio greenter/pdf es escribible", function() {
    if (!is_dir('greenter/pdf')) {
        mkdir('greenter/pdf', 0755, true);
    }
    return is_writable('greenter/pdf');
});

$test->test("Directorio Fotos es escribible", function() {
    if (!is_dir('Fotos')) {
        mkdir('Fotos', 0755, true);
    }
    return is_writable('Fotos');
});

// ============================================
// 6. PRUEBAS DE SEGURIDAD
// ============================================
echo "\n" . Colors::$YELLOW . "═══ 6. PRUEBAS DE SEGURIDAD ═══" . Colors::$NC . "\n";

$test->test("Password hash funciona", function() {
    $password = "test123";
    $hash = password_hash($password, PASSWORD_DEFAULT);
    return password_verify($password, $hash);
});

$test->test("Password hash rechaza contraseñas incorrectas", function() {
    $password = "test123";
    $hash = password_hash($password, PASSWORD_DEFAULT);
    return !password_verify("wrong_password", $hash);
});

$test->test("Función htmlspecialchars disponible", function() {
    $test = htmlspecialchars("<script>alert('xss')</script>");
    return strpos($test, '<script>') === false;
});

// ============================================
// 7. PRUEBAS DE DOCKER
// ============================================
echo "\n" . Colors::$YELLOW . "═══ 7. PRUEBAS DE DOCKER ═══" . Colors::$NC . "\n";

$test->test("Dockerfile.production existe", function() {
    return file_exists('Dockerfile.production');
});

$test->test("docker-compose.production.yml existe", function() {
    return file_exists('docker-compose.production.yml');
});

$test->test(".dockerignore existe", function() {
    return file_exists('.dockerignore');
});

$test->test("Script deploy.sh existe", function() {
    return file_exists('deploy.sh');
});

// ============================================
// 8. PRUEBAS DE DOCUMENTACIÓN
// ============================================
echo "\n" . Colors::$YELLOW . "═══ 8. PRUEBAS DE DOCUMENTACIÓN ═══" . Colors::$NC . "\n";

$test->test("README.md existe", function() {
    return file_exists('README.md');
});

$test->test("IMPLEMENTACION_JWT.md existe", function() {
    return file_exists('IMPLEMENTACION_JWT.md');
});

$test->test("DESPLIEGUE_VPS.md existe", function() {
    return file_exists('DESPLIEGUE_VPS.md');
});

$test->test("CONFIGURACION_PRODUCCION.md existe", function() {
    return file_exists('CONFIGURACION_PRODUCCION.md');
});

// ============================================
// 9. PRUEBAS DE JAVASCRIPT
// ============================================
echo "\n" . Colors::$YELLOW . "═══ 9. PRUEBAS DE JAVASCRIPT ═══" . Colors::$NC . "\n";

$test->test("jwt_handler.js existe", function() {
    return file_exists('js/jwt_handler.js');
});

$test->test("console_comprobantes.js existe", function() {
    return file_exists('js/console_comprobantes.js');
});

$test->test("console_salidas_diarias.js existe", function() {
    return file_exists('js/console_salidas_diarias.js');
});

$test->test("console_usuario.js existe", function() {
    return file_exists('js/console_usuario.js');
});

// ============================================
// 10. PRUEBAS DE CONFIGURACIÓN
// ============================================
echo "\n" . Colors::$YELLOW . "═══ 10. PRUEBAS DE CONFIGURACIÓN ═══" . Colors::$NC . "\n";

$test->test("composer.json es válido", function() {
    $json = file_get_contents('composer.json');
    $data = json_decode($json);
    return json_last_error() === JSON_ERROR_NONE;
});

$test->test("Configuración PHP adecuada (memory_limit)", function() {
    $memory = ini_get('memory_limit');
    $memory_bytes = return_bytes($memory);
    return $memory_bytes >= 128 * 1024 * 1024; // Mínimo 128MB
});

$test->test("Configuración PHP adecuada (max_execution_time)", function() {
    $time = ini_get('max_execution_time');
    return $time == 0 || $time >= 60; // 0 = sin límite, o mínimo 60 segundos
});

// Función auxiliar
function return_bytes($val) {
    $val = trim($val);
    $last = strtolower($val[strlen($val)-1]);
    $val = (int)$val;
    switch($last) {
        case 'g': $val *= 1024;
        case 'm': $val *= 1024;
        case 'k': $val *= 1024;
    }
    return $val;
}

// ============================================
// RESUMEN FINAL
// ============================================
$allPassed = $test->summary();

if ($allPassed) {
    echo "\n" . Colors::$GREEN;
    echo "╔════════════════════════════════════════════════════════╗\n";
    echo "║  ✓✓✓ TODAS LAS PRUEBAS PASARON EXITOSAMENTE ✓✓✓       ║\n";
    echo "║  El sistema está listo para producción                ║\n";
    echo "╚════════════════════════════════════════════════════════╝\n";
    echo Colors::$NC;
    exit(0);
} else {
    echo "\n" . Colors::$RED;
    echo "╔════════════════════════════════════════════════════════╗\n";
    echo "║  ✗✗✗ ALGUNAS PRUEBAS FALLARON ✗✗✗                     ║\n";
    echo "║  Revisa los errores antes de desplegar                ║\n";
    echo "╚════════════════════════════════════════════════════════╝\n";
    echo Colors::$NC;
    exit(1);
}
