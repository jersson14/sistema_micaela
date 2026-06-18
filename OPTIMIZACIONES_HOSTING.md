# 🚀 OPTIMIZACIONES PARA HOSTING - Salidas Diarias

## ❌ PROBLEMAS IDENTIFICADOS (Por qué fallaba en hosting)

### 1. **Timeouts Arbitrarios**
```javascript
// ❌ ANTES: Timeouts fijos que fallan con latencia
setTimeout(function() {
  listar_pasajerosEditar(data.id_salidas_diarias);
  listar_encomiendasEditar(data.id_salidas_diarias);
}, 300); // ← Puede ser muy poco en hosting lento
```

### 2. **Sin Control de Errores de Red**
```javascript
// ❌ ANTES: No manejaba timeouts ni errores de conexión
ajax: {
  url: "...",
  // Sin timeout
  // Sin manejo de errores de red
}
```

### 3. **Event Listeners Duplicados**
```javascript
// ❌ ANTES: Se acumulaban eventos en cada apertura
$(".encomienda-checkbox").on("change", function() {
  // Este evento se duplicaba cada vez
});
```

### 4. **Carga Secuencial (Lenta)**
```javascript
// ❌ ANTES: Esperaba una tabla, luego la otra
listar_pasajerosEditar(id);
listar_encomiendasEditar(id); // Esperaba a la primera
```

### 5. **Sin Feedback de Carga**
```javascript
// ❌ ANTES: Usuario no sabía si estaba cargando
processing: false, // ← No mostraba "Cargando..."
```

---

## ✅ SOLUCIONES IMPLEMENTADAS

### 1. **Evento Modal en Lugar de setTimeout**
```javascript
// ✅ AHORA: Usa evento nativo del modal
$("#modal_editar").one("shown.bs.modal", function () {
  // Se ejecuta cuando el modal está completamente visible
  // Más confiable que setTimeout
});
```

**Ventaja:** Funciona independiente de la velocidad del servidor.

---

### 2. **Promesas para Control de Flujo**
```javascript
// ✅ AHORA: Funciones retornan Promises
function listar_pasajerosEditar(id) {
  return new Promise(function(resolve, reject) {
    // ... código AJAX
    dataSrc: function (json) {
      resolve(json.data); // ← Notifica cuando termina
      return json.data;
    },
    error: function (xhr, error, thrown) {
      reject(error); // ← Notifica si falla
    }
  });
}
```

**Ventaja:** Puedes esperar a que termine o manejar errores.

---

### 3. **Carga en Paralelo**
```javascript
// ✅ AHORA: Carga ambas tablas al mismo tiempo
Promise.all([
  listar_pasajerosEditar(data.id_salidas_diarias),
  listar_encomiendasEditar(data.id_salidas_diarias)
]).catch(function(error) {
  console.error("Error cargando datos:", error);
  Swal.fire("Error", "No se pudieron cargar todos los datos", "error");
});
```

**Ventaja:** 
- Más rápido (paralelo vs secuencial)
- Maneja errores de ambas cargas

---

### 4. **Timeouts de Red**
```javascript
// ✅ AHORA: Timeout de 10 segundos
ajax: {
  url: "...",
  timeout: 10000, // ← 10 segundos máximo
  error: function (xhr, error, thrown) {
    // Maneja timeout y otros errores
  }
}
```

**Ventaja:** No se queda colgado esperando indefinidamente.

---

### 5. **Limpieza de Event Listeners**
```javascript
// ✅ AHORA: Limpia eventos antes de agregar nuevos
function configurarEventosEncomiendas() {
  // Remover eventos previos
  $(".encomienda-checkbox").off("change");
  $("#check_all_encomiendas_editar").off("change");
  
  // Agregar eventos frescos
  $(document).on("change", ".encomienda-checkbox", function() {
    actualizarTotalSeleccionados();
  });
}
```

**Ventaja:** No se acumulan eventos duplicados.

---

### 6. **Optimización de DataTables**
```javascript
// ✅ AHORA: Configuración optimizada
{
  processing: true,        // ← Muestra "Cargando..."
  serverSide: false,       // ← Datos pequeños, procesar en cliente
  deferRender: true,       // ← Renderiza solo lo visible
  destroy: true,           // ← Limpia tabla anterior
}
```

**Ventaja:** Más rápido y con feedback visual.

---

### 7. **Limpieza Completa de Tablas**
```javascript
// ✅ AHORA: Limpia completamente antes de recrear
if ($.fn.DataTable.isDataTable("#tabla_pasajeros_editar")) {
  $("#tabla_pasajeros_editar").DataTable().clear().destroy();
  $("#tabla_pasajeros_editar").empty(); // ← Limpia HTML también
}
```

**Ventaja:** Evita conflictos de tablas previas.

---

### 8. **Feedback de Carga en Eliminación**
```javascript
// ✅ AHORA: Muestra loading al eliminar
Swal.fire({
  title: "Eliminando...",
  allowOutsideClick: false,
  showConfirmButton: false,
  willOpen: () => {
    Swal.showLoading();
  },
});
```

**Ventaja:** Usuario sabe que está procesando.

---

