# 🚀 DESPLIEGUE FINAL - SISTEMA TOURS MICAELA

## 📋 RESUMEN DEL PROYECTO

Sistema de gestión de transportes con facturación electrónica SUNAT desplegado en VPS Ubuntu con Docker.

**Dominio:** https://micaela-tours.com  
**IP VPS:** 72.61.40.91  
**Fecha de despliegue:** Noviembre 2024

---

## 🏗️ ARQUITECTURA IMPLEMENTADA

```
┌─────────────────────────────────────────────────────────┐
│                    VPS Ubuntu 20.04+                     │
├─────────────────────────────────────────────────────────┤
│                                                          │
│  ┌────────────────────────────────────────────────┐    │
│  │  tours_micaela_app_vps (PHP 8.2 + Apache)     │    │
│  │  - Puerto 80 (HTTP) → Redirige a HTTPS        │    │
│  │  - Puerto 443 (HTTPS) con SSL Let's Encrypt   │    │
│  │  - Facturación electrónica SUNAT (Greenter)   │    │
│  │  - Generación de reportes PDF (mPDF)          │    │
│  └────────────────┬───────────────────────────────┘    │
│                   │                                      │
│  ┌────────────────▼───────────────────────────────┐    │
│  │  tours_micaela_db_vps (MySQL 8.0)             │    │
│  │  - Puerto interno: 3306                        │    │
│  │  - Puerto externo: 3307                        │    │
│  │  - Charset: utf8mb4_unicode_ci                 │    │
│  └────────────────┬───────────────────────────────┘    │
│                   │                                      │
│  ┌────────────────▼───────────────────────────────┐    │
│  │  tours_micaela_phpmyadmin_vps                  │    │
│  │  - Puerto: 8081                                │    │
│  │  - Gestión visual de base de datos            │    │
│  └────────────────────────────────────────────────┘    │
│                                                          │
│  Volúmenes persistentes:                                │
│  - mysql_data_vps (Base de datos)                       │
│  - sessions_data_vps (Sesiones PHP)                     │
│  - ./greenter/* (Certificados y XMLs SUNAT)             │
│  - ./Fotos/* (Imágenes del sistema)                     │
│  - ./ssl/* (Certificados SSL)                           │
│                                                          │
└─────────────────────────────────────────────────────────┘
```

---

## 📦 COMPONENTES PRINCIPALES

### 1. Docker Compose
- **Archivo:** `docker-compose.ssl.yml`
- **Servicios:** MySQL 8.0, phpMyAdmin, Aplicación PHP/Apache
- **Red:** Bridge privada con subnet 172.25.0.0/16

### 2. Base de Datos
- **Motor:** MySQL 8.0
- **Charset:** utf8mb4_unicode_ci
- **Conexiones máximas:** 200
- **Buffer pool:** 512M

### 3. Aplicación Web
- **PHP:** 8.2 con Apache
- **Framework:** Nativo PHP
- **Facturación:** Greenter (SUNAT Perú)
- **Reportes:** mPDF

### 4. Seguridad
- **SSL:** Let's Encrypt (renovación automática)
- **Firewall:** UFW configurado
- **HTTPS:** Redirección automática desde HTTP

---

## 🔧 CONFIGURACIÓN REALIZADA

### 1. Instalación de Docker en VPS

```bash
# Actualizar sistema
sudo apt update && sudo apt upgrade -y

# Instalar dependencias
sudo apt install -y curl git ufw ca-certificates gnupg lsb-release

# Instalar Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh
rm get-docker.sh

# Agregar usuario al grupo docker
sudo usermod -aG docker $USER

# Instalar Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose

# Iniciar Docker
sudo systemctl enable docker
sudo systemctl start docker
```

---

### 2. Configuración de Firewall

```bash
sudo ufw allow 22/tcp      # SSH
sudo ufw allow 80/tcp      # HTTP
sudo ufw allow 443/tcp     # HTTPS
sudo ufw allow 8081/tcp    # phpMyAdmin
sudo ufw --force enable
sudo ufw status
```

---

