# 🔔 Modal de Recordatorio de Pagos

## 📋 DESCRIPCIÓN

Modal elegante que aparece automáticamente para recordar al administrador sobre los pagos anuales de servicios del sistema.

---

## 🎯 CARACTERÍSTICAS

### ✅ Aparece Solo en Fechas Específicas:
- **3 de Octubre** de cada año
- **17 de Octubre** de cada año

### ✅ Solo para Administrador:
- Verifica que `$_SESSION['S_ROL'] == "1"`
- Otros usuarios NO ven el modal

### ✅ Se Muestra Una Vez por Día:
- Usa `localStorage` para recordar
- No molesta múltiples veces el mismo día

### ✅ Diseño Elegante:
- Gradientes modernos
- Iconos Font Awesome
- Animaciones suaves
- Responsive (se adapta a móviles)

---

## 💰 SERVICIOS INCLUIDOS

| Servicio | Costo Anual | Descripción |
|----------|-------------|-------------|
| **Hosting** | S/ 380 | Servidor Web |
| **API SUNAT (Nubefact)** | S/ 740 | Facturación Electrónica |
| **Certificado PEM** | S/ 280 | Seguridad SSL |
| **API RENIEC** | S/ 100 | Consulta DNI |
| **TOTAL** | **S/ 1,500** | |

---

## 👨‍💻 INFORMACIÓN DE CONTACTO

**Proveedor:**
- 👤 Ing. Jersson Jorge Corilla Miranda
- 📱 974 031 318
- 📧 jersson1407miranda@gmail.com

**Botones de Contacto:**
- ✅ WhatsApp directo (mensaje pre-escrito)
- ✅ Email (mailto)
- ✅ Teléfono (tel)

---

## 🎨 DISEÑO

