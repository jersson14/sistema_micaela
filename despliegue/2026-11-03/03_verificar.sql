-- Verificación después de las migraciones (solo lectura).
-- Debe mostrar: SP_ELIMINAR_SALIDA_DIARIA = 2 parámetros, SP_MODIFICAR_SALIDA_INCOMPLETO_ESTATUS = 4,
-- los procedimientos de encomiendas presentes y 0 rutinas con otra intercalación.

SELECT r.ROUTINE_NAME AS procedimiento,
       (SELECT COUNT(*) FROM information_schema.PARAMETERS p
         WHERE p.SPECIFIC_SCHEMA = r.ROUTINE_SCHEMA AND p.SPECIFIC_NAME = r.ROUTINE_NAME
           AND p.PARAMETER_MODE IS NOT NULL) AS parametros,
       r.LAST_ALTERED AS actualizado
FROM information_schema.ROUTINES r
WHERE r.ROUTINE_SCHEMA = DATABASE()
  AND r.ROUTINE_NAME IN ('SP_ELIMINAR_SALIDA_DIARIA', 'SP_MODIFICAR_SALIDA_INCOMPLETO_ESTATUS',
                         'SP_REGISTRAR_ENCOMIENDA', 'SP_MODIFICAR_ENCOMIENDA')
ORDER BY r.ROUTINE_NAME;

SELECT COUNT(*) AS rutinas_con_otra_intercalacion
FROM information_schema.ROUTINES
WHERE ROUTINE_SCHEMA = DATABASE() AND DATABASE_COLLATION <> 'utf8mb4_unicode_ci';

-- Migración 4: debe mostrar 1
SELECT COUNT(*) AS notificar_licencia
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'choferes' AND COLUMN_NAME = 'notificar_licencia';
