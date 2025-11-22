# ✅ CONFIGURACIÓN DE PRODUCCIÓN - JWT

## 🎯 CONFIGURACIÓN APLICADA

```
✅ Access Token: 2 horas
✅ Refresh Token: 7 días
✅ Renovación automática: Si quedan < 15 minutos
✅ Inactividad máxima: 2 horas
✅ Verificación: Cada 5 minutos
✅ Panel de debug: REMOVIDO (solo logs en consola)
```

---

## 📊 COMPORTAMIENTO DEL SISTEMA

### Usuario Activo:
```
09:00 → Login (token válido hasta 11:00)
10:45 → Sistema detecta: quedan 15 minutos
10:45 → Renueva automáticamente (nuevo token hasta 12:45)
12:30 → Renueva otra vez (nuevo token hasta 14:30)
... continúa mientras el usuario esté activo
```

### Usuario Inactivo:
```
09:00 → Login (token válido hasta 11:00)
09:00-11:00 → Usuario no hace nada (sin clicks, sin movimiento)
11:00 → Sistema cierra sesión automáticamente
```

---

## 🔍 LOGS EN CONSOLA (F12)

El sistema mostrará mensajes cada 5 minutos en la consola del navegador:

### Logs Normales:
```
🔐 JWT: Token válido por 115m 30s | Inactivo: 2m | Usuario: Activo
🔐 JWT: Token válido por 110m 15s | Inactivo: 0m | Usuario: Activo
🔐 JWT: Token válido por 105m 45s | Inactivo: 1m | Usuario: Activo
```

### Cuando se Renueva:
```
🔐 JWT: Token válido por 14m 30s | Inactivo: 0m | Usuario: Activo
⚠️ Token próximo a expirar (usuario activo), refrescando...
Token refrescado exitosamente
🔐 JWT: Token válido por 119m 58s | Inactivo: 0m | Usuario: Activo
```

### Cuando Está Inactivo:
```
🔐 JWT: Token válido por 45m 20s | Inactivo: 75m | Usuario: Activo
🔐 JWT: Token válido por 40m 10s | Inactivo: 80m | Usuario: Activo
🔐 JWT: Token válido por 35m 05s | Inactivo: 85m | Usuario: Activo
🔐 JWT: Token válido por 30m 00s | Inactivo: 90m | Usuario: Activo
🔐 JWT: Token válido por 25m 15s | Inactivo: 95m | Usuario: Activo
🔐 JWT: Token válido por 20m 30s | Inactivo: 100m | Usuario: Activo
🔐 JWT: Token válido por 15m 45s | Inactivo: 105m | Usuario: Activo
🔐 JWT: Token válido por 10m 20s | Inactivo: 110m | Usuario: Activo
🔐 JWT: Token válido por 5m 10s | Inactivo: 115m | Usuario: Activo
🔐 JWT: Token válido por 0m 30s | Inactivo: 120m | Usuario: Inactivo
❌ Usuario inactivo por más de 2 horas, cerrando sesión...
🚪 Cerrando sesión...
```

---

## 📁 ARCHIVOS MODIFICADOS

### 1. `utilitario/JWTHelper.php`
```php
✅ generateToken(): 2 horas (antes: 5 minutos)
✅ generateRefreshToken(): 7 días (antes: 10 minutos)
✅ isTokenExpiringSoon(): < 15 minutos (antes: < 2 minutos)
```

### 2. `js/jwt_handler.js`
```javascript
✅ refreshThreshold: 15 minutos (antes: 2 minutos)
✅ checkInterval: 5 minutos (antes: 30 segundos)
✅ inactivityTimeout: 2 horas (antes: 5 minutos)
```

### 3. `controller/usuario/controlador_iniciar_sesion.php`
```php
✅ generateToken($userData, 2) // 2 horas
✅ expires_in: 7200 // 2 horas en segundos
```

### 4. `controller/usuario/controlador_refresh_token.php`
```php
✅ generateToken($userData, 2) // 2 horas
✅ expires_in: 7200 // 2 horas en segundos
```

### 5. `view/index.php`
```php
✅ $tiempo_maximo = 2 * 3600; // 2 horas
✅ Panel de debug REMOVIDO
```

---

## 🔐 SEGURIDAD IMPLEMENTADA

### Contraseñas:
- ✅ Hasheadas con bcrypt (cost 10)
- ✅ Irreversibles
- ✅ Salt automático

### Tokens:
- ✅ Firmados con HMAC-SHA256
- ✅ Expiración de 2 horas
- ✅ Renovación automática si usuario activo
- ✅ No se renuevan si usuario inactivo

### Sesiones:
- ✅ Cierre automático por inactividad (2 horas)
- ✅ Validación en cada request
- ✅ Tokens en localStorage (no en cookies)

---

## 🎯 CASOS DE USO