### 3. Configuración DNS del Dominio

En el panel del proveedor de dominio:

```
Tipo: A
Nombre: @
Valor: 72.61.40.91
TTL: 3600

Tipo: A
Nombre: www
Valor: 72.61.40.91
TTL: 3600
```

**Registros CAA (ya configurados):**
- `CAA @ 0 issue "letsencrypt.org"`
- `CAA @ 0 issuewild "letsencrypt.org"`

---

### 4. Despliegue de la Aplicación

```bash
# Clonar repositorio
cd /root
git clone https://github.com/jersson14/sistema_micaela.git
cd sistema_micaela

# Configurar variables de entorno
cp .env.vps .env
nano .env
# Cambiar contraseñas por unas seguras

# Crear directorios necesarios
mkdir -p greenter/xml greenter/cdr greenter/pdf greenter/certificados
mkdir -p Fotos controller/usuario/fotos controller/choferes/fotos controller/empresa/FOTOS
mkdir -p backup ssl
chmod -R 755 greenter Fotos controller backup

# Desplegar con Docker
docker-compose -f docker-compose.vps.yml up -d --build
```

---

### 5. Configuración SSL con Let's Encrypt

```bash
# Instalar Certbot
apt-get update
apt-get install -y certbot python3-certbot-apache

# Detener contenedores temporalmente
docker-compose -f docker-compose.ssl.yml down

# Obtener certificado SSL
certbot certonly --standalone \
  --preferred-challenges http \
  --email jersson1407miranda@gmail.com \
  --agree-tos \
  --no-eff-email \
  -d micaela-tours.com \
  -d www.micaela-tours.com

# Copiar certificados al proyecto
mkdir -p ./ssl
cp /etc/letsencrypt/live/micaela-tours.com/fullchain.pem ./ssl/
cp /etc/letsencrypt/live/micaela-tours.com/privkey.pem ./ssl/
chmod 644 ./ssl/*.pem

# Levantar con SSL
docker-compose -f docker-compose.ssl.yml up -d --build
```

**Renovación automática configurada:**
- Script: `/root/renovar_ssl.sh`
- Cron: Cada 2 meses automáticamente

---

### 6. Importación de Base de Datos

**Usando phpMyAdmin:**
1. Acceder a: `http://72.61.40.91:8081`
2. Usuario: `root`
3. Contraseña: (la configurada en `.env`)
4. Seleccionar base de datos `micaela`
5. Importar archivo `.sql`

**Usando línea de comandos:**
```bash
docker exec -i tours_micaela_db_vps mysql -uroot -p"CONTRASEÑA" micaela < backup.sql
```

---

## 🔌 CONFIGURACIÓN DE CONEXIONES A BASE DE DATOS

### Archivos Actualizados para Docker

Todos los archivos de conexión fueron actualizados para usar la clase `conexionBD` con PDO:

#### 1. Conexión Principal
**Archivo:** `model/model_conexion.php`

```php
<?php
class conexionBD {
    private $pdo;

    public function conexionPDO() {
        $host = getenv('DB_HOST') ?: 'db';
        $usuario = getenv('DB_USER') ?: 'micaela_user';
        $contrasena = getenv('DB_PASSWORD') ?: 'micaela_pass_2024_VPS';
        $bdName = getenv('DB_NAME') ?: 'micaela';
        $puerto = getenv('DB_PORT') ?: 3306;

        try {
            $this->pdo = new PDO(
                "mysql:host=$host;port=$puerto;dbname=$bdName;charset=utf8mb4",
                $usuario,
                $contrasena
            );
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
            return $this->pdo;
        } catch (PDOException $e) {
            error_log('Error de conexión: ' . $e->getMessage());
            return null;
        }
    }
}
?>
```

#### 2. Conexión para Reportes PDF (mPDF)
**Archivo:** `view/MPDF/conexion.php`

