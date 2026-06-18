# 🚀 DESPLIEGUE VPS - TOURS MICAELA

## 📦 ¿QUÉ INCLUYE ESTE DESPLIEGUE?

✅ **MySQL 8.0** - Base de datos con persistencia de datos
✅ **phpMyAdmin** - Interfaz web para gestionar la BD
✅ **Aplicación PHP/Apache** - Tu sistema completo
✅ **Volúmenes persistentes** - Datos y sesiones no se pierden
✅ **Configuración optimizada** - Lista para producción

---

## ⚡ INICIO RÁPIDO (3 COMANDOS)

```bash
# 1. Configurar contraseñas
cp .env.vps .env && nano .env

# 2. Desplegar todo
chmod +x deploy_vps.sh && ./deploy_vps.sh

# 3. Importar BD en phpMyAdmin
# Abrir: http://TU_IP:8081
```

---

## 📚 DOCUMENTACIÓN COMPLETA

### Para principiantes:
👉 **[PASOS_RAPIDOS_VPS_COMPLETO.md](PASOS_RAPIDOS_VPS_COMPLETO.md)** - Guía paso a paso simplificada

### Para usuarios avanzados:
👉 **[DESPLIEGUE_VPS_COMPLETO.md](DESPLIEGUE_VPS_COMPLETO.md)** - Documentación técnica completa

### Checklist:
👉 **[CHECKLIST_VPS.md](CHECKLIST_VPS.md)** - Lista de verificación del despliegue

---

## 🎯 ACCESOS

Después del despliegue, tendrás acceso a:

| Servicio | URL | Credenciales |
|----------|-----|--------------|
| **Aplicación** | `http://TU_IP` | Las de tu sistema |
| **phpMyAdmin** | `http://TU_IP:8081` | root / (tu contraseña) |
| **MySQL** | `TU_IP:3307` | micaela_user / (tu contraseña) |

---

## 📁 ARCHIVOS IMPORTANTES

```
docker-compose.vps.yml          # Configuración Docker completa
.env.vps                        # Plantilla de variables de entorno
deploy_vps.sh                   # Script de despliegue automático
instalar_docker_vps.sh          # Instalador de Docker
model/model_conexion_vps.php    # Conexión BD para Docker
```

---

## 🔧 COMANDOS ÚTILES

```bash
# Ver estado de contenedores
docker ps

# Ver logs en tiempo real
docker-compose -f docker-compose.vps.yml logs -f

# Reiniciar servicios
docker-compose -f docker-compose.vps.yml restart

# Detener todo
docker-compose -f docker-compose.vps.yml down

# Backup de base de datos
docker exec tours_micaela_db_vps mysqldump -uroot -p"CONTRASEÑA" micaela > backup.sql

# Actualizar código
git pull && docker-compose -f docker-compose.vps.yml up -d --build app
```

---

## 🐛 SOLUCIÓN DE PROBLEMAS

### No puedo acceder a la aplicación
```bash
docker ps                                    # Ver si los contenedores están corriendo
docker logs tours_micaela_app_vps           # Ver logs de la aplicación
sudo ufw status                              # Verificar firewall
```

### Error de conexión a base de datos
```bash
docker logs tours_micaela_db_vps            # Ver logs de MySQL
docker exec tours_micaela_db_vps mysqladmin ping -h localhost -uroot -p"CONTRASEÑA"
```

### phpMyAdmin no carga
```bash
docker restart tours_micaela_phpmyadmin_vps  # Reiniciar phpMyAdmin
docker logs tours_micaela_phpmyadmin_vps     # Ver logs
```

---

## 🔒 SEGURIDAD

### Contraseñas
- ⚠️ **IMPORTANTE:** Cambia las contraseñas en `.env` antes de desplegar
- Usa contraseñas largas con letras, números y símbolos
- No uses las contraseñas de ejemplo

### Firewall
```bash
sudo ufw allow 80/tcp      # HTTP
sudo ufw allow 443/tcp     # HTTPS
sudo ufw allow 8081/tcp    # phpMyAdmin
sudo ufw enable
```

