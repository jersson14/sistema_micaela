# ✅ Checklist de Implementación JWT

## Pre-Implementación

- [x] ✅ Instalar firebase/php-jwt
- [x] ✅ Crear JWTHelper.php
- [x] ✅ Crear AuthMiddleware.php
- [x] ✅ Crear controlador_refresh_token.php
- [x] ✅ Crear jwt_handler.js
- [x] ✅ Actualizar controlador_iniciar_sesion.php
- [x] ✅ Actualizar console_usuario.js
- [x] ✅ Crear script de migración de contraseñas
- [x] ✅ Ejecutar tests (8/8 pasados)

## Activación (Hacer en orden)

### 1. Backup
- [ ] 🔴 Hacer backup completo de la base de datos
- [ ] 🔴 Hacer backup de archivos del proyecto
- [ ] 🔴 Documentar estado actual del sistema

### 2. Migración de Contraseñas
- [ ] 🔴 Ejecutar: `php utilitario/migrate_passwords.php`
- [ ] 🔴 Verificar que todas las contraseñas se migraron
- [ ] 🔴 Probar login con usuario de prueba
- [ ] 🔴 Verificar que usuarios existentes pueden loguearse

### 3. Configuración
- [ ] 🔴 Cambiar clave secreta en `utilitario/JWTHelper.php`
- [ ] 🔴 Generar clave segura: `openssl rand -base64 32`
- [ ] 🔴 Configurar variable de entorno JWT_SECRET_KEY (opcional)
- [ ] 🔴 Revisar tiempos de expiración (2h access, 7d refresh)

### 4. Frontend
- [ ] 🔴 Incluir `jwt_handler.js` en `view/index.php`
- [ ] 🔴 Agregar después de jQuery:
  ```html
  <script src="../js/jwt_handler.js"></script>
  ```
- [ ] 🔴 Verificar que se carga correctamente (F12 → Network)

### 5. Pruebas de Funcionalidad
- [ ] 🔴 Probar login con usuario válido
- [ ] 🔴 Verificar tokens en localStorage (F12 → Application)
- [ ] 🔴 Verificar que se crean: access_token, refresh_token, token_expires
- [ ] 🔴 Navegar por el sistema (verificar que todo funciona)
- [ ] 🔴 Probar login con credenciales incorrectas
- [ ] 🔴 Probar con usuario inactivo

### 6. Pruebas de Seguridad
- [ ] 🔴 Cerrar sesión y verificar que redirige al login
- [ ] 🔴 Intentar acceder a página interna sin login
- [ ] 🔴 Modificar token en localStorage (debe rechazar)
- [ ] 🔴 Esperar expiración de token (debe renovar automáticamente)
- [ ] 🔴 Verificar que contraseñas están hasheadas en BD

### 7. Proteger Endpoints (Opcional - Gradual)
- [ ] 🟡 Proteger controladores de usuario
- [ ] 🟡 Proteger controladores de encomiendas
- [ ] 🟡 Proteger controladores de reservas
- [ ] 🟡 Proteger controladores de facturación
- [ ] 🟡 Proteger controladores de reportes

Ejemplo:
```php
<?php
require_once '../../utilitario/AuthMiddleware.php';
AuthMiddleware::requireAuth();
// resto del código...
```

### 8. Producción
- [ ] 🔴 Verificar que HTTPS está habilitado
- [ ] 🔴 Cambiar clave secreta (diferente a desarrollo)
- [ ] 🔴 Configurar variables de entorno
- [ ] 🔴 Deshabilitar modo debug
- [ ] 🔴 Verificar logs de errores
- [ ] 🔴 Monitorear intentos de login fallidos

## Post-Implementación

### Monitoreo
- [ ] 🟡 Revisar logs de errores diariamente
- [ ] 🟡 Monitorear intentos de login fallidos
- [ ] 🟡 Verificar que tokens se renuevan correctamente
- [ ] 🟡 Revisar performance del sistema

### Mantenimiento
- [ ] 🟡 Rotar clave secreta cada 6 meses
- [ ] 🟡 Actualizar firebase/php-jwt periódicamente
- [ ] 🟡 Revisar y actualizar tiempos de expiración
- [ ] 🟡 Documentar cambios realizados

## Troubleshooting

### Si algo falla:

#### Login no funciona
1. Verificar que contraseñas están hasheadas
2. Revisar consola del navegador (F12)
3. Verificar que `controlador_iniciar_sesion.php` se actualizó
4. Verificar que `console_usuario.js` se actualizó

#### Tokens no se guardan
1. Verificar que `jwt_handler.js` está incluido
2. Verificar localStorage del navegador
3. Verificar que no está en modo incógnito
4. Revisar consola para errores JavaScript

#### Sesión expira constantemente
1. Verificar hora del servidor
2. Verificar que `jwt_handler.js` está funcionando
3. Revisar tiempos de expiración en `JWTHelper.php`
4. Verificar que refresh token funciona

#### Error "Token inválido"
1. Verificar que clave secreta es la misma
2. Verificar formato del token en header
3. Revisar logs del servidor
4. Verificar que token no está expirado

## Rollback (Si es necesario)

Si algo sale mal, puedes revertir:

1. **Restaurar archivos modificados:**
   - `controller/usuario/controlador_iniciar_sesion.php`
   - `js/console_usuario.js`

2. **Remover inclusión de jwt_handler.js:**
   - Quitar de `view/index.php`

3. **Las contraseñas hasheadas seguirán funcionando**
   - `password_verify()` es compatible con contraseñas antiguas

4. **Restaurar backup de BD (solo si es necesario)**

## Comandos Útiles

```bash
# Ejecutar tests
php utilitario/test_jwt.php

# Migrar contraseñas
php utilitario/migrate_passwords.php

# Generar clave secreta
openssl rand -base64 32

# Ver logs de PHP
tail -f /xampp/apache/logs/error.log

# Verificar composer
composer show firebase/php-jwt
```

## Contactos de Soporte

- Documentación: `IMPLEMENTACION_JWT.md`
- Ejemplos: `utilitario/ejemplo_proteger_endpoint.php`
- Tests: `utilitario/test_jwt.php`

---

## Leyenda

- 🔴 **Crítico** - Debe hacerse antes de producción
- 🟡 **Recomendado** - Mejora la seguridad/funcionalidad
- ✅ **Completado** - Ya está hecho

---

**Última actualización:** 2024  
**Versión:** 1.0  
**Sistema:** Tours Micaela
