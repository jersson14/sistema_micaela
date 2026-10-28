-- =====================================================================
-- Registrar y editar encomiendas (24/10/2026)
--
-- Misma firma que los procedimientos anteriores (no cambia el PHP que los llama).
--
-- SP_REGISTRAR_ENCOMIENDA
--   * Antes rechazaba como "ya existe" cualquier otra encomienda del mismo
--     emisor al mismo receptor con el mismo conductor en el día. Ahora solo
--     bloquea la repetición idéntica en los últimos 2 minutos (doble clic).
--   * El correlativo se toma con bloqueo dentro de una transacción para que
--     dos registros simultáneos no reciban el mismo número.
--
-- SP_MODIFICAR_ENCOMIENDA
--   * Guardaba la fecha en "descripcion" (se perdía la descripción).
--   * Cambiaba el usuario que registró por el que edita.
--   * Sobrescribía todos los ingresos de la encomienda con un mismo monto.
--   * Contaba la propia encomienda como duplicada.
--   Ahora actualiza cada campo en su lugar y ajusta los ingresos por la
--   diferencia de cada concepto (pago principal / domicilio).
--
-- Aplicar con:  mysql --comments ... < este_archivo.sql
-- =====================================================================

SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

DELIMITER $$

DROP PROCEDURE IF EXISTS SP_REGISTRAR_ENCOMIENDA$$
CREATE PROCEDURE SP_REGISTRAR_ENCOMIENDA(
    IN CONDUC INT, IN IDORI INT, IN IDDES INT, IN FECHA DATETIME, IN DESCR TEXT,
    IN TIPODOCEMI VARCHAR(255), IN NRODCOEMI CHAR(20), IN NOMEMI VARCHAR(255), IN CELEMI CHAR(9),
    IN TIPODOCRECE VARCHAR(255), IN NRODOCRECEP CHAR(20), IN NOMRECEP VARCHAR(255), IN CELERECEP CHAR(9),
    IN PAG DECIMAL(8,2), IN PORPAGAR DECIMAL(8,2), IN ADOMICILIO DECIMAL(8,2), IN USU INT)
BEGIN
    DECLARE repetida INT DEFAULT 0;
    DECLARE cantidad INT DEFAULT 0;
    DECLARE cod CHAR(12);
    DECLARE proce1 VARCHAR(25);
    DECLARE proce2 VARCHAR(25);
    DECLARE IDEMI INT DEFAULT NULL;
    DECLARE IDRECEP INT DEFAULT NULL;
    DECLARE IDENCO INT;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    SET proce1 = (SELECT rutas.nombre FROM rutas WHERE rutas.idrutas = IDORI);
    SET proce2 = (SELECT rutas.nombre FROM rutas WHERE rutas.idrutas = IDDES);

    -- Emisor: se actualiza si existe, si no se crea
    SELECT MAX(id_cliente) INTO IDEMI
    FROM clientes
    WHERE tipo_documento = TIPODOCEMI COLLATE utf8mb4_unicode_ci AND nro_documento = NRODCOEMI COLLATE utf8mb4_unicode_ci;

    IF IDEMI IS NOT NULL THEN
        UPDATE clientes
        SET nombre_completo = NOMEMI, procedencia = proce1, celular = CELEMI, updated_at = NOW()
        WHERE tipo_documento = TIPODOCEMI COLLATE utf8mb4_unicode_ci AND nro_documento = NRODCOEMI COLLATE utf8mb4_unicode_ci;
    ELSE
        INSERT INTO clientes(tipo_documento, nro_documento, nombre_completo, procedencia, celular, direccion, email, created_at)
        VALUES(TIPODOCEMI, NRODCOEMI, NOMEMI, proce1, CELEMI, '', '', NOW());
        SET IDEMI = LAST_INSERT_ID();
    END IF;

    -- Receptor: igual
    SELECT MAX(id_cliente) INTO IDRECEP
    FROM clientes
    WHERE tipo_documento = TIPODOCRECE COLLATE utf8mb4_unicode_ci AND nro_documento = NRODOCRECEP COLLATE utf8mb4_unicode_ci;

    IF IDRECEP IS NOT NULL THEN
        UPDATE clientes
        SET nombre_completo = NOMRECEP, procedencia = proce2, celular = CELERECEP, updated_at = NOW()
        WHERE tipo_documento = TIPODOCRECE COLLATE utf8mb4_unicode_ci AND nro_documento = NRODOCRECEP COLLATE utf8mb4_unicode_ci;
    ELSE
        INSERT INTO clientes(tipo_documento, nro_documento, nombre_completo, procedencia, celular, direccion, email, created_at)
        VALUES(TIPODOCRECE, NRODOCRECEP, NOMRECEP, proce2, CELERECEP, '', '', NOW());
        SET IDRECEP = LAST_INSERT_ID();
    END IF;

    -- Doble clic: la misma encomienda idéntica registrada hace menos de 2 minutos
    SELECT COUNT(*) INTO repetida
    FROM encomiendas
    WHERE id_conductor = CONDUC
      AND id_cliente_emisor = IDEMI
      AND id_cliente_receptor = IDRECEP
      AND id_origen = IDORI
      AND id_destino = IDDES
      AND descripcion = DESCR COLLATE utf8mb4_unicode_ci
      AND pago = PAG AND por_pagar = PORPAGAR AND a_domicilio = ADOMICILIO
      AND created_at >= NOW() - INTERVAL 2 MINUTE;

    IF repetida > 0 THEN
        ROLLBACK;
        SELECT 2;
    ELSE
        -- Correlativo con bloqueo: dos registros a la vez no toman el mismo número
        SELECT IFNULL(MAX(doc_nrocorrelativo), 0) INTO cantidad
        FROM encomiendas
        FOR UPDATE;

        SET cod = CONCAT('E-', IF(cantidad + 1 > 9999999, cantidad + 1, LPAD(cantidad + 1, 7, '0')));

        INSERT INTO encomiendas(
            boleta_nro, id_conductor, id_origen, id_destino, fecha_hora, descripcion,
            id_cliente_emisor, id_cliente_receptor, pago, por_pagar, a_domicilio,
            id_usuario, observacion, estado_pago, created_at, estado_encomienda, doc_nrocorrelativo
        ) VALUES (
            cod, CONDUC, IDORI, IDDES, FECHA, DESCR,
            IDEMI, IDRECEP, PAG, PORPAGAR, ADOMICILIO,
            USU, '', IF(PAG > 0 OR ADOMICILIO > 0, 'PAGADO', 'POR PAGAR'), NOW(), 'PENDIENTE', cantidad + 1
        );

        SET IDENCO = LAST_INSERT_ID();

        IF PAG > 0 THEN
            INSERT INTO ingresos(id_encomiendas, id_indicador, monto, igv, monto_total, observacion, estado, id_usu, created_at)
            VALUES(IDENCO, 7, PAG, 0, PAG, 'ENVIO DE ENCOMIENDA - PAGO PRINCIPAL', 'VALIDO', USU, NOW());
        END IF;

        IF ADOMICILIO > 0 THEN
            INSERT INTO ingresos(id_encomiendas, id_indicador, monto, igv, monto_total, observacion, estado, id_usu, created_at)
            VALUES(IDENCO, 7, ADOMICILIO, 0, ADOMICILIO, 'ENVIO DE ENCOMIENDA - SERVICIO A DOMICILIO', 'VALIDO', USU, NOW());
        END IF;

        INSERT INTO historial_estados(id_encomienda, estado, observacion, motivo_anula, fecha_anula, created_at, idusu)
        VALUES(IDENCO, 'PENDIENTE', 'ES EL PRIMER REGISTRO', NULL, NULL, NOW(), USU);

        COMMIT;

        SELECT IDENCO;
    END IF;
