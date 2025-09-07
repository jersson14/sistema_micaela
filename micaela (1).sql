-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3307
-- Tiempo de generación: 23-08-2025 a las 16:16:52
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `micaela`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_CARGAR_RUTAS` ()   SELECT
rutas.idrutas,
rutas.nombre
FROM rutas
WHERE rutas.estado='ACTIVO'$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_CARGAR_SELECT_AREA` ()   SELECT
areas_hospital.id_area,areas_hospital.nombre
FROM areas_hospital
WHERE estado_area="ACTIVO"$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_CARGAR_SELECT_CHOFERES` ()   SELECT
	choferes.id_chofer, 
	choferes.nro_doc, 
	choferes.nombres_apellidos
FROM
	choferes
WHERE choferes.estado='ACTIVO'$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_CARGAR_SELECT_ROLES` ()   SELECT
roles.id_role,roles.rol
FROM roles
WHERE estado="ACTIVO"$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_CARGAR_SELECT_SUCURSAL` ()   SELECT
sucursales.id_sucursal,sucursales.sucrusal
FROM sucursales
WHERE estado="ACTIVO"$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_CARGAR_TRAER_PRECIO` (IN `ID` INT)   SELECT
practicas.cod_practica,
practicas.valor
FROM practicas
WHERE practicas.`id_práctica`=ID$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_CARGAR_USUARIOS` ()   SELECT 
    usuario.id_usuario,
    usuario.dni_usuario,
    CONCAT_WS(' ', usuario.usu_nombre, usuario.usu_apellido) AS USUARIO
FROM usuario
WHERE usuario.usu_estatus = 'ACTIVO'$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_ELIMINAR_CHOFERES` (IN `ID` INT)   DELETE FROM choferes WHERE choferes.id_chofer=ID$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_ELIMINAR_CLIENTE` (IN `ID` INT)   DELETE FROM clientes WHERE clientes.id_cliente=ID$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_ELIMINAR_INDICADOR` (IN `ID` INT)   DELETE FROM indicadores where indicadores.id_indicador=ID$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_ELIMINAR_ROL` (IN `ID` INT)   DELETE FROM roles WHERE roles.id_role=ID$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_ELIMINAR_RUTA` (IN `ID` INT)   DELETE FROM rutas WHERE rutas.idrutas=ID$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_ELIMINAR_SERVICIOS` (IN `ID` INT)   DELETE FROM servicios WHERE servicios.id_servicio=ID$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_ELIMINAR_SUCURSAL` (IN `ID` INT)   DELETE FROM sucursales WHERE sucursales.id_sucursal=ID$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_LISTAR_CHOFERES` ()   SELECT
choferes.id_chofer,
choferes.tipo_documen,
choferes.nro_doc,
choferes.nombres_apellidos,
choferes.celular,
choferes.celular_2,
choferes.procedencia,
choferes.direccion,
choferes.marca_vehiculo,
choferes.placa_vehiculo,
choferes.nro_licencia,
choferes.fecha_vencimiento_licencia,
choferes.clase_categoria,
choferes.estado,
choferes.created_at,
date_format(choferes.created_at, "%d-%m-%Y - %H:%i:%s") as fecha_formateada,
choferes.updated_at,
date_format(choferes.updated_at, "%d-%m-%Y - %H:%i:%s") as fecha_formateada2,
choferes.id_usuario,
choferes.foto,
usuario.dni_usuario,
usuario.usu_nombre,
usuario.usu_apellido,
CONCAT_WS(' ',usuario.usu_nombre,usuario.usu_apellido) AS Usuario
FROM
choferes
INNER JOIN usuario ON choferes.id_usuario = usuario.id_usuario
ORDER BY choferes.created_at DESC$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_LISTAR_CLIENTES` ()   SELECT
	clientes.id_cliente, 
	clientes.tipo_documento, 
	clientes.nro_documento, 
	clientes.nombre_completo, 
	clientes.procedencia, 
	clientes.celular, 
	clientes.direccion, 
	clientes.email, 
	clientes.total_viajes, 
	clientes.ultimo_viaje,
	date_format(clientes.ultimo_viaje, "%d-%m-%Y") as fecha_ultimo_viaje,	
	clientes.created_at, 
		date_format(clientes.created_at, "%d-%m-%Y - %H:%i:%s") as fecha_formateada,	

	clientes.updated_at,
		date_format(clientes.updated_at, "%d-%m-%Y - %H:%i:%s") as fecha_formateada2

FROM
	clientes$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_LISTAR_EMPRESA` ()   SELECT
empresa.id_empresa,
empresa.logo,
empresa.nombre,
empresa.email,
empresa.codigo,
empresa.telefono,
empresa.direccion,
empresa.created_at,
empresa.updated_ar,
empresa.razon_social,
empresa.nombre_comercial,
empresa.tipo_documento,
empresa.numero_documento,
empresa.ubigeo,
empresa.urbanizacion,
empresa.distrito,
empresa.provincia,
empresa.departamento,
empresa.codigo_pais,
empresa.certificado_path,
empresa.certificado_password,
empresa.usuario_sol,
empresa.clave_sol,
empresa.endpoint_sunat,
empresa.modo_prueba
FROM
empresa$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_LISTAR_ENCOMIENDAS` ()   SELECT
    emisor.id_cliente AS id_emisor, 
    emisor.tipo_documento AS tipo_doc_emisor, 
    emisor.nro_documento AS nro_doc_emisor, 
    emisor.nombre_completo AS nombre_emisor, 
    emisor.celular AS celular_emisor, 
    emisor.direccion AS direccion_emisor, 
    receptor.id_cliente AS id_receptor, 
    receptor.tipo_documento AS tipo_doc_receptor, 
    receptor.nro_documento AS nro_doc_receptor, 
    receptor.nombre_completo AS nombre_receptor, 
    receptor.celular AS celular_receptor, 
    receptor.direccion AS direccion_receptor, 
    choferes.id_chofer, 
    choferes.tipo_documen, 
    choferes.nro_doc, 
    choferes.nombres_apellidos, 
    choferes.celular AS celular_chofer, 
    usuario.id_usuario, 
    usuario.dni_usuario, 
    usuario.usu_nombre, 
    usuario.usu_apellido, 
    encomiendas.id_encomienda, 
    encomiendas.boleta_nro, 
    encomiendas.id_conductor, 
    encomiendas.fecha_hora, 
    DATE_FORMAT(encomiendas.fecha_hora, "%d-%m-%Y - %H:%i:%s") AS fecha_formateada, 
    encomiendas.descripcion, 
    encomiendas.id_cliente_emisor, 
    encomiendas.id_cliente_receptor, 
    encomiendas.pago, 
    encomiendas.por_pagar, 
    encomiendas.a_domicilio, 
    encomiendas.id_usuario, 
    encomiendas.observacion, 
    encomiendas.estado_pago, 
    encomiendas.estado_encomienda, 
    encomiendas.motivo_anulacion, 
    encomiendas.fecha_anulacion, 
    encomiendas.created_at, 
    DATE_FORMAT(encomiendas.created_at, "%d-%m-%Y - %H:%i:%s") AS fecha_formateada2, 
    encomiendas.updated_at, 
    DATE_FORMAT(encomiendas.updated_at, "%d-%m-%Y - %H:%i:%s") AS fecha_formateada3, 
    encomiendas.id_origen, 
    encomiendas.id_destino, 
    rutas_origen.nombre AS nombre_origen,
    rutas_destino.nombre AS nombre_destino
FROM
    encomiendas
INNER JOIN clientes AS emisor
    ON encomiendas.id_cliente_emisor = emisor.id_cliente
INNER JOIN clientes AS receptor
    ON encomiendas.id_cliente_receptor = receptor.id_cliente
INNER JOIN usuario
    ON encomiendas.id_usuario = usuario.id_usuario
INNER JOIN choferes
    ON encomiendas.id_conductor = choferes.id_chofer
INNER JOIN rutas AS rutas_origen
    ON rutas_origen.idrutas = encomiendas.id_origen
INNER JOIN rutas AS rutas_destino
    ON rutas_destino.idrutas = encomiendas.id_destino$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_LISTAR_INDICADORES` ()   SELECT
indicadores.id_indicador,
indicadores.tipo_indicador,
indicadores.nombres,
indicadores.descripcion,
indicadores.estado,
indicadores.created_at,
indicadores.updated_at,
indicadores.id_usuario,
	date_format(indicadores.created_at, "%d-%m-%Y - %H:%i:%s") as fecha_formateada,
	date_format(indicadores.updated_at, "%d-%m-%Y - %H:%i:%s") as fecha_formateada2,
usuario.usu_usuario,
	CONCAT_WS(' ',usuario.usu_nombre,usuario.usu_apellido) AS USUARIO 

FROM indicadores inner join usuario
ON indicadores.id_usuario=usuario.id_usuario$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_LISTAR_ROLES` ()   SELECT
roles.id_role,
roles.rol,
roles.descripcion,
roles.estado,
roles.created_at,
	date_format(roles.created_at, "%d-%m-%Y - %H:%i:%s") as fecha_formateada,
roles.updated_at,
	date_format(roles.updated_at, "%d-%m-%Y - %H:%i:%s") as fecha_formateada2
