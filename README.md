# 🚌 Sistema Web de Salidas Diarias y Facturación Electrónica - Tours Micaela

Sistema integral de gestión para empresas de transporte interprovincial desarrollado con PHP y MySQL, con integración completa de facturación electrónica SUNAT mediante **Greenter**.

---

## 📌 Características Principales

### 🎫 Gestión de Transporte
✅ Control de salidas diarias con rutas, horarios y conductores  
✅ Gestión de reservas y venta de pasajes  
✅ Sistema de encomiendas con seguimiento de estados  
✅ Registro de choferes con control de vencimientos  
✅ Administración de rutas, servicios y tarifas  
✅ Control de asistencia de pasajeros  

### 💼 Facturación Electrónica SUNAT
✅ Emisión de **Facturas (01)** y **Boletas (03)** electrónicas  
✅ Generación de **Notas de Crédito (07)** y **Notas de Débito (08)**  
✅ Integración con **Greenter** (biblioteca PHP para SUNAT)  
✅ Generación automática de XML firmado digitalmente  
✅ Envío directo a SUNAT con respuesta CDR  
✅ Almacenamiento de hash, XML y CDR  
✅ Consulta de RUC/DNI en tiempo real  

### 📊 Gestión Financiera
✅ Control de ingresos y gastos por sucursal  
✅ Reportes de diferencias y cierres de caja  
✅ Múltiples tipos de pago (efectivo, tarjeta, transferencia)  
✅ Dashboard con indicadores en tiempo real  

### 👥 Administración
✅ Sistema de usuarios con roles y permisos  
✅ Gestión de sucursales y empresas  
✅ Reportes PDF con mPDF  
✅ Interfaz responsive con AdminLTE 3.0  

---

## 🛠 Tecnologías Utilizadas

| Categoría               | Tecnología                |
|-------------------------|---------------------------|
| Lenguaje Backend        | PHP 7.4+                  |
| Base de Datos           | MySQL (puerto 3307)       |
| Facturación Electrónica | Greenter 5.0+             |
| Generación PDF          | mPDF                      |
| Frontend                | HTML5, CSS3, JavaScript   |
| Framework CSS           | Bootstrap 4/5             |
| Plantilla Admin         | AdminLTE 3.0              |
| Arquitectura            | MVC (Model-View-Controller)|
| Gestión Dependencias    | Composer                  |

---

## 📂 Estructura del Proyecto

```
sistema-tours-micaela/
├── controller/              # Controladores por módulo
│   ├── choferes/
│   ├── clientes/
│   ├── comprobante/        # Lógica de facturación
│   ├── encomiendas/
│   ├── gastos/
│   ├── ingresos/
│   ├── reservas/
│   ├── salidas_diarias/
│   ├── usuario/
│   └── ...
├── model/                   # Modelos de datos
│   ├── model_conexion.php
│   ├── model_comprobante.php
│   ├── model_encomiendas.php
│   └── ...
├── view/                    # Vistas (interfaz de usuario)
│   ├── comprobantes/
│   ├── encomiendas/
│   ├── MPDF/               # Generación de PDFs
│   └── ...
├── greenter/               # Integración SUNAT
│   ├── config/             # Configuración Greenter
│   ├── xml/                # XMLs generados
│   ├── cdr/                # Respuestas CDR de SUNAT
│   ├── pdf/                # PDFs de comprobantes
│   ├── certificados/       # Certificado digital
│   ├── factura_bd.php      # Script de envío a SUNAT
│   └── comunicacion_baja.php
├── vendor/                 # Dependencias Composer
├── js/                     # Scripts JavaScript
├── img/                    # Recursos gráficos
├── Fotos/                  # Fotos de usuarios/choferes
├── composer.json           # Dependencias del proyecto
├── index.php               # Login principal
└── README.md
```

---

## 🔧 Requisitos del Sistema

- **PHP**: 7.4 o superior
- **MySQL**: 5.7+ o MariaDB 10.4+
- **Apache**: 2.4+ con mod_rewrite
- **Composer**: Para gestión de dependencias
- **Extensiones PHP requeridas**:
  - PDO y PDO_MySQL
  - OpenSSL
  - SOAP
  - XML
  - DOM
  - ZipArchive
- **Certificado Digital**: Para firma electrónica (formato .pem)
- **Conexión a Internet**: Para envío a SUNAT

---

## 🚀 Instalación

### Opción A: Instalación con Docker (Recomendado) 🐳

La forma más rápida de desplegar el sistema en cualquier PC.

**Requisitos:**
- Docker Desktop instalado
- Backup de la base de datos (.sql)

