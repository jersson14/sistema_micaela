
# 🎫 Sistema de Venta de Pasajes con Facturación Electrónica

Bienvenido al repositorio del **Sistema de Venta de Pasajes** desarrollado con tecnologías web modernas y cumpliendo con los estándares de facturación electrónica en Perú mediante la API de **NubeFACT**.

---

## 📌 Características Principales

✅ Venta de pasajes con control de rutas, horarios y tarifas  
✅ Registro y gestión de clientes  
✅ Emisión de **boletas y facturas electrónicas** con NubeFACT  
✅ Reportes PDF con **mPDF**  
✅ Interfaz moderna con **AdminLTE 3.0**  
✅ Panel administrativo responsivo  
✅ Arquitectura limpia bajo patrón **MVC**  
✅ Base de datos relacional en **MySQL**

---

## 🛠 Tecnologías Utilizadas

| Tipo                    | Herramienta/Framework    |
|-------------------------|---------------------------|
| Lenguaje Backend        | PHP                       |
| Lenguaje Frontend       | HTML5, CSS3, JavaScript   |
| Framework CSS           | Bootstrap                 |
| Plantilla Admin         | AdminLTE 3.0              |
| Reportes PDF            | mPDF                      |
| Facturación Electrónica | NubeFACT API              |
| Arquitectura            | MVC                       |
| Servidor Web            | Apache                    |
| Base de Datos           | MySQL                     |

---

## 📂 Estructura del Proyecto (MVC)

```
/app
  /controllers
  /models
  /views
/public
  /css
  /js
  /img
/config
/vendor (mPDF)
/facturacion (integración con NubeFACT)
index.php
.htaccess
```

---

## 🔧 Requisitos del Sistema

- PHP 8.1 o superior
- Apache 2.4+
- MySQL 10.4.32+
- Composer (para instalar mPDF)
- Conexión a internet para API de NubeFACT
- Navegador moderno (Chrome, Firefox, Edge)

---

## 🚀 Instalación

1. Clona este repositorio:
   ```bash
   git clone https://github.com/tu_usuario/sistema-venta-pasajes.git
   ```

2. Crea una base de datos en MySQL:
   - Nombre sugerido: `pasajes_db`
   - Importa el archivo `pasajes_db.sql` desde phpMyAdmin

3. Configura la conexión a la base de datos:
   - Edita el archivo `/config/database.php` con tus credenciales.

4. Instala dependencias (mPDF):
   ```bash
   composer require mpdf/mpdf
   ```

5. Configura NubeFACT:
   - Edita `/facturacion/nubefact_config.php` y coloca tu **token** y **URL de envío** proporcionados por NubeFACT.

6. Ejecuta el proyecto en tu navegador:
   ```
   http://localhost/sistema-venta-pasajes/public/
   ```

---

## 🧾 Integración con NubeFACT

El sistema está integrado con la **API RESTful de NubeFACT**, permitiendo emitir comprobantes electrónicos válidos ante la SUNAT.

- Boletas y facturas se generan y envían automáticamente tras una venta.
- El sistema recibe y guarda los archivos **PDF**, **XML**, y el **hash CDR**.
- Compatible con boletas electrónicas, facturas electrónicas y notas de crédito.

---

## 📊 Reportes PDF con mPDF

Se generan documentos y reportes en PDF usando la biblioteca **mPDF**:

- Comprobantes detallados
- Reporte de ventas por fecha
- Historial por cliente, ruta o usuario
- Copias de boletas/facturas emitidas

---

## 🖥 Panel Administrativo (AdminLTE 3.0)

Diseñado con **AdminLTE 3.0**, incluye:

- Dashboard con resumen de ventas
- Gestión de rutas, destinos y horarios
- Registro y edición de usuarios
- Vista de ventas y estado de comprobantes
- Estilo responsive y moderno

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

- [AdminLTE](https://adminlte.io)
- [mPDF](https://mpdf.github.io)
- [NubeFACT](https://nubefact.com)
- Comunidad PHP & Open Source
