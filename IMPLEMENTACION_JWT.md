# Implementación JWT en Tours Micaela

## ✅ ¿Qué se ha implementado?

Se ha agregado autenticación JWT (JSON Web Tokens) al sistema sin romper la funcionalidad existente. El sistema ahora tiene:

### 1. **Autenticación con JWT**
- Tokens de acceso (Access Token) con expiración de 2 horas
- Tokens de refresco (Refresh Token) con expiración de 7 días
- Renovación automática de tokens antes de expirar

### 2. **Seguridad Mejorada**
- Contraseñas hasheadas con `password_hash()` (bcrypt)
- Validación de tokens en cada request
- Protección contra ataques de fuerza bruta

### 3. **Compatibilidad Total**
- El sistema sigue funcionando con sesiones PHP tradicionales
- No se rompe ninguna funcionalidad existente
- Migración gradual sin interrupciones

## 📁 Archivos Creados

```
utilitario/
├── JWTHelper.php                    # Clase para generar y validar tokens
├── AuthMiddleware.php               # Middleware de autenticación
├── jwt_config.php                   # Configuración JWT
├── migrate_passwords.php            # Script de migración de contraseñas
└── ejemplo_proteger_endpoint.php    # Ejemplos de uso

controller/usuario/
└── controlador_refresh_token.php    # Endpoint para refrescar tokens

js/
└── jwt_handler.js                   # Manejo automático de tokens en frontend
```

## 🚀 Pasos para Activar JWT

### Paso 1: Migrar Contraseñas a Bcrypt

**IMPORTANTE:** Ejecutar solo UNA VEZ

```bash
php utilitario/migrate_passwords.php
```

Este script:
- Verifica qué contraseñas ya están hasheadas
- Actualiza las contraseñas que no lo están
- No modifica contraseñas ya hasheadas

### Paso 2: Incluir JWT Handler en Páginas Internas

Agregar en `view/index.php` (después de jQuery):

```html
<!-- JWT Handler -->
<script src="../js/jwt_handler.js"></script>
```

### Paso 3: Cambiar la Clave Secreta (PRODUCCIÓN)

Editar `utilitario/JWTHelper.php` línea 9:

```php
private static $secret_key = "TU_CLAVE_SECRETA_AQUI";
```

Generar una clave segura:
```bash
openssl rand -base64 32
```

O usar una variable de entorno:
```php
private static $secret_key;

public function __construct() {
    self::$secret_key = getenv('JWT_SECRET_KEY') ?: 'clave_por_defecto';
}
```

## 🔒 Cómo Proteger Endpoints

### Opción 1: Protección Simple (Recomendado)

Al inicio de cualquier controlador:

```php
<?php
require_once '../../utilitario/AuthMiddleware.php';
AuthMiddleware::requireAuth();

// Tu código aquí...
```

### Opción 2: Protección con Respuesta JSON

Para APIs:

```php
<?php
require_once '../../utilitario/AuthMiddleware.php';

if (!AuthMiddleware::checkAuth()) {
    header('Content-Type: application/json');
    http_response_code(401);
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

// Tu código aquí...
```

### Opción 3: Obtener Datos del Usuario

```php
<?php
require_once '../../utilitario/AuthMiddleware.php';
AuthMiddleware::requireAuth();

$usuario = AuthMiddleware::getCurrentUser();
$id_usuario = $usuario['id_usuario'];
$nombre = $usuario['nombre'];
```

## 🔄 Flujo de Autenticación

### Login
1. Usuario ingresa credenciales
2. Sistema valida con `password_verify()`
3. Se generan access_token y refresh_token
4. Tokens se guardan en localStorage
5. Se crea sesión PHP (compatibilidad)

### Requests Subsecuentes
1. JavaScript agrega token al header `Authorization: Bearer TOKEN`
2. Servidor valida token
3. Si es válido, procesa request
4. Si está expirado, intenta refrescar automáticamente

### Renovación Automática
1. Cada 5 minutos verifica expiración
2. Si quedan menos de 15 minutos, refresca token
3. Usa refresh_token para obtener nuevo access_token
4. Actualiza localStorage

