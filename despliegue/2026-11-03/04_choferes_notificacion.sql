-- Migración 4: permite desactivar, por chofer, el aviso de vencimiento de licencia.
-- Agrega la columna choferes.notificar_licencia (1 = avisar, 0 = no avisar).
-- Se puede ejecutar varias veces: si la columna ya existe no hace nada.
-- (MySQL 8.0 no admite "ADD COLUMN IF NOT EXISTS", por eso se consulta information_schema.)

SET @existe := (SELECT COUNT(*) FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'choferes'
                  AND COLUMN_NAME = 'notificar_licencia');

SET @sql := IF(@existe = 0,
  'ALTER TABLE choferes ADD COLUMN notificar_licencia TINYINT(1) NOT NULL DEFAULT 1 AFTER fecha_vencimiento_licencia',
  'SELECT ''La columna notificar_licencia ya existe'' AS aviso');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