### Caso 1: Jornada Laboral Normal
```
08:00 → Login
08:00-10:00 → Trabaja (registra encomiendas)
09:45 → Renueva automáticamente
10:00-12:00 → Sigue trabajando
11:45 → Renueva automáticamente
12:00-13:00 → Almuerzo (cierra navegador)
13:00 → Regresa, hace login de nuevo
13:00-17:00 → Trabaja
15:45 → Renueva automáticamente
17:00 → Cierra sesión manualmente
```

### Caso 2: Usuario Olvida Cerrar Sesión
```
08:00 → Login
08:00-09:00 → Trabaja
09:00 → Se va (olvida cerrar sesión)
11:00 → Sistema cierra sesión automáticamente (2 horas inactivo)
```

### Caso 3: Trabajo Interrumpido
```
08:00 → Login
08:00-09:30 → Trabaja
09:30-10:00 → Reunión (no toca PC)
10:00 → Regresa, sigue trabajando
10:45 → Renueva automáticamente (porque volvió a estar activo)
```

---

## ⚙️ CONFIGURACIÓN AVANZADA

### Si Quieres Cambiar los Tiempos:

#### Access Token (Archivo: `utilitario/JWTHelper.php`, línea 19)
```php
// Cambiar el 2 por las horas que quieras
public static function generateToken($data, $expiration_hours = 2)

// Ejemplos:
= 1    // 1 hora (más seguro, menos cómodo)
= 2    // 2 horas (recomendado)
= 4    // 4 horas (menos seguro, más cómodo)
```

#### Refresh Token (Archivo: `utilitario/JWTHelper.php`, línea 42)
```php
// Cambiar el 7 por los días que quieras
'exp' => $time + (7 * 24 * 3600), // 7 días

// Ejemplos:
(1 * 24 * 3600)   // 1 día
(7 * 24 * 3600)   // 7 días (recomendado)
(30 * 24 * 3600)  // 30 días
```

#### Umbral de Renovación (Archivo: `utilitario/JWTHelper.php`, línea 85)
```php
// Cambiar 900 (15 minutos) por los segundos que quieras
return $time_left < 900;

// Ejemplos:
< 600   // 10 minutos
< 900   // 15 minutos (recomendado)
< 1800  // 30 minutos
```

#### Tiempo de Inactividad (Archivo: `js/jwt_handler.js`, línea 14)
```javascript
// Cambiar 2 horas por el tiempo que quieras
inactivityTimeout: 2 * 3600 * 1000, // 2 horas

// Ejemplos:
1 * 3600 * 1000    // 1 hora
2 * 3600 * 1000    // 2 horas (recomendado)
4 * 3600 * 1000    // 4 horas
```

---

## 🚨 IMPORTANTE ANTES DE PRODUCCIÓN

### ✅ Checklist Final:

- [x] ✅ Tokens configurados a 2 horas
- [x] ✅ Refresh token configurado a 7 días
- [x] ✅ Inactividad configurada a 2 horas
- [x] ✅ Panel de debug removido
- [ ] 🔴 **CAMBIAR CLAVE SECRETA** en `JWTHelper.php`
- [ ] 🔴 **HABILITAR HTTPS** en producción
- [ ] 🔴 **EJECUTAR MIGRACIÓN DE CONTRASEÑAS** (una sola vez)
- [ ] 🔴 **HACER BACKUP** de base de datos

### Cambiar Clave Secreta:

Editar `utilitario/JWTHelper.php`, línea 9:

```php
// CAMBIAR ESTO:
private static $secret_key = "ToursMicaela2024_SecretKey_ChangeInProduction";

// POR ESTO (generar con: openssl rand -base64 32):
private static $secret_key = "TU_CLAVE_SUPER_SECRETA_AQUI";
```

### Migrar Contraseñas:

**Ejecutar UNA SOLA VEZ:**
```bash
php utilitario/migrate_passwords.php
```

---

## 📞 SOPORTE

### Ver Logs en Consola:
1. Presiona F12
2. Ve a la pestaña "Console"
3. Verás los logs de JWT cada 5 minutos

### Comandos Útiles (Consola):
```javascript
// Ver tiempo restante
const expires = new Date(parseInt(localStorage.getItem('token_expires')));
console.log('Token expira en:', expires);

// Ver última actividad
console.log('Última actividad:', new Date(window.JWTHandler.lastActivity));

// Ver si está activo
console.log('¿Usuario activo?', window.JWTHandler.isUserActive());
```

---

## ✅ RESUMEN

Tu sistema ahora tiene:

1. ✅ **Autenticación JWT moderna** con tokens de 2 horas
2. ✅ **Renovación automática** si el usuario está activo
3. ✅ **Cierre automático** después de 2 horas de inactividad
4. ✅ **Contraseñas seguras** con bcrypt
5. ✅ **Logs en consola** para monitoreo
6. ✅ **Sin interrupciones** para el usuario

**El sistema está listo para producción.** Solo falta:
- Cambiar la clave secreta
- Habilitar HTTPS
- Migrar contraseñas
- Hacer backup

---

**Fecha de configuración:** Noviembre 2024  
**Versión:** 1.0 Producción  
**Sistema:** Tours Micaela - Facturación Electrónica
