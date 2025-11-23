# 🔧 Solución: Problema de Sesiones en VPS

## 📋 Problema Identificado

Las sesiones no se están creando correctamente en el VPS, causando que:
- Los controladores devuelvan error 500
- Las variables de sesión lleguen vacías a `view/index.php`
- El sistema funcione en local pero no en producción

## ✅ Solución Implementada

### 1. Archivos Modificados

#### ✨ Nuevo: `utilitario/session_config.php`
Configuración centralizada de sesiones con:
- Tiempo de vida de 2 horas
- Ruta personalizada para sesiones
- Protecciones de seguridad (XSS, CSRF)
- Regeneración periódica de ID de sesión

#### 📝 Actualizados:
- `index.php` - Usa configuración centralizada
- `view/index.php` - Usa configuración centralizada
- `controller/usuario/controlador_crear_sesion.php` - Mejorado con validaciones
- `js/console_usuario.js` - Mejor manejo de errores

### 2. Archivos de Diagnóstico Creados

#### 🔍 `test_sesiones.php`
Script web para verificar configuración de sesiones.

**Uso:**
```
http://tu-dominio.com/test_sesiones.php
```

**Verifica:**
- Estado de sesiones PHP
- Permisos de directorios
- Configuración de cookies
- Variables de sesión activas

#### 🛠️ `fix_sessions_vps.sh`
Script bash para configurar permisos automáticamente.

**Uso:**
```bash
bash fix_sessions_vps.sh
```

## 🚀 Pasos para Implementar en VPS

### Paso 1: Subir Archivos al VPS

```bash
# Desde tu máquina local, sube los archivos
scp -r utilitario/session_config.php usuario@tu-vps:/ruta/del/proyecto/utilitario/
scp test_sesiones.php usuario@tu-vps:/ruta/del/proyecto/
scp fix_sessions_vps.sh usuario@tu-vps:/ruta/del/proyecto/
scp SOLUCION_SESIONES_VPS.md usuario@tu-vps:/ruta/del/proyecto/

# O usa Git
git add .
git commit -m "Fix: Configuración de sesiones para VPS"
git push origin main

# En el VPS
cd /ruta/del/proyecto
git pull origin main
```

### Paso 2: Ejecutar Script de Configuración

```bash
# Conectarse al VPS
ssh usuario@tu-vps

# Ir al directorio del proyecto
cd /ruta/del/proyecto

# Dar permisos de ejecución
chmod +x fix_sessions_vps.sh

# Ejecutar el script
bash fix_sessions_vps.sh

# Si necesitas permisos de root
sudo bash fix_sessions_vps.sh
```

### Paso 3: Verificar Configuración

1. **Accede al test de sesiones:**
   ```
   http://tu-dominio.com/test_sesiones.php
   ```

2. **Verifica que todo esté en verde:**
   - ✅ Session Status: ACTIVA
   - ✅ Writable Session Path: SI
   - ✅ Session Path Exists: SI

3. **Si hay errores rojos:**
   - Verifica permisos del directorio
   - Ejecuta el script con `sudo`
   - Contacta con soporte del hosting

### Paso 4: Probar el Login

1. Accede a: `http://tu-dominio.com`
2. Ingresa credenciales
3. Verifica que:
   - No haya errores 500
   - Se cree la sesión correctamente
   - Se redirija al dashboard
   - Las variables de sesión estén disponibles

### Paso 5: Verificar Logs (si hay problemas)

```bash
# Logs de PHP
tail -f /var/log/php-fpm/error.log
# o
tail -f /var/log/php/error.log

# Logs de Apache
tail -f /var/log/apache2/error.log

# Logs de Nginx
tail -f /var/log/nginx/error.log
```

## 🔍 Diagnóstico de Problemas Comunes

### Problema 1: "Session Status: INACTIVA"