```php
<?php
date_default_timezone_set('America/Lima');
require_once __DIR__ . '/../../model/model_conexion.php';

try {
    $conexionBD = new conexionBD();
    $conexion = $conexionBD->conexionPDO();
    
    if ($conexion === null) {
        throw new Exception("No se pudo establecer conexión");
    }
} catch (Exception $e) {
    error_log("Error de conexión mPDF: " . $e->getMessage());
    die("Error al conectar con la base de datos.");
}
?>
```

#### 3. Conexión para Greenter (Facturación SUNAT)
**Archivo:** `greenter/config/config_greenter.php`

```php
<?php
use Greenter\Ws\Services\SunatEndpoints;
use Greenter\See;

require __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../model/model_conexion.php';

function getConnection() {
    try {
        $conexionBD = new conexionBD();
        $pdo = $conexionBD->conexionPDO();
        
        if ($pdo === null) {
            throw new Exception("No se pudo establecer conexión");
        }
        return $pdo;
    } catch (Exception $e) {
        error_log("Error de conexión Greenter: " . $e->getMessage());
        die("❌ Error de conexión a la base de datos\n");
    }
}
```

---

## 📄 REPORTES PDF (mPDF)

### Archivos de Reportes Actualizados

Todos los reportes fueron convertidos de MySQLi a PDO:

1. **`view/MPDF/REPORTE/boleta_pago.php`** - Boletas de encomiendas
2. **`view/MPDF/REPORTE/manifiesto.php`** - Manifiesto de pasajeros
3. **`view/MPDF/REPORTE/pdf_comprobante.php`** - Comprobantes A4
4. **`view/MPDF/REPORTE/ticket_comprobante.php`** - Tickets de comprobantes
5. **`view/MPDF/REPORTE/ticket_nota_de_credito.php`** - Tickets de notas

**Cambios realizados:**
- ✅ Reemplazado `$mysqli` por `$conexion` (PDO)
- ✅ Uso de `require_once __DIR__ . '/../conexion.php'`
- ✅ Prepared statements con `$stmt->execute()`
- ✅ `fetch(PDO::FETCH_ASSOC)` en lugar de `fetch_assoc()`

---

## 🧾 FACTURACIÓN ELECTRÓNICA SUNAT (Greenter)

### Configuración del Certificado Digital

**Ubicación del certificado:**
```
greenter/certificados/certificado_produccion.pem
```

**Subir certificado al VPS:**

```bash
# Desde tu máquina local (Windows)
scp C:\xampp\htdocs\sistema_micaela\greenter\certificados\certificado_produccion.pem root@72.61.40.91:/root/sistema_micaela/greenter/certificados/

# En el VPS, dar permisos
chmod 644 /root/sistema_micaela/greenter/certificados/certificado_produccion.pem

# Reiniciar contenedor
docker restart tours_micaela_app_vps
```

**Verificar que el certificado esté en el contenedor:**
```bash
docker exec tours_micaela_app_vps ls -la /var/www/html/greenter/certificados/
```

### Configuración de Greenter

El sistema detecta automáticamente el modo (Beta/Producción) desde la base de datos:

- **Modo Beta (Pruebas):** `modo_prueba = 1` en tabla `empresa`
- **Modo Producción:** `modo_prueba = 0` en tabla `empresa`

**Credenciales SOL:**
- RUC de la empresa
- Usuario SOL (ej: FACTURA1)
- Clave SOL

Estos datos se obtienen automáticamente de la tabla `empresa` en la base de datos.

---

## 🔐 VARIABLES DE ENTORNO

**Archivo:** `.env`

```env
# Base de datos MySQL
MYSQL_ROOT_PASSWORD=TuContraseñaSuperSegura123!
MYSQL_DATABASE=micaela
MYSQL_USER=micaela_user
MYSQL_PASSWORD=OtraContraseñaSegura456!
```

**⚠️ IMPORTANTE:** Cambia las contraseñas por unas seguras antes de desplegar.

---

## 📊 ACCESOS AL SISTEMA

### Aplicación Web
- **URL:** https://micaela-tours.com
- **Protocolo:** HTTPS con certificado SSL válido
- **Redirección:** HTTP → HTTPS automática

