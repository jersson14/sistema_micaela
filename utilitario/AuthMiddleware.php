<?php
require_once __DIR__ . '/JWTHelper.php';

class AuthMiddleware
{
    /**
     * Verifica que el usuario esté autenticado (sesión o token)
     * Mantiene compatibilidad con el sistema de sesiones actual
     * @return bool
     */
    public static function checkAuth()
    {
        // Primero verificar sesión tradicional (compatibilidad)
        session_start();
        if (isset($_SESSION['S_ID']) && !empty($_SESSION['S_ID'])) {
            return true;
        }

        // Si no hay sesión, verificar token JWT
        $token = JWTHelper::getTokenFromHeader();
        
        if (!$token) {
            // También buscar token en cookie
            $token = $_COOKIE['jwt_token'] ?? null;
        }

        if ($token) {
            $decoded = JWTHelper::validateToken($token);
            
            if ($decoded && isset($decoded->data)) {
                // Crear sesión desde el token para compatibilidad
                self::createSessionFromToken($decoded->data);
                return true;
            }
        }

        return false;
    }

    /**
     * Middleware para proteger rutas
     * Redirige al login si no está autenticado
     */
    public static function requireAuth()
    {
        if (!self::checkAuth()) {
            // Si es una petición AJAX, devolver JSON
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                http_response_code(401);
                echo json_encode([
                    'error' => true,
                    'message' => 'No autenticado',
                    'redirect' => '/index.php'
                ]);
                exit;
            }
            
            // Redirigir al login
            header('Location: /index.php');
            exit;
        }
    }

    /**
     * Crea variables de sesión desde los datos del token
     * @param object $data Datos del token
     */
    private static function createSessionFromToken($data)
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        $_SESSION['S_ID'] = $data->id_usuario ?? '';
        $_SESSION['S_DNIUSUARIO'] = $data->dni_usuario ?? '';
        $_SESSION['S_USU'] = $data->usuario ?? '';
        $_SESSION['S_ROL'] = $data->id_role ?? '';
        $_SESSION['S_COMPLETOS'] = $data->nombres_completos ?? '';
        $_SESSION['S_NOMBRE'] = $data->nombre ?? '';
        $_SESSION['S_FOTO'] = $data->foto ?? '';
        $_SESSION['S_FOTO_EMPRESA'] = $data->foto_empresa ?? '';
        $_SESSION['S_RAZON'] = $data->razon ?? '';
        $_SESSION['S_NOMBRE_ROL'] = $data->nombre_rol ?? '';
        $_SESSION['S_SUCURSAL'] = $data->sucursal ?? '';
    }

    /**
     * Obtiene los datos del usuario actual
     * @return array|null
     */
    public static function getCurrentUser()
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        if (isset($_SESSION['S_ID'])) {
            return [
                'id_usuario' => $_SESSION['S_ID'],
                'dni_usuario' => $_SESSION['S_DNIUSUARIO'] ?? '',
                'usuario' => $_SESSION['S_USU'] ?? '',
                'rol' => $_SESSION['S_ROL'] ?? '',
                'nombre' => $_SESSION['S_NOMBRE'] ?? '',
                'foto' => $_SESSION['S_FOTO'] ?? ''
            ];
        }

        return null;
    }

    /**
     * Cierra la sesión y elimina tokens
     */
    public static function logout()
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        // Limpiar sesión
        $_SESSION = array();
        
        // Destruir cookie de sesión
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // Eliminar cookie JWT
        setcookie('jwt_token', '', time() - 3600, '/', '', false, true);
        setcookie('refresh_token', '', time() - 3600, '/', '', false, true);
        
        session_destroy();
    }
}