### Colores por Servicio:
- **Hosting**: Morado (#667eea → #764ba2)
- **API SUNAT**: Rosa (#f093fb → #f5576c)
- **Certificado PEM**: Azul (#4facfe → #00f2fe)
- **API RENIEC**: Verde (#43e97b → #38f9d7)

### Animaciones:
- ✅ Icono de campana con shake cada 3 segundos
- ✅ Tarjetas con efecto hover (elevan al pasar mouse)
- ✅ Modal aparece con fade-in suave

---

## 🔧 FUNCIONAMIENTO TÉCNICO

### 1. Verificación de Fecha:
```javascript
const hoy = new Date();
const mes = hoy.getMonth() + 1;
const dia = hoy.getDate();

const esFechaRecordatorio = (mes === 10 && (dia === 3 || dia === 17));
```

### 2. Control de Visualización:
```javascript
const fechaHoy = hoy.toISOString().split('T')[0];
const ultimaVezMostrado = localStorage.getItem('recordatorio_pagos_mostrado');

if (esFechaRecordatorio && ultimaVezMostrado !== fechaHoy) {
  // Mostrar modal
  localStorage.setItem('recordatorio_pagos_mostrado', fechaHoy);
}
```

### 3. Aparición Automática:
```javascript
setTimeout(function() {
  $('#modalRecordatorioPagos').modal('show');
}, 2000); // Aparece 2 segundos después de cargar
```

---

## 📱 RESPONSIVE

El modal se adapta perfectamente a:
- ✅ Desktop (pantallas grandes)
- ✅ Tablet (pantallas medianas)
- ✅ Móvil (pantallas pequeñas)

---

## 🧪 CÓMO PROBAR

### Opción 1: Cambiar Fecha del Sistema
```bash
# En Windows (como administrador)
date 10-03-2024
```

### Opción 2: Modificar Temporalmente el Código
En `view/index.php`, cambiar:
```javascript
// TEMPORAL PARA PRUEBAS
const esFechaRecordatorio = true; // Siempre mostrar
```

### Opción 3: Limpiar localStorage
En consola del navegador (F12):
```javascript
localStorage.removeItem('recordatorio_pagos_mostrado');
location.reload();
```

---

## 🎯 EJEMPLO DE USO

### Escenario 1: 3 de Octubre
```
Usuario: Administrador
Fecha: 03-10-2024
Hora: 09:00 AM

1. Administrador hace login
2. Espera 2 segundos
3. Aparece modal de recordatorio
4. Ve los servicios a pagar
5. Puede contactar por WhatsApp
6. Cierra el modal
7. No vuelve a aparecer ese día
```

### Escenario 2: 17 de Octubre
```
Usuario: Administrador
Fecha: 17-10-2024
Hora: 02:00 PM

1. Administrador ya está logueado
2. Recarga la página
3. Aparece modal de recordatorio
4. Contacta al proveedor
5. Cierra el modal
```

### Escenario 3: Otro Usuario
```
Usuario: Asistente (ROL = 2)
Fecha: 03-10-2024

1. Usuario hace login
2. NO aparece el modal
3. Solo el administrador lo ve
```

---

## 🔄 MODIFICAR FECHAS

Para cambiar las fechas de recordatorio, editar en `view/index.php`:

```javascript
// Cambiar estas líneas:
const esFechaRecordatorio = (mes === 10 && (dia === 3 || dia === 17));

// Por ejemplo, para 1 y 15 de Noviembre:
const esFechaRecordatorio = (mes === 11 && (dia === 1 || dia === 15));
```

---

## 💡 MODIFICAR SERVICIOS

Para agregar o modificar servicios, editar en `view/index.php`:

```html
<!-- Agregar nueva tarjeta -->
<div class="col-md-6 mb-3">
  <div class="card" style="border-radius: 15px; border: 2px solid #e3e8ef;">
    <div class="card-body" style="padding: 20px;">
      <div class="d-flex align-items-center mb-2">
        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #FA8BFF 0%, #2BD2FF 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
          <i class="fas fa-cloud text-white" style="font-size: 24px;"></i>
        </div>
        <div>
          <h6 style="margin: 0; font-weight: 600; color: #333;">Nuevo Servicio</h6>
          <p style="margin: 0; color: #999; font-size: 13px;">Descripción</p>
        </div>
      </div>
      <div class="text-right">
        <span style="font-size: 24px; font-weight: 700; color: #2BD2FF;">S/ 500</span>
        <p style="margin: 0; color: #999; font-size: 12px;">Anual</p>
      </div>
    </div>
  </div>
</div>
```

Y actualizar el total:
```html
<h3 class="text-white" style="margin: 0; font-weight: 700; font-size: 32px;">
  S/ 2,000.00 <!-- Nuevo total -->
</h3>
```

---

## 📞 MODIFICAR CONTACTO

Para cambiar la información de contacto, editar en `view/index.php`:

```html
<!-- Nombre -->
<strong>Nuevo Nombre</strong>

<!-- Teléfono -->
<a href="tel:999999999">999 999 999</a>

<!-- Email -->
<a href="mailto:nuevo@email.com">nuevo@email.com</a>

<!-- WhatsApp -->
<a href="https://wa.me/51999999999?text=Mensaje%20personalizado">
```

---

## 🎨 PERSONALIZAR COLORES

### Cambiar Color del Header:
```html
<div class="modal-header" style="background: linear-gradient(135deg, #TU_COLOR_1 0%, #TU_COLOR_2 100%);">
```

### Cambiar Color de Tarjeta:
```html
<div style="background: linear-gradient(135deg, #TU_COLOR_1 0%, #TU_COLOR_2 100%);">
```

### Gradientes Sugeridos:
- Azul: `#667eea → #764ba2`
- Rosa: `#f093fb → #f5576c`
- Verde: `#43e97b → #38f9d7`
- Naranja: `#fa709a → #fee140`
- Morado: `#a8edea → #fed6e3`

---

## 🐛 TROUBLESHOOTING

### El modal no aparece:
1. ✅ Verificar que eres administrador (ROL = 1)
2. ✅ Verificar que es 3 o 17 de octubre
3. ✅ Limpiar localStorage
4. ✅ Verificar consola del navegador (F12)

### El modal aparece múltiples veces:
1. ✅ Verificar que localStorage funciona
2. ✅ Limpiar caché del navegador
3. ✅ Verificar que no hay múltiples includes del archivo

### El WhatsApp no abre:
1. ✅ Verificar que el número es correcto
2. ✅ Verificar que tiene WhatsApp instalado
3. ✅ Probar en móvil

---

## ✅ CHECKLIST DE IMPLEMENTACIÓN

- [x] ✅ Modal creado en view/index.php
- [x] ✅ Solo visible para administrador
- [x] ✅ Aparece en fechas específicas (3 y 17 de octubre)
- [x] ✅ Se muestra una vez por día
- [x] ✅ Diseño responsive
- [x] ✅ Animaciones incluidas
- [x] ✅ Botón de WhatsApp funcional
- [x] ✅ Información de contacto completa
- [ ] 🔴 Probar en fecha real (3 o 17 de octubre)

---

## 📅 CALENDARIO DE RECORDATORIOS

| Fecha | Evento |
|-------|--------|
| **3 de Octubre** | 🔔 Primer recordatorio |
| **17 de Octubre** | 🔔 Segundo recordatorio |

---

**¡El modal está listo y funcionando!** 🎉

Solo aparecerá automáticamente el 3 y 17 de octubre de cada año, exclusivamente para el administrador.
