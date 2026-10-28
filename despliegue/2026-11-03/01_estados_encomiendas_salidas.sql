-- =====================================================================
-- Estados de encomiendas y salidas diarias (24/10/2026)
--
-- No agrega estados ni columnas: solo reemplaza procedimientos para que
-- los cambios de estado sean consistentes. Las reglas de qué cambio está
-- permitido viven en model/model_estados_encomienda.php; aquí se repiten
-- las validaciones que dependen del estado guardado en la BD.
--
-- Cambia la firma de:
--   SP_MODIFICAR_SALIDA_INCOMPLETO_ESTATUS(ID, USU, OBSERVA, LLEGARON)
--   SP_ELIMINAR_SALIDA_DIARIA(ID, USU)
-- (los modelos PHP de esta misma versión ya los llaman así).
--
-- Las tablas usan utf8mb4_unicode_ci. Si la BD tiene otra intercalación
-- por defecto (p. ej. utf8mb4_general_ci), los parámetros y variables de
-- los procedimientos la heredan y comparar con columnas da
-- "Illegal mix of collations"; por eso se fija aquí y en las variables
-- que se comparan con columnas.
-- =====================================================================

SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

DELIMITER $$

-- ---------------------------------------------------------------------
-- Cambio manual de estado de una encomienda.
-- Al anular ya no se ponen los montos en 0: queda el registro de lo que
-- se cobró y los ingresos pasan a ANULADO.
-- ---------------------------------------------------------------------
DROP PROCEDURE IF EXISTS SP_MODIFICAR_ESTADO_ENCOMIENDA$$
CREATE PROCEDURE SP_MODIFICAR_ESTADO_ENCOMIENDA(
    IN ID INT, IN ESTA VARCHAR(20), IN OBSER TEXT, IN ANULA VARCHAR(255), IN USU INT)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    IF ESTA = 'ANULADO' THEN
        UPDATE encomiendas
        SET estado_encomienda = 'ANULADO',
            estado_pago = 'ANULADO',
            observacion = OBSER,
            motivo_anulacion = ANULA,
            fecha_anulacion = NOW(),
            updated_at = NOW()
        WHERE id_encomienda = ID;

        INSERT INTO historial_estados(id_encomienda, estado, observacion, motivo_anula, fecha_anula, created_at, idusu)
        VALUES(ID, 'ANULADO', LEFT(OBSER, 255), ANULA, NOW(), NOW(), USU);

        UPDATE ingresos
        SET estado = 'ANULADO',
            id_usu = USU,
            motivo_anulacion = ANULA,
            fecha_anulacion = NOW()
        WHERE id_encomiendas = ID AND estado = 'VALIDO';
    ELSE
        UPDATE encomiendas
        SET estado_encomienda = ESTA,
            observacion = OBSER,
            motivo_anulacion = NULL,
            fecha_anulacion = NULL,
            updated_at = NOW()
        WHERE id_encomienda = ID;

        INSERT INTO historial_estados(id_encomienda, estado, observacion, motivo_anula, fecha_anula, created_at, idusu)
        VALUES(ID, ESTA, LEFT(OBSER, 255), NULL, NULL, NOW(), USU);
    END IF;

    COMMIT;
END$$

-- ---------------------------------------------------------------------
-- Cobro del saldo POR PAGAR.
-- El saldo se toma de la BD (no del navegador) y el ingreso es el saldo,
-- no el monto recibido: antes el vuelto quedaba registrado como ingreso.
-- SALDOPEN se mantiene en la firma por compatibilidad y no se usa.
-- ---------------------------------------------------------------------
DROP PROCEDURE IF EXISTS SP_REALIZAR_PAGO_ENCOMIENDA$$
CREATE PROCEDURE SP_REALIZAR_PAGO_ENCOMIENDA(
    IN ID INT, IN ESTA VARCHAR(20), IN SALDOPEN DECIMAL(8,2), IN MONTORECI DECIMAL(8,2), IN USU INT)