FROM
roles$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_LISTAR_RUTAS` ()   SELECT
	rutas.idrutas, 
	rutas.nombre, 
	rutas.descripcion, 
	rutas.estado, 
	rutas.created_at, 
  	date_format(rutas.created_at, "%d-%m-%Y - %H:%i:%s") as fecha_formateada,

	rutas.updated_at,
  	date_format(rutas.updated_at, "%d-%m-%Y - %H:%i:%s") as fecha_formateada2

FROM
	rutas$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_LISTAR_SERVICIOS` ()   SELECT
servicios.id_servicio,
servicios.nombre,
servicios.costo,
servicios.descripcion,
servicios.estado,
servicios.created_at,
date_format(servicios.created_at, "%d-%m-%Y - %H:%i:%s") as fecha_formateada,
servicios.updated_at,
date_format(servicios.updated_at, "%d-%m-%Y - %H:%i:%s") as fecha_formateada2,
servicios.id_usuario,
usuario.usu_nombre,
usuario.usu_apellido,
CONCAT_WS(' ',usuario.usu_nombre,usuario.usu_apellido) AS USUARIO,
usuario.dni_usuario
FROM
servicios
INNER JOIN usuario ON servicios.id_usuario = usuario.id_usuario$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_LISTAR_SUCURSAL` ()   SELECT
sucursales.id_sucursal,
sucursales.sucrusal,
sucursales.telefono1,
sucursales.telefono2,
sucursales.direccion,
sucursales.descripcion,
sucursales.created_at,
date_format(sucursales.created_at, "%d-%m-%Y - %H:%i:%s") as fecha_formateada,
sucursales.updated_at,
date_format(sucursales.updated_at, "%d-%m-%Y - %H:%i:%s") as fecha_formateada2,
sucursales.id_empresa,
sucursales.estado,
empresa.id_empresa,
empresa.nombre,
empresa.razon_social
FROM
sucursales
INNER JOIN empresa ON sucursales.id_empresa = empresa.id_empresa$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_LISTAR_USUARIO` ()   SELECT
usuario.id_usuario,
usuario.dni_usuario,
usuario.usu_nombre,
usuario.usu_apellido,
CONCAT_WS(' ',usuario.usu_nombre,usuario.usu_apellido) AS USUARIO,
usuario.usu_email,
usuario.usu_direccion,
usuario.usu_usuario,
usuario.usu_contrasenia,
usuario.usu_estatus,
usuario.usu_telefono,
usuario.created_at,
usuario.updated_at,
date_format(usuario.created_at, "%d-%m-%Y - %H:%i:%s") AS fecha_formateada,
usuario.usu_foto,
usuario.id_role,
usuario.id_sucursal,
roles.rol,
sucursales.id_sucursal,
sucursales.sucrusal
FROM
usuario
INNER JOIN roles ON usuario.id_role = roles.id_role
INNER JOIN sucursales ON usuario.id_sucursal = sucursales.id_sucursal$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_MODIFICAR_CHOFERES` (IN `ID` INT, IN `DNI` CHAR(20), IN `NOMBRE_APE` VARCHAR(255), IN `CELU1` CHAR(9), IN `CELU2` CHAR(9), IN `PROCE` CHAR(255), IN `DIREC` TEXT, IN `FOTO` VARCHAR(255), IN `MARCA` VARCHAR(255), IN `PLACA` VARCHAR(255), IN `CLASE_CATE` VARCHAR(255), IN `NRO_LICE` VARCHAR(255), IN `FECHA_VENCI` DATE, IN `ESTA` VARCHAR(20), IN `USU` INT)   BEGIN
    DECLARE CANTIDAD_DNI INT;

    -- Verificar si el DNI ya existe en otro chofer distinto al que se está actualizando
    SET @CANTIDAD_DNI := (
        SELECT COUNT(*) FROM choferes
        WHERE nro_doc = DNI AND id_chofer <> ID
    );

    -- Si no hay duplicados de DNI en otros choferes, continuar
    IF @CANTIDAD_DNI = 0 THEN
        UPDATE choferes
        SET 
            nro_doc = DNI,
            nombres_apellidos = NOMBRE_APE,
            celular = CELU1,
            celular_2 = CELU2,
            procedencia = PROCE,
            direccion = DIREC,
            marca_vehiculo = MARCA,
            placa_vehiculo = PLACA,
            clase_categoria = CLASE_CATE,
            nro_licencia = NRO_LICE,
            fecha_vencimiento_licencia = FECHA_VENCI,
            estado = ESTA,
            updated_at = NOW(),
            id_usuario = USU,
            foto = FOTO
        WHERE id_chofer = ID;

        SELECT 1; -- Actualización exitosa

    ELSE
        SELECT 2; -- DNI ya existe en otro chofer
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_MODIFICAR_CHOFERES_SIN_FOTO` (IN `ID` INT, IN `DNI` CHAR(20), IN `NOMBRE_APE` VARCHAR(255), IN `CELU1` CHAR(9), IN `CELU2` CHAR(9), IN `PROCE` CHAR(255), IN `DIREC` TEXT, IN `MARCA` VARCHAR(255), IN `PLACA` VARCHAR(255), IN `CLASE_CATE` VARCHAR(255), IN `NRO_LICE` VARCHAR(255), IN `FECHA_VENCI` DATE, IN `ESTA` VARCHAR(20), IN `USU` INT)   BEGIN
    DECLARE CANTIDAD_DNI INT;

    -- Verificar si el DNI ya existe en otro chofer distinto al que se está actualizando
    SET @CANTIDAD_DNI := (
        SELECT COUNT(*) FROM choferes
        WHERE nro_doc = DNI AND id_chofer <> ID
    );

    -- Si no hay duplicados de DNI en otros choferes, continuar
    IF @CANTIDAD_DNI = 0 THEN
        UPDATE choferes
        SET 
            nro_doc = DNI,
            nombres_apellidos = NOMBRE_APE,
            celular = CELU1,
            celular_2 = CELU2,
            procedencia = PROCE,
            direccion = DIREC,
            marca_vehiculo = MARCA,
            placa_vehiculo = PLACA,
            clase_categoria = CLASE_CATE,
            nro_licencia = NRO_LICE,
            fecha_vencimiento_licencia = FECHA_VENCI,
            estado = ESTA,
            updated_at = NOW(),
            id_usuario = USU
        WHERE id_chofer = ID;

        SELECT 1; -- Actualización exitosa

    ELSE
        SELECT 2; -- DNI ya existe en otro chofer
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_MODIFICAR_CLIENTES` (IN `ID` INT, IN `TIPODOC` VARCHAR(20), IN `DNI` CHAR(20), IN `NOMBRE_APE` VARCHAR(255), IN `PROCE` VARCHAR(255), IN `CELU` CHAR(9), IN `DIREC` TEXT, IN `EMAIL` VARCHAR(255))   BEGIN
    DECLARE CANTIDAD_DNI INT;

    -- Verificar si el DNI ya existe en otro chofer distinto al que se está actualizando
    SET @CANTIDAD_DNI := (
        SELECT COUNT(*) FROM clientes
        WHERE nro_documento = DNI AND id_cliente <> ID
    );

    -- Si no hay duplicados de DNI en otros choferes, continuar
    IF @CANTIDAD_DNI = 0 THEN
        UPDATE clientes
        SET 
            tipo_documento = TIPODOC,
            nro_documento = DNI,
            nombre_completo = NOMBRE_APE,
            procedencia = PROCE,
            celular = CELU,
            direccion = DIREC,
            email = EMAIL,
            updated_at = NOW()
        WHERE id_cliente = ID;

        SELECT 1; -- Actualización exitosa

    ELSE
        SELECT 2; -- DNI ya existe en otro chofer
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_MODIFICAR_EMPRESA` (IN `ID` INT, IN `NOMBRE` VARCHAR(250), `RAZON` VARCHAR(500), `NOM_COME` VARCHAR(500), `TIPO_DOC` VARCHAR(255), `NRO_DOC` VARCHAR(11), IN `EMAIL` VARCHAR(250), IN `COD` VARCHAR(10), IN `TELEFONO` VARCHAR(20), IN `DIRECCION` VARCHAR(250), `UBI` VARCHAR(6), `URBANI` VARCHAR(100), `DISTRI` VARCHAR(50), `PROVIN` VARCHAR(50), `DEPARTA` VARCHAR(50), `COD_PAIS` VARCHAR(2), `USUSOL` VARCHAR(20), `CLAVESOL` VARCHAR(100))   UPDATE empresa SET
	nombre=NOMBRE,
	razon_social= RAZON,
	nombre_comercial=NOM_COME,
	tipo_documento=TIPO_DOC,
	numero_documento=NRO_DOC,
	email=EMAIL,
	codigo=COD,
	telefono=TELEFONO,
	direccion=DIRECCION,
	ubigeo=UBI,
	urbanizacion=URBANI,
	distrito=DISTRI,
	provincia=PROVIN,
	departamento=DEPARTA,
	codigo_pais=COD_PAIS,
	usuario_sol=USUSOL,
	clave_sol=CLAVESOL,
	updated_ar =NOW()
	WHERE id_empresa=ID$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_MODIFICAR_EMPRESA_FOTO` (IN `ID` INT, IN `RUTA` VARCHAR(255))   UPDATE empresa SET
logo=RUTA
WHERE id_empresa=ID$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_MODIFICAR_INDICADOR` (IN `ID` INT, IN `TIPO` VARCHAR(20), IN `NOMBRE_INDI` VARCHAR(255), IN `DESCRIP` TEXT, IN `ESTA` VARCHAR(20), IN `USU` INT)   BEGIN
DECLARE INDIACTUAL VARCHAR(255);
DECLARE CANTIDAD INT;
SET @INDIACTUAL:=(SELECT nombres FROM indicadores WHERE id_indicador=ID);
IF @INDIACTUAL = NOMBRE_INDI THEN
	UPDATE indicadores SET
	tipo_indicador=TIPO,
	nombres=NOMBRE_INDI,
	descripcion=DESCRIP,
	estado=ESTA,
	id_usuario=USU,
	updated_at =NOW()
	WHERE id_indicador=ID;
	SELECT 1;