### phpMyAdmin
- **URL:** http://72.61.40.91:8081
- **Usuario:** `root`
- **Contraseña:** La configurada en `.env` (MYSQL_ROOT_PASSWORD)

### MySQL (Acceso externo)
- **Host:** 72.61.40.91
- **Puerto:** 3307
- **Usuario:** `micaela_user`
- **Contraseña:** La configurada en `.env` (MYSQL_PASSWORD)
- **Base de datos:** `micaela`

---

## 🔄 COMANDOS ÚTILES

### Gestión de Contenedores

```bash
# Ver estado de contenedores
docker ps

# Ver logs en tiempo real
docker-compose -f docker-compose.ssl.yml logs -f

# Ver logs de un servicio específico
docker logs tours_micaela_app_vps --tail 50
docker logs tours_micaela_db_vps --tail 50

# Reiniciar servicios
docker-compose -f docker-compose.ssl.yml restart

# Reiniciar un contenedor específico
docker restart tours_micaela_app_vps

# Detener todo
docker-compose -f docker-compose.ssl.yml down

# Reconstruir y levantar
docker-compose -f docker-compose.ssl.yml up -d --build
```

### Actualización de Código

```bash
# Actualizar desde Git
cd /root/sistema_micaela
git pull origin jersson

# Reiniciar solo la aplicación
docker restart tours_micaela_app_vps

# O reconstruir si hay cambios en Dockerfile
docker-compose -f docker-compose.ssl.yml up -d --build app
```

### Backup de Base de Datos

```bash
# Crear backup
docker exec tours_micaela_db_vps mysqldump -uroot -p"CONTRASEÑA" micaela > backup_$(date +%Y%m%d_%H%M%S).sql

# Restaurar backup
docker exec -i tours_micaela_db_vps mysql -uroot -p"CONTRASEÑA" micaela < backup.sql
```

### Gestión de Certificados SSL

```bash
# Ver certificados instalados
certbot certificates

# Renovar manualmente
/root/renovar_ssl.sh

# Ver log de renovaciones
cat /var/log/ssl-renewal.log
```

---

## 🐛 SOLUCIÓN DE PROBLEMAS

### Problema: Aplicación no carga

```bash
# Verificar contenedores
docker ps

# Ver logs de errores
docker logs tours_micaela_app_vps --tail 50

# Verificar firewall
sudo ufw status

# Reiniciar contenedor
docker restart tours_micaela_app_vps
```

### Problema: Error de conexión a BD

```bash
# Verificar que MySQL esté corriendo
docker ps | grep db

# Probar conexión
docker exec tours_micaela_db_vps mysqladmin ping -h localhost -uroot -p"CONTRASEÑA"

# Ver logs de MySQL
docker logs tours_micaela_db_vps --tail 50

# Verificar variables de entorno
docker exec tours_micaela_app_vps env | grep DB_
```

### Problema: Reportes no generan

```bash
# Verificar que el archivo de conexión exista
docker exec tours_micaela_app_vps ls -la /var/www/html/view/MPDF/conexion.php

# Ver errores de PHP
docker exec tours_micaela_app_vps tail -f /var/log/apache2/error.log

# Verificar permisos
docker exec tours_micaela_app_vps ls -la /var/www/html/view/MPDF/REPORTE/
```

### Problema: Facturación SUNAT falla

```bash
# Verificar certificado
docker exec tours_micaela_app_vps ls -la /var/www/html/greenter/certificados/

# Ver logs de Greenter
docker logs tours_micaela_app_vps | grep -i greenter

# Verificar conexión a SUNAT
docker exec tours_micaela_app_vps curl -I https://e-beta.sunat.gob.pe
```

### Problema: SSL no funciona

```bash
# Verificar certificados
ls -la /root/sistema_micaela/ssl/

# Verificar que estén en el contenedor
docker exec tours_micaela_app_vps ls -la /etc/ssl/certs/fullchain.pem
docker exec tours_micaela_app_vps ls -la /etc/ssl/private/privkey.pem

# Reconstruir contenedor
docker-compose -f docker-compose.ssl.yml down
docker-compose -f docker-compose.ssl.yml up -d --build
```

