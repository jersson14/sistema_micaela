# 🚀 Pasos Rápidos para Solucionar Sesiones en VPS

## ⚡ Solución Rápida (5 minutos)

### 1️⃣ Subir archivos al VPS

```bash
# Opción A: Usando Git (RECOMENDADO)
git add .
git commit -m "Fix: Sesiones VPS"
git push

# En el VPS
cd /ruta/del/proyecto
git pull
```

```bash
# Opción B: Usando SCP
scp -r utilitario/session_config.php usuario@vps:/ruta/proyecto/utilitario/
scp test_sesiones.php usuario@vps:/ruta/proyecto/
scp fix_sessions_vps.sh usuario@vps:/ruta/proyecto/
```

### 2️⃣ Ejecutar script de configuración

```bash
# Conectar al VPS
ssh usuario@tu-vps

# Ir al proyecto
cd /ruta/del/proyecto

# Ejecutar script
bash fix_sessions_vps.sh
```

### 3️⃣ Verificar

```
http://tu-dominio.com/test_sesiones.php
```

**Debe mostrar:**
- ✅ Session Status: ACTIVA
- ✅ Writable Session Path: SI

### 4️⃣ Probar login

```
http://tu-dominio.com
```

---

## 🔍 Si No Funciona

### Verificar permisos manualmente:

```bash
# Crear directorio de sesiones
mkdir -p /tmp/php_sessions

# Dar permisos
chmod 700 /tmp/php_sessions

# Cambiar propietario (reemplaza www-data)
chown -R www-data:www-data /tmp/php_sessions

# Reiniciar servidor
sudo systemctl restart apache2
# o
sudo systemctl restart nginx && sudo systemctl restart php-fpm
```

### Ver logs de errores:

```bash
# PHP
tail -f /var/log/php-fpm/error.log

# Apache
tail -f /var/log/apache2/error.log

# Nginx
tail -f /var/log/nginx/error.log
```

---

## 📋 Checklist Final

- [ ] Archivos subidos al VPS
- [ ] Script ejecutado sin errores
- [ ] test_sesiones.php muestra todo verde
- [ ] Login funciona
- [ ] Dashboard carga correctamente
- [ ] No hay errores 500

---

## 🆘 Problema Persiste?

1. Captura pantalla de `test_sesiones.php`
2. Copia logs de error
3. Verifica con hosting si hay restricciones

---

## 📝 Cambios Realizados

### Archivos Nuevos:
- ✨ `utilitario/session_config.php` - Configuración centralizada
- 🔍 `test_sesiones.php` - Diagnóstico web
- 🛠️ `fix_sessions_vps.sh` - Script de configuración
- 📖 `SOLUCION_SESIONES_VPS.md` - Documentación completa

### Archivos Modificados:
- 📝 `index.php` - Usa session_config.php
- 📝 `view/index.php` - Usa session_config.php
- 📝 `controller/usuario/controlador_crear_sesion.php` - Mejor validación
- 📝 `js/console_usuario.js` - Mejor manejo de errores

---

## ✅ Qué Se Solucionó

1. **Configuración inconsistente** → Ahora centralizada
2. **Permisos incorrectos** → Script automático
3. **Sin validaciones** → Validaciones mejoradas
4. **Sin diagnóstico** → Test completo
5. **Errores sin capturar** → Logs detallados

---

**Tiempo estimado:** 5-10 minutos
**Dificultad:** Baja
**Requiere:** Acceso SSH al VPS