ELSE
SET @CANTIDAD:=(SELECT COUNT(*) FROM indicadores WHERE nombres=NOMBRE_INDI);
	IF @CANTIDAD=0 THEN
	UPDATE indicadores SET
	tipo_indicador=TIPO,
	nombres=NOMBRE_INDI,
	descripcion=DESCRIP,
	estado=ESTA,
	id_usuario=USU,
	updated_at =NOW()
	WHERE id_indicador=ID;
		SELECT 1;	
	ELSE
		SELECT 2;	
	END IF;
END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_MODIFICAR_ROL` (IN `ID` INT, IN `NROL` VARCHAR(255), IN `DESCRIP` TEXT, IN `ESTA` VARCHAR(20))   BEGIN
DECLARE ROLACTUAL VARCHAR(255);
DECLARE CANTIDAD INT;
SET @ROLACTUAL:=(SELECT rol FROM roles WHERE roles.id_role=ID);
IF @ROLACTUAL = NROL THEN
	UPDATE roles SET
	roles.rol=NROL,
	roles.descripcion=DESCRIP,
	roles.estado=ESTA,
	roles.updated_at=NOW()
	WHERE id_role=ID;
	SELECT 1;
ELSE
SET @CANTIDAD:=(SELECT COUNT(*) FROM roles WHERE roles.rol=NROL);
	IF @CANTIDAD=0 THEN
		UPDATE roles SET
		roles.rol=NROL,
		roles.descripcion=DESCRIP,
		roles.estado=ESTA,
		roles.updated_at=NOW()		
		WHERE id_role=ID;
		SELECT 1;	
	ELSE
		SELECT 2;	
	END IF;
END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_MODIFICAR_RUTA` (IN `ID` INT, IN `NOM` VARCHAR(255), IN `DESCRIP` TEXT, IN `ESTA` VARCHAR(20))   BEGIN
DECLARE RUTAACTUAL VARCHAR(255);
DECLARE CANTIDAD INT;
SET @RUTAACTUAL:=(SELECT rutas.nombre FROM rutas WHERE rutas.idrutas=ID);
IF @RUTAACTUAL = NOM THEN
	UPDATE rutas SET
	rutas.nombre=NOM,
	rutas.descripcion=DESCRIP,
	rutas.estado=ESTA,
	rutas.updated_at=NOW()
	WHERE rutas.idrutas=ID;
	SELECT 1;
ELSE
SET @CANTIDAD:=(SELECT COUNT(*) FROM rutas WHERE rutas.nombre=NOM);
	IF @CANTIDAD=0 THEN
	UPDATE rutas SET
	rutas.nombre=NOM,
	rutas.descripcion=DESCRIP,
	rutas.estado=ESTA,
	rutas.updated_at=NOW()
	WHERE rutas.idrutas=ID;
		SELECT 1;	
	ELSE
		SELECT 2;	
	END IF;
END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_MODIFICAR_SERVICIOS` (IN `ID` INT, IN `NSERVI` VARCHAR(255), IN `COSTO` DECIMAL(8,2), IN `DESCRIP` TEXT, IN `ESTA` VARCHAR(20), IN `USU` INT)   BEGIN
DECLARE SERVIACTUAL VARCHAR(255);
DECLARE CANTIDAD INT;
SET @SERVIACTUAL:=(SELECT servicios.nombre FROM servicios WHERE servicios.id_servicio=ID);
IF @SERVIACTUAL = NSERVI THEN
	UPDATE servicios SET
	servicios.nombre=NSERVI,
	servicios.costo=COSTO,
	servicios.descripcion=DESCRIP,
	servicios.estado=ESTA,
	servicios.id_usuario=USU,
	servicios.updated_at=NOW()
	WHERE servicios.id_servicio=ID;
	SELECT 1;
ELSE
SET @CANTIDAD:=(SELECT COUNT(*) FROM servicios WHERE servicios.nombre=NSERVI);
	IF @CANTIDAD=0 THEN
	UPDATE servicios SET
	servicios.nombre=NSERVI,
	servicios.costo=COSTO,
	servicios.descripcion=DESCRIP,
	servicios.estado=ESTA,
	servicios.id_usuario=USU,
	servicios.updated_at=NOW()
	WHERE servicios.id_servicio=ID;
		SELECT 1;	
	ELSE
		SELECT 2;	
	END IF;
END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_MODIFICAR_SUCURSAL` (IN `ID` INT, IN `NSUCURSAL` VARCHAR(255), IN `TELE1` CHAR(9), IN `TELE2` CHAR(9), IN `DIREC` TEXT, IN `DESCRIP` TEXT, IN `ESTA` VARCHAR(20))   BEGIN
DECLARE SUCUACTUAL VARCHAR(255);
DECLARE CANTIDAD INT;
SET @SUCUACTUAL:=(SELECT sucursales.sucrusal FROM sucursales WHERE sucursales.id_sucursal=ID);
IF @SUCUACTUAL = NSUCURSAL THEN
	UPDATE sucursales SET
	sucursales.sucrusal=NSUCURSAL,
	sucursales.telefono1=TELE1,
	sucursales.telefono2=TELE2,
	sucursales.direccion=DIREC,
	sucursales.descripcion=DESCRIP,
	sucursales.estado=ESTA,
	sucursales.updated_at=NOW()
	WHERE sucursales.id_sucursal=ID;
	SELECT 1;
ELSE
SET @CANTIDAD:=(SELECT COUNT(*) FROM sucursales WHERE sucursales.sucrusal=NSUCURSAL);
	IF @CANTIDAD=0 THEN
	UPDATE sucursales SET
	sucursales.sucrusal=NSUCURSAL,
	sucursales.telefono1=TELE1,
	sucursales.telefono2=TELE2,
	sucursales.direccion=DIREC,
	sucursales.descripcion=DESCRIP,
	sucursales.estado=ESTA,
	sucursales.updated_at=NOW()
	WHERE sucursales.id_sucursal=ID;
		SELECT 1;	
	ELSE
		SELECT 2;	
	END IF;
END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_MODIFICAR_USUARIO` (IN `ID` INT, IN `DNI` CHAR(8), IN `NOMBRE` VARCHAR(50), IN `APELLIDO` VARCHAR(50), IN `EMAIL` VARCHAR(255), IN `TELEFONO` CHAR(11), IN `DIRECCION` TEXT, IN `FOTO` VARCHAR(255), IN `USU` VARCHAR(255), IN `ROL` INT, IN `SUCU` INT)   BEGIN
    DECLARE CANTIDAD_DNI INT;
    DECLARE CANTIDAD_USU INT;
    DECLARE USUARIOACTUAL VARCHAR(255);

    -- Verificar si el DNI ya existe en otro registro (que no sea el mismo usuario)
    SET @CANTIDAD_DNI := (SELECT COUNT(*) FROM usuario WHERE dni_usuario = DNI AND id_usuario != ID_USUARIO);

    -- Verificar si el nombre de usuario ya existe en otro registro (que no sea el mismo usuario)
    SET @CANTIDAD_USU := (SELECT COUNT(*) FROM usuario WHERE usu_usuario = USU AND id_usuario != ID_USUARIO);

    -- Si el DNI y el nombre de usuario no existen como duplicados
    IF @CANTIDAD_DNI = 0 AND @CANTIDAD_USU = 0 THEN
        -- Realizar la actualización del usuario
        UPDATE usuario
        SET 
            dni_usuario = DNI,
            usu_nombre = NOMBRE,
            usu_apellido = APELLIDO,
            usu_email = EMAIL,
            usu_telefono = TELEFONO,
            usu_direccion = DIRECCION,
            usu_usuario = USU,
            id_role = ROL,
						id_sucursal=SUCU,
            usu_foto = FOTO,
            updated_at = NOW()
        WHERE id_usuario = ID;

        SELECT 1; -- Indicar que la actualización fue exitosa

    ELSE
        -- Si hay duplicados en el DNI o el nombre de usuario
        SELECT 2; -- Indicar que el DNI o el nombre de usuario ya existen
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_MODIFICAR_USUARIO_CONTRA` (IN `ID` INT, IN `CONTRA` VARCHAR(255))   UPDATE usuario SET
usuario.usu_contrasenia=CONTRA
WHERE usuario.id_usuario=ID$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_MODIFICAR_USUARIO_ESTATUS` (IN `ID` INT, IN `ESTATUS` VARCHAR(20))   UPDATE usuario SET
usuario.usu_estatus=ESTATUS
WHERE usuario.id_usuario=ID$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_REGISTRAR_AREA` (IN `NAREA` VARCHAR(255), IN `DESCRIP` VARCHAR(255), IN `USU` INT)   BEGIN
DECLARE CANTIDAD INT;
SET @CANTIDAD:=(SELECT COUNT(*) FROM areas_hospital where nombre=NAREA);
IF @CANTIDAD = 0 THEN
INSERT INTO areas_hospital(nombre,descripcion,id_usuario,created_at,estado_area)VALUE(NAREA,DESCRIP,USU,NOW(),'ACTIVO');
SELECT 1;
ELSE
SELECT 2;