### Problema: Sesiones expiran muy rápido (se cierra sesión al recargar)

```bash
# Verificar configuración actual de sesiones
docker exec tours_micaela_app_vps php -r "echo 'Tiempo de sesión: ' . ini_get('session.gc_maxlifetime') . ' segundos\n';"

# Configurar sesiones para 2 horas (7200 segundos)
docker exec -u root tours_micaela_app_vps bash -c 'echo "session.save_path = /tmp/sessions
session.gc_maxlifetime = 7200
session.cookie_lifetime = 7200
session.gc_probability = 1
session.gc_divisor = 100" > /usr/local/etc/php/conf.d/sessions.ini'

# Crear directorio de sesiones con permisos
docker exec -u root tours_micaela_app_vps mkdir -p /tmp/sessions
docker exec -u root tours_micaela_app_vps chmod 1777 /tmp/sessions
docker exec -u root tours_micaela_app_vps chown www-data:www-data /tmp/sessions

# Reiniciar contenedor
docker restart tours_micaela_app_vps

# Verificar que se aplicó
docker exec tours_micaela_app_vps php -r "echo 'save_path: ' . ini_get('session.save_path') . '\n';"
docker exec tours_micaela_app_vps php -r "echo 'Duración: ' . (ini_get('session.gc_maxlifetime')/60) . ' minutos\n';"
```

**Tiempos de sesión recomendados:**
- 2 horas (7200): Uso normal
- 4 horas (14400): Uso extendido
- 8 horas (28800): Jornada laboral completa

### Problema: Error 403 Forbidden al acceder a fotos

```bash
# Dar permisos a carpetas de fotos desde el host
cd /root/sistema_micaela
sudo chown -R 33:33 controller/choferes/fotos
sudo chown -R 33:33 controller/usuario/fotos
sudo chown -R 33:33 controller/empresa/FOTOS
sudo chown -R 33:33 Fotos

sudo chmod -R 755 controller/choferes/fotos
sudo chmod -R 755 controller/usuario/fotos
sudo chmod -R 755 controller/empresa/FOTOS
sudo chmod -R 755 Fotos

# Crear .htaccess para permitir acceso
echo "Options +Indexes
Require all granted" > controller/choferes/fotos/.htaccess

echo "Options +Indexes
Require all granted" > controller/usuario/fotos/.htaccess

echo "Options +Indexes
Require all granted" > controller/empresa/FOTOS/.htaccess

# Reiniciar contenedor
docker restart tours_micaela_app_vps
```

---

## ⚙️ CONFIGURACIONES ADICIONALES POST-DESPLIEGUE

### 1. Configuración de Sesiones PHP

Las sesiones PHP están configuradas para durar **2 horas** (7200 segundos) para evitar que los usuarios sean desconectados frecuentemente.

**Configuración aplicada:**
```ini
session.save_path = /tmp/sessions
session.gc_maxlifetime = 7200      # 2 horas
session.cookie_lifetime = 7200     # 2 horas
session.gc_probability = 1
session.gc_divisor = 100
```

**Verificar configuración:**
```bash
docker exec tours_micaela_app_vps php -i | grep session
```

**Cambiar duración de sesiones:**
```bash
# Para 4 horas (14400 segundos)
docker exec -u root tours_micaela_app_vps bash -c 'echo "session.save_path = /tmp/sessions
session.gc_maxlifetime = 14400
session.cookie_lifetime = 14400
session.gc_probability = 1
session.gc_divisor = 100" > /usr/local/etc/php/conf.d/sessions.ini'

docker restart tours_micaela_app_vps
```

### 2. Permisos de Carpetas de Fotos

Las carpetas de fotos necesitan permisos especiales porque están montadas como volúmenes desde el host.

**Carpetas configuradas:**
- `controller/choferes/fotos/`
- `controller/usuario/fotos/`
- `controller/empresa/FOTOS/`
- `Fotos/`

**Permisos aplicados:**
- Owner: `33:33` (www-data en Docker)
- Permisos: `755` (lectura/escritura para owner, lectura para otros)
- `.htaccess` con `Require all granted`

