# 🚌 Optimización Operativa en Tours Micaela

Sistema integral de facturación electrónica y gestión operativa para una empresa de transporte interprovincial, desarrollado para eliminar la duplicidad de registros, los errores manuales y la falta de visibilidad sobre el negocio.

![PHP](https://img.shields.io/badge/PHP-7.4%2B-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-4479A1?logo=mysql&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-Contenedores-2496ED?logo=docker&logoColor=white)
![SUNAT](https://img.shields.io/badge/Facturación-SUNAT%20%2F%20Greenter-success)
![License](https://img.shields.io/badge/Licencia-MIT-blue)

---

## El Desafío (2025)

Tours Micaela Abancay enfrentaba serios problemas operativos debido a la gestión manual y dispersa de sus procesos:

- ❌ **Duplicidad y errores** — la gestión manual generaba pérdida de información operativa.
- ❌ **Demoras en ventas** — falta de integración entre la emisión de comprobantes y el área de ventas.
- ❌ **Operaciones a ciegas** — la ausencia de reportes automáticos limitaba la toma de decisiones.

### Preguntas de investigación

> "¿De qué manera la gestión manual... genera duplicidad, errores y pérdida de información...?"
>
> "¿Cómo la falta de integración... provoca demoras y errores de facturación...?"
>
> "¿En qué medida la ausencia de reportes... dificulta el control y limita las decisiones...?"

---

## La Solución Tecnológica

### 🧾 Facturación con Greenter

Implementación del motor [Greenter](https://github.com/thegreenter/greenter) para la generación, firma digital y envío de comprobantes electrónicos (XML/CDR) directamente a SUNAT, asegurando validez tributaria en cada transacción.

### 🐳 Despliegue con Docker

Infraestructura contenerizada en VPS remoto. Uso de scripts `.sh` personalizados para automatizar el despliegue, las migraciones y el mantenimiento de los servicios.

### 🪪 Integración RENIEC

Uso de la API **DECOLECTA** para consultar datos de clientes (RUC/DNI) en tiempo real, agilizando el proceso de emisión de boletos y facturas en ventanilla.

---

## Arquitectura del Sistema (MVC + Docker)

```text
┌─────────────────────────────────────────────┐
│                  VIEW (Frontend)             │
│        HTML / CSS / JS / Bootstrap           │
└───────────────────┬───────────────────────────┘
                     │
┌────────────────────▼──────────────────────────┐
│            Backend Logic (PHP - MVC)           │
│         Controladores + Greenter Lib           │
└───────────────────┬───────────────────────────┘
                     │
┌────────────────────▼──────────────────────────┐
│           MySQL Database (Datos Transac.)      │
└───────────────────┬───────────────────────────┘
                     │
┌────────────────────▼──────────────────────────┐
│         Docker Container — Despliegue VPS      │
└─────────────────────────────────────────────────┘
```

---

## Resultados e Impacto

| Métrica | Resultado |
|---|---|
| 📈 **+10k** | Facturas/mes procesadas masivamente sin errores de validación |
| ✅ **0%** | Duplicidad — eliminación total de errores por doble registro |
| 👁 **100%** | Visibilidad — reportes en tiempo real para la gerencia |

---

## 📌 Características Principales

### 🎫 Gestión de Transporte

- Control de salidas diarias con rutas, horarios y conductores
- Gestión de reservas y venta de pasajes
- Sistema de encomiendas con seguimiento de estados
- Registro de choferes con control de vencimientos
- Administración de rutas, servicios y tarifas
- Control de asistencia de pasajeros

### 💼 Facturación Electrónica SUNAT

- Emisión de **Facturas (01)** y **Boletas (03)** electrónicas
- Generación de **Notas de Crédito (07)** y **Notas de Débito (08)**
- Generación automática de XML firmado digitalmente
- Envío directo a SUNAT con respuesta CDR
- Almacenamiento de hash, XML y CDR
- Consulta de RUC/DNI en tiempo real (API DECOLECTA / RENIEC)

### 📊 Gestión Financiera

- Control de ingresos y gastos por sucursal
- Reportes de diferencias y cierres de caja
- Múltiples tipos de pago (efectivo, tarjeta, transferencia)
- Dashboard con indicadores en tiempo real

### 👥 Administración

- Sistema de usuarios con roles y permisos
- Gestión de sucursales y empresas
- Reportes PDF con mPDF
- Interfaz responsive con AdminLTE 3.0

---

## 🛠 Stack Tecnológico

| Categoría | Tecnología |
|---|---|
| Lenguaje Backend | PHP 7.4+ |
| Base de Datos | MySQL 5.7+ |
| Facturación Electrónica | Greenter 5.0+ |
| Generación de PDF | mPDF |
| Frontend | HTML5, CSS3, JavaScript |
| Framework CSS | Bootstrap 4/5 |
| Plantilla Admin | AdminLTE 3.0 |
| Arquitectura | MVC (Model-View-Controller) |
| Contenerización | Docker / Docker Compose |
| Gestión de Dependencias | Composer |
| Consulta de Identidad | API DECOLECTA (RENIEC) |

---

## 📂 Estructura del Proyecto

```text
sistema-tours-micaela/
├── controller/        # Controladores por módulo (choferes, comprobante, encomiendas, ...)
├── model/             # Modelos de datos y acceso a BD
├── view/              # Vistas e interfaz de usuario (incluye generación de PDFs)
├── greenter/          # Integración con SUNAT (firma, envío, certificados)
├── js/                # Scripts del frontend
├── img/               # Recursos gráficos
├── composer.json      # Dependencias del proyecto
├── docker-compose.yml # Orquestación de servicios
├── Dockerfile         # Imagen de la aplicación PHP/Apache
├── index.php          # Punto de entrada / login
└── README.md
```

> Por seguridad, los archivos sensibles (certificados digitales, backups de base de datos, variables de entorno y credenciales) están excluidos del repositorio mediante `.gitignore`.

---

## 🧾 Flujo de Facturación Electrónica

1. **Registro del cliente** — se valida y registra con datos completos (RUC/DNI, dirección, ubigeo) consultados en tiempo real.
2. **Generación del comprobante** — se crea con estado `PENDIENTE`.
3. **Creación del XML** — se genera y firma digitalmente con Greenter.
4. **Envío a SUNAT** — se transmite vía SOAP y se recibe la respuesta CDR.
5. **Actualización de estado** — se persiste el hash, código de respuesta y estado final (`ACEPTADO` o `RECHAZADO`).

| Código | Tipo de Comprobante | Serie de Ejemplo |
|---|---|---|
| 01 | Factura | F001 |
| 03 | Boleta de Venta | B001 |
| 07 | Nota de Crédito | FN01 / BN01 |
| 08 | Nota de Débito | FD01 / BD01 |

---

## 🔐 Seguridad

- Sesiones PHP con validación de usuario
- Contraseñas hasheadas con `password_hash()`
- Validación de permisos por rol
- Protección contra SQL Injection mediante PDO
- Certificado digital para firma electrónica
- Credenciales y archivos sensibles fuera del control de versiones

---

## 👨‍💻 Autor

**Ingeniero Jersson**
Especialista en Ingeniería de Sistemas y Desarrollo de Software
📧 jersson14071996@gmail.com

---

## 📄 Licencia

Este proyecto está bajo la licencia **MIT**. Puedes usarlo, modificarlo y distribuirlo libremente para fines académicos o comerciales, mencionando al autor original.
