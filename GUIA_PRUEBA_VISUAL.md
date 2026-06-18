# 🧪 GUÍA DE PRUEBA VISUAL - JWT

## 📋 QUÉ VAS A VER AL PROBAR

### ✅ PASO 1: Hacer Login

1. **Abre tu navegador** y ve a: `http://localhost/sistema_micaela/`

2. **Ingresa tus credenciales** (usuario y contraseña)

3. **Presiona F12** para abrir DevTools (Herramientas de Desarrollador)

4. **Haz clic en "Iniciar Sesión"**

### 🎯 QUÉ DEBES VER:

#### En la Consola (Tab "Console"):
```
JWT Handler inicializado
Token próximo a expirar, refrescando... (después de un tiempo)
Token refrescado exitosamente
```

#### En Local Storage (Tab "Application" → "Local Storage"):
Debes ver 3 valores guardados:

```
access_token: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
refresh_token: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
token_expires: 1732244567890
```

#### En la Respuesta del Login (Tab "Network"):
1. Ve a la pestaña "Network" (Red)
2. Busca la petición a `controlador_iniciar_sesion.php`
3. Haz clic en ella
4. Ve a "Response" (Respuesta)

Debes ver algo como:
```json
{
  "success": true,
  "data": [...],
  "tokens": {
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "refresh_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "expires_in": 7200
  }
}
```

---

## 🔍 PASO 2: Verificar Tokens en el Navegador

### Opción A: Usando DevTools

1. **Presiona F12**
2. Ve a **Application** (o Aplicación)
3. En el menú izquierdo, expande **Local Storage**
4. Haz clic en tu dominio (localhost)
5. **Verás 3 valores:**

| Key | Value | Descripción |
|-----|-------|-------------|
| `access_token` | eyJ0eXAi... | Token de acceso (2 horas) |
| `refresh_token` | eyJ0eXAi... | Token de refresco (7 días) |
| `token_expires` | 1732244567890 | Timestamp de expiración |

### Opción B: Usando la Consola

1. **Presiona F12**
2. Ve a **Console**
3. Escribe y presiona Enter:

```javascript
console.log('Access Token:', localStorage.getItem('access_token'));
console.log('Refresh Token:', localStorage.getItem('refresh_token'));
console.log('Expira en:', new Date(parseInt(localStorage.getItem('token_expires'))));
```

**Debes ver:**
```
Access Token: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
Refresh Token: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
Expira en: Fri Nov 22 2024 02:30:45 GMT-0500
```

---

## 🔄 PASO 3: Ver Renovación Automática

### Método Rápido (Simular expiración próxima):

1. **Abre la Consola** (F12 → Console)

2. **Ejecuta este código** para simular que el token expira en 10 minutos:

```javascript
// Simular que el token expira en 10 minutos
localStorage.setItem('token_expires', Date.now() + (10 * 60 * 1000));
console.log('Token configurado para expirar en 10 minutos');
```

3. **Espera 5 minutos** (o recarga la página)

4. **Debes ver en la consola:**
```
Token próximo a expirar, refrescando...
Token refrescado exitosamente
```

5. **Verifica que el token cambió:**
```javascript
console.log('Nuevo token:', localStorage.getItem('access_token'));
```

---

## 🚪 PASO 4: Probar Cierre de Sesión

1. **Haz clic en "Cerrar Sesión"**

2. **Abre DevTools** (F12) → Application → Local Storage

3. **Verifica que los tokens se eliminaron:**
   - `access_token` → Eliminado ✓
   - `refresh_token` → Eliminado ✓
   - `token_expires` → Eliminado ✓

4. **Debes ser redirigido al login**

---

## 🔐 PASO 5: Probar Token Inválido

### Simular token manipulado:

1. **Haz login normalmente**

2. **Abre la Consola** (F12 → Console)

3. **Modifica el token:**
```javascript
localStorage.setItem('access_token', 'token_falso_123');
console.log('Token modificado a uno inválido');
```

4. **Recarga la página** (F5)

5. **Debes ver:**
   - Mensaje en consola: "Sesión expirada, redirigiendo al login..."
   - Redirigido automáticamente al login

---

## ⏱️ CAMBIAR TIEMPOS DE EXPIRACIÓN

### 📝 Archivo: `utilitario/JWTHelper.php`

#### Cambiar Access Token (línea 19):

```php
// ANTES (2 horas):
public static function generateToken($data, $expiration_hours = 2)

// CAMBIAR A 1 HORA:
public static function generateToken($data, $expiration_hours = 1)

// CAMBIAR A 4 HORAS:
public static function generateToken($data, $expiration_hours = 4)

// CAMBIAR A 30 MINUTOS:
public static function generateToken($data, $expiration_hours = 0.5)
```

#### Cambiar Refresh Token (línea 42):