**Aplicar permisos:**
```bash
cd /root/sistema_micaela
sudo chown -R 33:33 controller/choferes/fotos controller/usuario/fotos controller/empresa/FOTOS Fotos
sudo chmod -R 755 controller/choferes/fotos controller/usuario/fotos controller/empresa/FOTOS Fotos
```

### 3. Volúmenes Persistentes

Los siguientes directorios están montados como volúmenes para persistir datos:

```yaml
volumes:
  - ./greenter/xml:/var/www/html/greenter/xml
  - ./greenter/cdr:/var/www/html/greenter/cdr
  - ./greenter/pdf:/var/www/html/greenter/pdf
  - ./greenter/certificados:/var/www/html/greenter/certificados
  - ./Fotos:/var/www/html/Fotos
  - ./controller/usuario/fotos:/var/www/html/controller/usuario/fotos
  - ./controller/choferes/fotos:/var/www/html/controller/choferes/fotos
  - ./controller/empresa/FOTOS:/var/www/html/controller/empresa/FOTOS
  - ./backup:/var/www/html/backup
  - sessions_data_vps:/tmp/sessions
  - ./ssl/fullchain.pem:/etc/ssl/certs/fullchain.pem:ro
  - ./ssl/privkey.pem:/etc/ssl/private/privkey.pem:ro
```

**Ventajas:**
- ✅ Los datos persisten aunque se elimine el contenedor
- ✅ Fácil acceso a archivos desde el host
- ✅ Backups más sencillos
- ✅ Actualizaciones sin pérdida de datos

---

## 📝 CHECKLIST DE VERIFICACIÓN

### Antes del Despliegue
- [ ] VPS con Ubuntu 20.04+ configurado
- [ ] Docker y Docker Compose instalados
- [ ] Dominio apuntando a la IP del VPS
- [ ] DNS propagado (verificado con nslookup)
- [ ] Firewall configurado (puertos 80, 443, 8081)
- [ ] Archivo `.env` con contraseñas seguras
- [ ] Certificado SUNAT disponible

### Durante el Despliegue
- [ ] Contenedores levantados correctamente
- [ ] Base de datos importada
- [ ] Certificado SSL obtenido
- [ ] Aplicación accesible por HTTPS
- [ ] phpMyAdmin funcionando

### Después del Despliegue
- [ ] Login funciona correctamente
- [ ] Reportes PDF se generan
- [ ] Facturación SUNAT operativa
- [ ] Sesiones persistentes
- [ ] Imágenes cargan correctamente
- [ ] Backup de BD configurado
- [ ] Renovación SSL automática activa

---

## 🎯 CARACTERÍSTICAS IMPLEMENTADAS

### Seguridad
- ✅ HTTPS con certificado SSL válido
- ✅ Renovación automática de certificados
- ✅ Firewall UFW configurado
- ✅ Cookies seguras (httponly, secure)
- ✅ Headers de seguridad (HSTS, X-Frame-Options)
- ✅ Contraseñas en variables de entorno

### Base de Datos
- ✅ MySQL 8.0 con charset utf8mb4
- ✅ Conexiones PDO con prepared statements
- ✅ Persistencia de datos en volúmenes
- ✅ phpMyAdmin para gestión visual
- ✅ Backups manuales y automáticos

### Aplicación
- ✅ PHP 8.2 con Apache
- ✅ Sesiones persistentes (2 horas de duración)
- ✅ Generación de reportes PDF (mPDF)
- ✅ Facturación electrónica SUNAT (Greenter)
- ✅ Subida de imágenes con permisos correctos
- ✅ Gestión de encomiendas y pasajeros

### DevOps
- ✅ Dockerizado completamente
- ✅ Docker Compose para orquestación
- ✅ Volúmenes para persistencia
- ✅ Health checks configurados
- ✅ Logs centralizados
- ✅ Despliegue con un comando

---

## 📚 DOCUMENTACIÓN ADICIONAL

