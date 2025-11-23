# 🔧 Solución: Error de Collation en MySQL

## 📋 Problema

```
SQLSTATE[HY000]: General error: 1267 
Illegal mix of collations (utf8mb4_unicode_ci,IMPLICIT) and (utf8mb4_0900_ai_ci,IMPLICIT)
```

La base de datos en el VPS tiene diferentes collations entre tablas, causando errores en las consultas.

---

## ✅ Solución Rápida (Opción 1: Script Automático)

### En el VPS:

```bash
# 1. Subir archivos
cd /var/www/html/sistema_micaela
git pull

# 2. Ejecutar script
bash fix_collation.sh
```

El script te pedirá:
- Confirmación
- Contraseña de MySQL

Luego convertirá automáticamente todas las tablas.

---

## 🛠️ Solución Manual (Opción 2)

### Paso 1: Conectar a MySQL

```bash
mysql -u root -p
```

### Paso 2: Seleccionar base de datos

```sql
USE bd_micaela;
```

### Paso 3: Cambiar collation de la base de datos

```sql
ALTER DATABASE bd_micaela CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Paso 4: Ver todas las tablas

```sql
SHOW TABLES;
```

### Paso 5: Convertir cada tabla

```sql
-- Reemplaza 'nombre_tabla' con cada tabla de tu base de datos
ALTER TABLE usuario CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE sucursal CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE rutas CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE reservas CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE encomiendas CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE salidas_diarias CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE comprobantes CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE clientes CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE choferes CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE servicios CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE roles CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE tipo_pago CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE ingresos CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE gastos CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE indicadores CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE configuracion CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Agrega todas las demás tablas que tengas
```

### Paso 6: Verificar

```sql
-- Ver collation de todas las tablas
SELECT 
    TABLE_NAME,
    TABLE_COLLATION
FROM 
    information_schema.TABLES
WHERE 
    TABLE_SCHEMA = 'bd_micaela';
```

Todas deben mostrar: `utf8mb4_unicode_ci`

---

## 🚀 Solución Ultra Rápida (Opción 3: Comando único)

```bash
# En el VPS
mysql -u root -p bd_micaela -e "
ALTER DATABASE bd_micaela CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
SELECT CONCAT('ALTER TABLE \`', table_name, '\` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;')
FROM information_schema.TABLES
WHERE table_schema = 'bd_micaela';" | grep ALTER | mysql -u root -p bd_micaela
```

---

## 🔍 Verificación

Después de ejecutar la solución:

### 1. Verificar collations

```bash
mysql -u root -p -e "
SELECT TABLE_NAME, TABLE_COLLATION 
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = 'bd_micaela';"
```

### 2. Reiniciar Apache

```bash
sudo systemctl restart apache2
```

### 3. Probar el sistema

```
http://72.61.40.91/sistema_micaela/
```

---

## 📊 Comandos de Diagnóstico

### Ver collation actual de la base de datos

```sql
SELECT DEFAULT_CHARACTER_SET_NAME, DEFAULT_COLLATION_NAME
FROM information_schema.SCHEMATA
WHERE SCHEMA_NAME = 'bd_micaela';
```

### Ver collation de todas las tablas

```sql
SELECT TABLE_NAME, TABLE_COLLATION
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = 'bd_micaela'
ORDER BY TABLE_NAME;
```

### Ver collation de todas las columnas

```sql
SELECT TABLE_NAME, COLUMN_NAME, COLLATION_NAME
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = 'bd_micaela'
AND COLLATION_NAME IS NOT NULL
ORDER BY TABLE_NAME, COLUMN_NAME;
```

---

## ⚠️ Notas Importantes

1. **Backup:** Haz backup de la base de datos antes de hacer cambios
   ```bash
   mysqldump -u root -p bd_micaela > backup_bd_micaela.sql
   ```

2. **Tiempo:** La conversión puede tardar dependiendo del tamaño de las tablas

3. **Conexiones:** Asegúrate de que no haya usuarios conectados durante la conversión

4. **Permisos:** Necesitas permisos de ALTER en la base de datos

---

## 🐛 Solución de Problemas

### Error: "Access denied"

```bash
# Verificar permisos del usuario
mysql -u root -p -e "SHOW GRANTS FOR 'root'@'localhost';"
```

### Error: "Table is locked"

```bash
# Desbloquear tablas
mysql -u root -p bd_micaela -e "UNLOCK TABLES;"
```

### Error: "Cannot convert"

Algunas tablas pueden tener restricciones. Intenta:

```sql
-- Deshabilitar foreign key checks temporalmente
SET FOREIGN_KEY_CHECKS=0;

-- Convertir tabla
ALTER TABLE nombre_tabla CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Habilitar foreign key checks
SET FOREIGN_KEY_CHECKS=1;
```

---

## 📚 Explicación Técnica

### ¿Por qué ocurre este error?

MySQL 8.0 usa `utf8mb4_0900_ai_ci` como collation por defecto, mientras que versiones anteriores usaban `utf8mb4_unicode_ci`. 

Cuando migras una base de datos de una versión a otra, o cuando creas tablas en diferentes momentos, pueden quedar con collations diferentes.

### ¿Qué hace la solución?

Unifica todas las tablas y columnas para usar la misma collation (`utf8mb4_unicode_ci`), permitiendo que MySQL compare strings sin errores.

---

## ✅ Checklist Final

- [ ] Backup de base de datos creado
- [ ] Script ejecutado sin errores
- [ ] Todas las tablas con `utf8mb4_unicode_ci`
- [ ] Apache reiniciado
- [ ] Sistema funcionando correctamente
- [ ] No hay errores 500 en los logs

---

**Versión:** 1.0  
**Sistema:** Tours Micaela  
**Fecha:** 2024
