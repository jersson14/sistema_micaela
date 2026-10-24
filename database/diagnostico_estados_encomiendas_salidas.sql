-- =====================================================================
-- Diagnóstico de estados de encomiendas y salidas diarias (SOLO LECTURA)
--
-- Lista los datos que quedaron inconsistentes con la lógica anterior a la
-- migración 2026-10-24_estados_encomiendas_salidas.sql. No modifica nada:
-- sirve para decidir qué corregir a mano antes o después de migrar.
-- =====================================================================

-- 1. Salidas EN TRANSITO con más de 24 h (nadie las completó)
SELECT '1. Salidas abiertas > 24 h' AS revision,
       s.id_salidas_diarias, s.salida_nro, s.fecha_hora,
       DATEDIFF(NOW(), s.fecha_hora) AS dias,
       (SELECT COUNT(*) FROM salida_encomienda se WHERE se.id_salida = s.id_salidas_diarias AND se.estado = '1') AS encomiendas
FROM salidas_diarias s
WHERE s.estado = 'EN TRANSITO' AND s.fecha_hora < NOW() - INTERVAL 24 HOUR
ORDER BY s.fecha_hora;

-- 2. Encomiendas entregadas sin cobrar
SELECT '2. Entregadas POR PAGAR' AS revision,
       e.id_encomienda, e.boleta_nro, e.por_pagar, e.updated_at
FROM encomiendas e
WHERE e.estado_encomienda = 'ENTREGADO' AND e.estado_pago = 'POR PAGAR';

-- 3. Encomiendas EN TRANSITO que no están en ninguna salida en tránsito
--    (cambiadas a mano o de salidas ya cerradas/eliminadas)
SELECT '3. En tránsito sin salida abierta' AS revision,
       e.id_encomienda, e.boleta_nro, e.fecha_hora,
       (SELECT GROUP_CONCAT(CONCAT(s.salida_nro, ' ', s.estado, IF(se.estado = '0', ' (quitada)', '')))
          FROM salida_encomienda se
          INNER JOIN salidas_diarias s ON s.id_salidas_diarias = se.id_salida
         WHERE se.id_encomienda = e.id_encomienda) AS salidas
FROM encomiendas e
WHERE e.estado_encomienda = 'EN TRANSITO'
  AND NOT EXISTS (SELECT 1
                    FROM salida_encomienda se
                    INNER JOIN salidas_diarias s ON s.id_salidas_diarias = se.id_salida
                   WHERE se.id_encomienda = e.id_encomienda AND se.estado = '1' AND s.estado = 'EN TRANSITO');

-- 4. Encomiendas activas en más de una salida en tránsito
SELECT '4. En varias salidas abiertas' AS revision,
       se.id_encomienda, GROUP_CONCAT(s.salida_nro) AS salidas
FROM salida_encomienda se
INNER JOIN salidas_diarias s ON s.id_salidas_diarias = se.id_salida
WHERE se.estado = '1' AND s.estado = 'EN TRANSITO'
GROUP BY se.id_encomienda
HAVING COUNT(*) > 1;

-- 5. Encomiendas cuyos ingresos válidos no cuadran con lo cobrado
--    (vuelto registrado como ingreso, o ajuste de precio que duplicó ingresos)
SELECT '5. Ingresos que no cuadran' AS revision,
       e.id_encomienda, e.boleta_nro, e.estado_encomienda,
       IFNULL(e.pago, 0) + IFNULL(e.a_domicilio, 0) AS cobrado,
       SUM(i.monto) AS ingresos_validos,
       SUM(i.monto) - (IFNULL(e.pago, 0) + IFNULL(e.a_domicilio, 0)) AS diferencia
FROM encomiendas e
INNER JOIN ingresos i ON i.id_encomiendas = e.id_encomienda AND i.estado = 'VALIDO'
WHERE e.estado_encomienda <> 'ANULADO'
GROUP BY e.id_encomienda
HAVING ABS(diferencia) > 0.009
ORDER BY diferencia DESC;

-- 6. Anuladas que todavía tienen ingresos válidos
SELECT '6. Anuladas con ingreso válido' AS revision,
       e.id_encomienda, e.boleta_nro, SUM(i.monto) AS monto
FROM encomiendas e
INNER JOIN ingresos i ON i.id_encomiendas = e.id_encomienda AND i.estado = 'VALIDO'
WHERE e.estado_encomienda = 'ANULADO'
GROUP BY e.id_encomienda;

-- 7. Salidas eliminadas cuyo ingreso sigue válido
SELECT '7. Salidas eliminadas con ingreso válido' AS revision,
       s.id_salidas_diarias, s.salida_nro, i.monto
FROM salidas_diarias s
INNER JOIN ingresos i ON i.id_salidas_diarias = s.id_salidas_diarias AND i.estado = 'VALIDO'
WHERE s.estado = 'ELIMINADO';

-- 8. Encomiendas en estado INCOMPLETO (lógica anterior: quedaban trabadas).
--    Con la nueva lógica se pueden pasar a PENDIENTE, OBSERVADO o ANULADO.
SELECT '8. Encomiendas INCOMPLETO' AS revision,
       e.id_encomienda, e.boleta_nro, LEFT(e.observacion, 80) AS observacion
FROM encomiendas e
WHERE e.estado_encomienda = 'INCOMPLETO';

-- 9. Salidas cuyo total de encomiendas no coincide con las que lleva
SELECT '9. Total de encomiendas desfasado' AS revision,
       s.id_salidas_diarias, s.salida_nro, s.estado, s.total_encomiendas,
       (SELECT COUNT(*) FROM salida_encomienda se WHERE se.id_salida = s.id_salidas_diarias AND se.estado = '1') AS reales
FROM salidas_diarias s
WHERE s.estado <> 'ELIMINADO'
HAVING s.total_encomiendas <> reales;