```php
// ANTES (7 días):
'exp' => $time + (7 * 24 * 3600), // 7 días

// CAMBIAR A 30 DÍAS:
'exp' => $time + (30 * 24 * 3600), // 30 días

// CAMBIAR A 1 DÍA:
'exp' => $time + (1 * 24 * 3600), // 1 día

// CAMBIAR A 2 HORAS:
'exp' => $time + (2 * 3600), // 2 horas
```

#### Cambiar Umbral de Renovación (línea 85):

```php
// ANTES (15 minutos):
return $time_left < 900; // 900 segundos = 15 minutos

// CAMBIAR A 30 MINUTOS:
return $time_left < 1800; // 1800 segundos = 30 minutos

// CAMBIAR A 5 MINUTOS:
return $time_left < 300; // 300 segundos = 5 minutos
```

### 📝 Archivo: `js/jwt_handler.js`

#### Cambiar Frecuencia de Verificación (línea 9):

```javascript
// ANTES (verificar cada 5 minutos):
checkInterval: 5 * 60 * 1000, // 5 minutos

// CAMBIAR A 1 MINUTO:
checkInterval: 1 * 60 * 1000, // 1 minuto

// CAMBIAR A 10 MINUTOS:
checkInterval: 10 * 60 * 1000, // 10 minutos
```

#### Cambiar Umbral de Renovación (línea 8):

```javascript
// ANTES (renovar si quedan menos de 15 minutos):
refreshThreshold: 15 * 60 * 1000, // 15 minutos

// CAMBIAR A 30 MINUTOS:
refreshThreshold: 30 * 60 * 1000, // 30 minutos

// CAMBIAR A 5 MINUTOS:
refreshThreshold: 5 * 60 * 1000, // 5 minutos
```

---

## 📊 TABLA DE TIEMPOS RECOMENDADOS

| Escenario | Access Token | Refresh Token | Renovación |
|-----------|--------------|---------------|------------|
| **Desarrollo** | 4 horas | 30 días | 30 min antes |
| **Producción (Normal)** | 2 horas | 7 días | 15 min antes |
| **Alta Seguridad** | 30 min | 1 día | 5 min antes |
| **Baja Seguridad** | 8 horas | 30 días | 1 hora antes |

---

## ✅ CHECKLIST DE PRUEBA

Marca cada prueba que hagas:

- [ ] Login exitoso
- [ ] Tokens guardados en localStorage
- [ ] Tokens visibles en DevTools
- [ ] Navegación por el sistema funciona
- [ ] Renovación automática funciona
- [ ] Cierre de sesión elimina tokens
- [ ] Token inválido redirige al login
- [ ] Tiempos de expiración cambiados (si aplica)

---

## 🎯 RESULTADO ESPERADO

### ✅ TODO FUNCIONA SI:

1. ✓ Puedes hacer login normalmente
2. ✓ Ves los tokens en localStorage
3. ✓ El sistema funciona igual que antes
4. ✓ Los tokens se renuevan automáticamente
5. ✓ Al cerrar sesión, los tokens se eliminan
6. ✓ No ves errores en la consola

### ❌ HAY PROBLEMA SI:

1. ✗ No puedes hacer login
2. ✗ No se guardan tokens en localStorage
3. ✗ Ves errores en la consola
4. ✗ Te redirige al login constantemente
5. ✗ Los tokens no se renuevan

**Si hay problemas, revisa:** `FAQ_JWT.md` - Sección "Problemas Comunes"

---

## 🔍 DECODIFICAR UN TOKEN (Opcional)

### Método 1: Usando jwt.io

1. Ve a: https://jwt.io
2. Copia tu `access_token` de localStorage
3. Pégalo en el campo "Encoded"
4. Verás los datos decodificados:

```json
{
  "iat": 1732237867,
  "exp": 1732245067,
  "aud": "localhost",
  "data": {
    "id_usuario": "1",
    "usuario": "admin",
    "nombre": "Administrador"
  }
}
```

### Método 2: Usando la Consola

```javascript
// Decodificar token (solo la parte del payload)
function decodeJWT(token) {
    const base64Url = token.split('.')[1];
    const base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
    const jsonPayload = decodeURIComponent(atob(base64).split('').map(function(c) {
        return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
    }).join(''));
    return JSON.parse(jsonPayload);
}

// Usar:
const token = localStorage.getItem('access_token');
console.log(decodeJWT(token));
```

---

## 📞 SOPORTE

Si algo no funciona:
1. Revisa `FAQ_JWT.md`
2. Ejecuta: `php utilitario/test_jwt.php`
3. Verifica la consola del navegador (F12)
4. Verifica logs del servidor

---

**¡Listo!** Ahora sabes exactamente qué esperar al probar JWT en tu aplicación.
