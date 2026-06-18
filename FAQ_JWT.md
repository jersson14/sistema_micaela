# ❓ Preguntas Frecuentes - JWT en Tours Micaela

## General

### ¿Qué es JWT?
JWT (JSON Web Token) es un estándar abierto (RFC 7519) que define una forma compacta y autónoma de transmitir información de forma segura entre partes como un objeto JSON. Esta información puede ser verificada y confiable porque está firmada digitalmente.

### ¿Por qué implementar JWT en mi proyecto?
- **Seguridad mejorada**: Contraseñas hasheadas con bcrypt, tokens con expiración
- **Escalabilidad**: Stateless, no depende de sesiones del servidor
- **Modernización**: Estándar de la industria para APIs y aplicaciones web
- **Multi-dispositivo**: Un token por dispositivo/sesión

### ¿Se romperá mi sistema actual?
**NO.** La implementación mantiene compatibilidad total con el sistema de sesiones PHP actual. Todo sigue funcionando igual, JWT es una capa adicional de seguridad.

## Implementación

### ¿Tengo que migrar todas las contraseñas?
**SÍ**, pero es automático. El script `migrate_passwords.php` lo hace por ti. Solo ejecútalo una vez:
```bash
php utilitario/migrate_passwords.php
```

### ¿Qué pasa con las contraseñas de los usuarios?
Las contraseñas se hashean con bcrypt (irreversible). Los usuarios pueden seguir usando sus contraseñas actuales, pero ahora están más seguras.

### ¿Tengo que modificar todos mis controladores?
**NO inmediatamente.** Puedes migrar gradualmente. Para proteger un endpoint, solo agrega 2 líneas:
```php
require_once '../../utilitario/AuthMiddleware.php';
AuthMiddleware::requireAuth();
```

### ¿Dónde cambio la clave secreta?
En `utilitario/JWTHelper.php`, línea 9:
```php
private static $secret_key = "TU_CLAVE_AQUI";
```

Genera una clave segura:
```bash
openssl rand -base64 32
```

## Funcionamiento

### ¿Cómo funciona el login ahora?
1. Usuario ingresa credenciales
2. Sistema valida con `password_verify()`
3. Se generan 2 tokens: access_token (2h) y refresh_token (7d)
4. Tokens se guardan en localStorage del navegador
5. Se crea sesión PHP (compatibilidad)

### ¿Qué es el access_token?
Token de corta duración (2 horas) que se usa para autenticar cada request. Se envía en el header `Authorization: Bearer TOKEN`.

### ¿Qué es el refresh_token?
Token de larga duración (7 días) que se usa para obtener un nuevo access_token cuando este expira. Más seguro porque no se envía en cada request.

### ¿Cómo se renuevan los tokens?
Automáticamente. El script `jwt_handler.js` verifica cada 5 minutos si el token está próximo a expirar (< 15 min) y lo renueva usando el refresh_token.

### ¿Qué pasa si el token expira?
- Si hay refresh_token válido: Se renueva automáticamente
- Si no hay refresh_token: Redirige al login

### ¿Dónde se guardan los tokens?
En `localStorage` del navegador:
- `access_token`: Token de acceso
- `refresh_token`: Token de refresco
- `token_expires`: Timestamp de expiración

## Seguridad

### ¿Es seguro guardar tokens en localStorage?
Sí, para aplicaciones web tradicionales. Para mayor seguridad:
- Usa HTTPS (obligatorio en producción)
- Tokens tienen expiración corta
- Refresh token se usa solo cuando es necesario

### ¿Qué pasa si alguien roba mi token?
- El token expira en 2 horas
- Solo funciona desde el mismo dominio
- Puedes invalidar tokens cerrando sesión
- En producción, usa HTTPS para prevenir robo

### ¿Las contraseñas están seguras?
**SÍ.** Se hashean con bcrypt (cost 10), que es:
- Irreversible (no se puede desencriptar)
- Resistente a ataques de fuerza bruta
- Incluye salt automático
- Estándar de la industria

### ¿Puedo ver las contraseñas en la base de datos?
**NO.** Las contraseñas hasheadas se ven así:
```
$2y$10$RonoZYrtW5Hugp7KBTRZre4...
```
Son irreversibles, nadie puede ver la contraseña original.

## Problemas Comunes

### "Token inválido" al hacer login
**Causas:**
- Clave secreta diferente entre generación y validación
- Token mal formado
- Token expirado

**Solución:**
1. Verificar que la clave secreta es la misma
2. Limpiar localStorage: `localStorage.clear()`
3. Intentar login nuevamente

### "Sesión expirada" constantemente
**Causas:**
- `jwt_handler.js` no está incluido
- Hora del servidor incorrecta
- Refresh token expirado

**Solución:**
1. Verificar que `jwt_handler.js` está en `view/index.php`
2. Verificar hora del servidor: `date`
3. Hacer login nuevamente

### Tokens no se guardan en localStorage
**Causas:**
- Navegador en modo incógnito
- localStorage deshabilitado
- Error en JavaScript

**Solución:**
1. Salir de modo incógnito
2. Verificar consola del navegador (F12)
3. Verificar que `jwt_handler.js` se carga