- **[PASOS_RAPIDOS_VPS_COMPLETO.md](PASOS_RAPIDOS_VPS_COMPLETO.md)** - Guía paso a paso
- **[CONFIGURAR_DOMINIO_SSL.md](CONFIGURAR_DOMINIO_SSL.md)** - Configuración SSL detallada
- **[COMANDOS_VPS_DOCKER.txt](COMANDOS_VPS_DOCKER.txt)** - Referencia de comandos
- **[CHECKLIST_VPS.md](CHECKLIST_VPS.md)** - Lista de verificación completa

---

## 🎉 RESULTADO FINAL

### Sistema Completamente Funcional

✅ **Aplicación Web:** https://micaela-tours.com  
✅ **Base de Datos:** MySQL 8.0 con phpMyAdmin  
✅ **Seguridad:** SSL/HTTPS con Let's Encrypt  
✅ **Facturación:** Integración con SUNAT Perú  
✅ **Reportes:** Generación de PDFs  
✅ **Persistencia:** Datos y sesiones guardados  
✅ **Monitoreo:** Logs y health checks  

### Tecnologías Utilizadas

- **Backend:** PHP 8.2
- **Base de Datos:** MySQL 8.0
- **Servidor Web:** Apache 2.4
- **Contenedores:** Docker + Docker Compose
- **SSL:** Let's Encrypt (Certbot)
- **Facturación:** Greenter (SUNAT Perú)
- **Reportes:** mPDF
- **Sistema Operativo:** Ubuntu 20.04+

---

## 📞 INFORMACIÓN DE CONTACTO

**Proyecto:** Sistema Tours Micaela  
**Desarrollador:** Jersson Miranda  
**Email:** jersson1407miranda@gmail.com  
**Repositorio:** https://github.com/jersson14/sistema_micaela  
**Dominio:** https://micaela-tours.com  

---

## 📅 HISTORIAL DE CAMBIOS

### Noviembre 2024
- ✅ Despliegue inicial en VPS
- ✅ Configuración de Docker Compose
- ✅ Implementación de SSL con Let's Encrypt
- ✅ Migración de conexiones MySQLi a PDO
- ✅ Configuración de Greenter para SUNAT
- ✅ Actualización de reportes mPDF
- ✅ Configuración de dominio micaela-tours.com
- ✅ Implementación de renovación automática SSL

---

**🎊 ¡DESPLIEGUE COMPLETADO EXITOSAMENTE!**

El sistema está en producción, funcionando correctamente con todas las características implementadas.

---

## 🔄 FLUJO DE TRABAJO PARA ACTUALIZACIONES

### Cada vez que hagas cambios en tu código local

Sigue estos pasos para actualizar el VPS:

#### 1️⃣ En tu máquina local (Windows)

```bash
# Verificar cambios realizados
git status

# Agregar todos los archivos modificados
git add .

# Hacer commit con mensaje descriptivo
git commit -m "Descripción de los cambios realizados"

# Subir cambios a GitHub
git push origin jersson
```

#### 2️⃣ En el VPS (Ubuntu)

```bash
# Conectar al VPS por SSH
ssh root@72.61.40.91

# Ir al directorio del proyecto
cd /root/sistema_micaela

# Descargar los cambios desde GitHub
git pull origin jersson

# Reiniciar el contenedor de la aplicación
docker restart tours_micaela_app_vps

# Ver logs para verificar que todo funciona
docker logs tours_micaela_app_vps --tail 30
```

---

### 📋 Casos Especiales

#### Si modificaste archivos de configuración de Docker (Dockerfile, docker-compose, etc.)

```bash
# En el VPS
cd /root/sistema_micaela
git pull origin jersson

# Reconstruir la imagen Docker
docker-compose -f docker-compose.ssl.yml down
docker-compose -f docker-compose.ssl.yml up -d --build

# Verificar que todo esté corriendo
docker ps
```

#### Si agregaste nuevas dependencias de Composer

```bash
# En el VPS
cd /root/sistema_micaela
git pull origin jersson

# Entrar al contenedor e instalar dependencias
docker exec -u root tours_micaela_app_vps composer install --no-dev --optimize-autoloader

# Reiniciar contenedor
docker restart tours_micaela_app_vps
```