BEGIN
    DECLARE saldo DECIMAL(8,2) DEFAULT 0;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    SELECT IFNULL(por_pagar, 0) INTO saldo
    FROM encomiendas
    WHERE id_encomienda = ID
    FOR UPDATE;

    IF saldo <= 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La encomienda no tiene saldo pendiente';
    END IF;

    IF MONTORECI < saldo THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El monto recibido es menor al saldo pendiente';
    END IF;

    UPDATE encomiendas
    SET estado_encomienda = ESTA,
        estado_pago = 'PAGADO',
        pago = IFNULL(pago, 0) + saldo,
        por_pagar = 0,
        observacion = CONCAT_WS(' ', 'SE REALIZO EL PAGO DE LA ENCOMIENDA CON UN TOTAL DE:', saldo, 'SOLES'),
        updated_at = NOW()
    WHERE id_encomienda = ID;

    INSERT INTO historial_estados(id_encomienda, estado, observacion, motivo_anula, fecha_anula, created_at, idusu, precio_anterior, precio_nuevi)
    VALUES(ID, ESTA,
           CONCAT('SE COBRO S/ ', saldo, ' (RECIBIDO S/ ', MONTORECI, ', VUELTO S/ ', MONTORECI - saldo, ')'),
           NULL, NULL, NOW(), USU, saldo, 0.00);

    INSERT INTO ingresos(id_encomiendas, id_indicador, monto, igv, monto_total, observacion, estado, id_usu, created_at)
    VALUES(ID, 7, saldo, 0, saldo, 'ENVIO DE ENCOMIENDA - PAGO PRINCIPAL', 'VALIDO', USU, NOW());

    COMMIT;
END$$

-- ---------------------------------------------------------------------
-- Ajuste de precio (encomiendas OBSERVADAS).
-- El monto anterior se toma de la BD y solo se ajusta el ingreso del
-- concepto que cambia; antes se sobrescribían todos los ingresos de la
-- encomienda con el monto nuevo y se duplicaba (pago + domicilio).
-- PAGO_ANTI se mantiene en la firma por compatibilidad y no se usa.
-- ---------------------------------------------------------------------
DROP PROCEDURE IF EXISTS SP_MODIFICAR_PAGO_ENCOMIENDA$$
CREATE PROCEDURE SP_MODIFICAR_PAGO_ENCOMIENDA(
    IN ID INT, IN ESTA VARCHAR(20), IN PAGO_ANTI DECIMAL(8,2), IN PAGO_NU DECIMAL(8,2), IN USU INT)