END$$

DROP PROCEDURE IF EXISTS SP_MODIFICAR_ENCOMIENDA$$
CREATE PROCEDURE SP_MODIFICAR_ENCOMIENDA(
    IN ID INT, IN CONDUC INT, IN IDORI INT, IN IDDES INT, IN FECHA DATETIME, IN DESCR TEXT,
    IN TIPODOCEMI VARCHAR(255), IN NRODCOEMI CHAR(20), IN NOMEMI VARCHAR(255), IN CELEMI CHAR(9),
    IN TIPODOCRECE VARCHAR(255), IN NRODOCRECEP CHAR(20), IN NOMRECEP VARCHAR(255), IN CELERECEP CHAR(9),
    IN PAG DECIMAL(8,2), IN PORPAGAR DECIMAL(8,2), IN ADOMICILIO DECIMAL(8,2), IN OBSERVA TEXT, IN USU INT)
BEGIN
    DECLARE est VARCHAR(20) DEFAULT NULL;
    DECLARE pago_ant DECIMAL(8,2) DEFAULT 0;
    DECLARE dom_ant DECIMAL(8,2) DEFAULT 0;
    DECLARE proce1 VARCHAR(25);
    DECLARE proce2 VARCHAR(25);
    DECLARE IDEMI INT DEFAULT NULL;
    DECLARE IDRECEP INT DEFAULT NULL;
    DECLARE id_ing INT DEFAULT NULL;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    SELECT estado_encomienda, IFNULL(pago, 0), IFNULL(a_domicilio, 0)
    INTO est, pago_ant, dom_ant
    FROM encomiendas
    WHERE id_encomienda = ID
    FOR UPDATE;

    IF est IS NULL OR est = 'ANULADO' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No se puede editar una encomienda anulada';
    END IF;

    SET proce1 = (SELECT rutas.nombre FROM rutas WHERE rutas.idrutas = IDORI);
    SET proce2 = (SELECT rutas.nombre FROM rutas WHERE rutas.idrutas = IDDES);

    SELECT MAX(id_cliente) INTO IDEMI
    FROM clientes
    WHERE tipo_documento = TIPODOCEMI COLLATE utf8mb4_unicode_ci AND nro_documento = NRODCOEMI COLLATE utf8mb4_unicode_ci;

    IF IDEMI IS NOT NULL THEN
        UPDATE clientes
        SET nombre_completo = NOMEMI, procedencia = proce1, celular = CELEMI, updated_at = NOW()
        WHERE tipo_documento = TIPODOCEMI COLLATE utf8mb4_unicode_ci AND nro_documento = NRODCOEMI COLLATE utf8mb4_unicode_ci;
    ELSE
        INSERT INTO clientes(tipo_documento, nro_documento, nombre_completo, procedencia, celular, direccion, email, created_at)
        VALUES(TIPODOCEMI, NRODCOEMI, NOMEMI, proce1, CELEMI, '', '', NOW());
        SET IDEMI = LAST_INSERT_ID();
    END IF;

    SELECT MAX(id_cliente) INTO IDRECEP
    FROM clientes
    WHERE tipo_documento = TIPODOCRECE COLLATE utf8mb4_unicode_ci AND nro_documento = NRODOCRECEP COLLATE utf8mb4_unicode_ci;

    IF IDRECEP IS NOT NULL THEN
        UPDATE clientes
        SET nombre_completo = NOMRECEP, procedencia = proce2, celular = CELERECEP, updated_at = NOW()
        WHERE tipo_documento = TIPODOCRECE COLLATE utf8mb4_unicode_ci AND nro_documento = NRODOCRECEP COLLATE utf8mb4_unicode_ci;
    ELSE
        INSERT INTO clientes(tipo_documento, nro_documento, nombre_completo, procedencia, celular, direccion, email, created_at)
        VALUES(TIPODOCRECE, NRODOCRECEP, NOMRECEP, proce2, CELERECEP, '', '', NOW());
        SET IDRECEP = LAST_INSERT_ID();
    END IF;

    -- La fecha de registro y el usuario que registró no cambian al editar
    UPDATE encomiendas
    SET id_conductor = CONDUC,
        id_origen = IDORI,
        id_destino = IDDES,
        descripcion = DESCR,
        id_cliente_emisor = IDEMI,
        id_cliente_receptor = IDRECEP,
        pago = PAG,
        por_pagar = PORPAGAR,
        a_domicilio = ADOMICILIO,
        estado_pago = IF(PORPAGAR > 0, 'POR PAGAR', 'PAGADO'),
        observacion = OBSERVA,
        updated_at = NOW()
    WHERE id_encomienda = ID;

    -- Ingresos: se ajusta cada concepto por la diferencia
    IF PAG <> pago_ant THEN
        SELECT MAX(id_ingreso) INTO id_ing
        FROM ingresos
        WHERE id_encomiendas = ID AND estado = 'VALIDO' AND observacion = 'ENVIO DE ENCOMIENDA - PAGO PRINCIPAL';

        IF id_ing IS NULL THEN
            IF PAG > 0 THEN
                INSERT INTO ingresos(id_encomiendas, id_indicador, monto, igv, monto_total, observacion, estado, id_usu, created_at)
                VALUES(ID, 7, PAG, 0, PAG, 'ENVIO DE ENCOMIENDA - PAGO PRINCIPAL', 'VALIDO', USU, NOW());
            END IF;
        ELSE
            UPDATE ingresos
            SET monto = monto + (PAG - pago_ant),
                monto_total = monto_total + (PAG - pago_ant),
                id_usu = USU,
                updated_at = NOW()
            WHERE id_ingreso = id_ing;
        END IF;
    END IF;

    IF ADOMICILIO <> dom_ant THEN
        SET id_ing = NULL;
        SELECT MAX(id_ingreso) INTO id_ing
        FROM ingresos
        WHERE id_encomiendas = ID AND estado = 'VALIDO' AND observacion = 'ENVIO DE ENCOMIENDA - SERVICIO A DOMICILIO';

        IF id_ing IS NULL THEN
            IF ADOMICILIO > 0 THEN
                INSERT INTO ingresos(id_encomiendas, id_indicador, monto, igv, monto_total, observacion, estado, id_usu, created_at)
                VALUES(ID, 7, ADOMICILIO, 0, ADOMICILIO, 'ENVIO DE ENCOMIENDA - SERVICIO A DOMICILIO', 'VALIDO', USU, NOW());
            END IF;
        ELSE
            UPDATE ingresos
            SET monto = monto + (ADOMICILIO - dom_ant),
                monto_total = monto_total + (ADOMICILIO - dom_ant),
                id_usu = USU,
                updated_at = NOW()
            WHERE id_ingreso = id_ing;
        END IF;
    END IF;

    INSERT INTO historial_estados(id_encomienda, estado, observacion, motivo_anula, fecha_anula, created_at, idusu)
    VALUES(ID, est, 'SE MODIFICO ESTE REGISTRO', NULL, NULL, NOW(), USU);

    COMMIT;

    SELECT 1;
END$$

DELIMITER ;