### 9. **Optimización de Selectores jQuery**
```javascript
// ❌ ANTES: Buscaba en todo el DOM cada vez
$(".encomienda-checkbox:checked").each(function() {
  seleccionadas.push($(this).val());
});

// ✅ AHORA: Usa map (más eficiente)
let seleccionadas = $(".encomienda-checkbox:checked")
  .map(function() {
    return $(this).val();
  }).get();
```

**Ventaja:** Más rápido, menos código.

---

### 10. **Manejo Robusto de Errores**
```javascript
// ✅ AHORA: Maneja todos los casos
success: function (response) {
  let resultado = parseInt(response); // ← Convierte a número
  
  if (resultado > 0) {
    // Éxito
  } else {
    // Fallo controlado
  }
},
error: function (xhr, status, error) {
  console.error("Error:", {xhr, status, error}); // ← Log detallado
  Swal.fire({
    icon: "error",
    title: "Error",
    text: "Verifique su conexión.", // ← Mensaje útil
  });
}
```

---

## 📊 COMPARACIÓN: ANTES vs AHORA

| Aspecto | Antes | Ahora |
|---------|-------|-------|
| **Carga de tablas** | Secuencial (lenta) | Paralela (rápida) |
| **Timeouts** | Fijos (300ms) | Evento modal (confiable) |
| **Timeout de red** | Sin límite | 10 segundos |
| **Event listeners** | Se duplicaban | Se limpian |
| **Feedback visual** | No | Sí (loading) |
| **Manejo de errores** | Básico | Completo |
| **Optimización** | No | Sí (deferRender) |
| **Limpieza de tablas** | Parcial | Completa |

---

## 🎯 RESULTADO ESPERADO

### En Local (Antes y Ahora):
- ✅ Funciona bien (red rápida)

### En Hosting (Antes):
- ❌ Tablas no cargaban
- ❌ Checkboxes no funcionaban
- ❌ Se quedaba colgado
- ❌ Eventos duplicados

### En Hosting (Ahora):
- ✅ Tablas cargan correctamente
- ✅ Checkboxes funcionan
- ✅ Timeout de 10 segundos
- ✅ Eventos limpios
- ✅ Feedback visual
- ✅ Manejo de errores

---

## 🔧 CONFIGURACIÓN ADICIONAL PARA HOSTING

### 1. Aumentar Timeout de PHP (si es necesario)

En `.htaccess`:
```apache
php_value max_execution_time 60
php_value max_input_time 60
```

### 2. Optimizar Base de Datos

Agregar índices en tablas:
```sql
-- Índices para mejorar velocidad de consultas
ALTER TABLE cliente_salida ADD INDEX idx_id_salida (id_salida);
ALTER TABLE encomienda_salida ADD INDEX idx_id_salida (id_salida);
```

### 3. Habilitar Compresión GZIP

En `.htaccess`:
```apache
<IfModule mod_deflate.c>
  AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript application/json
</IfModule>
```

### 4. Cache de Consultas

En controladores PHP:
```php
// Agregar headers de cache para datos que no cambian frecuentemente
header("Cache-Control: max-age=300"); // 5 minutos
```

---

## 🐛 TROUBLESHOOTING

### Si sigue sin funcionar en hosting:

#### 1. Verificar Logs de Errores
```javascript
// Agregar en console_salidas_diarias.js
console.log("Iniciando carga de modal...");
console.log("ID de salida:", data.id_salidas_diarias);
```

#### 2. Verificar Respuesta del Servidor
```javascript
// En las funciones AJAX
success: function(response) {
  console.log("Respuesta del servidor:", response);
  console.log("Tipo de respuesta:", typeof response);
}
```

#### 3. Verificar Timeout
```javascript
// Si 10 segundos no es suficiente, aumentar:
timeout: 30000, // 30 segundos
```

#### 4. Verificar Conexión a BD
En los controladores PHP:
```php
error_log("Consultando pasajeros para salida: " . $id);
error_log("Resultado: " . json_encode($resultado));
```

---

## ✅ CHECKLIST DE VERIFICACIÓN

- [x] ✅ Promesas implementadas
- [x] ✅ Carga en paralelo
- [x] ✅ Timeouts de red (10 seg)
- [x] ✅ Event listeners limpios
- [x] ✅ Feedback visual (loading)
- [x] ✅ Manejo de errores completo
- [x] ✅ Optimización DataTables
- [x] ✅ Limpieza completa de tablas
- [ ] 🔴 Probar en hosting
- [ ] 🔴 Verificar logs de errores
- [ ] 🔴 Optimizar base de datos (índices)

---

## 📞 COMANDOS ÚTILES PARA DEBUG

### En Consola del Navegador (F12):
```javascript
// Ver si las tablas están inicializadas
console.log("Pasajeros:", typeof tbl_detalle_pasajerosEditar);
console.log("Encomiendas:", typeof tbl_detalle_encomiendasEditar);

// Ver checkboxes
console.log("Total checkboxes:", $(".encomienda-checkbox").length);
console.log("Checkboxes marcados:", $(".encomienda-checkbox:checked").length);

// Forzar recarga de tablas
if (tbl_detalle_pasajerosEditar) {
  tbl_detalle_pasajerosEditar.ajax.reload();
}
```

---

**Las optimizaciones están aplicadas y listas para probar en hosting.** 🚀