BEGIN
    DECLARE monto_pago DECIMAL(8,2) DEFAULT 0;
    DECLARE monto_por_pagar DECIMAL(8,2) DEFAULT 0;
    DECLARE monto_a_domicilio DECIMAL(8,2) DEFAULT 0;
    DECLARE anterior DECIMAL(8,2) DEFAULT 0;
    DECLARE concepto VARCHAR(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL;
    DECLARE id_ing INT DEFAULT NULL;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    SELECT IFNULL(pago, 0), IFNULL(por_pagar, 0), IFNULL(a_domicilio, 0)
    INTO monto_pago, monto_por_pagar, monto_a_domicilio
    FROM encomiendas
    WHERE id_encomienda = ID
    FOR UPDATE;

    -- Mismo orden que muestra la pantalla como "monto anterior"
    IF monto_pago > 0 THEN
        SET anterior = monto_pago;
        SET concepto = 'ENVIO DE ENCOMIENDA - PAGO PRINCIPAL';
        UPDATE encomiendas SET estado_encomienda = ESTA, pago = PAGO_NU, updated_at = NOW() WHERE id_encomienda = ID;
    ELSEIF monto_por_pagar > 0 THEN
        -- Aún no cobrado: no hay ingreso que ajustar
        SET anterior = monto_por_pagar;
        UPDATE encomiendas SET estado_encomienda = ESTA, por_pagar = PAGO_NU, updated_at = NOW() WHERE id_encomienda = ID;
    ELSEIF monto_a_domicilio > 0 THEN
        SET anterior = monto_a_domicilio;
        SET concepto = 'ENVIO DE ENCOMIENDA - SERVICIO A DOMICILIO';
        UPDATE encomiendas SET estado_encomienda = ESTA, a_domicilio = PAGO_NU, updated_at = NOW() WHERE id_encomienda = ID;
    ELSE
        SET concepto = 'ENVIO DE ENCOMIENDA - PAGO PRINCIPAL';
        UPDATE encomiendas SET estado_encomienda = ESTA, pago = PAGO_NU, estado_pago = 'PAGADO', updated_at = NOW() WHERE id_encomienda = ID;
    END IF;

    IF concepto IS NOT NULL AND PAGO_NU <> anterior THEN
        -- Se ajusta el ingreso más reciente del concepto por la diferencia,
        -- así la suma de ingresos queda igual al monto nuevo
        SELECT MAX(id_ingreso) INTO id_ing
        FROM ingresos
        WHERE id_encomiendas = ID AND estado = 'VALIDO' AND observacion = concepto;

        IF id_ing IS NULL THEN
            INSERT INTO ingresos(id_encomiendas, id_indicador, monto, igv, monto_total, observacion, estado, id_usu, created_at)
            VALUES(ID, 7, PAGO_NU - anterior, 0, PAGO_NU - anterior, concepto, 'VALIDO', USU, NOW());
        ELSE
            UPDATE ingresos
            SET monto = monto + (PAGO_NU - anterior),
                monto_total = monto_total + (PAGO_NU - anterior),
                id_usu = USU,
                updated_at = NOW()
            WHERE id_ingreso = id_ing;
        END IF;
    END IF;

    INSERT INTO historial_estados(id_encomienda, estado, observacion, motivo_anula, fecha_anula, created_at, idusu, precio_anterior, precio_nuevi)
    VALUES(ID, ESTA, 'SE AJUSTO EL PAGO Y MODIFICO ESTADO', NULL, NULL, NOW(), USU, anterior, PAGO_NU);

    COMMIT;
END$$

-- ---------------------------------------------------------------------
-- Subir una encomienda a una salida.
-- Solo encomiendas PENDIENTES y salidas EN TRANSITO. Si la encomienda ya
-- está activa en esta salida no hace nada; si se había quitado antes, la
-- reactiva (antes quedaba inactiva aunque se volviera a marcar).
-- ---------------------------------------------------------------------
DROP PROCEDURE IF EXISTS SP_REGISTRAR_DETALLE_ENCOMIENDAS$$
CREATE PROCEDURE SP_REGISTRAR_DETALLE_ENCOMIENDAS(IN IDSALI INT, IN IDENCO INT)
BEGIN
    DECLARE usuario_id INT DEFAULT NULL;
    DECLARE estado_salida VARCHAR(20) DEFAULT NULL;
    DECLARE estado_enco VARCHAR(20) DEFAULT NULL;
    DECLARE activa INT DEFAULT 0;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    SELECT id_usuario, estado INTO usuario_id, estado_salida
    FROM salidas_diarias
    WHERE id_salidas_diarias = IDSALI
    FOR UPDATE;

    IF usuario_id IS NULL OR usuario_id = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No se encontró la salida o usuario asociado';
    END IF;

    IF estado_salida <> 'EN TRANSITO' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La salida ya fue cerrada; no se pueden agregar encomiendas';
    END IF;

    SELECT estado_encomienda INTO estado_enco
    FROM encomiendas
    WHERE id_encomienda = IDENCO
    FOR UPDATE;

    SELECT COUNT(*) INTO activa
    FROM salida_encomienda
    WHERE id_salida = IDSALI AND id_encomienda = IDENCO AND estado = '1';

    IF activa = 0 THEN
        IF estado_enco IS NULL OR estado_enco <> 'PENDIENTE' THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La encomienda no está PENDIENTE (puede estar en otra salida o cerrada)';
        END IF;

        IF EXISTS(SELECT 1 FROM salida_encomienda WHERE id_salida = IDSALI AND id_encomienda = IDENCO) THEN
            UPDATE salida_encomienda
            SET estado = '1', updated_at = NOW()
            WHERE id_salida = IDSALI AND id_encomienda = IDENCO;
        ELSE
            INSERT INTO salida_encomienda(id_salida, id_encomienda, created_at)
            VALUES(IDSALI, IDENCO, NOW());
        END IF;

        UPDATE encomiendas
        SET estado_encomienda = 'EN TRANSITO', updated_at = NOW()
        WHERE id_encomienda = IDENCO;

        INSERT INTO historial_estados(id_encomienda, estado, observacion, created_at, idusu)
        VALUES(IDENCO, 'EN TRANSITO', 'LA ENCOMIENDA ESTA EN TRANSITO', NOW(), usuario_id);
    END IF;

    UPDATE salidas_diarias
    SET total_encomiendas = (SELECT COUNT(*) FROM salida_encomienda WHERE id_salida = IDSALI AND estado = '1')
    WHERE id_salidas_diarias = IDSALI;

    COMMIT;
END$$

-- ---------------------------------------------------------------------
-- Quitar una encomienda de una salida (al editar la salida).
-- ---------------------------------------------------------------------
DROP PROCEDURE IF EXISTS SP_MODIFICAR_DETALLE_ENCOMIENDAS$$
CREATE PROCEDURE SP_MODIFICAR_DETALLE_ENCOMIENDAS(IN IDSALI INT, IN IDENCO INT)
BEGIN
    DECLARE usuario_id INT DEFAULT NULL;
    DECLARE estado_salida VARCHAR(20) DEFAULT NULL;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    SELECT id_usuario, estado INTO usuario_id, estado_salida
    FROM salidas_diarias
    WHERE id_salidas_diarias = IDSALI
    FOR UPDATE;

    IF usuario_id IS NULL OR usuario_id = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No se encontró la salida o usuario asociado';
    END IF;

    IF estado_salida <> 'EN TRANSITO' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La salida ya fue cerrada; no se pueden quitar encomiendas';
    END IF;

    IF NOT EXISTS(SELECT 1 FROM salida_encomienda WHERE id_salida = IDSALI AND id_encomienda = IDENCO AND estado = '1') THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La encomienda no está en esta salida';
    END IF;

    UPDATE salida_encomienda
    SET estado = '0', updated_at = NOW()
    WHERE id_salida = IDSALI AND id_encomienda = IDENCO;

    -- Solo vuelve a PENDIENTE si seguía viajando con esta salida
    IF (SELECT estado_encomienda FROM encomiendas WHERE id_encomienda = IDENCO) = 'EN TRANSITO' THEN
        UPDATE encomiendas
        SET estado_encomienda = 'PENDIENTE', updated_at = NOW()
        WHERE id_encomienda = IDENCO;

        INSERT INTO historial_estados(id_encomienda, estado, observacion, created_at, idusu)
        VALUES(IDENCO, 'PENDIENTE', 'LA ENCOMIENDA SE RETIRÓ DE LA SALIDA DIARIA', NOW(), usuario_id);
    END IF;

    UPDATE salidas_diarias
    SET total_encomiendas = (SELECT COUNT(*) FROM salida_encomienda WHERE id_salida = IDSALI AND estado = '1')
    WHERE id_salidas_diarias = IDSALI;

    COMMIT;
END$$

-- ---------------------------------------------------------------------
-- Completar una salida: sus encomiendas activas que siguen EN TRANSITO
-- pasan a EN AGENCIA. Antes también movía las que se habían quitado de
-- la salida y se podía completar una salida ya cerrada.
-- ---------------------------------------------------------------------
DROP PROCEDURE IF EXISTS SP_MODIFICAR_SALIDA_ESTATUS$$
CREATE PROCEDURE SP_MODIFICAR_SALIDA_ESTATUS(IN ID INT, IN USU INT)
BEGIN
    DECLARE estado_salida VARCHAR(20) DEFAULT NULL;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    SELECT estado INTO estado_salida
    FROM salidas_diarias
    WHERE id_salidas_diarias = ID
    FOR UPDATE;

    IF estado_salida IS NULL OR estado_salida <> 'EN TRANSITO' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Solo se puede completar una salida EN TRANSITO';
    END IF;

    UPDATE salidas_diarias
    SET estado = 'COMPLETADO', updated_at = NOW()
    WHERE id_salidas_diarias = ID;

    INSERT INTO historial_salidas_diarias(id_salida, estado, observacion, created, usu)
    VALUES(ID, 'COMPLETADO', 'SE CULMINO EL VIAJE', NOW(), USU);

    INSERT INTO historial_estados(id_encomienda, estado, observacion, motivo_anula, fecha_anula, created_at, idusu)
    SELECT e.id_encomienda, 'EN AGENCIA', 'LLEGO LA ENCOMIENDA ESTA EN AGENCIA', NULL, NULL, NOW(), USU
    FROM salida_encomienda se
    INNER JOIN encomiendas e ON e.id_encomienda = se.id_encomienda
    WHERE se.id_salida = ID AND se.estado = '1' AND e.estado_encomienda = 'EN TRANSITO';

    UPDATE encomiendas e
    INNER JOIN salida_encomienda se ON se.id_encomienda = e.id_encomienda
    SET e.estado_encomienda = 'EN AGENCIA', e.updated_at = NOW()
    WHERE se.id_salida = ID AND se.estado = '1' AND e.estado_encomienda = 'EN TRANSITO';

    COMMIT;
END$$

-- ---------------------------------------------------------------------
-- Cerrar una salida como INCOMPLETA (accidente, desvío, etc.).
-- LLEGARON: ids de encomiendas que sí llegaron, separados por coma.
-- Esas pasan a EN AGENCIA; las demás a OBSERVADO con el motivo, para
-- reenviarlas (PENDIENTE) o anularlas después.
-- ---------------------------------------------------------------------
DROP PROCEDURE IF EXISTS SP_MODIFICAR_SALIDA_INCOMPLETO_ESTATUS$$
CREATE PROCEDURE SP_MODIFICAR_SALIDA_INCOMPLETO_ESTATUS(IN ID INT, IN USU INT, IN OBSERVA TEXT, IN LLEGARON TEXT)
BEGIN
    DECLARE estado_salida VARCHAR(20) DEFAULT NULL;
    DECLARE nro VARCHAR(20) DEFAULT '';
    DECLARE lista TEXT DEFAULT '';
    DECLARE motivo VARCHAR(255) DEFAULT '';

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    SELECT estado, salida_nro INTO estado_salida, nro
    FROM salidas_diarias
    WHERE id_salidas_diarias = ID
    FOR UPDATE;

    IF estado_salida IS NULL OR estado_salida <> 'EN TRANSITO' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Solo se puede cerrar una salida EN TRANSITO';
    END IF;

    SET lista = REPLACE(IFNULL(LLEGARON, ''), ' ', '');
    SET motivo = LEFT(CONCAT('NO LLEGO EN LA SALIDA ', nro, ': ', IFNULL(OBSERVA, '')), 255);

    UPDATE salidas_diarias
    SET estado = 'INCOMPLETO', observacion = OBSERVA, updated_at = NOW()
    WHERE id_salidas_diarias = ID;

    INSERT INTO historial_salidas_diarias(id_salida, estado, observacion, created, usu)
    VALUES(ID, 'INCOMPLETO', OBSERVA, NOW(), USU);

    -- Las que llegaron
    INSERT INTO historial_estados(id_encomienda, estado, observacion, motivo_anula, fecha_anula, created_at, idusu)
    SELECT e.id_encomienda, 'EN AGENCIA', 'LLEGO LA ENCOMIENDA ESTA EN AGENCIA', NULL, NULL, NOW(), USU
    FROM salida_encomienda se
    INNER JOIN encomiendas e ON e.id_encomienda = se.id_encomienda
    WHERE se.id_salida = ID AND se.estado = '1' AND e.estado_encomienda = 'EN TRANSITO'
      AND FIND_IN_SET(e.id_encomienda, lista) > 0;

    UPDATE encomiendas e
    INNER JOIN salida_encomienda se ON se.id_encomienda = e.id_encomienda
    SET e.estado_encomienda = 'EN AGENCIA', e.updated_at = NOW()
    WHERE se.id_salida = ID AND se.estado = '1' AND e.estado_encomienda = 'EN TRANSITO'
      AND FIND_IN_SET(e.id_encomienda, lista) > 0;

    -- Las que no llegaron (las que llegaron ya no están EN TRANSITO)
    INSERT INTO historial_estados(id_encomienda, estado, observacion, motivo_anula, fecha_anula, created_at, idusu)
    SELECT e.id_encomienda, 'OBSERVADO', motivo, NULL, NULL, NOW(), USU
    FROM salida_encomienda se
    INNER JOIN encomiendas e ON e.id_encomienda = se.id_encomienda
    WHERE se.id_salida = ID AND se.estado = '1' AND e.estado_encomienda = 'EN TRANSITO';

    UPDATE encomiendas e
    INNER JOIN salida_encomienda se ON se.id_encomienda = e.id_encomienda
    SET e.estado_encomienda = 'OBSERVADO', e.observacion = motivo, e.updated_at = NOW()
    WHERE se.id_salida = ID AND se.estado = '1' AND e.estado_encomienda = 'EN TRANSITO';

    COMMIT;
END$$

-- ---------------------------------------------------------------------
-- Eliminar una salida: solo EN TRANSITO. Libera sus encomiendas
-- (vuelven a PENDIENTE) y anula el ingreso de la salida. Antes las
-- encomiendas quedaban EN TRANSITO para siempre y el ingreso seguía válido.
-- ---------------------------------------------------------------------
DROP PROCEDURE IF EXISTS SP_ELIMINAR_SALIDA_DIARIA$$
CREATE PROCEDURE SP_ELIMINAR_SALIDA_DIARIA(IN ID INT, IN USU INT)
BEGIN
    DECLARE estado_salida VARCHAR(20) DEFAULT NULL;
    DECLARE nro VARCHAR(20) DEFAULT '';

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    SELECT estado, salida_nro INTO estado_salida, nro
    FROM salidas_diarias
    WHERE id_salidas_diarias = ID
    FOR UPDATE;

    IF estado_salida IS NULL OR estado_salida <> 'EN TRANSITO' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Solo se puede eliminar una salida EN TRANSITO';
    END IF;

    INSERT INTO historial_estados(id_encomienda, estado, observacion, motivo_anula, fecha_anula, created_at, idusu)
    SELECT e.id_encomienda, 'PENDIENTE', CONCAT('SE ELIMINO LA SALIDA ', nro, '; LA ENCOMIENDA VUELVE A PENDIENTE'), NULL, NULL, NOW(), USU
    FROM salida_encomienda se
    INNER JOIN encomiendas e ON e.id_encomienda = se.id_encomienda
    WHERE se.id_salida = ID AND se.estado = '1' AND e.estado_encomienda = 'EN TRANSITO';

    UPDATE encomiendas e
    INNER JOIN salida_encomienda se ON se.id_encomienda = e.id_encomienda
    SET e.estado_encomienda = 'PENDIENTE', e.updated_at = NOW()
    WHERE se.id_salida = ID AND se.estado = '1' AND e.estado_encomienda = 'EN TRANSITO';

    UPDATE salida_encomienda
    SET estado = '0', updated_at = NOW()
    WHERE id_salida = ID AND estado = '1';

    UPDATE salidas_diarias
    SET estado = 'ELIMINADO', total_encomiendas = 0, updated_at = NOW()
    WHERE id_salidas_diarias = ID;

    INSERT INTO historial_salidas_diarias(id_salida, estado, observacion, created, usu)
    VALUES(ID, 'ELIMINADO', 'SE ELIMINO LA SALIDA DIARIA', NOW(), USU);

    UPDATE ingresos
    SET estado = 'ANULADO',
        id_usu = USU,
        motivo_anulacion = 'SALIDA DIARIA ELIMINADA',
        fecha_anulacion = NOW()
    WHERE id_salidas_diarias = ID AND estado = 'VALIDO';

    COMMIT;
END$$

-- ---------------------------------------------------------------------
-- Editar una salida: solo mientras está EN TRANSITO.
-- El total de encomiendas se cuenta desde la BD; TOTALENCO se mantiene
-- en la firma por compatibilidad y no se usa.
-- ---------------------------------------------------------------------
DROP PROCEDURE IF EXISTS SP_MODIFICAR_SALIDA_DIARIA$$
CREATE PROCEDURE SP_MODIFICAR_SALIDA_DIARIA(
    IN ID INT, IN MONT DECIMAL(8,2), IN FECHAACTU DATETIME, IN OBSER TEXT, IN USU INT, IN TOTALPASA INT, IN TOTALENCO INT)
BEGIN
    DECLARE estado_salida VARCHAR(20) DEFAULT NULL;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    SELECT estado INTO estado_salida
    FROM salidas_diarias
    WHERE id_salidas_diarias = ID
    FOR UPDATE;

    IF estado_salida IS NULL OR estado_salida <> 'EN TRANSITO' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Solo se puede editar una salida EN TRANSITO';
    END IF;

    UPDATE salidas_diarias
    SET monto = MONT,
        observacion = OBSER,
        updated_at = FECHAACTU,
        total_pasajeros = TOTALPASA,
        total_encomiendas = (SELECT COUNT(*) FROM salida_encomienda WHERE id_salida = ID AND estado = '1')
    WHERE id_salidas_diarias = ID;

    INSERT INTO historial_salidas_diarias(id_salida, estado, observacion, created, usu)
    VALUES(ID, 'EN TRANSITO', 'SE MODIFICO ESTA SALIDA DIARIA', NOW(), USU);

    UPDATE ingresos
    SET monto = MONT,
        monto_total = MONT,
        id_usu = USU,
        updated_at = FECHAACTU
    WHERE id_salidas_diarias = ID AND estado = 'VALIDO';

    COMMIT;

    SELECT ID AS id_salidas_diarias;
END$$

DELIMITER ;