END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_REGISTRAR_CHOFERES` (IN `TIPO_DOC` VARCHAR(20), IN `DNI` CHAR(20), IN `NOMBRE_APE` VARCHAR(255), IN `CELU1` CHAR(9), IN `CELU2` CHAR(9), IN `PROCE` CHAR(255), IN `DIREC` TEXT, IN `FOTO` VARCHAR(255), IN `MARCA` VARCHAR(255), IN `PLACA` VARCHAR(255), IN `CLASE_CATE` VARCHAR(255), IN `NRO_LICE` VARCHAR(255), IN `FECHA_VENCI` DATE, IN `USU` INT)   BEGIN
    DECLARE CANTIDAD_DNI INT;
    DECLARE CANTIDAD_USU INT;

    -- Verificar si el DNI ya existe
    SET @CANTIDAD_DNI := (SELECT COUNT(*) FROM choferes WHERE nro_doc = DNI);

    -- Si no existe un DNI duplicado ni un usuario duplicado
    IF @CANTIDAD_DNI = 0 THEN
        -- Insertar el nuevo usuario
        INSERT INTO choferes (
            tipo_documen,nro_doc, nombres_apellidos, celular, celular_2, 
            procedencia, direccion, marca_vehiculo, placa_vehiculo, 
            clase_categoria,nro_licencia, fecha_vencimiento_licencia,estado, created_at, id_usuario,foto
        ) VALUES (
            TIPO_DOC,DNI, NOMBRE_APE, CELU1, CELU2, 
            PROCE,DIREC,MARCA,PLACA,CLASE_CATE,NRO_LICE,FECHA_VENCI,'ACTIVO',CURDATE(), USU,FOTO
        );

        SELECT 1; -- Indicar que la inserción fue exitosa

    ELSE
        -- Si hay duplicados en el DNI o el nombre de usuario
        SELECT 2; -- Indicar que el DNI o el nombre de usuario ya existen
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_REGISTRAR_INDICADOR` (IN `TIPO` VARCHAR(20), IN `NOMBRE_INDI` VARCHAR(255), IN `DESCRIP` TEXT, IN `USU` INT)   BEGIN
DECLARE CANTIDAD INT;
SET @CANTIDAD:=(SELECT COUNT(*) FROM indicadores where nombres=NOMBRE_INDI);
IF @CANTIDAD = 0 THEN
INSERT INTO indicadores(tipo_indicador,nombres,descripcion,estado,created_at,id_usuario)VALUE(TIPO,NOMBRE_INDI,DESCRIP,'ACTIVO',NOW(),USU);
SELECT 1;
ELSE
SELECT 2;

END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_REGISTRAR_ROLES` (IN `NROL` VARCHAR(255), IN `DESCRIP` VARCHAR(255), IN `ESTADO` VARCHAR(20), IN `FECHA` DATETIME)   BEGIN
DECLARE CANTIDAD INT;
SET @CANTIDAD:=(SELECT COUNT(*) FROM roles where rol=NROL);
IF @CANTIDAD = 0 THEN
INSERT INTO roles(rol,descripcion,estado,created_at)VALUE(NROL,DESCRIP,ESTADO,FECHA);
SELECT 1;
ELSE
SELECT 2;

END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_REGISTRAR_RUTA` (IN `NOM` VARCHAR(255), IN `DESCRIP` TEXT)   BEGIN
DECLARE CANTIDAD INT;


SET @CANTIDAD:=(SELECT COUNT(*) FROM rutas WHERE rutas.nombre=NOM);

IF @CANTIDAD =0 THEN
INSERT INTO rutas(nombre,descripcion,estado,created_at)
VALUES(NOM,DESCRIP,'ACTIVO',NOW());
SELECT 1;
ELSE
SELECT 2;
END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_REGISTRAR_SERVICIOS` (IN `NSERVICIO` VARCHAR(255), IN `COST` DECIMAL(8,2), IN `DESCRIP` TEXT, IN `USU` INT)   BEGIN
DECLARE CANTIDAD INT;
SET @CANTIDAD:=(SELECT COUNT(*) FROM servicios where servicios.nombre=NSERVICIO);
IF @CANTIDAD = 0 THEN
INSERT INTO servicios(nombre,costo,descripcion,estado,created_at,id_usuario)VALUE(NSERVICIO,COST,DESCRIP,'ACTIVO',NOW(),USU);
SELECT 1;
ELSE
SELECT 2;

END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_REGISTRAR_SUCURSAL` (IN `NSUCURSAL` VARCHAR(255), IN `TELE1` CHAR(9), IN `TELE2` CHAR(9), IN `DIREC` TEXT, IN `DESCRIP` TEXT)   BEGIN
DECLARE CANTIDAD INT;
SET @CANTIDAD:=(SELECT COUNT(*) FROM sucursales where sucursales.sucrusal=NSUCURSAL);
IF @CANTIDAD = 0 THEN
INSERT INTO sucursales(sucrusal,telefono1,telefono2,direccion,descripcion,created_at,id_empresa,estado)VALUE(NSUCURSAL,TELE1,TELE2,DIREC,DESCRIP,NOW(),1,'ACTIVO');
SELECT 1;
ELSE
SELECT 2;

END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_REGISTRAR_USUARIO` (IN `DNI` CHAR(8), IN `NOMBRE` VARCHAR(50), IN `APELLIDO` VARCHAR(50), IN `EMAIL` VARCHAR(255), IN `TELEFONO` CHAR(11), IN `DIRECCION` TEXT, IN `FOTO` VARCHAR(255), IN `USU` VARCHAR(255), IN `CONTRA` VARCHAR(255), IN `ROL` INT, IN `SUCU` INT)   BEGIN
    DECLARE CANTIDAD_DNI INT;
    DECLARE CANTIDAD_USU INT;

    -- Verificar si el DNI ya existe
    SET @CANTIDAD_DNI := (SELECT COUNT(*) FROM usuario WHERE dni_usuario = DNI);

    -- Verificar si el nombre de usuario ya existe
    SET @CANTIDAD_USU := (SELECT COUNT(*) FROM usuario WHERE usu_usuario = USU);

    -- Si no existe un DNI duplicado ni un usuario duplicado
    IF @CANTIDAD_DNI = 0 AND @CANTIDAD_USU = 0 THEN
        -- Insertar el nuevo usuario
        INSERT INTO usuario (
            dni_usuario, usu_nombre, usu_apellido, usu_email, 
            usu_telefono, usu_direccion, usu_usuario, usu_contrasenia, 
            id_role,usu_estatus, usu_foto,id_sucursal, created_at, updated_at
        ) VALUES (
            DNI, NOMBRE, APELLIDO, EMAIL, 
            TELEFONO, DIRECCION, USU, CONTRA, 
            ROL,'ACTIVO', FOTO,SUCU, NOW(), NOW()
        );

        SELECT 1; -- Indicar que la inserción fue exitosa

    ELSE
        -- Si hay duplicados en el DNI o el nombre de usuario
        SELECT 2; -- Indicar que el DNI o el nombre de usuario ya existen
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_VERIFICAR_USUARIO` (IN `USU` VARCHAR(255))   SELECT
	usuario.id_usuario, 
	usuario.dni_usuario,
	usuario.usu_nombre, 
	usuario.usu_apellido,
	CONCAT_WS(' ',usuario.usu_nombre,usuario.usu_apellido) AS USUARIO,  
	usuario.usu_email, 
	usuario.usu_direccion, 
	usuario.usu_usuario, 
	usuario.usu_contrasenia, 
	usuario.id_role, 
	usuario.usu_estatus, 
	usuario.usu_telefono,
	usuario.id_sucursal, 
	usuario.created_at,
	usuario.updated_at,
	usuario.usu_foto,
	empresa.id_empresa,
	empresa.logo,
	empresa.nombre,
	roles.rol
FROM
	usuario
	INNER JOIN sucursales on usuario.id_sucursal = sucursales.id_sucursal
	INNER JOIN empresa ON sucursales.id_empresa = empresa.id_empresa
	INNER JOIN roles ON roles.id_role=usuario.id_role
		
where usuario.usu_usuario = BINARY USU$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `catalogo_sunat`
--