**Solución:**
```bash
# Verificar que PHP pueda iniciar sesiones
php -r "session_start(); echo 'OK';"

# Si falla, verificar php.ini
php -i | grep session
```

### Problema 2: "Writable Session Path: NO"

**Solución:**
```bash
# Obtener ruta de sesiones
SESSION_PATH=$(php -r "echo session_save_path();")

# Crear directorio si no existe
mkdir -p $SESSION_PATH

# Dar permisos
chmod 700 $SESSION_PATH

# Cambiar propietario (reemplaza www-data con tu usuario web)
chown -R www-data:www-data $SESSION_PATH
```

### Problema 3: Error 500 en controladores

**Causas posibles:**
1. Permisos incorrectos en archivos PHP
2. Errores de sintaxis (espacios antes de `<?php`)
3. Módulo de sesiones de PHP no habilitado

**Solución:**
```bash
# Verificar permisos
find . -name "*.php" -exec chmod 644 {} \;

# Verificar módulo de sesiones
php -m | grep session

# Si no está, instalarlo
sudo apt-get install php-session
# o
sudo yum install php-session

# Reiniciar servidor web
sudo systemctl restart apache2
# o
sudo systemctl restart nginx
sudo systemctl restart php-fpm
```

### Problema 4: Sesiones se pierden al recargar

**Solución:**
```bash
# Verificar configuración de cookies
php -i | grep cookie

# Asegurarse de que session.cookie_domain esté vacío o correcto
# Editar php.ini si es necesario
sudo nano /etc/php/7.4/apache2/php.ini

# Buscar y configurar:
session.cookie_domain = ""
session.cookie_path = "/"
session.cookie_httponly = 1

# Reiniciar servidor
sudo systemctl restart apache2
```

## 📊 Verificación Final

### Checklist de Verificación

- [ ] `test_sesiones.php` muestra todo en verde
- [ ] Login funciona correctamente
- [ ] Dashboard carga sin errores 500
- [ ] Variables de sesión están disponibles
- [ ] No hay errores en logs de PHP
- [ ] Sesiones persisten al navegar

### Comandos de Verificación Rápida

```bash
# 1. Verificar permisos de sesiones
ls -la /tmp/php_sessions/

# 2. Verificar configuración PHP
php -i | grep session

# 3. Verificar logs en tiempo real
tail -f /var/log/apache2/error.log

# 4. Probar sesión desde CLI
php -r "session_start(); \$_SESSION['test']='ok'; echo 'Session ID: ' . session_id();"
```

## 🆘 Soporte Adicional

Si después de seguir todos los pasos el problema persiste:

1. **Captura de pantalla de:**
   - Resultado de `test_sesiones.php`
   - Errores en consola del navegador (F12)
   - Logs de PHP/Apache

2. **Información del servidor:**
   ```bash
   php -v
   cat /etc/os-release
   ps aux | grep -E 'apache|nginx|php-fpm'
   ```

3. **Verifica con tu proveedor de hosting:**
   - Restricciones de sesiones
   - Permisos de directorios
   - Configuración de PHP

## 📝 Notas Importantes

- **Seguridad:** El directorio de sesiones debe tener permisos 700
- **HTTPS:** Si usas HTTPS, cambia `session.cookie_secure` a 1 en `session_config.php`
- **Producción:** Elimina o protege `test_sesiones.php` después de verificar
- **Backup:** Siempre haz backup antes de modificar archivos en producción

## ✨ Mejoras Implementadas

1. **Configuración centralizada** - Un solo archivo para todas las sesiones
2. **Mejor manejo de errores** - Logs detallados en JavaScript
3. **Validaciones mejoradas** - Verificación de datos antes de crear sesión
4. **Seguridad mejorada** - Protecciones XSS, CSRF, regeneración de ID
5. **Diagnóstico automático** - Scripts para detectar y solucionar problemas

---

**Última actualización:** 2024
**Versión:** 1.0
**Sistema:** Tours Micaela - Gestión de Viajes