#### Si modificaste la base de datos (nuevas tablas, columnas, etc.)

```bash
# Opción 1: Ejecutar SQL manualmente en phpMyAdmin
# http://72.61.40.91:8081

# Opción 2: Desde línea de comandos
docker exec -i tours_micaela_db_vps mysql -uroot -p"TU_CONTRASEÑA" micaela < cambios.sql
```

#### Si subiste nuevos certificados o archivos sensibles

```bash
# Desde tu máquina local (Windows)
scp C:\ruta\al\archivo.pem root@72.61.40.91:/root/sistema_micaela/greenter/certificados/

# En el VPS, dar permisos
chmod 644 /root/sistema_micaela/greenter/certificados/archivo.pem

# Reiniciar contenedor
docker restart tours_micaela_app_vps
```

---

### ⚡ Comandos Rápidos (Resumen)

**Flujo normal de actualización:**
```bash
# LOCAL
git add .
git commit -m "Descripción del cambio"
git push origin jersson

# VPS
ssh root@72.61.40.91
cd /root/sistema_micaela
git pull origin jersson
docker restart tours_micaela_app_vps
```

**Con reconstrucción de imagen:**
```bash
# VPS
cd /root/sistema_micaela
git pull origin jersson
docker-compose -f docker-compose.ssl.yml down
docker-compose -f docker-compose.ssl.yml up -d --build
```

---

### 🔍 Verificación Post-Actualización

Después de cada actualización, verifica que todo funcione:

```bash
# Ver estado de contenedores
docker ps

# Ver logs en tiempo real
docker logs tours_micaela_app_vps -f

# Probar la aplicación
curl -I https://micaela-tours.com

# Verificar que no haya errores
docker logs tours_micaela_app_vps --tail 50 | grep -i error
```

---

### 📝 Buenas Prácticas

1. **Siempre haz commit con mensajes descriptivos:**
   ```bash
   git commit -m "Fix: Corregir error en reportes PDF"
   git commit -m "Feat: Agregar nueva funcionalidad de pagos"
   git commit -m "Update: Actualizar certificado SUNAT"
   ```

2. **Verifica los cambios antes de hacer push:**
   ```bash
   git status
   git diff
   ```

3. **Haz backup de la base de datos antes de cambios importantes:**
   ```bash
   docker exec tours_micaela_db_vps mysqldump -uroot -p"CONTRASEÑA" micaela > backup_$(date +%Y%m%d).sql
   ```

4. **Prueba en local antes de subir a producción:**
   - Verifica que todo funcione en tu XAMPP local
   - Revisa que no haya errores en los logs
   - Prueba las funcionalidades modificadas

5. **Mantén un registro de cambios:**
   - Documenta cambios importantes
   - Anota versiones y fechas
   - Guarda backups antes de actualizaciones grandes

---

### 🚨 En Caso de Emergencia

Si algo sale mal después de una actualización:

```bash
# Revertir al commit anterior
git log --oneline  # Ver historial
git reset --hard COMMIT_ANTERIOR
git push -f origin jersson

# En el VPS
cd /root/sistema_micaela
git reset --hard origin/jersson
docker restart tours_micaela_app_vps

# O restaurar desde backup
docker exec -i tours_micaela_db_vps mysql -uroot -p"CONTRASEÑA" micaela < backup.sql
```

---

### 📞 Checklist de Actualización

- [ ] Cambios probados en local
- [ ] Backup de base de datos realizado (si aplica)
- [ ] `git add .` ejecutado
- [ ] `git commit -m "mensaje"` ejecutado
- [ ] `git push origin jersson` ejecutado
- [ ] SSH al VPS conectado
- [ ] `git pull origin jersson` ejecutado
- [ ] Contenedor reiniciado
- [ ] Logs verificados sin errores
- [ ] Aplicación probada en navegador
- [ ] Funcionalidades verificadas

---

**¡Listo! Ahora tienes un flujo de trabajo completo para mantener tu aplicación actualizada.** 🚀