**Pasos rápidos:**
```bash
# 1. Clonar o copiar el proyecto
cd sistema-tours-micaela

# 2. Copiar backup de BD
cp /ruta/backup.sql backup/micaela.sql

# 3. Copiar certificado digital
cp /ruta/certificado.pem greenter/certificados/certificado.pem

# 4. Levantar servicios
docker-compose up -d

# 5. Acceder
# Aplicación: http://localhost:8080
# phpMyAdmin: http://localhost:8081
```

📖 **Guía completa**: Ver [DOCKER_SETUP.md](DOCKER_SETUP.md)

---

### Opción B: Instalación Manual

### 1. Clonar el repositorio
```bash
git clone https://github.com/tu_usuario/sistema-tours-micaela.git
cd sistema-tours-micaela
```

### 2. Instalar dependencias con Composer
```bash
composer install
```

Esto instalará automáticamente:
- greenter/core
- greenter/greenter
- greenter/lite
- mPDF (si está configurado)

### 3. Configurar la base de datos

Importa el archivo SQL en MySQL:
```bash
mysql -u root -p micaela < micaela.sql
```

O desde phpMyAdmin:
- Crea una base de datos llamada `micaela`
- Importa el archivo `micaela.sql`

### 4. Configurar conexión a la base de datos

Edita `model/model_conexion.php`:
```php
$host = "localhost";
$usuario = "root";
$contrasena = "tu_contraseña";
$bdName = "micaela";
$puerto = 3307; // Ajusta según tu configuración
```

### 5. Configurar Greenter para SUNAT

Edita `greenter/config/config_greenter.php`:
```php
// Certificado digital (.pem)
define('CERT_PATH', __DIR__ . '/../certificados/certificado.pem');

// RUC de la empresa
define('RUC_EMPRESA', '20XXXXXXXXX');

// Modo: 'beta' para pruebas, 'produccion' para real
define('MODO_SUNAT', 'beta');
```

Coloca tu certificado digital en `greenter/certificados/certificado.pem`

### 6. Configurar permisos de carpetas
```bash
chmod -R 755 greenter/xml
chmod -R 755 greenter/cdr
chmod -R 755 greenter/pdf
chmod -R 755 Fotos
```

### 7. Acceder al sistema

Abre en tu navegador:
```
http://localhost/sistema-tours-micaela/
```

**Credenciales por defecto** (verificar en la BD):
- Usuario: `admin`
- Contraseña: `admin123`

---

## 🧾 Facturación Electrónica con Greenter

### Flujo de emisión de comprobantes

1. **Registro del cliente**: Se valida y registra en `cliente_sunat` con datos completos (RUC/DNI, dirección, ubigeo)

2. **Generación del comprobante**: Se crea en la tabla `comprobantes` con estado `PENDIENTE`

3. **Creación del XML**: El script `greenter/factura_bd.php` genera el XML firmado digitalmente

4. **Envío a SUNAT**: Se envía mediante SOAP y se recibe la respuesta CDR

5. **Actualización de estado**: Se guarda el hash, código de respuesta y estado (`ACEPTADO` o `RECHAZADO`)

### Tipos de comprobantes soportados

| Código | Tipo                  | Serie Ejemplo |
|--------|-----------------------|---------------|
| 01     | Factura               | F001          |
| 03     | Boleta de Venta       | B001          |
| 07     | Nota de Crédito       | FN01 / BN01   |
| 08     | Nota de Débito        | FD01 / BD01   |

### Ejecutar envío manual a SUNAT
```bash
php greenter/factura_bd.php [ID_COMPROBANTE]
```

---

## 📊 Módulos del Sistema

### 🚌 Salidas Diarias
- Programación de viajes con ruta, conductor y vehículo
- Registro de pasajeros y encomiendas por viaje
- Control de estados: Programado, En Ruta, Finalizado
- Asistencia de pasajeros

### 📦 Encomiendas
- Registro de remitente y destinatario
- Seguimiento por código único
- Estados: Enviado, En Tránsito, Recibido
- Control de pagos

### 💰 Ingresos y Gastos
- Registro por sucursal y usuario
- Categorización por indicadores
- Reportes de diferencias de caja
- Cierre diario

### 👤 Usuarios y Roles
- Sistema de permisos por rol
- Registro de actividad
- Gestión de sucursales asignadas

---

## 📱 Capturas de Pantalla

_(Puedes agregar imágenes del sistema aquí)_

---

## 🔐 Seguridad

- Sesiones PHP con validación de usuario
- Contraseñas hasheadas (recomendado usar `password_hash()`)
- Validación de permisos por rol
- Protección contra SQL Injection mediante PDO
- Certificado digital para firma electrónica

---

