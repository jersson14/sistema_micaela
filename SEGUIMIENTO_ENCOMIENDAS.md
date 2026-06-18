# Sistema de Seguimiento de Encomiendas

## Descripción
Sistema público de seguimiento de encomiendas para clientes de Tours Micaela. Permite rastrear el estado de las encomiendas sin necesidad de iniciar sesión.

## Archivos Creados

### 1. `seguimiento.php`
- Página principal de seguimiento
- Interfaz moderna y responsive
- Accesible públicamente sin autenticación

### 2. `controller/encomiendas/controlador_seguimiento.php`
- Controlador que procesa las búsquedas
- Recibe el número de boleta y retorna la información

### 3. `model/model_encomiendas.php` (modificado)
- Agregado método `Buscar_Encomienda_Por_Boleta()`
- Consulta información de la encomienda y su historial de estados

### 4. `js/seguimiento.js`
- Lógica de búsqueda con AJAX
- Renderizado dinámico de resultados
- Timeline animado del historial

## Características

✅ **Búsqueda por número de boleta**
- Búsqueda rápida y sencilla
- Validación de campos

✅ **Información completa**
- Datos del remitente y destinatario
- Origen y destino
- Conductor asignado
- Montos y estado de pago
- Descripción del paquete

✅ **Historial de estados**
- Timeline visual con todos los cambios de estado
- Fechas y observaciones
- Estado actual destacado

✅ **Estados soportados**
- PENDIENTE
- EN TRANSITO
- EN AGENCIA
- ENTREGADO
- OBSERVADO
- ANULADO
- INCOMPLETO

✅ **Diseño moderno**
- Interfaz responsive
- Animaciones suaves
- Compatible con móviles
- Colores corporativos de Tours Micaela

## Cómo usar

### Para clientes:
1. Acceder a: `http://tu-dominio.com/seguimiento.php`
2. Ingresar el número de boleta
3. Hacer clic en "Buscar"
4. Ver el estado actual y el historial completo

### URL de acceso:
```
http://localhost/seguimiento.php
```

## Requisitos técnicos

- PHP 7.0+
- MySQL/MariaDB
- jQuery 3.6.0
- SweetAlert2
- Font Awesome 6.4.0

## Tablas de base de datos utilizadas

### `encomienda`
- Información principal de la encomienda
- Relaciones con clientes, sucursales y conductores

### `historial_estados`
- Registro de todos los cambios de estado
- Observaciones y fechas de cada cambio

## Notas importantes

⚠️ **Seguridad**: El sistema solo muestra información de seguimiento, no permite modificaciones.

⚠️ **Privacidad**: Solo se muestra información necesaria para el seguimiento, sin datos sensibles.

⚠️ **Performance**: Las consultas están optimizadas con índices en `boleta_nro`.

## Próximas mejoras sugeridas

- [ ] Búsqueda por DNI del destinatario
- [ ] Notificaciones por email/SMS
- [ ] Código QR para seguimiento rápido
- [ ] Compartir link de seguimiento
- [ ] Historial de búsquedas recientes
- [ ] Mapa de ubicación en tiempo real
