# 🔧 Solución de Sesiones para VPS - Tours Micaela

## 📌 Problema

El sistema funciona correctamente en **local** pero en el **VPS** las sesiones no se crean, causando:
- ❌ Errores 500 en todos los controladores
- ❌ Variables de sesión vacías en `view/index.php`
- ❌ Imposibilidad de iniciar sesión

## ✅ Solución Implementada

Se ha creado una **configuración centralizada de sesiones** con validaciones mejoradas y scripts de diagnóstico automático.

---

## 🚀 Implementación Rápida (3 pasos)

### 1️⃣ Subir al VPS

```bash
# Opción A: Git (Recomendado)
git add .
git commit -m "Fix: Sesiones VPS"
git push

# En el VPS
cd /ruta/proyecto
git pull
```

### 2️⃣ Ejecutar script

```bash
bash deploy_fix.sh
```

### 3️⃣ Verificar

```
http://tu-dominio.com/test_sesiones.php
```

**¡Listo!** 🎉

---

## 📁 Archivos Creados

### 🔧 Configuración
- **`utilitario/session_config.php`** - Configuración centralizada de sesiones
  - Tiempo de vida: 2 horas
  - Protecciones de seguridad
  - Ruta personalizada

### 🔍 Diagnóstico
- **`test_sesiones.php`** - Verificación web de configuración
- **`check_php_files.sh`** - Verifica sintaxis de archivos PHP

### 🛠️ Scripts de Instalación
- **`deploy_fix.sh`** - Instalación automática completa ⭐
- **`fix_sessions_vps.sh`** - Configuración de permisos
- **`COMANDOS_VPS.txt`** - Comandos para copiar/pegar

### 📖 Documentación
- **`SOLUCION_SESIONES_VPS.md`** - Guía completa detallada
- **`PASOS_RAPIDOS_VPS.md`** - Guía rápida
- **`README_SESIONES.md`** - Este archivo

---

## 📝 Archivos Modificados

### ✏️ Backend
- `index.php` - Usa configuración centralizada
- `view/index.php` - Usa configuración centralizada
- `controller/usuario/controlador_crear_sesion.php` - Validaciones mejoradas

### ✏️ Frontend
- `js/console_usuario.js` - Mejor manejo de errores y logs

---

## 🎯 Qué Hace la Solución

### Antes ❌
```php
session_start(); // Sin configuración
$_SESSION['S_ID'] = $id; // Sin validación
```

### Después ✅
```php
require_once 'utilitario/session_config.php'; // Configuración centralizada
// Validaciones
if (!isset($_POST['idusuario'])) {
    echo json_encode(['success' => false, 'message' => 'Error']);
    exit;
}
$_SESSION['S_ID'] = $id;
session_write_close(); // Forzar escritura
```

---

## 🔍 Verificación

### Test Automático
```
http://tu-dominio.com/test_sesiones.php
```

**Debe mostrar:**
- ✅ Session Status: ACTIVA
- ✅ Writable Session Path: SI
- ✅ Session Path Exists: SI

### Test Manual
1. Acceder a `http://tu-dominio.com`
2. Iniciar sesión
3. Verificar que no haya errores 500
4. Verificar que el dashboard cargue correctamente

---

## 🐛 Solución de Problemas

### Problema: "Session Status: INACTIVA"

```bash
# Verificar módulo de sesiones
php -m | grep session

# Si no aparece, instalar
sudo apt-get install php-session
sudo systemctl restart apache2
```

### Problema: "Writable Session Path: NO"

```bash
# Ejecutar script de permisos
bash fix_sessions_vps.sh

# O manualmente
sudo mkdir -p /tmp/php_sessions
sudo chmod 700 /tmp/php_sessions
sudo chown -R www-data:www-data /tmp/php_sessions
```

### Problema: Error 500 persiste

```bash
# Ver logs en tiempo real
tail -f /var/log/apache2/error.log

# Verificar sintaxis PHP
php -l controller/usuario/controlador_crear_sesion.php

# Verificar permisos
ls -la controller/usuario/
```

---

## 📊 Checklist de Implementación

- [ ] Archivos subidos al VPS
- [ ] Script `deploy_fix.sh` ejecutado
- [ ] `test_sesiones.php` muestra todo verde
- [ ] Login funciona correctamente
- [ ] Dashboard carga sin errores
- [ ] Variables de sesión disponibles
- [ ] No hay errores 500 en consola

---

## 🔐 Seguridad

La solución incluye:
- ✅ Protección XSS (`httponly`)
- ✅ Protección CSRF (`samesite`)
- ✅ Regeneración periódica de ID de sesión
- ✅ Validación de datos de entrada
- ✅ Permisos restrictivos (700)
- ✅ Tiempo de expiración (2 horas)

---

## 📞 Soporte

Si después de seguir todos los pasos el problema persiste:

1. **Captura de pantalla de:**
   - `test_sesiones.php`
   - Consola del navegador (F12)
   - Logs de error

2. **Ejecuta y guarda:**
   ```bash
   php -i > phpinfo.txt
   ls -la /tmp/php_sessions/ > permisos.txt
   ```

3. **Contacta con:**
   - Soporte técnico del hosting
   - Administrador del servidor

---

## 📚 Documentación Adicional

- **Guía Completa:** `SOLUCION_SESIONES_VPS.md`
- **Guía Rápida:** `PASOS_RAPIDOS_VPS.md`
- **Comandos:** `COMANDOS_VPS.txt`

---

## ⚡ Comandos Rápidos

```bash
# Conectar al VPS
ssh usuario@vps

# Ir al proyecto
cd /ruta/proyecto

# Actualizar código
git pull

# Ejecutar solución
bash deploy_fix.sh

# Verificar
curl http://tu-dominio.com/test_sesiones.php
```

---

## 🎓 Aprendizajes

### Causas Comunes de Problemas de Sesión en VPS:

1. **Permisos incorrectos** en directorio de sesiones
2. **Configuración inconsistente** entre archivos
3. **Falta de validaciones** en creación de sesión
4. **Espacios antes de `<?php`** en archivos
5. **Módulo de sesiones** no habilitado
6. **Propietario incorrecto** del directorio

### Solución:
✅ Configuración centralizada
✅ Scripts de diagnóstico
✅ Validaciones mejoradas
✅ Documentación completa

---

## 📈 Mejoras Implementadas

| Antes | Después |
|-------|---------|
| Sin configuración centralizada | ✅ `session_config.php` |
| Sin validaciones | ✅ Validaciones completas |
| Sin diagnóstico | ✅ `test_sesiones.php` |
| Configuración manual | ✅ Scripts automáticos |
| Sin logs de error | ✅ Logs detallados |

---

## 🏆 Resultado Esperado

Después de implementar la solución:

✅ Login funciona correctamente
✅ Sesiones persisten
✅ No hay errores 500
✅ Dashboard carga correctamente
✅ Variables de sesión disponibles
✅ Sistema estable en producción

---

**Versión:** 1.0
**Fecha:** 2024
**Sistema:** Tours Micaela - Gestión de Viajes
**Autor:** Equipo de Desarrollo

---

## 🌟 ¿Funcionó?

Si la solución funcionó correctamente:

1. ✅ Elimina o protege `test_sesiones.php`
2. ✅ Verifica que todo funcione en producción
3. ✅ Haz backup de la configuración
4. ✅ Documenta cualquier cambio adicional

---

**¡Éxito en tu implementación!** 🚀