## 🚀 Despliegue en GitHub y VPS

### Guías de Despliegue Disponibles

El proyecto incluye documentación completa para desplegar en producción:

- **`RESUMEN_GITHUB_VPS.md`** - Resumen ejecutivo del proceso completo
- **`COMANDOS_RAPIDOS.md`** - Comandos copy-paste listos para usar
- **`GITHUB_DEPLOYMENT.md`** - Guía detallada paso a paso
- **`check-before-github.bat`** - Script de verificación (Windows)
- **`check-before-github.sh`** - Script de verificación (Linux/Mac)

### Proceso Rápido

```bash
# 1. Verificar antes de subir
.\check-before-github.bat

# 2. Subir a GitHub
git init
git add .
git commit -m "Initial commit"
git remote add origin https://github.com/TU_USUARIO/sistema-tours-micaela.git
git push -u origin main

# 3. Desplegar en VPS
ssh usuario@tu-vps-ip
git clone https://github.com/TU_USUARIO/sistema-tours-micaela.git
cd sistema-tours-micaela
# Configurar archivos sensibles (ver COMANDOS_RAPIDOS.md)
docker-compose -f docker-compose.production.yml up -d
```

---

## 🐛 Solución de Problemas

### Error: "No se puede conectar a la base de datos"
- Verifica que MySQL esté corriendo en el puerto 3307
- Confirma usuario y contraseña en `model_conexion.php`

### Error: "Class 'Greenter\...' not found"
- Ejecuta `composer install` en la raíz del proyecto
- Verifica que exista la carpeta `vendor/`

### Error al enviar a SUNAT: "Certificado inválido"
- Verifica que el archivo .pem esté en `greenter/certificados/`
- Confirma que el certificado no haya expirado
- Asegúrate de que el RUC coincida con el del certificado

### Comprobante queda en estado PENDIENTE
- Revisa los logs en `greenter/envio_log.txt`
- Ejecuta manualmente: `php greenter/factura_bd.php [ID]`
- Verifica conexión a internet

---

## � Despliegpue con Docker

El proyecto incluye configuración completa de Docker para facilitar la migración entre PCs.

### Archivos Docker incluidos:
- `Dockerfile` - Imagen de la aplicación PHP/Apache
- `docker-compose.yml` - Orquestación de servicios
- `.dockerignore` - Archivos excluidos de la imagen
- `Makefile` - Comandos simplificados
- `DOCKER_SETUP.md` - Guía completa de instalación

### Comandos rápidos:
```bash
# Levantar servicios
docker-compose up -d

# Ver logs
docker-compose logs -f

# Hacer backup de BD
docker exec tours_micaela_db mysqldump -uroot -proot_password_2024 micaela > backup/backup.sql

# Detener servicios
docker-compose down
```

### Puertos expuestos:
- **8080**: Aplicación web
- **8081**: phpMyAdmin
- **3307**: MySQL

---

## 📝 Notas Importantes

- El sistema usa el puerto **3307** para MySQL (no el estándar 3306)
- La zona horaria está configurada para **America/Lima** (UTC-5)
- Los archivos SQL incluyen datos de ejemplo
- Se recomienda usar el modo **beta** de SUNAT para pruebas antes de producción
- **Con Docker**: La migración a otra PC es tan simple como copiar el proyecto y ejecutar `docker-compose up -d`

---

## 🤝 Contribuciones

Las contribuciones son bienvenidas. Por favor:
1. Haz un fork del proyecto
2. Crea una rama para tu feature (`git checkout -b feature/nueva-funcionalidad`)
3. Commit tus cambios (`git commit -m 'Agregar nueva funcionalidad'`)
4. Push a la rama (`git push origin feature/nueva-funcionalidad`)
5. Abre un Pull Request

---

## 👨‍💻 Autor

**Ingeniero Jersson**  
Especialista en Ingeniería de Sistemas y Desarrollo de Software  
📧 jersson14071996@gmail.com

---

## 📄 Licencia

Este proyecto está bajo la licencia **MIT**.  
Puedes usarlo, modificarlo y distribuirlo libremente para fines académicos o comerciales, mencionando al autor original.

---

## 🙌 Agradecimientos

- [Greenter](https://github.com/thegreenter/greenter) - Biblioteca PHP para facturación electrónica SUNAT
- [AdminLTE](https://adminlte.io) - Plantilla de administración
- [mPDF](https://mpdf.github.io) - Generación de PDFs
- [Composer](https://getcomposer.org) - Gestor de dependencias PHP
- Comunidad PHP & Open Source

---

## 📞 Soporte

Para reportar bugs o solicitar nuevas funcionalidades, abre un issue en GitHub o contacta al autor.