### Recomendaciones adicionales
- Instala certificado SSL con Let's Encrypt
- Configura backups automáticos
- Restringe acceso a phpMyAdmin por IP (opcional)
- Cambia el puerto de phpMyAdmin a uno aleatorio

---

## 📊 ARQUITECTURA

```
┌─────────────────────────────────────────┐
│           VPS (Ubuntu 20.04+)           │
├─────────────────────────────────────────┤
│                                         │
│  ┌──────────────────────────────────┐  │
│  │   tours_micaela_app_vps          │  │
│  │   PHP 8.2 + Apache               │  │
│  │   Puerto: 80                     │  │
│  └──────────────┬───────────────────┘  │
│                 │                       │
│  ┌──────────────▼───────────────────┐  │
│  │   tours_micaela_db_vps           │  │
│  │   MySQL 8.0                      │  │
│  │   Puerto: 3307 (externo)         │  │
│  └──────────────┬───────────────────┘  │
│                 │                       │
│  ┌──────────────▼───────────────────┐  │
│  │   tours_micaela_phpmyadmin_vps   │  │
│  │   phpMyAdmin                     │  │
│  │   Puerto: 8081                   │  │
│  └──────────────────────────────────┘  │
│                                         │
│  Volúmenes persistentes:                │
│  - mysql_data_vps (Base de datos)       │
│  - sessions_data_vps (Sesiones PHP)     │
│  - ./greenter/* (Archivos generados)    │
│  - ./Fotos/* (Imágenes)                 │
│                                         │
└─────────────────────────────────────────┘
```

---

## 🎓 FLUJO DE TRABAJO

### Primera vez (Instalación completa)
1. Instalar Docker: `./instalar_docker_vps.sh`
2. Configurar `.env`: `cp .env.vps .env && nano .env`
3. Desplegar: `./deploy_vps.sh`
4. Importar BD en phpMyAdmin
5. Verificar que todo funcione

### Actualizaciones de código
```bash
git pull
docker-compose -f docker-compose.vps.yml up -d --build app
```

### Backups regulares
```bash
# Crear backup
docker exec tours_micaela_db_vps mysqldump -uroot -p"CONTRASEÑA" micaela > backup_$(date +%Y%m%d).sql

# Restaurar backup
docker exec -i tours_micaela_db_vps mysql -uroot -p"CONTRASEÑA" micaela < backup.sql
```

---

## 📞 SOPORTE

Si tienes problemas:

1. **Revisa los logs:**
   ```bash
   docker-compose -f docker-compose.vps.yml logs -f
   ```

2. **Verifica el estado:**
   ```bash
   docker ps
   sudo ufw status
   ```

3. **Consulta la documentación:**
   - [PASOS_RAPIDOS_VPS_COMPLETO.md](PASOS_RAPIDOS_VPS_COMPLETO.md)
   - [DESPLIEGUE_VPS_COMPLETO.md](DESPLIEGUE_VPS_COMPLETO.md)
   - [CHECKLIST_VPS.md](CHECKLIST_VPS.md)

---

## ✅ DIFERENCIAS CON DESARROLLO LOCAL

| Aspecto | Local | VPS |
|---------|-------|-----|
| **Archivo Docker** | `docker-compose.yml` | `docker-compose.vps.yml` |
| **Puerto MySQL** | 3306 | 3307 |
| **Conexión BD** | `model_conexion.php` | `model_conexion_vps.php` |
| **Variables** | Hardcoded | Variables de entorno |
| **Persistencia** | Volúmenes locales | Volúmenes VPS |
| **Seguridad** | Básica | Optimizada |

---

## 🎉 ¡LISTO!

Tu aplicación está lista para producción con:
- ✅ Base de datos MySQL persistente
- ✅ phpMyAdmin para gestión fácil
- ✅ Aplicación PHP optimizada
- ✅ Sesiones funcionando correctamente
- ✅ Archivos persistentes
- ✅ Configuración de seguridad básica

**¿Funciona en tu local? ¡Funcionará en el VPS!** 🚀

---

**Última actualización:** Noviembre 2024
**Versión:** 1.0
**Mantenedor:** Tours Micaela Team
