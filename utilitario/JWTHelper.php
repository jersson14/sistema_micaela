<?php
require_once __DIR__ . '/../vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTHelper
{
    // Clave secreta - CAMBIAR EN PRODUCCIÓN
    private static $secret_key = "ToursMicaela2024_SecretKey_ChangeInProduction";
    private static $encrypt = 'HS256';
    private static $aud = null;

    /**
     * Genera un token JWT
     * @param array $data Datos del usuario a incluir en el token
     * @param int $expiration_hours Horas de expiración (default: 2 horas)
     * @return string Token JWT
     */
    public static function generateToken($data, $expiration_hours = 2)
    {
        $time = time();
        
        $token = array(
            'iat' => $time, // Tiempo de emisión
            'exp' => $time + ($expiration_hours * 3600), // Expiración en HORAS
            'aud' => self::Aud(),
            'data' => $data // Datos del usuario
        );

        return JWT::encode($token, self::$secret_key, self::$encrypt);
    }

    /**
     * Genera un refresh token (válido por 7 días)
     * @param array $data Datos mínimos del usuario
     * @return string Refresh token
     */
    public static function generateRefreshToken($data)
    {
        $time = time();
        
        $token = array(
            'iat' => $time,
            'exp' => $time + (7 * 24 * 3600), // 7 días
            'aud' => self::Aud(),
            'type' => 'refresh',
            'data' => array(
                'id_usuario' => $data['id_usuario'],
                'usuario' => $data['usuario']
            )
        );

        return JWT::encode($token, self::$secret_key, self::$encrypt);
    }

    /**
     * Valida y decodifica un token JWT
     * @param string $token Token a validar
     * @return object|false Datos del token o false si es inválido
     */
    public static function validateToken($token)
    {
        try {
            $decoded = JWT::decode($token, new Key(self::$secret_key, self::$encrypt));
            
            // Verificar audiencia
            if ($decoded->aud !== self::Aud()) {
                return false;
            }
            
            return $decoded;
        } catch (Exception $e) {
            // Token inválido o expirado
            return false;
        }
    }

    /**
     * Verifica si un token está próximo a expirar (menos de 15 minutos)
     * @param string $token Token a verificar
     * @return bool
     */
    public static function isTokenExpiringSoon($token)
    {
        try {
            $decoded = JWT::decode($token, new Key(self::$secret_key, self::$encrypt));
            $time_left = $decoded->exp - time();
            
            // Si quedan menos de 15 minutos (900 segundos)
            return $time_left < 900;
        } catch (Exception $e) {
            return true;
        }
    }

    /**
     * Obtiene la audiencia (identificador único del servidor)
     * @return string
     */
    private static function Aud()
    {
        if (self::$aud === null) {
            self::$aud = @$_SERVER['HTTP_HOST'] ?: 'localhost';
        }
        return self::$aud;
    }

    /**
     * Extrae el token del header Authorization
     * @return string|null Token o null si no existe
     */
    public static function getTokenFromHeader()
    {
        $headers = null;
        
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            $requestHeaders = array_combine(
                array_map('ucwords', array_keys($requestHeaders)), 
                array_values($requestHeaders)
            );
            
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }

        // Extraer el token del formato "Bearer TOKEN"
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }
}