### Logout
1. Limpia tokens de localStorage
2. Destruye sesión PHP
3. Elimina cookies
4. Redirige al login

## 📊 Ventajas de esta Implementación

### ✅ Seguridad
- **Contraseñas hasheadas**: Bcrypt con salt automático
- **Tokens con expiración**: Access token 2h, Refresh token 7 días
- **Validación en cada request**: Verifica firma y expiración
- **Protección CSRF**: Tokens en header, no en cookies

### ✅ Escalabilidad
- **Stateless**: No depende de sesiones del servidor
- **Microservicios ready**: Fácil integración con APIs
- **Multi-dispositivo**: Un token por dispositivo

### ✅ Compatibilidad
- **Sin romper nada**: Sistema actual sigue funcionando
- **Migración gradual**: Activar JWT por módulos
- **Fallback a sesiones**: Si JWT falla, usa sesión PHP

## 🛠️ Configuración Avanzada

### Cambiar Tiempo de Expiración

En `utilitario/JWTHelper.php`:

```php
// Access token (default: 2 horas)
public static function generateToken($data, $expiration_hours = 2)

// Refresh token (default: 7 días)
public static function generateRefreshToken($data)
// Modificar: $time + (7 * 24 * 3600)
```

### Deshabilitar JWT Temporalmente

En `utilitario/jwt_config.php`:

```php
define('JWT_ENABLED', false);
```

### Modo Solo JWT (Sin Sesiones)

En `utilitario/AuthMiddleware.php`, comentar:

```php
// Comentar estas líneas para modo solo JWT
if (isset($_SESSION['S_ID']) && !empty($_SESSION['S_ID'])) {
    return true;
}
```

## 🧪 Pruebas

### Probar Login
1. Abrir consola del navegador (F12)
2. Hacer login
3. Verificar en localStorage:
   - `access_token`
   - `refresh_token`
   - `token_expires`

### Probar Renovación
1. Cambiar `token_expires` a un valor próximo:
   ```javascript
   localStorage.setItem('token_expires', Date.now() + 600000); // 10 minutos
   ```
2. Esperar 5 minutos
3. Verificar en consola: "Token próximo a expirar, refrescando..."

### Probar Expiración
1. Cambiar `token_expires` a pasado:
   ```javascript
   localStorage.setItem('token_expires', Date.now() - 1000);
   ```
2. Recargar página
3. Debe redirigir al login

## 📝 Notas Importantes

### ⚠️ Antes de Producción

1. **Cambiar clave secreta** en `JWTHelper.php`
2. **Ejecutar migración de contraseñas** una sola vez
3. **Habilitar HTTPS** (obligatorio para tokens)
4. **Configurar CORS** si usas APIs externas
5. **Backup de base de datos** antes de migrar

### 🔐 Seguridad

- **NUNCA** commitear la clave secreta al repositorio
- Usar variables de entorno en producción
- Habilitar HTTPS en producción
- Rotar claves periódicamente
- Monitorear intentos de login fallidos

### 🐛 Troubleshooting

**Problema:** "Token inválido"
- Verificar que la clave secreta sea la misma
- Verificar formato del token en header

**Problema:** "Sesión expirada constantemente"
- Verificar hora del servidor
- Verificar que JavaScript esté incluido

**Problema:** "No se guardan tokens"
- Verificar localStorage del navegador
- Verificar que no esté en modo incógnito

## 📚 Recursos

- [JWT.io](https://jwt.io/) - Debugger de tokens
- [Firebase PHP-JWT](https://github.com/firebase/php-jwt) - Librería usada
- [OWASP JWT Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/JSON_Web_Token_for_Java_Cheat_Sheet.html)

## 🤝 Soporte

Para dudas o problemas:
1. Revisar este documento
2. Verificar logs del servidor
3. Verificar consola del navegador
4. Contactar al equipo de desarrollo

---

**Versión:** 1.0  
**Fecha:** 2024  
**Sistema:** Tours Micaela - Facturación Electrónica
