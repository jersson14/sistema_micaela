# 🚀 Instalación Rápida - Tours Micaela

## Después de clonar desde GitHub

### 📋 Requisitos
- ✅ Docker Desktop instalado y corriendo
- ✅ Backup de base de datos (.sql)
- ✅ Certificado digital (.pem)

---

## ⚡ Pasos Rápidos

### 1. Clonar el repositorio
```bash
git clone https://github.com/TU_USUARIO/sistema-tours-micaela.git
cd sistema-tours-micaela
```

### 2. Copiar archivos sensibles (NO están en GitHub)

#### a) Base de datos
```cmd
REM Copia tu backup SQL a la carpeta backup
copy "C:\ruta\a\tu\backup.sql" backup\micaela.sql
```

#### b) Certificado digital
```cmd
REM Copia tu certificado .pem
copy "C:\ruta\a\tu\certificado.pem" greenter\certificados\certificado.pem
```

### 3. Ejecutar el sistema
```cmd
start.bat
```

### 4. Acceder
- **Aplicación**: http://localhost:8080
- **phpMyAdmin**: http://localhost:8081
  - Usuario: `root`
  - Contraseña: `root_password_2024`

---

## 📁 Archivos que DEBES copiar manualmente

Estos archivos NO están en GitHub por seguridad:

```
backup/
  └── micaela.sql                    ← Tu backup de base de datos

greenter/certificados/
  └── certificado.pem                ← Tu certificado digital SUNAT
```

---

## 🔧 Si algo falla

```cmd
REM Ver logs
docker-compose logs -f

REM Reiniciar
docker-compose restart

REM Detener todo
docker-compose down

REM Volver a construir
docker-compose build
docker-compose up -d
```

---

## ✅ Checklist

- [ ] Docker Desktop instalado y corriendo
- [ ] Repositorio clonado
- [ ] Backup SQL copiado a `backup/micaela.sql`
- [ ] Certificado copiado a `greenter/certificados/certificado.pem`
- [ ] Ejecutado `start.bat`
- [ ] Accedido a http://localhost:8080
- [ ] Sistema funcionando correctamente

---

## 📞 Soporte

Si tienes problemas, revisa:
- `DOCKER_SETUP.md` - Guía completa
- `README.md` - Documentación del proyecto

Contacto: jersson14071996@gmail.com