CREATE TABLE `catalogo_sunat` (
  `id_catalogo` int(11) NOT NULL,
  `numero_catalogo` tinyint(4) NOT NULL,
  `codigo` varchar(10) NOT NULL,
  `descripcion` varchar(500) NOT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `catalogo_sunat`
--

INSERT INTO `catalogo_sunat` (`id_catalogo`, `numero_catalogo`, `codigo`, `descripcion`, `activo`, `created_at`) VALUES
(1, 6, '0', 'DOC.TRIB.NO.DOM.SIN.RUC', 1, '2025-07-21 18:53:08'),
(2, 6, '1', 'Documento Nacional de Identidad', 1, '2025-07-21 18:53:08'),
(3, 6, '4', 'Carnet de extranjería', 1, '2025-07-21 18:53:08'),
(4, 6, '6', 'Registro Único de Contribuyentes', 1, '2025-07-21 18:53:08'),
(5, 6, '7', 'Pasaporte', 1, '2025-07-21 18:53:08'),
(6, 6, 'A', 'Cédula Diplomática de identidad', 1, '2025-07-21 18:53:08'),
(7, 7, '10', 'Gravado - Operación Onerosa', 1, '2025-07-21 18:53:08'),
(8, 7, '11', 'Gravado – Retiro por premio', 1, '2025-07-21 18:53:08'),
(9, 7, '20', 'Exonerado - Operación Onerosa', 1, '2025-07-21 18:53:08'),
(10, 7, '30', 'Inafecto - Operación Onerosa', 1, '2025-07-21 18:53:08'),
(11, 3, 'NIU', 'Unidad (Bienes)', 1, '2025-07-21 18:53:08'),
(12, 3, 'ZZ', 'Unidad (Servicios)', 1, '2025-07-21 18:53:08'),
(13, 3, 'KGM', 'Kilogramo', 1, '2025-07-21 18:53:08'),
(14, 3, 'MTR', 'Metro', 1, '2025-07-21 18:53:08');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `choferes`
--

CREATE TABLE `choferes` (
  `id_chofer` int(11) NOT NULL,
  `tipo_documen` varchar(50) DEFAULT NULL,
  `nro_doc` char(20) DEFAULT NULL,
  `nombres_apellidos` varchar(255) DEFAULT NULL,
  `celular` char(9) DEFAULT NULL,
  `celular_2` char(9) DEFAULT NULL,
  `procedencia` varchar(255) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `marca_vehiculo` varchar(255) DEFAULT NULL,
  `placa_vehiculo` varchar(255) DEFAULT NULL,
  `clase_categoria` varchar(255) DEFAULT NULL,
  `nro_licencia` varchar(255) DEFAULT NULL,
  `fecha_vencimiento_licencia` date DEFAULT NULL,
  `estado` enum('ACTIVO','INACTIVO') DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `foto` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `choferes`
--

INSERT INTO `choferes` (`id_chofer`, `tipo_documen`, `nro_doc`, `nombres_apellidos`, `celular`, `celular_2`, `procedencia`, `direccion`, `marca_vehiculo`, `placa_vehiculo`, `clase_categoria`, `nro_licencia`, `fecha_vencimiento_licencia`, `estado`, `created_at`, `updated_at`, `id_usuario`, `foto`) VALUES
(2, 'DNI', '66878747', 'ANDRES PEÑA VALVERDE', '987441447', NULL, 'CUSCO', 'AV. PACHACUTEC N° 32', 'HYUNDAY', 'AS-231D', 'CONDUCCIÓN VEHICULO', 'AIIB', '2028-06-13', 'ACTIVO', '2025-07-29 10:22:34', NULL, 1, NULL),
(3, 'DNI', '09251155', 'EDUARDO ALVAREZ ASTETE', '975777881', '954844848', 'ABANCAY', 'JR. CUSCO N° 231', 'KIA', 'S5-84841', 'AIIIA', 'T09251155', '2028-10-23', 'INACTIVO', '2025-08-06 00:00:00', '2025-08-09 12:32:08', 1, 'controller/choferes/fotos/IMG6-8-2025-12-605.avif'),
(4, 'DNI', '72155445', 'JORDAN JEPHERSON MENDOZA CUMPA', '974785471', '987888444', 'CUSCO', 'AV. PACHACUTEC N° 4454', 'HYUNDAY', '52D-D1D1', 'AIIA', 'T72155445', '2027-10-22', 'ACTIVO', '2025-08-06 00:00:00', '2025-08-06 12:01:01', 1, 'controller/choferes/fotos/IMG6-8-2025-11-420.png');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id_cliente` int(11) NOT NULL,
  `tipo_documento` enum('DNI','PASAPORTE','CE','RUC') DEFAULT NULL,
  `nro_documento` char(20) DEFAULT NULL,
  `nombre_completo` varchar(255) DEFAULT NULL,
  `procedencia` varchar(255) DEFAULT NULL,
  `celular` char(9) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `email` text DEFAULT NULL,
  `total_viajes` int(255) DEFAULT NULL,
  `ultimo_viaje` date DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id_cliente`, `tipo_documento`, `nro_documento`, `nombre_completo`, `procedencia`, `celular`, `direccion`, `email`, `total_viajes`, `ultimo_viaje`, `created_at`, `updated_at`) VALUES
(1, 'DNI', '78847841', 'JUAN CAMACHO PERALTAS', 'CUSCO', '997847141', 'Jr. Canada N° 235', 'JUAN@GMAIL.COM', 2, '2025-01-22', '2025-08-09 11:10:04', '2025-08-09 12:12:59'),
(2, 'PASAPORTE', '72541155898', 'DANIELA PERALTA CHAVEZ', 'ABANCAY', '987114787', 'Av. Chile N° 233', '', 5, '2025-02-02', '2025-08-09 11:10:58', '2025-08-09 12:13:04');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comprobantes`
--

CREATE TABLE `comprobantes` (
  `id_comprobante` int(11) NOT NULL,
  `tipo_comprobante` enum('01','03','07','08') NOT NULL,
  `serie` varchar(4) NOT NULL,
  `correlativo` int(11) NOT NULL,
  `numero_completo` varchar(20) GENERATED ALWAYS AS (concat(`serie`,'-',lpad(`correlativo`,8,'0'))) VIRTUAL,
  `fecha_emision` date NOT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `hora_emision` time DEFAULT curtime(),
  `id_cliente` int(11) NOT NULL,
  `moneda` enum('PEN','USD') DEFAULT 'PEN',
  `tipo_cambio` decimal(10,4) DEFAULT 1.0000,
  `subtotal` decimal(10,2) NOT NULL,
  `total_descuentos` decimal(10,2) DEFAULT 0.00,
  `total_gravada` decimal(10,2) NOT NULL,
  `total_exonerada` decimal(10,2) DEFAULT 0.00,
  `total_inafecta` decimal(10,2) DEFAULT 0.00,
  `total_gratuita` decimal(10,2) DEFAULT 0.00,
  `total_igv` decimal(10,2) NOT NULL,
  `total_isc` decimal(10,2) DEFAULT 0.00,
  `total_otros_tributos` decimal(10,2) DEFAULT 0.00,
  `total_impuestos` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `observaciones` text DEFAULT NULL,
  `orden_compra` varchar(100) DEFAULT NULL,
  `guia_remision` varchar(100) DEFAULT NULL,
  `estado_sunat` enum('PENDIENTE','ENVIADO','ACEPTADO','RECHAZADO') DEFAULT 'PENDIENTE',
  `codigo_hash` varchar(100) DEFAULT NULL,
  `codigo_qr` text DEFAULT NULL,
  `xml_firmado` longtext DEFAULT NULL,
  `cdr_sunat` longtext DEFAULT NULL,
  `pdf_generado` longtext DEFAULT NULL,
  `ticket_sunat` varchar(50) DEFAULT NULL,
  `codigo_respuesta_sunat` varchar(10) DEFAULT NULL,
  `descripcion_respuesta_sunat` text DEFAULT NULL,
  `fecha_envio_sunat` datetime DEFAULT NULL,
  `motivo_anulacion` text DEFAULT NULL,
  `fecha_anulacion` datetime DEFAULT NULL,
  `id_servicio` int(11) DEFAULT NULL,
  `id_usuario` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comprobante_detalle`
--

CREATE TABLE `comprobante_detalle` (
  `id_detalle` int(11) NOT NULL,
  `id_comprobante` int(11) NOT NULL,
  `orden_item` tinyint(4) NOT NULL,
  `codigo_producto` varchar(30) DEFAULT NULL,
  `codigo_producto_sunat` varchar(30) DEFAULT NULL,
  `descripcion` varchar(500) NOT NULL,
  `unidad_medida` varchar(10) DEFAULT 'NIU',
  `cantidad` decimal(10,4) NOT NULL DEFAULT 1.0000,
  `precio_unitario` decimal(10,6) NOT NULL,
  `precio_referencial` decimal(10,6) DEFAULT NULL,
  `factor_descuento` decimal(8,6) DEFAULT 0.000000,
  `descuento` decimal(10,2) DEFAULT 0.00,
  `valor_unitario` decimal(10,6) NOT NULL,
  `valor_venta` decimal(10,2) NOT NULL,
  `codigo_tipo_precio` enum('01','02') DEFAULT '01',
  `afectacion_igv` enum('10','11','12','13','14','15','16','17','20','21','30','31','32','33','34','35','36','37','40') DEFAULT '10',
  `porcentaje_igv` decimal(5,2) DEFAULT 18.00,
  `igv` decimal(10,2) NOT NULL DEFAULT 0.00,
  `codigo_isc` varchar(10) DEFAULT NULL,
  `porcentaje_isc` decimal(5,2) DEFAULT 0.00,
  `isc` decimal(10,2) DEFAULT 0.00,
  `total_impuestos_item` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_item` decimal(10,2) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresa`
--

CREATE TABLE `empresa` (
  `id_empresa` int(11) NOT NULL,
  `logo` varchar(500) DEFAULT NULL,
  `nombre` varchar(500) NOT NULL,
  `razon_social` varchar(500) NOT NULL,
  `nombre_comercial` varchar(500) DEFAULT NULL,
  `tipo_documento` varchar(255) NOT NULL DEFAULT '6',
  `numero_documento` varchar(11) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `codigo` char(6) DEFAULT '',
  `telefono` varchar(250) DEFAULT NULL,
  `direccion` varchar(500) DEFAULT NULL,
  `ubigeo` varchar(6) NOT NULL DEFAULT '150101',
  `urbanizacion` varchar(100) DEFAULT NULL,
  `distrito` varchar(50) NOT NULL,
  `provincia` varchar(50) NOT NULL,
  `departamento` varchar(50) NOT NULL,
  `codigo_pais` varchar(2) NOT NULL DEFAULT 'PE',
  `certificado_path` varchar(500) DEFAULT NULL,
  `certificado_password` varchar(100) DEFAULT NULL,
  `usuario_sol` varchar(20) DEFAULT NULL,
  `clave_sol` varchar(100) DEFAULT NULL,
  `endpoint_sunat` varchar(200) DEFAULT 'https://e-beta.sunat.gob.pe/ol-ti-itcpfegem-beta/billService',
  `modo_prueba` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_ar` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empresa`
--

INSERT INTO `empresa` (`id_empresa`, `logo`, `nombre`, `razon_social`, `nombre_comercial`, `tipo_documento`, `numero_documento`, `email`, `codigo`, `telefono`, `direccion`, `ubigeo`, `urbanizacion`, `distrito`, `provincia`, `departamento`, `codigo_pais`, `certificado_path`, `certificado_password`, `usuario_sol`, `clave_sol`, `endpoint_sunat`, `modo_prueba`, `created_at`, `updated_ar`) VALUES
(1, 'controller/empresa/FOTOS/IMG26-7-2025-15-888.jpg', 'TOURS MICAELA', 'ETTOM S.A.', 'EMPRESA DE TRANSPORTES TOURS MICAELA', 'RUC', '20603540647', 'TOURSMICAELA@GMAIL.COM', '01', '+51983152885', 'PROLONGACIÓN HUANCAVELICA S/N', '150101', '', 'ABANCAY', 'ABANCAY', 'APURIMAC', 'PE', NULL, NULL, '', '', 'https://e-beta.sunat.gob.pe/ol-ti-itcpfegem-beta/billService', 1, '2025-01-18 14:56:21', '2025-07-26 16:22:50');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `encomiendas`
--

CREATE TABLE `encomiendas` (
  `id_encomienda` int(11) NOT NULL,
  `boleta_nro` varchar(255) DEFAULT NULL,
  `id_conductor` int(11) DEFAULT NULL,
  `id_origen` int(11) DEFAULT NULL,
  `id_destino` int(11) DEFAULT NULL,
  `fecha_hora` datetime DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `id_cliente_emisor` int(8) DEFAULT NULL,
  `id_cliente_receptor` int(11) DEFAULT NULL,
  `pago` decimal(8,2) DEFAULT NULL,
  `por_pagar` decimal(8,2) DEFAULT NULL,
  `a_domicilio` decimal(8,2) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `observacion` text DEFAULT NULL,
  `estado_pago` enum('PAGADO','POR PAGAR','ANULADO') DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `estado_encomienda` enum('PENDIENTE','ENTREGADO','OBSERVADO','EN TRANSITO','EN AGENCIA','ANULADO') DEFAULT NULL,
  `motivo_anulacion` varchar(255) DEFAULT NULL,
  `fecha_anulacion` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `encomiendas`
--

INSERT INTO `encomiendas` (`id_encomienda`, `boleta_nro`, `id_conductor`, `id_origen`, `id_destino`, `fecha_hora`, `descripcion`, `id_cliente_emisor`, `id_cliente_receptor`, `pago`, `por_pagar`, `a_domicilio`, `id_usuario`, `observacion`, `estado_pago`, `created_at`, `updated_at`, `estado_encomienda`, `motivo_anulacion`, `fecha_anulacion`) VALUES
(1, '33444', 4, 2, 1, '2025-08-22 09:12:49', 'COSTAL DE PANES', 2, 1, 0.00, 50.00, 0.00, 1, NULL, 'POR PAGAR', '2025-08-23 09:13:12', NULL, 'EN TRANSITO', NULL, NULL),
(5, '222332', 2, 1, 2, '2025-08-21 09:12:05', 'CAJA PEQUEÑA', 1, 2, 30.00, 0.00, 0.00, 1, NULL, 'PAGADO', '2025-08-21 09:12:31', NULL, 'PENDIENTE', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gastos`
--

CREATE TABLE `gastos` (
  `id_gastos` int(11) NOT NULL,
  `id_indicador` int(11) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `monto` decimal(8,2) DEFAULT NULL,
  `observacion` text DEFAULT NULL,
  `estado` enum('ANULADO','VALIDO') DEFAULT NULL,
  `motivo_anulacion` text DEFAULT NULL,
  `fecha_anulacion` date DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `indicadores`
--

CREATE TABLE `indicadores` (
  `id_indicador` int(11) NOT NULL,
  `tipo_indicador` enum('INGRESOS','GASTOS') DEFAULT NULL,
  `nombres` varchar(255) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` enum('ACTIVO','INACTIVO') DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `indicadores`
--

INSERT INTO `indicadores` (`id_indicador`, `tipo_indicador`, `nombres`, `descripcion`, `estado`, `created_at`, `updated_at`, `id_usuario`) VALUES
(1, 'INGRESOS', 'PAGOS DE CLIENTES', 'Pagos por viaje', 'ACTIVO', '2025-07-29 09:25:32', NULL, 1),
(2, 'GASTOS', 'PAGO DE LUZ', 'Servicio de luz', 'ACTIVO', '2025-07-29 09:25:51', NULL, 1),
(3, 'GASTOS', 'COMPRA DE MATERIALES', 'Compra de materiales de oficina u otros', 'ACTIVO', '2025-07-29 09:26:12', NULL, 1),
(4, 'INGRESOS', 'SERVICIO DE ENCOMIENDA', 'COBRO POR ENVíO DE ENCOMIENDAS', 'ACTIVO', '2025-07-29 09:26:48', '2025-07-29 09:40:17', 1),
(6, '', 'PAGO DE AGUA', 'SERVICIO DE PAGO DE AGUA', 'ACTIVO', '2025-07-29 09:39:21', '2025-07-29 09:39:59', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingresos`
--

CREATE TABLE `ingresos` (
  `id_ingreso` int(11) NOT NULL,
  `id_encomiendas` int(11) DEFAULT NULL,
  `id_salidas_diarias` int(11) DEFAULT NULL,
  `id_indicador` int(11) DEFAULT NULL,
  `monto` decimal(8,2) DEFAULT NULL,
  `igv` decimal(8,2) DEFAULT NULL,
  `monto_total` decimal(8,2) DEFAULT NULL,
  `observacion` varchar(255) DEFAULT NULL,
  `estado` enum('ANULADO','VALIDO') DEFAULT NULL,
  `id_usu` int(11) DEFAULT NULL,
  `motivo_anulacion` varchar(255) DEFAULT NULL,
  `fecha_anulacion` date DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id_role` int(11) NOT NULL,
  `rol` varchar(255) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `estado` enum('ACTIVO','INACTIVO') DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id_role`, `rol`, `descripcion`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'ADMINISTRADOR', 'EL QUE TIENE ACCESO A TODO', 'ACTIVO', '2025-07-26 12:02:59', '2025-07-27 12:57:05'),
(2, 'SECRETARIA', 'TIENE ACCESO SOLO A POCOS MóDULOS ', 'ACTIVO', '2025-07-26 12:03:15', '2025-07-27 12:57:19');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rutas`
--

CREATE TABLE `rutas` (
  `idrutas` int(11) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `descripcion` varchar(800) DEFAULT NULL,
  `estado` enum('ACTIVO','INACTIVO') DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rutas`
--

INSERT INTO `rutas` (`idrutas`, `nombre`, `descripcion`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'ABANCAY', 'Ruta hacia Abancay', 'ACTIVO', '2025-08-23 08:34:00', NULL),
(2, 'CUSCO', 'Ruta hacia Cusco', 'ACTIVO', '2025-08-23 08:34:10', NULL),
(3, 'CURAHUASI', 'Ruta solo hasta Curahuasi', 'ACTIVO', '2025-08-23 08:34:27', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `salidas_diarias`
--

CREATE TABLE `salidas_diarias` (
  `id_salidas_diarias` int(11) NOT NULL,
  `id_conductor` int(11) NOT NULL,
  `monto` decimal(8,2) DEFAULT NULL,
  `fecha_hora` datetime DEFAULT NULL,
  `id_origen` int(11) DEFAULT NULL,
  `id_destino` int(11) DEFAULT NULL,
  `id_cliente` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `observacion` text DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `estado` enum('SALIO','ELIMINADO','INCOMPLETO') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `series_comprobantes`
--

CREATE TABLE `series_comprobantes` (
  `id_serie` int(11) NOT NULL,
  `tipo_comprobante` enum('01','03','07','08') NOT NULL,
  `serie` varchar(4) NOT NULL,
  `correlativo_actual` int(11) DEFAULT 0,
  `correlativo_maximo` int(11) DEFAULT 99999999,
  `activa` tinyint(1) DEFAULT 1,
  `id_empresa` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `series_comprobantes`
--

INSERT INTO `series_comprobantes` (`id_serie`, `tipo_comprobante`, `serie`, `correlativo_actual`, `correlativo_maximo`, `activa`, `id_empresa`, `created_at`, `updated_at`) VALUES
(1, '01', 'F001', 0, 99999999, 1, 1, '2025-07-21 18:53:04', '2025-07-21 18:53:04'),
(2, '03', 'B001', 0, 99999999, 1, 1, '2025-07-21 18:53:04', '2025-07-21 18:53:04'),
(3, '07', 'FC01', 0, 99999999, 1, 1, '2025-07-21 18:53:04', '2025-07-21 18:53:04'),
(4, '07', 'BC01', 0, 99999999, 1, 1, '2025-07-21 18:53:04', '2025-07-21 18:53:04'),
(5, '08', 'FD01', 0, 99999999, 1, 1, '2025-07-21 18:53:04', '2025-07-21 18:53:04'),
(6, '08', 'BD01', 0, 99999999, 1, 1, '2025-07-21 18:53:04', '2025-07-21 18:53:04');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios`
--

CREATE TABLE `servicios` (
  `id_servicio` int(11) NOT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `costo` decimal(8,2) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `estado` enum('ACTIVO','INACTIVO') DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `servicios`
--

INSERT INTO `servicios` (`id_servicio`, `nombre`, `costo`, `descripcion`, `estado`, `created_at`, `updated_at`, `id_usuario`) VALUES
(1, 'SERVICIO DE ABANCAY - CUSCO', 50.00, 'Carros que van desde Abancay hasta cusco', 'ACTIVO', '2025-07-27 16:51:42', NULL, 1),
(2, 'SERVICIO DE CUSCO - ABANCAY', 50.00, 'Carros que van de Cusco - Abancay', 'ACTIVO', '2025-07-27 16:52:22', NULL, 1),
(3, 'ENCOMIENDAS', 30.00, 'ENVIO DE ENCOMIENDAS', 'ACTIVO', '2025-07-27 17:06:11', '2025-07-27 17:24:52', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sucursales`
--

CREATE TABLE `sucursales` (
  `id_sucursal` int(11) NOT NULL,
  `sucrusal` varchar(255) DEFAULT NULL,
  `telefono1` char(9) DEFAULT NULL,
  `telefono2` char(9) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `id_empresa` int(255) DEFAULT NULL,
  `estado` enum('ACTIVO','INACTIVO') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sucursales`
--

INSERT INTO `sucursales` (`id_sucursal`, `sucrusal`, `telefono1`, `telefono2`, `direccion`, `descripcion`, `created_at`, `updated_at`, `id_empresa`, `estado`) VALUES
(1, 'CUSCO', '983152886', '', 'PACHACUTEC S/N', 'SUCURSAL EN CUSCO', '2025-07-26 16:32:18', '2025-07-27 15:54:11', 1, 'ACTIVO'),
(2, 'ABANCAY', '983152885', '', 'PROLONGACIÓN HUANCAVELICA S/N', 'SUCURSAL EN ABANCAY', '2025-07-26 16:32:51', '2025-07-27 15:54:19', 1, 'ACTIVO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `dni_usuario` char(8) DEFAULT NULL,
  `usu_nombre` varchar(255) DEFAULT NULL,
  `usu_apellido` varchar(255) DEFAULT NULL,
  `usu_email` varchar(255) DEFAULT NULL,
  `usu_telefono` char(11) DEFAULT NULL,
  `usu_direccion` varchar(255) DEFAULT NULL,
  `usu_usuario` varchar(255) DEFAULT NULL,
  `usu_contrasenia` varchar(255) DEFAULT NULL,
  `id_role` int(11) DEFAULT NULL,
  `usu_estatus` enum('DESACTIVADO','ACTIVO') DEFAULT NULL,
  `usu_foto` varchar(500) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `dni_usuario`, `usu_nombre`, `usu_apellido`, `usu_email`, `usu_telefono`, `usu_direccion`, `usu_usuario`, `usu_contrasenia`, `id_role`, `usu_estatus`, `usu_foto`, `id_sucursal`, `created_at`, `updated_at`) VALUES
(1, '72646121', 'JERSSON', 'CORILLA MIRANDA', 'jersson1@gmail.com', '974031312', 'AV. PERÚ N° 323', 'jersson', '$2y$12$LfYcbb0t9NbbspTrbweeSu2M36w0P6jvwf5nU6YenKTfoCNK.ckTe', 1, 'ACTIVO', 'controller/usuario/fotos/IMG1-3-2025-12-51.jpg', 2, '2025-01-18 14:56:34', '2025-03-01 12:48:47'),
(2, '15155115', 'ESTEFANY', 'CHAVEZ PEDRAZA', 'estefany2025@gmail.com', '9511515155', 'AV. PERÚ N° 323', 'ESTEFANY2025', '$2y$12$4hHkLyuAcnD4QHgPOOPs2.trZVKC4Br3P6vamRqOVYUPibEjsoUzW', 1, 'ACTIVO', 'controller/usuario/fotos/IMG1-3-2025-12-451.jpeg', 2, '2025-01-18 15:43:46', '2025-03-01 14:49:43'),
(3, '23355655', 'GONZALO', 'JORDAN', 'gonzalo2025@gmail.com', '921118588', 'AV. PERÚ N° 111', 'GONZALO2025', '$2y$12$LfYcbb0t9NbbspTrbweeSu2M36w0P6jvwf5nU6YenKTfoCNK.ckTe', 2, 'ACTIVO', 'controller/usuario/fotos/IMG27-7-2025-16-410.jpeg', 2, '2025-01-25 00:00:00', '2025-07-27 16:41:24'),
(4, '55445454', 'JAVIER', 'DAMIAN CHIPA', 'javier21@gmail.com', '92122002202', 'JR. CUSCO N° 23', 'JAVIER2025', '$2y$12$LfYcbb0t9NbbspTrbweeSu2M36w0P6jvwf5nU6YenKTfoCNK.ckTe', 1, 'ACTIVO', 'controller/usuario/fotos/IMG1-3-2025-12-683.jpg', 1, '2025-01-25 00:00:00', '2025-03-01 12:51:51'),
(5, '15155115', 'SANDRO', 'CHAVEZ LOAYZA', 'sandro21@gmail.com', '9511515155', 'AV. PERÚ N° 323', 'sandro2025', '$2y$12$LfYcbb0t9NbbspTrbweeSu2M36w0P6jvwf5nU6YenKTfoCNK.ckTe', 2, 'ACTIVO', 'controller/usuario/fotos/IMG1-3-2025-12-100.jpg', 1, '2025-01-25 09:57:53', '2025-03-01 12:39:29'),
(6, '66663222', 'JIMENA', 'PEDRAZA', 'jimena12@gmail.com', '9211100000', 'JR. AREQUIPA N° 233', 'JIMENA2025', '$2y$12$LfYcbb0t9NbbspTrbweeSu2M36w0P6jvwf5nU6YenKTfoCNK.ckTe', 1, 'ACTIVO', 'controller/usuario/fotos/IMG1-3-2025-12-947.jpeg', 1, '2025-01-25 11:19:45', '2025-03-01 12:52:32'),
(7, '66226226', 'JUANA', 'CHAVEZ', 'juana12@gmail.com', '92262662', 'JR. CUSCO N° 321', 'JUANA2025', '$2y$12$jv9br.jav/dRWEKf5TFSpuB8UUas4.voLfT2cCQOWv5v8WMdrM4dK', 1, 'ACTIVO', 'controller/usuario/fotos/IMG1-3-2025-12-755.jpeg', 1, '2025-03-01 12:43:15', '2025-03-01 12:43:15'),
(8, '62626266', 'DANIEL', 'CHAVEZ HUAMAN', 'daniel12@gmail.com', '9616216515', 'JR. AREQUIPA N° 323', 'daniel2025', '$2y$12$6gABxP4qvZo3GLMHBe77BeXtN/qhiYlqgDDPzF/HqgqFMSz49Bzji', 2, 'ACTIVO', 'controller/usuario/fotos/IMG1-3-2025-12-3.jpg', 1, '2025-03-01 12:46:46', '2025-03-01 12:49:04'),
(9, '72551154', 'ANABELL', 'CHAVEZ PEÑA', 'anabell12@gmail.com', '974484848', 'JR. CANADA', 'anabell2025', '12345678', 2, 'ACTIVO', 'controller/usuario/fotos/IMG27-7-2025-16-223.jpg', 1, '2025-07-27 16:28:34', '2025-07-27 16:42:08'),
(10, '26611151', 'MAGDA', 'PEÑA CHAVEZ', 'magda12@gmail.com', '985774777', 'JR. CUSCO', 'magda2025', '$2y$12$NwZxVXvuV3WFq1Uxd0U4wOe8Wp85c6E9jPKo5ZwpcWAf4TrSaYR1a', 1, 'ACTIVO', 'controller/usuario/fotos/', 1, '2025-07-27 16:29:57', '2025-07-27 16:29:57');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `catalogo_sunat`
--
ALTER TABLE `catalogo_sunat`
  ADD PRIMARY KEY (`id_catalogo`),
  ADD UNIQUE KEY `unique_codigo_catalogo` (`numero_catalogo`,`codigo`),
  ADD KEY `idx_catalogo` (`numero_catalogo`);

--
-- Indices de la tabla `choferes`
--
ALTER TABLE `choferes`
  ADD PRIMARY KEY (`id_chofer`),
  ADD KEY `fk_usu_chofe` (`id_usuario`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id_cliente`);

--
-- Indices de la tabla `comprobantes`
--
ALTER TABLE `comprobantes`
  ADD PRIMARY KEY (`id_comprobante`),
  ADD UNIQUE KEY `unique_comprobante` (`tipo_comprobante`,`serie`,`correlativo`),
  ADD KEY `id_servicio` (`id_servicio`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `idx_fecha_emision` (`fecha_emision`),
  ADD KEY `idx_cliente` (`id_cliente`),
  ADD KEY `idx_estado` (`estado_sunat`);

--
-- Indices de la tabla `comprobante_detalle`
--
ALTER TABLE `comprobante_detalle`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `idx_comprobante` (`id_comprobante`);

--
-- Indices de la tabla `empresa`
--
ALTER TABLE `empresa`
  ADD PRIMARY KEY (`id_empresa`);

--
-- Indices de la tabla `encomiendas`
--
ALTER TABLE `encomiendas`
  ADD PRIMARY KEY (`id_encomienda`),
  ADD KEY `fk_conductor` (`id_conductor`),
  ADD KEY `fk_usu_enco` (`id_usuario`),
  ADD KEY `fk_clie_encomienda` (`id_cliente_emisor`),
  ADD KEY `fk_cli2_encomi` (`id_cliente_receptor`),
  ADD KEY `fk_origen_enco_idx` (`id_origen`),
  ADD KEY `fk_destino_enco_idx` (`id_destino`);

--
-- Indices de la tabla `gastos`
--
ALTER TABLE `gastos`
  ADD PRIMARY KEY (`id_gastos`),
  ADD KEY `fk_indi_gas` (`id_indicador`),
  ADD KEY `fk_usu_indi` (`id_user`);

--
-- Indices de la tabla `indicadores`
--
ALTER TABLE `indicadores`
  ADD PRIMARY KEY (`id_indicador`),
  ADD KEY `dk_id_usu` (`id_usuario`);

--
-- Indices de la tabla `ingresos`
--
ALTER TABLE `ingresos`
  ADD PRIMARY KEY (`id_ingreso`),
  ADD KEY `fk_salida_ingre` (`id_salidas_diarias`),
  ADD KEY `fk_enco_ingre` (`id_encomiendas`),
  ADD KEY `dk_indi_ingre` (`id_indicador`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_role`),
  ADD KEY `rol` (`rol`);

--
-- Indices de la tabla `rutas`
--
ALTER TABLE `rutas`
  ADD PRIMARY KEY (`idrutas`);

--
-- Indices de la tabla `salidas_diarias`
--
ALTER TABLE `salidas_diarias`
  ADD PRIMARY KEY (`id_salidas_diarias`),
  ADD KEY `fk_conductor1` (`id_conductor`),
  ADD KEY `fk_cli_sali1` (`id_cliente`),
  ADD KEY `fk_usu_sali` (`id_usuario`),
  ADD KEY `fk_id_origin_idx` (`id_origen`),
  ADD KEY `fk_id_desti_idx` (`id_destino`);

--
-- Indices de la tabla `series_comprobantes`
--
ALTER TABLE `series_comprobantes`
  ADD PRIMARY KEY (`id_serie`),
  ADD UNIQUE KEY `unique_serie` (`tipo_comprobante`,`serie`,`id_empresa`),
  ADD KEY `id_empresa` (`id_empresa`);

--
-- Indices de la tabla `servicios`
--
ALTER TABLE `servicios`
  ADD PRIMARY KEY (`id_servicio`),
  ADD KEY `fk_usu_servi` (`id_usuario`);

--
-- Indices de la tabla `sucursales`
--
ALTER TABLE `sucursales`
  ADD PRIMARY KEY (`id_sucursal`),
  ADD KEY `fk_empresa` (`id_empresa`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD KEY `fk_empresa` (`id_sucursal`),
  ADD KEY `dk212` (`id_role`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `catalogo_sunat`
--
ALTER TABLE `catalogo_sunat`
  MODIFY `id_catalogo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `choferes`
--
ALTER TABLE `choferes`
  MODIFY `id_chofer` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `comprobantes`
--
ALTER TABLE `comprobantes`
  MODIFY `id_comprobante` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `comprobante_detalle`
--
ALTER TABLE `comprobante_detalle`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `empresa`
--
ALTER TABLE `empresa`
  MODIFY `id_empresa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `encomiendas`
--
ALTER TABLE `encomiendas`
  MODIFY `id_encomienda` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `gastos`
--
ALTER TABLE `gastos`
  MODIFY `id_gastos` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `indicadores`
--
ALTER TABLE `indicadores`
  MODIFY `id_indicador` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `ingresos`
--
ALTER TABLE `ingresos`
  MODIFY `id_ingreso` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_role` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `rutas`
--
ALTER TABLE `rutas`
  MODIFY `idrutas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `salidas_diarias`
--
ALTER TABLE `salidas_diarias`
  MODIFY `id_salidas_diarias` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `series_comprobantes`
--
ALTER TABLE `series_comprobantes`
  MODIFY `id_serie` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `servicios`
--
ALTER TABLE `servicios`
  MODIFY `id_servicio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `sucursales`
--
ALTER TABLE `sucursales`
  MODIFY `id_sucursal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `choferes`
--
ALTER TABLE `choferes`
  ADD CONSTRAINT `fk_usu_chofe` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `comprobantes`
--
ALTER TABLE `comprobantes`
  ADD CONSTRAINT `comprobantes_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`),
  ADD CONSTRAINT `comprobantes_ibfk_2` FOREIGN KEY (`id_servicio`) REFERENCES `servicios` (`id_servicio`),
  ADD CONSTRAINT `comprobantes_ibfk_3` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `comprobante_detalle`
--
ALTER TABLE `comprobante_detalle`
  ADD CONSTRAINT `comprobante_detalle_ibfk_1` FOREIGN KEY (`id_comprobante`) REFERENCES `comprobantes` (`id_comprobante`) ON DELETE CASCADE;

--
-- Filtros para la tabla `encomiendas`
--
ALTER TABLE `encomiendas`
  ADD CONSTRAINT `fk_cli2_encomi` FOREIGN KEY (`id_cliente_receptor`) REFERENCES `clientes` (`id_cliente`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_clie_encomienda` FOREIGN KEY (`id_cliente_emisor`) REFERENCES `clientes` (`id_cliente`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_conductor` FOREIGN KEY (`id_conductor`) REFERENCES `choferes` (`id_chofer`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_id_des` FOREIGN KEY (`id_destino`) REFERENCES `rutas` (`idrutas`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_id_ori` FOREIGN KEY (`id_destino`) REFERENCES `rutas` (`idrutas`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_usu_enco` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `gastos`
--
ALTER TABLE `gastos`
  ADD CONSTRAINT `fk_indi_gas` FOREIGN KEY (`id_indicador`) REFERENCES `indicadores` (`id_indicador`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `indicadores`
--
ALTER TABLE `indicadores`
  ADD CONSTRAINT `dk_id_usu` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `ingresos`
--
ALTER TABLE `ingresos`
  ADD CONSTRAINT `dk_indi_ingre` FOREIGN KEY (`id_indicador`) REFERENCES `indicadores` (`id_indicador`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_enco_ingre` FOREIGN KEY (`id_encomiendas`) REFERENCES `encomiendas` (`id_encomienda`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_salida_ingre` FOREIGN KEY (`id_salidas_diarias`) REFERENCES `salidas_diarias` (`id_salidas_diarias`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `salidas_diarias`
--
ALTER TABLE `salidas_diarias`
  ADD CONSTRAINT `fk_cli_sali1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_conductor1` FOREIGN KEY (`id_conductor`) REFERENCES `choferes` (`id_chofer`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_id_desti` FOREIGN KEY (`id_destino`) REFERENCES `rutas` (`idrutas`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_id_origin` FOREIGN KEY (`id_origen`) REFERENCES `rutas` (`idrutas`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_usu_sali` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `series_comprobantes`
--
ALTER TABLE `series_comprobantes`
  ADD CONSTRAINT `series_comprobantes_ibfk_1` FOREIGN KEY (`id_empresa`) REFERENCES `empresa` (`id_empresa`);

--
-- Filtros para la tabla `servicios`
--
ALTER TABLE `servicios`
  ADD CONSTRAINT `fk_usu_servi` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `sucursales`
--
ALTER TABLE `sucursales`
  ADD CONSTRAINT `fk_empresa` FOREIGN KEY (`id_empresa`) REFERENCES `empresa` (`id_empresa`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `dk212` FOREIGN KEY (`id_role`) REFERENCES `roles` (`id_role`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_sucur` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`) ON DELETE NO ACTION ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