### Login funciona pero no guarda sesión
**Causas:**
- `controlador_crear_sesion.php` no se ejecuta
- Error en JavaScript

**Solución:**
1. Verificar consola del navegador
2. Verificar que `console_usuario.js` se actualizó
3. Limpiar caché del navegador

## Configuración

### ¿Puedo cambiar el tiempo de expiración?
**SÍ.** En `utilitario/JWTHelper.php`:

Access token (línea 20):
```php
public static function generateToken($data, $expiration_hours = 2)
```

Refresh token (línea 35):
```php
$time + (7 * 24 * 3600) // 7 días
```

### ¿Puedo deshabilitar JWT temporalmente?
**SÍ.** En `utilitario/jwt_config.php`:
```php
define('JWT_ENABLED', false);
```

### ¿Cómo uso variables de entorno?
Crea archivo `.env`:
```
JWT_SECRET_KEY=tu_clave_super_secreta_aqui
```

En `JWTHelper.php`:
```php
private static $secret_key;

public function __construct() {
    self::$secret_key = getenv('JWT_SECRET_KEY') ?: 'default';
}
```

## Desarrollo

### ¿Cómo pruebo que JWT funciona?
```bash
php utilitario/test_jwt.php
```

Debe mostrar: "8/8 TESTS PASADOS ✓"

### ¿Cómo veo los tokens en el navegador?
1. Abrir DevTools (F12)
2. Ir a Application → Local Storage
3. Ver: access_token, refresh_token, token_expires

### ¿Cómo decodifico un token?
Visita [jwt.io](https://jwt.io) y pega el token. Verás:
- Header (algoritmo)
- Payload (datos del usuario)
- Signature (firma)

### ¿Cómo protejo un nuevo endpoint?
```php
<?php
require_once '../../utilitario/AuthMiddleware.php';
AuthMiddleware::requireAuth();

// Tu código aquí
```

### ¿Cómo obtengo datos del usuario actual?
```php
$usuario = AuthMiddleware::getCurrentUser();
$id = $usuario['id_usuario'];
$nombre = $usuario['nombre'];
```

## Producción

### ¿Qué debo hacer antes de producción?
1. ✅ Cambiar clave secreta
2. ✅ Habilitar HTTPS
3. ✅ Hacer backup de BD
4. ✅ Ejecutar migración de contraseñas
5. ✅ Probar en ambiente de desarrollo

### ¿Necesito HTTPS?
**SÍ, obligatorio en producción.** Sin HTTPS, los tokens pueden ser interceptados.

### ¿Cómo monitoreo intentos de login?
Puedes agregar logging en `controlador_iniciar_sesion.php`:
```php
// Login fallido
error_log("Login fallido: $usu desde " . $_SERVER['REMOTE_ADDR']);
```

### ¿Cada cuánto debo rotar la clave secreta?
Recomendado: cada 6 meses o si sospechas compromiso.

## Migración

### ¿Puedo revertir los cambios?
**SÍ.** Las contraseñas hasheadas seguirán funcionando. Solo necesitas:
1. Restaurar archivos modificados
2. Quitar inclusión de `jwt_handler.js`

### ¿Los usuarios deben cambiar sus contraseñas?
**NO.** Las contraseñas actuales siguen funcionando, solo están más seguras.

### ¿Qué pasa con usuarios que ya están logueados?
Seguirán logueados con sesión PHP. En su próximo login obtendrán tokens JWT.

## Rendimiento

### ¿JWT es más lento que sesiones?
**NO.** JWT es más rápido porque:
- No requiere consultas a BD para validar
- Stateless (no depende del servidor)
- Validación local con firma

### ¿Cuánto espacio ocupan los tokens?
- Access token: ~500-800 bytes
- Refresh token: ~300-500 bytes
- Total en localStorage: ~1-2 KB (insignificante)

## Soporte

### ¿Dónde encuentro más información?
- `IMPLEMENTACION_JWT.md` - Guía completa
- `RESUMEN_JWT.txt` - Resumen visual
- `CHECKLIST_JWT.md` - Lista de verificación
- `utilitario/ejemplo_proteger_endpoint.php` - Ejemplos

### ¿Cómo reporto un problema?
1. Verificar logs del servidor
2. Verificar consola del navegador
3. Ejecutar tests: `php utilitario/test_jwt.php`
4. Revisar esta FAQ

### ¿Puedo contactar soporte?
Sí, con la siguiente información:
- Descripción del problema
- Logs de error
- Pasos para reproducir
- Versión de PHP
- Navegador usado

---

## Recursos Adicionales

- [JWT.io](https://jwt.io/) - Debugger de tokens
- [Firebase PHP-JWT](https://github.com/firebase/php-jwt) - Librería usada
- [OWASP JWT Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/JSON_Web_Token_for_Java_Cheat_Sheet.html)
- [PHP password_hash](https://www.php.net/manual/es/function.password-hash.php)

---

**¿No encuentras tu pregunta?** Revisa `IMPLEMENTACION_JWT.md` o contacta al equipo de desarrollo.
