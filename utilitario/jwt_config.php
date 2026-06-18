<?php
/**
 * Configuración JWT
 * 
 * IMPORTANTE: Cambiar JWT_SECRET_KEY en producción
 * Generar una clave segura con: openssl rand -base64 32
 */

// Clave secreta para firmar tokens (CAMBIAR EN PRODUCCIÓN)
define('JWT_SECRET_KEY', getenv('JWT_SECRET_KEY') ?: 'ToursMicaela2024_SecretKey_ChangeInProduction');

// Algoritmo de encriptación
define('JWT_ALGORITHM', 'HS256');

// Tiempo de expiración del access token (en horas)
define('JWT_ACCESS_TOKEN_EXPIRATION', 1);

// Tiempo de expiración del refresh token (en días)
define('JWT_REFRESH_TOKEN_EXPIRATION', 7);

// Habilitar/deshabilitar JWT (para migración gradual)
define('JWT_ENABLED', true);

// Modo de compatibilidad (mantener sesiones PHP tradicionales)
define('JWT_COMPATIBILITY_MODE', true);
