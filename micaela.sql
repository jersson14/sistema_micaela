-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3307
-- Tiempo de generación: 22-09-2025 a las 00:14:18
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
CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_ANULAR_GASTOS` (IN `ID` INT, IN `DESCRIP` VARCHAR(255), IN `USU` INT)   UPDATE gastos
SET
id_user=USU,
motivo_anulacion=DESCRIP,
fecha_anulacion=NOW(),
estado='ANULADO'

WHERE gastos.id_gastos=ID$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_BUSCAR_PERSONA_POR_DOCUMENTO` (IN `documento` CHAR(200))   SELECT
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
clientes.created_at,
clientes.updated_at
FROM
clientes
where clientes.nro_documento=documento$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_CARGAR_INDICADORES` ()   SELECT
indicadores.id_indicador,
indicadores.nombres
FROM
indicadores
WHERE indicadores.estado='ACTIVO' and indicadores.tipo_indicador='GASTOS'$$

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

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_ELIMINAR_ENCOMIENDA` (IN `ID` INT)   DELETE FROM encomiendas WHERE encomiendas.id_encomienda=ID$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_ELIMINAR_INDICADOR` (IN `ID` INT)   DELETE FROM indicadores where indicadores.id_indicador=ID$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_ELIMINAR_ROL` (IN `ID` INT)   DELETE FROM roles WHERE roles.id_role=ID$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_ELIMINAR_RUTA` (IN `ID` INT)   DELETE FROM rutas WHERE rutas.idrutas=ID$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_ELIMINAR_SALIDA_DIARIA` (IN `ID` INT)   UPDATE salidas_diarias
SET
salidas_diarias.estado='ELIMINADO'
WHERE salidas_diarias.id_salidas_diarias=ID$$

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

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_LISTAR_DIFERENCIA` ()   BEGIN
    DECLARE fecha_inicio DATE;
    DECLARE fecha_fin DATE;
    DECLARE totalgasto DECIMAL(8,2);
    DECLARE totalingresos DECIMAL(8,2);
    DECLARE total DECIMAL(8,2);
    -- Definir las fechas de inicio y fin como la fecha actual
    SET fecha_inicio = CURDATE();
    SET fecha_fin = CURDATE();
    -- Calcular el total de gastos
    SELECT IFNULL(SUM(monto), 0) 
    INTO totalgasto
    FROM gastos 
    WHERE DATE(created_at) BETWEEN fecha_inicio AND fecha_fin AND estado = 'VALIDO';
    -- Calcular el total de ingresos
    SELECT IFNULL(SUM(monto_total), 0) 
    INTO totalingresos
    FROM ingresos 
    WHERE DATE(created_at) BETWEEN fecha_inicio AND fecha_fin AND estado = 'VALIDO';
    -- Calcular la diferencia entre ingresos y gastos
    SET total = totalingresos - totalgasto;
    -- Devolver el resultado
    SELECT 
        DATE_FORMAT(fecha_inicio, "%d-%m-%Y") AS FechaInicial,
        DATE_FORMAT(fecha_fin, "%d-%m-%Y") AS FechaFinal,
        CONCAT('S/. ', FORMAT(totalingresos, 2)) AS TotalIngresos,
        CONCAT('S/. ', FORMAT(totalgasto, 2)) AS TotalGastos,
        total AS DiferenciaNum,  -- Número sin formato
        CONCAT('S/. ', FORMAT(total, 2)) AS Diferencia; -- String formateado
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_LISTAR_DIFERENCIA_FILTRO` (IN `FECHAINI` DATE, IN `FECHAFIN` DATE)   BEGIN

    DECLARE totalgasto DECIMAL(8,2);
    DECLARE totalingresos DECIMAL(8,2);
    DECLARE total DECIMAL(8,2);



    -- Calcular el total de gastos
    SELECT IFNULL(SUM(monto), 0) 
    INTO totalgasto
    FROM gastos 
    WHERE DATE(created_at) BETWEEN FECHAINI AND FECHAFIN AND estado = 'VALIDO';

    -- Calcular el total de ingresos
    SELECT IFNULL(SUM(monto_total), 0) 
    INTO totalingresos
    FROM ingresos 
    WHERE DATE(created_at) BETWEEN FECHAINI AND FECHAFIN AND estado = 'VALIDO';

    -- Calcular la diferencia entre ingresos y gastos
    SET total = totalingresos - totalgasto;

    -- Devolver el resultado
    SELECT 
        DATE_FORMAT(FECHAINI, "%d-%m-%Y") AS FechaInicial,
        DATE_FORMAT(FECHAFIN, "%d-%m-%Y") AS FechaFinal,
        CONCAT('S/. ', FORMAT(totalingresos, 2)) AS TotalIngresos,
        CONCAT('S/. ', FORMAT(totalgasto, 2)) AS TotalGastos,
        CONCAT('S/. ', FORMAT(total, 2)) AS Diferencia;
END$$

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
    ON rutas_destino.idrutas = encomiendas.id_destino
		
ORDER BY encomiendas.fecha_hora DESC$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_LISTAR_ENCOMIENDAS_FECHA_USUARIO` (IN `FECHAINI` DATE, IN `FECHAHAS` DATE, IN `USU` INT)   BEGIN
    SELECT
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
        ON rutas_destino.idrutas = encomiendas.id_destino
    WHERE 
        DATE(encomiendas.fecha_hora) BETWEEN FECHAINI AND FECHAHAS OR encomiendas.id_usuario=USU
    ORDER BY encomiendas.fecha_hora DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_LISTAR_ENCOMIENDAS_RUTA_ESTADO` (IN `ORI` INT, IN `DES` INT, IN `ESTA` VARCHAR(20))   BEGIN
    SELECT
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
        ON rutas_destino.idrutas = encomiendas.id_destino
    WHERE 
        (ORI IS NULL OR ORI = 0 OR encomiendas.id_origen = ORI) 
        AND 
        (DES IS NULL OR DES = 0 OR encomiendas.id_destino = DES) 
        AND 
        (ESTA IS NULL OR ESTA = '' OR encomiendas.estado_encomienda = ESTA)
    ORDER BY encomiendas.fecha_hora DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_LISTAR_GASTOS` ()   SELECT
    gastos.id_gastos,
    gastos.id_indicador,
    gastos.id_user,
    gastos.cantidad,
    gastos.monto,
    gastos.observacion,
    gastos.estado,
    gastos.motivo_anulacion,
    gastos.fecha_anulacion,
    DATE_FORMAT(gastos.fecha_anulacion, "%d-%m-%Y") AS fecha_anulada,
    gastos.created_at,
    DATE_FORMAT(gastos.created_at, "%d-%m-%Y - %H:%i:%s") AS fecha_formateada,
    gastos.updated_at,
    DATE_FORMAT(gastos.updated_at, "%d-%m-%Y - %H:%i:%s") AS fecha_formateada2,
    usuario.usu_usuario,
    CONCAT_WS(' ', usuario.usu_nombre, usuario.usu_apellido) AS USUARIO,
    indicadores.tipo_indicador,
    indicadores.nombres
FROM gastos 
INNER JOIN indicadores ON gastos.id_indicador = indicadores.id_indicador 
INNER JOIN usuario ON gastos.id_user = usuario.id_usuario
WHERE DATE(gastos.created_at) = CURDATE()$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_LISTAR_GASTOS_FECHAS` (IN `IDINDI` INT, IN `FECHAINI` DATE, IN `FECHAFIN` DATE)   SELECT
    gastos.id_gastos,
    gastos.id_indicador,
    gastos.id_user,
    gastos.cantidad,
    gastos.monto,
    gastos.observacion,
    gastos.estado,
    gastos.motivo_anulacion,
    gastos.fecha_anulacion,
    DATE_FORMAT(gastos.fecha_anulacion, "%d-%m-%Y") AS fecha_anulada,
    gastos.created_at,
    DATE_FORMAT(gastos.created_at, "%d-%m-%Y - %H:%i:%s") AS fecha_formateada,
    gastos.updated_at,
    DATE_FORMAT(gastos.updated_at, "%d-%m-%Y - %H:%i:%s") AS fecha_formateada2,
    usuario.usu_usuario,
    CONCAT_WS(' ', usuario.usu_nombre, usuario.usu_apellido) AS USUARIO,
    indicadores.tipo_indicador,
    indicadores.nombres
FROM gastos 
INNER JOIN indicadores ON gastos.id_indicador = indicadores.id_indicador 
INNER JOIN usuario ON gastos.id_user = usuario.id_usuario
WHERE indicadores.id_indicador=IDINDI OR DATE(gastos.created_at) BETWEEN FECHAINI AND FECHAFIN$$

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

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_LISTAR_SALIDAS_DIARIAS` ()   SELECT
  s.id_salidas_diarias,
	s.salida_nro,
  s.id_conductor,
  s.monto,
  s.fecha_hora,
  DATE_FORMAT(s.fecha_hora, "%d-%m-%Y - %H:%i:%s") AS fecha_formateada_salida,

  s.id_origen,
  s.id_destino,
	s.total_pasajeros,
	s.total_encomiendas,
  s.created_at,
  DATE_FORMAT(s.created_at, "%d-%m-%Y - %H:%i:%s") AS fecha_formateada_creado,

  s.updated_at,
  DATE_FORMAT(s.updated_at, "%d-%m-%Y - %H:%i:%s") AS fecha_formateada_actualizado,

  s.observacion,
  s.id_usuario,
  s.estado,

  -- ORIGEN Y DESTINO BIEN DEFINIDOS
  r_origen.nombre    AS origen_nombre,
  r_origen.descripcion AS origen_descripcion,
  r_destino.nombre   AS destino_nombre,
  r_destino.descripcion AS destino_descripcion,

  -- DATOS DEL CHOFER
  c.tipo_documen,
  c.nro_doc,
  c.nombres_apellidos,
  c.celular,

  -- DATOS DEL USUARIO (CONCATENADO)
  u.dni_usuario,
  CONCAT(u.usu_nombre, ' ', u.usu_apellido) AS usuario_nombre_completo

FROM salidas_diarias s
INNER JOIN rutas r_origen  ON s.id_origen  = r_origen.idrutas
INNER JOIN rutas r_destino ON s.id_destino = r_destino.idrutas
INNER JOIN choferes c      ON s.id_conductor = c.id_chofer
INNER JOIN usuario u       ON s.id_usuario   = u.id_usuario
WHERE NOT s.estado ='ELIMINADO'
ORDER BY s.fecha_hora DESC$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_LISTAR_SALIDAS_FECHA_USUARIO` (IN `FECHAINI` DATE, IN `FECHAHAS` DATE, IN `USU` INT)   SELECT
  s.id_salidas_diarias,
	s.salida_nro,
  s.id_conductor,
  s.monto,
  s.fecha_hora,
  DATE_FORMAT(s.fecha_hora, "%d-%m-%Y - %H:%i:%s") AS fecha_formateada_salida,

  s.id_origen,
  s.id_destino,
	s.total_pasajeros,
	s.total_encomiendas,
  s.created_at,
  DATE_FORMAT(s.created_at, "%d-%m-%Y - %H:%i:%s") AS fecha_formateada_creado,

  s.updated_at,
  DATE_FORMAT(s.updated_at, "%d-%m-%Y - %H:%i:%s") AS fecha_formateada_actualizado,

  s.observacion,
  s.id_usuario,
  s.estado,

  -- ORIGEN Y DESTINO BIEN DEFINIDOS
  r_origen.nombre    AS origen_nombre,
  r_origen.descripcion AS origen_descripcion,
  r_destino.nombre   AS destino_nombre,
  r_destino.descripcion AS destino_descripcion,

  -- DATOS DEL CHOFER
  c.tipo_documen,
  c.nro_doc,
  c.nombres_apellidos,
  c.celular,

  -- DATOS DEL USUARIO (CONCATENADO)
  u.dni_usuario,
  CONCAT(u.usu_nombre, ' ', u.usu_apellido) AS usuario_nombre_completo

FROM salidas_diarias s
INNER JOIN rutas r_origen  ON s.id_origen  = r_origen.idrutas
INNER JOIN rutas r_destino ON s.id_destino = r_destino.idrutas
INNER JOIN choferes c      ON s.id_conductor = c.id_chofer
INNER JOIN usuario u       ON s.id_usuario   = u.id_usuario
WHERE 
        DATE(s.fecha_hora) BETWEEN FECHAINI AND FECHAHAS OR s.id_usuario=USU
    ORDER BY s.fecha_hora DESC$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_LISTAR_SALIDAS_RUTA_ESTADO` (IN `ORI` INT, IN `DES` INT, IN `ESTA` VARCHAR(20))   SELECT
  s.id_salidas_diarias,
	s.salida_nro,
  s.id_conductor,
  s.monto,
  s.fecha_hora,
  DATE_FORMAT(s.fecha_hora, "%d-%m-%Y - %H:%i:%s") AS fecha_formateada_salida,

  s.id_origen,
  s.id_destino,
	s.total_pasajeros,
	s.total_encomiendas,
  s.created_at,
  DATE_FORMAT(s.created_at, "%d-%m-%Y - %H:%i:%s") AS fecha_formateada_creado,

  s.updated_at,
  DATE_FORMAT(s.updated_at, "%d-%m-%Y - %H:%i:%s") AS fecha_formateada_actualizado,

  s.observacion,
  s.id_usuario,
  s.estado,

  -- ORIGEN Y DESTINO BIEN DEFINIDOS
  r_origen.nombre    AS origen_nombre,
  r_origen.descripcion AS origen_descripcion,
  r_destino.nombre   AS destino_nombre,
  r_destino.descripcion AS destino_descripcion,

  -- DATOS DEL CHOFER
  c.tipo_documen,
  c.nro_doc,
  c.nombres_apellidos,
  c.celular,

  -- DATOS DEL USUARIO (CONCATENADO)
  u.dni_usuario,
  CONCAT(u.usu_nombre, ' ', u.usu_apellido) AS usuario_nombre_completo

FROM salidas_diarias s
INNER JOIN rutas r_origen  ON s.id_origen  = r_origen.idrutas
INNER JOIN rutas r_destino ON s.id_destino = r_destino.idrutas
INNER JOIN choferes c      ON s.id_conductor = c.id_chofer
INNER JOIN usuario u       ON s.id_usuario   = u.id_usuario
WHERE 
        (ORI IS NULL OR ORI = 0 OR s.id_origen = ORI) 
        AND 
        (DES IS NULL OR DES = 0 OR s.id_destino = DES) 
        AND 
        (ESTA IS NULL OR ESTA = '' OR s.estado = ESTA)
ORDER BY s.fecha_hora DESC$$

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

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_LISTAR_TOTAL_CHOFERES` ()   SELECT 
COUNT(id_chofer)as TOTAL_CHOFERES
FROM choferes$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_LISTAR_TOTAL_CLIENTES` ()   SELECT 
COUNT(id_cliente)as TOTAL_CLIENTES
FROM clientes$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_LISTAR_TOTAL_ENCOMIENDAS` ()   BEGIN
    SELECT 
        COUNT(id_encomienda) AS TOTAL_ENCOMIENDAS_MES
    FROM encomiendas;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_LISTAR_TOTAL_ENCOMIENDAS_DIARIAS` ()   SELECT 
COUNT(id_encomienda)as TOTAL_ENCOMIENDAS_DIA
FROM encomiendas
WHERE DATE(encomiendas.fecha_hora) = CURDATE()$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_LISTAR_TOTAL_ENCOMIENDAS_MES` ()   BEGIN
    SELECT 
        COUNT(id_encomienda) AS TOTAL_ENCOMIENDAS_MES
    FROM encomiendas
    WHERE YEAR(fecha_hora) = YEAR(CURDATE())
      AND MONTH(fecha_hora) = MONTH(CURDATE());
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_LISTAR_TOTAL_ENCOMIENDAS_SEMANALES` ()   BEGIN
    SELECT 
        COUNT(id_encomienda) AS TOTAL_ENCOMIENDAS_SEMANA
    FROM encomiendas
    WHERE YEARWEEK(encomiendas.fecha_hora, 1) = YEARWEEK(CURDATE(), 1);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_LISTAR_TOTAL_GASTOS_HOY` ()   BEGIN
    SELECT 
        CONCAT('S/ ',SUM(gastos.monto)) AS TOTAL_GASTOS_HOY
    FROM gastos
    WHERE DATE(gastos.created_at) =CURDATE() AND NOT gastos.estado ='ANULADO';
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_LISTAR_TOTAL_GASTOS_MES` ()   BEGIN
    SELECT 
        CONCAT('S/ ', FORMAT(SUM(gastos.monto), 2)) AS TOTAL_GASTOS_MES
    FROM gastos
    WHERE YEAR(gastos.created_at) = YEAR(CURDATE())
      AND MONTH(gastos.created_at) = MONTH(CURDATE()) AND NOT gastos.estado='ANULADO';
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_LISTAR_TOTAL_INGRESOS_HOY` ()   BEGIN
    SELECT 
        CONCAT('S/ ',SUM(ingresos.monto_total)) AS TOTAL_INGRESOS_HOY
    FROM ingresos
    WHERE DATE(ingresos.created_at) =CURDATE();
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_LISTAR_TOTAL_INGRESOS_MES` ()   BEGIN
    SELECT 
        CONCAT('S/ ', FORMAT(SUM(ingresos.monto_total), 2)) AS TOTAL_INGRESOS_MES
    FROM ingresos
    WHERE YEAR(ingresos.created_at) = YEAR(CURDATE())
      AND MONTH(ingresos.created_at) = MONTH(CURDATE());
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_LISTAR_TOTAL_SALIDAS` ()   BEGIN
    SELECT 
        COUNT(id_salidas_diarias) AS TOTAL_SALIDAS
    FROM salidas_diarias;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_LISTAR_TOTAL_SALIDAS_DIA` ()   BEGIN
    SELECT 
        COUNT(id_salidas_diarias) AS TOTAL_ENCOMIENDAS_DIA
    FROM salidas_diarias
    WHERE DATE(salidas_diarias.fecha_hora) =CURDATE();
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_LISTAR_TOTAL_SALIDAS_MES` ()   BEGIN
    SELECT 
        COUNT(id_salidas_diarias) AS TOTAL_SALIDAS_MES
    FROM salidas_diarias
    WHERE YEAR(fecha_hora) = YEAR(CURDATE())
      AND MONTH(fecha_hora) = MONTH(CURDATE());
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_LISTAR_TOTAL_SALIDAS_SEMANA` ()   BEGIN
    SELECT 
        COUNT(id_salidas_diarias) AS TOTAL_SALIDAS_SEMANA
    FROM salidas_diarias
    WHERE YEARWEEK(fecha_hora, 1) = YEARWEEK(CURDATE(), 1);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_LISTAR_TOTAL_SERVICIOS` ()   SELECT 
COUNT(id_servicio)as TOTAL_SERVICIOS
FROM servicios$$

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

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_LISTA_HISTORIAL_ESTADOS` (IN `ID` INT)   SELECT
    historial_estados.id_historial_estado,
    historial_estados.id_encomienda,
    historial_estados.estado,
    historial_estados.observacion,
    historial_estados.precio_anterior,
    historial_estados.precio_nuevi,
    historial_estados.motivo_anula,
    historial_estados.fecha_anula,
    DATE_FORMAT(historial_estados.fecha_anula, "%d-%m-%Y - %H:%i:%s") as fecha_formateada,
    historial_estados.created_at,
    DATE_FORMAT(historial_estados.created_at, "%d-%m-%Y - %H:%i:%s") as fecha_formateada2,
    historial_estados.idusu,
    usuario.usu_nombre,
    usuario.usu_apellido,
    CONCAT_WS(' ', usuario.usu_nombre, usuario.usu_apellido) AS USUARIO
FROM
    historial_estados
    INNER JOIN encomiendas ON historial_estados.id_encomienda = encomiendas.id_encomienda
    INNER JOIN usuario ON historial_estados.idusu = usuario.id_usuario  -- Cambio aquí
WHERE 
    historial_estados.id_encomienda = ID$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_LISTA_HISTORIAL_ESTADOS_SALIDA` (IN `ID` INT)   BEGIN
    SELECT
        h.id_historial_salida, 
        h.id_salida, 
        h.estado, 
        h.observacion, 
        h.usu, 
        h.created, 
        DATE_FORMAT(h.created, "%d-%m-%Y - %H:%i:%s") AS fecha_formateada,
        u.usu_nombre, 
        u.usu_apellido,
        CONCAT_WS(' ', u.usu_nombre, u.usu_apellido) AS USUARIO
    FROM historial_salidas_diarias h
    INNER JOIN usuario u 
        ON u.id_usuario = h.usu
    WHERE h.id_salida = ID;
END$$

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

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_MODIFICAR_ENCOMIENDA` (IN `ID` INT, IN `CONDUC` INT, IN `IDORI` INT, IN `IDDES` INT, IN `FECHA` DATETIME, IN `DESCR` TEXT, IN `TIPODOCEMI` VARCHAR(255), IN `NRODCOEMI` CHAR(20), IN `NOMEMI` VARCHAR(255), IN `CELEMI` CHAR(9), IN `TIPODOCRECE` VARCHAR(255), IN `NRODOCRECEP` CHAR(20), IN `NOMRECEP` VARCHAR(255), IN `CELERECEP` CHAR(9), IN `PAG` DECIMAL(8,2), IN `PORPAGAR` DECIMAL(8,2), IN `ADOMICILIO` DECIMAL(8,2), IN `OBSERVA` TEXT, IN `USU` INT)   BEGIN
    DECLARE Contar INT;
    DECLARE proce1 VARCHAR(25);
    DECLARE proce2 VARCHAR(25);
		DECLARE esta VARCHAR(25);
    DECLARE IDEMI INT;
    DECLARE IDRECEP INT;
    DECLARE estado_pago_calc VARCHAR(20);
    DECLARE existe_emisor INT DEFAULT 0;
    DECLARE existe_receptor INT DEFAULT 0;
    
    -- Obtener nombres de rutas
    SET proce1 = (SELECT rutas.nombre FROM rutas WHERE rutas.idrutas = IDORI);
    SET proce2 = (SELECT rutas.nombre FROM rutas WHERE rutas.idrutas = IDDES);
		-- OBTENER ESTADO
    SET esta = (SELECT encomiendas.estado_encomienda FROM encomiendas WHERE encomiendas.id_encomienda = ID);

    -- VALIDACIÓN 1: Verificar si el cliente emisor ya existe
    SELECT COUNT(*), IFNULL(MAX(clientes.id_cliente), 0) 
    INTO existe_emisor, IDEMI
    FROM clientes 
    WHERE tipo_documento = TIPODOCEMI AND nro_documento = NRODCOEMI;
    
    IF existe_emisor > 0 THEN
        -- Cliente emisor existe, actualizar datos
        UPDATE clientes 
        SET nombre_completo = NOMEMI,
            procedencia = proce1,
            celular = CELEMI,
            updated_at = NOW()
        WHERE tipo_documento = TIPODOCEMI AND nro_documento = NRODCOEMI;
    ELSE
        -- Cliente emisor no existe, insertar nuevo
        INSERT INTO clientes(tipo_documento, nro_documento, nombre_completo, procedencia, celular, direccion, email, created_at)
        VALUES(TIPODOCEMI, NRODCOEMI, NOMEMI, proce1, CELEMI, '', '', NOW());
        
        SET IDEMI = LAST_INSERT_ID();
    END IF;
    
    -- VALIDACIÓN 1: Verificar si el cliente receptor ya existe
    SELECT COUNT(*), IFNULL(MAX(clientes.id_cliente), 0) 
    INTO existe_receptor, IDRECEP
    FROM clientes 
    WHERE tipo_documento = TIPODOCRECE AND nro_documento = NRODOCRECEP;
    
    IF existe_receptor > 0 THEN
        -- Cliente receptor existe, actualizar datos
        UPDATE clientes 
        SET nombre_completo = NOMRECEP,
            procedencia = proce2,
            celular = CELERECEP,
            updated_at = NOW()
        WHERE tipo_documento = TIPODOCRECE AND nro_documento = NRODOCRECEP;
    ELSE
        -- Cliente receptor no existe, insertar nuevo
        INSERT INTO clientes(tipo_documento, nro_documento, nombre_completo, procedencia, celular, direccion, email, created_at)
        VALUES(TIPODOCRECE, NRODOCRECEP, NOMRECEP, proce2, CELERECEP, '', '', NOW());
        
        SET IDRECEP = LAST_INSERT_ID();
    END IF;
    
    -- Verificar si ya existe una encomienda con los mismos datos
    SET Contar = (SELECT COUNT(*) 
                  FROM encomiendas 
                  WHERE id_conductor = CONDUC 
                    AND id_cliente_emisor = IDEMI 
                    AND id_cliente_receptor = IDRECEP
                    AND DATE(fecha_hora) = DATE(FECHA));
    
    -- Generar correlativo para la boleta
    
    IF Contar = 0 THEN
     
		         IF PAG > 0 OR ADOMICILIO>0 THEN
        -- Insertar la encomienda con los IDs obtenidos
								UPDATE encomiendas
								SET
										id_conductor=CONDUC, 
										id_origen=IDORI, 
										id_destino=IDDES, 
										updated_at=FECHA, 
										descripcion=FECHA, 
										id_cliente_emisor=IDEMI, 
										id_cliente_receptor=IDRECEP, 
										pago=PAG, 
										por_pagar=PORPAGAR,
										estado_pago='PAGADO',
										a_domicilio=ADOMICILIO, 
										id_usuario=USU, 
										observacion=OBSERVA
								WHERE id_encomienda=ID;
							END IF;
							IF PORPAGAR > 0 THEN
        -- Insertar la encomienda con los IDs obtenidos
								UPDATE encomiendas
								SET
										id_conductor=CONDUC, 
										id_origen=IDORI, 
										id_destino=IDDES, 
										updated_at=FECHA, 
										descripcion=FECHA, 
										id_cliente_emisor=IDEMI, 
										id_cliente_receptor=IDRECEP, 
										pago=PAG, 
										por_pagar=PORPAGAR,
										estado_pago='POR PAGAR',
										a_domicilio=ADOMICILIO, 
										id_usuario=USU, 
										observacion=OBSERVA
								WHERE id_encomienda=ID;
							END IF;
        
        -- INSERTAR EN INGRESOS SOLO SI EL PAGO ES MAYOR A 0
        IF PAG > 0 THEN
				
						UPDATE ingresos
						SET
						ingresos.monto=PAG,
						ingresos.igv=0,
						ingresos.monto_total=PAG,
						ingresos.id_usu=USU,
						ingresos.updated_at=NOW()
						WHERE ingresos.id_encomiendas=ID;
        END IF;
        
        -- INSERTAR EN INGRESOS SOLO SI A DOMICILIO ES MAYOR A 0
        IF ADOMICILIO > 0 THEN
         	UPDATE ingresos
						SET
						ingresos.monto=ADOMICILIO,
						ingresos.igv=0,
						ingresos.monto_total=ADOMICILIO,
						ingresos.id_usu=USU,
						ingresos.updated_at=NOW()
						WHERE ingresos.id_encomiendas=ID;
        END IF;
        
				 INSERT INTO historial_estados(
            id_encomienda,
            estado,
            observacion,
            motivo_anula,
            fecha_anula,
            created_at,
            idusu
        )
        VALUES(ID, esta, 'SE MODIFICO ESTE REGISTRO', NULL, NULL, NOW(), USU);
        -- Retornar éxito con el ID de la encomienda
        SELECT 1;
        
    ELSE
        -- La encomienda ya existe
        SELECT 2;
        
    END IF;
    
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_MODIFICAR_ESTADO_ENCOMIENDA` (IN `ID` INT, IN `ESTA` VARCHAR(20), IN `OBSER` TEXT, IN `ANULA` VARCHAR(255), IN `USU` INT)   BEGIN
    -- Actualizar encomiendas con lógica condicional para fecha_anulacion
    IF ANULA IS NOT NULL AND ANULA != '' THEN
        UPDATE encomiendas
        SET
            encomiendas.estado_encomienda = ESTA,
            encomiendas.observacion = OBSER,
            encomiendas.motivo_anulacion = ANULA,
            encomiendas.fecha_anulacion = NOW(),
						encomiendas.id_usuario=USU
        WHERE encomiendas.id_encomienda = ID;
    ELSE
        UPDATE encomiendas
        SET
            encomiendas.estado_encomienda = ESTA,
            encomiendas.observacion = OBSER,
            encomiendas.motivo_anulacion = NULL,
            encomiendas.fecha_anulacion = NULL,
						encomiendas.id_usuario=USU
        WHERE encomiendas.id_encomienda = ID;
    END IF;

    -- Insert en historial_estados con lógica condicional para fecha_anula
    IF ANULA IS NOT NULL AND ANULA != '' THEN
        INSERT INTO historial_estados(
            id_encomienda,
            estado,
            observacion,
            motivo_anula,
            fecha_anula,
            created_at,
            idusu
        )
        VALUES(ID, ESTA, OBSER, ANULA, NOW(), NOW(), USU);
    ELSE
        INSERT INTO historial_estados(
            id_encomienda,
            estado,
            observacion,
            motivo_anula,
            fecha_anula,
            created_at,
            idusu
        )
        VALUES(ID, ESTA, OBSER, NULL, NULL, NOW(), USU);
    END IF;

    -- Actualizar ingresos solo si el estado es ANULADO
    IF ESTA = 'ANULADO' THEN
        UPDATE ingresos
        SET
            ingresos.estado = 'ANULADO',
            id_usu = USU,
            motivo_anulacion = ANULA,
            fecha_anulacion = NOW()
        WHERE ingresos.id_encomiendas = ID;
    END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_MODIFICAR_GASTOS` (IN `ID` INT, IN `IDINDI` INT, IN `CANTIDAD` INT, IN `MONTO` DECIMAL(8,2), IN `DESCRIP` VARCHAR(255), IN `USU` INT)   UPDATE gastos
SET
id_indicador=IDINDI,
id_user=USU,
cantidad=CANTIDAD,
monto=MONTO,
observacion=DESCRIP,
updated_at=NOW()
WHERE gastos.id_gastos=ID$$

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

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_MODIFICAR_PAGO_ENCOMIENDA` (IN `ID` INT, IN `ESTA` VARCHAR(20), IN `PAGO_ANTI` DECIMAL(8,2), IN `PAGO_NU` DECIMAL(8,2), IN `USU` INT)   BEGIN
    DECLARE monto_pago DECIMAL(8,2) DEFAULT 0;
    DECLARE monto_por_pagar DECIMAL(8,2) DEFAULT 0;
    DECLARE monto_a_domicilio DECIMAL(8,2) DEFAULT 0;
    
    -- Obtener los montos actuales de la encomienda
    SELECT 
        IFNULL(pago, 0),
        IFNULL(por_pagar, 0),
        IFNULL(a_domicilio, 0)
    INTO 
        monto_pago,
        monto_por_pagar,
        monto_a_domicilio
    FROM encomiendas 
    WHERE id_encomienda = ID;
    
    -- Actualizar encomiendas según el tipo de pago encontrado
    IF monto_pago > 0 THEN
        -- Si el monto está en 'pago', actualizar ese campo
        UPDATE encomiendas
        SET
            encomiendas.estado_encomienda = ESTA,
            encomiendas.pago = PAGO_NU,
						encomiendas.id_usuario=USU
        WHERE encomiendas.id_encomienda = ID;
        
    ELSEIF monto_por_pagar > 0 THEN
        -- Si el monto está en 'por_pagar', actualizar ese campo
        UPDATE encomiendas
        SET
            encomiendas.estado_encomienda = ESTA,
            encomiendas.por_pagar = PAGO_NU,
						encomiendas.id_usuario=USU
        WHERE encomiendas.id_encomienda = ID;
        
    ELSEIF monto_a_domicilio > 0 THEN
        -- Si el monto está en 'a_domicilio', actualizar ese campo
        UPDATE encomiendas
        SET
            encomiendas.estado_encomienda = ESTA,
            encomiendas.a_domicilio = PAGO_NU,
						encomiendas.id_usuario=USU
        WHERE encomiendas.id_encomienda = ID;
        
    ELSE
        -- Si no hay monto en ningún campo, actualizar 'pago' por defecto
        UPDATE encomiendas
        SET
            encomiendas.estado_encomienda = ESTA,
            encomiendas.pago = PAGO_NU
        WHERE encomiendas.id_encomienda = ID;
    END IF;
    
    -- Insertar en historial_estados
    INSERT INTO historial_estados(
        id_encomienda,
        estado,
        observacion,
        motivo_anula,
        fecha_anula,
        created_at,
        idusu,
        precio_anterior,
        precio_nuevi
    )
    VALUES(ID, ESTA, 'SE AJUSTO EL PAGO Y MODIFICO ESTADO', NULL, NULL, NOW(), USU, PAGO_ANTI, PAGO_NU);
    
    -- Actualizar ingresos
    UPDATE ingresos
    SET
        ingresos.monto = PAGO_NU,
        ingresos.id_usu = USU,
        ingresos.monto_total = PAGO_NU
    WHERE ingresos.id_encomiendas = ID;
    
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_MODIFICAR_SALIDA_ESTATUS` (IN `ID` INT, IN `USU` INT)   BEGIN
    -- 1) Completar la salida
    UPDATE salidas_diarias
    SET estado='COMPLETADO',
        updated_at=NOW()
    WHERE id_salidas_diarias=ID;

    INSERT INTO historial_salidas_diarias(id_salida,estado,observacion,created,usu) 
    VALUES (ID,'COMPLETADO','SE CULMINO EL VIAJE',NOW(),USU);

    -- 2) Actualizar todas las encomiendas asociadas
    UPDATE encomiendas
    SET estado_encomienda='EN AGENCIA'
    WHERE id_encomienda IN (
        SELECT id_encomienda
        FROM salida_encomienda
        WHERE id_salida=ID
    );

    -- 3) Insertar historial de cada encomienda
    INSERT INTO historial_estados(
        id_encomienda,
        estado,
        observacion,
        motivo_anula,
        fecha_anula,
        created_at,
        idusu
    )
    SELECT se.id_encomienda,
           'EN AGENCIA',
           'LLEGO LA ENCOMIENDA ESTA EN AGENCIA',
           NULL,
           NULL,
           NOW(),
           USU
    FROM salida_encomienda se
    WHERE se.id_salida=ID;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_MODIFICAR_SALIDA_INCOMPLETO_ESTATUS` (IN `ID` INT, IN `USU` INT, IN `OBSERVA` TEXT)   BEGIN
    -- 1) Completar la salida
    UPDATE salidas_diarias
    SET estado='INCOMPLETO',
        updated_at=NOW(),
        observacion=OBSERVA
    WHERE id_salidas_diarias=ID;

    INSERT INTO historial_salidas_diarias(id_salida,estado,observacion,created,usu) 
    VALUES (ID,'INCOMPLETO',OBSERVA,NOW(),USU);

    -- 2) Actualizar todas las encomiendas asociadas
    UPDATE encomiendas
    SET estado_encomienda='INCOMPLETO',
    observacion=OBSERVA
    WHERE id_encomienda IN (
        SELECT id_encomienda
        FROM salida_encomienda
        WHERE id_salida=ID
    );

    -- 3) Insertar historial de cada encomienda
    INSERT INTO historial_estados(
        id_encomienda,
        estado,
        observacion,
        motivo_anula,
        fecha_anula,
        created_at,
        idusu
    )
    SELECT se.id_encomienda,
           'INCOMPLETO',
          OBSERVA,
           NULL,
           NULL,
           NOW(),
           USU
    FROM salida_encomienda se
    WHERE se.id_salida=ID;
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_REALIZAR_PAGO_ENCOMIENDA` (IN `ID` INT, IN `ESTA` VARCHAR(20), IN `SALDOPEN` DECIMAL(8,2), IN `MONTORECI` DECIMAL(8,2), IN `USU` INT)   BEGIN      
		-- Si el monto está en 'por_pagar', actualizar ese campo
		UPDATE encomiendas
		SET
				encomiendas.estado_encomienda = ESTA,
				encomiendas.estado_pago='PAGADO',
				encomiendas.id_usuario=USU,
				encomiendas.observacion=CONCAT_WS(' ','SE REALIZO EL PAGO DE LA ENCOMIENDA CON UN TOTAL DE: ',MONTORECI,' SOLES')
		WHERE encomiendas.id_encomienda = ID;
        
    -- Insertar en historial_estados
    INSERT INTO historial_estados(
        id_encomienda,
        estado,
        observacion,
        motivo_anula,
        fecha_anula,
        created_at,
        idusu,
        precio_anterior,
        precio_nuevi
    )
    VALUES(ID, ESTA, CONCAT_WS(' ','SE REALIZO EL PAGO DE LA ENCOMIENDA CON UN TOTAL DE: ',SALDOPEN,' SOLES'), NULL, NULL, NOW(), USU,SALDOPEN, 0.00);
    
    -- Actualizar ingresos
  INSERT INTO ingresos(id_encomiendas, id_indicador, monto, igv, monto_total, observacion, estado, id_usu, created_at)
            VALUES(ID, 7, MONTORECI, 0, MONTORECI, 'ENVIO DE ENCOMIENDA - PAGO PRINCIPAL', 'VALIDO', USU, NOW());
    
END$$

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

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_REGISTRAR_ENCOMIENDA` (IN `CONDUC` INT, IN `IDORI` INT, IN `IDDES` INT, IN `FECHA` DATETIME, IN `DESCR` TEXT, IN `TIPODOCEMI` VARCHAR(255), IN `NRODCOEMI` CHAR(20), IN `NOMEMI` VARCHAR(255), IN `CELEMI` CHAR(9), IN `TIPODOCRECE` VARCHAR(255), IN `NRODOCRECEP` CHAR(20), IN `NOMRECEP` VARCHAR(255), IN `CELERECEP` CHAR(9), IN `PAG` DECIMAL(8,2), IN `PORPAGAR` DECIMAL(8,2), IN `ADOMICILIO` DECIMAL(8,2), IN `USU` INT)   BEGIN
    DECLARE Contar INT;
    DECLARE cantidad INT;
    DECLARE cod CHAR(12);
    DECLARE proce1 VARCHAR(25);
    DECLARE proce2 VARCHAR(25);
    DECLARE IDEMI INT;
    DECLARE IDRECEP INT;
    DECLARE IDENCO INT; -- ID de la encomienda insertada
    DECLARE estado_pago_calc VARCHAR(20);
    DECLARE existe_emisor INT DEFAULT 0;
    DECLARE existe_receptor INT DEFAULT 0;
    
    -- Obtener nombres de rutas
    SET proce1 = (SELECT rutas.nombre FROM rutas WHERE rutas.idrutas = IDORI);
    SET proce2 = (SELECT rutas.nombre FROM rutas WHERE rutas.idrutas = IDDES);
    
    -- VALIDACIÓN 1: Verificar si el cliente emisor ya existe
    SELECT COUNT(*), IFNULL(MAX(clientes.id_cliente), 0) 
    INTO existe_emisor, IDEMI
    FROM clientes 
    WHERE tipo_documento = TIPODOCEMI AND nro_documento = NRODCOEMI;
    
    IF existe_emisor > 0 THEN
        -- Cliente emisor existe, actualizar datos
        UPDATE clientes 
        SET nombre_completo = NOMEMI,
            procedencia = proce1,
            celular = CELEMI,
            updated_at = NOW()
        WHERE tipo_documento = TIPODOCEMI AND nro_documento = NRODCOEMI;
    ELSE
        -- Cliente emisor no existe, insertar nuevo
        INSERT INTO clientes(tipo_documento, nro_documento, nombre_completo, procedencia, celular, direccion, email, created_at)
        VALUES(TIPODOCEMI, NRODCOEMI, NOMEMI, proce1, CELEMI, '', '', NOW());
        
        SET IDEMI = LAST_INSERT_ID();
    END IF;
    
    -- VALIDACIÓN 1: Verificar si el cliente receptor ya existe
    SELECT COUNT(*), IFNULL(MAX(clientes.id_cliente), 0) 
    INTO existe_receptor, IDRECEP
    FROM clientes 
    WHERE tipo_documento = TIPODOCRECE AND nro_documento = NRODOCRECEP;
    
    IF existe_receptor > 0 THEN
        -- Cliente receptor existe, actualizar datos
        UPDATE clientes 
        SET nombre_completo = NOMRECEP,
            procedencia = proce2,
            celular = CELERECEP,
            updated_at = NOW()
        WHERE tipo_documento = TIPODOCRECE AND nro_documento = NRODOCRECEP;
    ELSE
        -- Cliente receptor no existe, insertar nuevo
        INSERT INTO clientes(tipo_documento, nro_documento, nombre_completo, procedencia, celular, direccion, email, created_at)
        VALUES(TIPODOCRECE, NRODOCRECEP, NOMRECEP, proce2, CELERECEP, '', '', NOW());
        
        SET IDRECEP = LAST_INSERT_ID();
    END IF;
    
    -- Verificar si ya existe una encomienda con los mismos datos
    SET Contar = (SELECT COUNT(*) 
                  FROM encomiendas 
                  WHERE id_conductor = CONDUC 
                    AND id_cliente_emisor = IDEMI 
                    AND id_cliente_receptor = IDRECEP
                    AND DATE(fecha_hora) = DATE(FECHA));
    
    -- Generar correlativo para la boleta
    SET cantidad = (SELECT IFNULL(MAX(doc_nrocorrelativo), 0) FROM encomiendas);
    
    IF Contar = 0 THEN
        -- Generar código de boleta
        IF cantidad >= 1 AND cantidad <= 8 THEN
            SET cod = CONCAT('E-000000', (cantidad + 1));
        ELSEIF cantidad >= 9 AND cantidad <= 98 THEN
            SET cod = CONCAT('E-00000', (cantidad + 1));
        ELSEIF cantidad >= 99 AND cantidad <= 998 THEN
            SET cod = CONCAT('E-0000', (cantidad + 1));
        ELSEIF cantidad >= 999 AND cantidad <= 9998 THEN
            SET cod = CONCAT('E-000', (cantidad + 1));
        ELSEIF cantidad >= 9999 AND cantidad <= 99998 THEN
            SET cod = CONCAT('E-00', (cantidad + 1));
        ELSEIF cantidad >= 99999 AND cantidad <= 999998 THEN
            SET cod = CONCAT('E-0', (cantidad + 1));
        ELSEIF cantidad >= 999999 THEN
            SET cod = CONCAT('E-', (cantidad + 1));
        ELSE
            SET cod = 'E-0000001';
        END IF;
        
        -- VALIDACIÓN 3: Determinar estado de pago
        IF PAG > 0 OR ADOMICILIO > 0 THEN
            SET estado_pago_calc = 'PAGADO';
        ELSE
            SET estado_pago_calc = 'POR PAGAR';
        END IF;
        
        -- Insertar la encomienda con los IDs obtenidos
        INSERT INTO encomiendas(
            boleta_nro, 
            id_conductor, 
            id_origen, 
            id_destino, 
            fecha_hora, 
            descripcion, 
            id_cliente_emisor, 
            id_cliente_receptor, 
            pago, 
            por_pagar, 
            a_domicilio, 
            id_usuario, 
            observacion, 
            estado_pago, 
            created_at, 
            estado_encomienda, 
            doc_nrocorrelativo
        ) 
        VALUES(
            cod, 
            CONDUC, 
            IDORI, 
            IDDES, 
            FECHA, 
            DESCR, 
            IDEMI, 
            IDRECEP, 
            PAG, 
            PORPAGAR, 
            ADOMICILIO, 
            USU, 
            '', 
            estado_pago_calc, 
            NOW(), 
            'PENDIENTE', 
            (cantidad + 1)
        );
        
        -- OBTENER EL ID DE LA ENCOMIENDA RECIÉN INSERTADA
        SET IDENCO = LAST_INSERT_ID();
        
        -- INSERTAR EN INGRESOS SOLO SI EL PAGO ES MAYOR A 0
        IF PAG > 0 THEN
            INSERT INTO ingresos(id_encomiendas, id_indicador, monto, igv, monto_total, observacion, estado, id_usu, created_at)
            VALUES(IDENCO, 7, PAG, 0, PAG, 'ENVIO DE ENCOMIENDA - PAGO PRINCIPAL', 'VALIDO', USU, NOW());
        END IF;
        
        -- INSERTAR EN INGRESOS SOLO SI A DOMICILIO ES MAYOR A 0
        IF ADOMICILIO > 0 THEN
            INSERT INTO ingresos(id_encomiendas, id_indicador, monto, igv, monto_total, observacion, estado, id_usu, created_at)
            VALUES(IDENCO, 7, ADOMICILIO, 0, ADOMICILIO, 'ENVIO DE ENCOMIENDA - SERVICIO A DOMICILIO', 'VALIDO', USU, NOW());
        END IF;
        
				 INSERT INTO historial_estados(
            id_encomienda,
            estado,
            observacion,
            motivo_anula,
            fecha_anula,
            created_at,
            idusu
        )
        VALUES(IDENCO, 'PENDIENTE', 'ES EL PRIMER REGISTRO', NULL, NULL, NOW(), USU);
        -- Retornar éxito con el ID de la encomienda
        SELECT IDENCO;
        
    ELSE
        -- La encomienda ya existe
        SELECT 2;
        
    END IF;
    
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_REGISTRAR_GASTOS` (IN `IDINDI` INT, IN `CANTIDAD` INT, IN `MONTO` DECIMAL(8,2), IN `DESCRIP` VARCHAR(255), IN `USU` INT)   INSERT INTO gastos (id_indicador,id_user,cantidad,monto,observacion,estado,created_at)
VALUES(IDINDI,USU,CANTIDAD,MONTO,DESCRIP,'VALIDO',NOW())$$

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
(3, 'DNI', '09251155', 'EDUARDO ALVAREZ ASTETE', '975777881', '954844848', 'ABANCAY', 'JR. CUSCO N° 231', 'KIA', 'S5-84841', 'AIIIA', 'T09251155', '2028-10-23', 'ACTIVO', '2025-08-06 00:00:00', '2025-09-20 15:01:00', 1, 'controller/choferes/fotos/IMG6-8-2025-12-605.avif'),
(4, 'DNI', '72155445', 'JORDAN JEPHERSON MENDOZA CUMPA', '974785471', '987888444', 'CUSCO', 'AV. PACHACUTEC N° 4454', 'HYUNDAY', '52D-D1D1', 'AIIA', 'T72155445', '2027-10-22', 'ACTIVO', '2025-08-06 00:00:00', '2025-08-06 12:01:01', 1, 'controller/choferes/fotos/IMG6-8-2025-11-420.png'),
(5, 'DNI', '75515511', 'RUBI CATHERIN HUANCA TUPIA', '985245514', '', 'CUSCO', 'AV. WANCHAQ N° 233', 'HYUNDAY', '4SD4-111', 'AIIB', 'T75515511', '2028-10-17', 'ACTIVO', '2025-09-07 00:00:00', NULL, 1, 'controller/choferes/fotos/');

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
(2, 'PASAPORTE', '72541155898', 'DANIELA PERALTA CHAVEZ', 'ABANCAY', '987114787', 'Av. Chile N° 233', '', 5, '2025-02-02', '2025-08-09 11:10:58', '2025-08-09 12:13:04'),
(5, 'DNI', '09747535', 'CELIA MIRANDA MUNGUIA', 'ABANCAY', '984757', '', '', NULL, NULL, '2025-09-07 11:23:41', '2025-09-07 11:24:15'),
(6, 'DNI', '72646121', 'JERSSON JORGE CORILLA MIRANDA', 'CUSCO', '985411233', '', '', NULL, NULL, '2025-09-07 11:24:15', '2025-09-21 15:40:01'),
(7, 'DNI', '72545444', 'MARIA DEL ROSARIO RIVAS MAITA', 'ABANCAY', '984112', '', '', NULL, NULL, '2025-09-07 11:25:17', NULL),
(13, 'DNI', '72511551', 'JARALY RUSBEL RAMOS COBEÑAS', 'ABANCAY', '985777', '', '', NULL, NULL, '2025-09-07 11:37:58', NULL),
(14, 'DNI', '72022112', 'FABRIZIO MIGUEL DESIDERIO MORALES', 'CUSCO', '925123', '', '', NULL, NULL, '2025-09-07 11:37:58', NULL),
(15, 'DNI', '72656546', 'JOSE DANIEL SALAZAR ONTON', 'ABANCAY', '982000', '', '', NULL, NULL, '2025-09-07 11:41:09', NULL),
(16, 'DNI', '75656565', 'ESTEFANY DEL PILAR MAYTA FIGUEROA', 'CUSCO', '985745', '', '', NULL, NULL, '2025-09-07 11:41:09', NULL),
(17, 'DNI', '75551155', 'CRISELDA CRISTOBAL PAQUI', 'CUSCO', '987475', '', '', NULL, NULL, '2025-09-07 11:45:03', NULL),
(18, 'DNI', '79262626', 'MARIANA SOPHIA SILVERA BLANCO', 'ABANCAY', '974214231', '', '', NULL, NULL, '2025-09-07 11:59:23', '2025-09-20 11:05:36'),
(19, 'DNI', '75215515', 'MICHEL YURI AIMA DIAZ', 'CUSCO', '940001', '', '', NULL, NULL, '2025-09-07 11:59:23', '2025-09-20 11:03:28'),
(20, 'DNI', '62844848', 'MIGUEL ANGEL ETHAN ECUYER GRENARD', 'CUSCO', '900014', '', '', NULL, NULL, '2025-09-07 12:00:55', NULL),
(21, 'DNI', '61155511', 'SAUL FRANCO BENAVIDES ESCUDERO', 'ABANCAY', '921147', '', '', NULL, NULL, '2025-09-07 12:00:55', NULL),
(22, 'DNI', '76515115', 'KEYSI SOLEY SOUZA AZANG', 'CUSCO', '985125', '', '', NULL, NULL, '2025-09-07 12:02:30', NULL),
(23, 'DNI', '76215511', 'DANNY JHONATAN COLLANTES BONATTI', 'ABANCAY', '900014', '', '', NULL, NULL, '2025-09-07 12:02:30', NULL),
(24, 'DNI', '76212120', 'JAIRO JAROL CUSI ROJAS', 'CUSCO', '952141478', '', '', NULL, NULL, '2025-09-07 12:18:09', NULL),
(25, 'DNI', '31188448', 'DALMIRO DANIEL OSORIO BULEJE', 'ABANCAY', '985124521', '', '', NULL, NULL, '2025-09-07 12:18:09', NULL),
(26, 'DNI', '75661515', 'JUAN BALDOMERO MANZANO CORONEL', 'ABANCAY', '951754148', '', '', NULL, NULL, '2025-09-16 17:07:09', NULL),
(27, 'DNI', '74511515', 'JULIANNE BAO YU WANG CERAS', 'CUSCO', '985414521', '', '', NULL, NULL, '2025-09-16 17:07:09', NULL),
(28, 'DNI', '72645445', 'PATRICIA NICOLE FARRO DIAZ', 'CUSCO', '974214231', '', '', NULL, NULL, '2025-09-20 11:06:02', '2025-09-20 11:11:22'),
(29, 'DNI', '75221815', 'LUZ CLARITA MOLINA ISIDRO', 'ABANCAY', '985414588', '', '', NULL, NULL, '2025-09-20 15:01:31', NULL),
(30, 'DNI', '72445445', 'LYA ANGELICA DEL ROCIO PALACIOS WONG', 'CUSCO', '978145141', '', '', NULL, NULL, '2025-09-20 15:01:31', NULL),
(31, 'DNI', '72448484', 'TATIANA LORENA MANCHEGO REYES', 'CUSCO', '985474148', '', '', NULL, NULL, '2025-09-20 15:14:36', NULL),
(32, 'DNI', '75215151', 'ERVIN ROMMEL GALVAN ORDAYA', 'ABANCAY', '987777874', '', '', NULL, NULL, '2025-09-20 15:14:36', NULL),
(33, 'DNI', '72654454', 'JOSE CARLOS SANTAMARIA BANCES', 'ABANCAY', '954511454', '', '', NULL, NULL, '2025-09-21 15:40:01', NULL);

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
  `estado_encomienda` enum('PENDIENTE','ENTREGADO','OBSERVADO','EN TRANSITO','EN AGENCIA','ANULADO','INCOMPLETO') DEFAULT NULL,
  `motivo_anulacion` varchar(255) DEFAULT NULL,
  `fecha_anulacion` date DEFAULT NULL,
  `doc_nrocorrelativo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `encomiendas`
--

INSERT INTO `encomiendas` (`id_encomienda`, `boleta_nro`, `id_conductor`, `id_origen`, `id_destino`, `fecha_hora`, `descripcion`, `id_cliente_emisor`, `id_cliente_receptor`, `pago`, `por_pagar`, `a_domicilio`, `id_usuario`, `observacion`, `estado_pago`, `created_at`, `updated_at`, `estado_encomienda`, `motivo_anulacion`, `fecha_anulacion`, `doc_nrocorrelativo`) VALUES
(1, '33444', 4, 2, 1, '2025-08-22 09:12:49', 'COSTAL DE PANES', 2, 1, 0.00, 50.00, 0.00, 1, '', 'ANULADO', '2025-08-23 09:13:12', NULL, 'ANULADO', 'EQUIVOCACION', '2025-09-16', NULL),
(5, '222332', 2, 1, 2, '2025-08-21 09:12:05', 'CAJA PEQUEÑA', 1, 2, 40.00, 0.00, 0.00, 2, NULL, 'PAGADO', '2025-08-21 09:12:31', NULL, 'EN AGENCIA', NULL, NULL, NULL),
(13, 'E-0000002', 2, 1, 2, '2025-09-07 11:37:00', 'SACO PEQUEñO', 13, 14, 25.00, 0.00, 0.00, 2, 'NO ACEPTA EL PRECIO', 'PAGADO', '2025-09-07 11:37:58', NULL, 'EN AGENCIA', NULL, NULL, 2),
(14, 'E-0000003', 2, 1, 2, '2025-09-07 11:40:00', 'CAJA DE CUADERNO ALPHA', 15, 16, 0.00, 30.00, 0.00, 1, 'SE REALIZO EL PAGO DE LA ENCOMIENDA CON UN TOTAL DE:  50.00  SOLES', 'PAGADO', '2025-09-07 11:41:09', NULL, 'ENTREGADO', NULL, NULL, 3),
(16, 'E-0000004', 4, 2, 1, '2025-09-07 11:58:00', '2025-09-20 11:11:00', 28, 6, 65.00, 0.00, 0.00, 1, 'TODO OK', 'PAGADO', '2025-09-07 11:59:23', '2025-09-20 11:11:00', 'PENDIENTE', NULL, NULL, 4),
(17, 'E-0000005', 4, 2, 1, '2025-09-07 12:00:00', 'SACO CON MAIZ', 20, 21, 0.00, 30.00, 0.00, 1, '', 'ANULADO', '2025-09-07 12:00:55', NULL, 'ANULADO', 'SE ANUYLA', '2025-09-16', 5),
(18, 'E-0000006', 5, 2, 1, '2025-09-07 12:01:00', 'CAJA PEQUEñA DE PINTURAS ESCOLARES', 22, 23, 0.00, 0.00, 25.00, 1, 'NO ACEPTA PRECIO', 'PAGADO', '2025-09-07 12:02:30', NULL, 'EN TRANSITO', NULL, NULL, 6),
(19, 'E-0000007', 4, 2, 1, '2025-09-07 12:17:00', '2 CAJAS MEDIANS DE UTILES ESCOLARES', 24, 25, 50.00, 0.00, 0.00, 1, '', 'ANULADO', '2025-09-07 12:18:09', NULL, 'ANULADO', 'SE EQUIVOCO YA NO ENVIARA', '2025-09-16', 7),
(21, 'E-0000008', 5, 1, 2, '2025-09-20 15:01:00', 'CAJA DE MATERIALES DE ESCRITORIO', 29, 30, 50.00, 0.00, 0.00, 1, '', 'PAGADO', '2025-09-20 15:01:31', NULL, 'PENDIENTE', NULL, NULL, 8),
(22, 'E-0000009', 3, 2, 1, '2025-09-20 15:13:00', 'UN DOCUMENTO EN MANILA', 31, 32, 0.00, 20.00, 0.00, 1, 'SE MALOGRO EL CARRO', 'POR PAGAR', '2025-09-20 15:14:36', NULL, 'INCOMPLETO', NULL, NULL, 9),
(23, 'E-0000010', 3, 1, 2, '2025-09-21 15:39:00', 'SE ENVIO CAJA DE ALMOHADAS', 33, 6, 50.00, 0.00, 0.00, 1, '', 'PAGADO', '2025-09-21 15:40:01', NULL, 'PENDIENTE', NULL, NULL, 10);

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

--
-- Volcado de datos para la tabla `gastos`
--

INSERT INTO `gastos` (`id_gastos`, `id_indicador`, `id_user`, `cantidad`, `monto`, `observacion`, `estado`, `motivo_anulacion`, `fecha_anulacion`, `created_at`, `updated_at`) VALUES
(1, 6, 1, 2, 3000.00, 'SDFSDF', 'VALIDO', NULL, NULL, '2025-09-06 15:11:10', '2025-09-06 15:38:55'),
(10, 3, 1, 21, 1500.00, 'HOLA Q TAL', 'ANULADO', 'SE TUVO Q DEVOLVER', '2025-09-06', '2025-09-06 15:35:25', NULL),
(11, 2, 1, 3, 850.00, 'SE PAGO LA LUZ DE 2 MESES', 'VALIDO', NULL, NULL, '2025-09-06 15:36:12', NULL),
(12, 3, 1, 2, 500.00, 'SE COMPRO 10 PAQUETES DE AGUA NATURAL', 'VALIDO', NULL, NULL, '2025-09-21 16:08:47', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_estados`
--

CREATE TABLE `historial_estados` (
  `id_historial_estado` int(11) NOT NULL,
  `id_encomienda` int(11) DEFAULT NULL,
  `estado` enum('PENDIENTE','ENTREGADO','OBSERVADO','EN TRANSITO','EN AGENCIA','ANULADO','INCOMPLETO') DEFAULT NULL,
  `observacion` varchar(255) DEFAULT NULL,
  `precio_anterior` decimal(8,2) DEFAULT NULL,
  `precio_nuevi` decimal(8,2) DEFAULT NULL,
  `motivo_anula` varchar(255) DEFAULT NULL,
  `fecha_anula` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `idusu` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `historial_estados`
--

INSERT INTO `historial_estados` (`id_historial_estado`, `id_encomienda`, `estado`, `observacion`, `precio_anterior`, `precio_nuevi`, `motivo_anula`, `fecha_anula`, `created_at`, `idusu`) VALUES
(1, 1, 'ANULADO', '', NULL, NULL, 'EQUIVOCACION', '2025-09-16 16:52:24', '2025-09-16 16:52:24', 1),
(5, 5, 'EN TRANSITO', NULL, 30.00, 40.00, NULL, NULL, '2025-09-16 17:51:12', 1),
(7, 19, 'ANULADO', '', NULL, NULL, 'SE EQUIVOCO YA NO ENVIARA', '2025-09-16 17:57:59', '2025-09-16 17:57:59', 1),
(8, 17, 'ANULADO', '', NULL, NULL, 'SE ANUYLA', '2025-09-16 17:58:46', '2025-09-16 17:58:46', 1),
(19, 14, 'EN AGENCIA', 'LLEGO', NULL, NULL, NULL, NULL, '2025-09-17 17:08:18', 1),
(23, 14, 'ENTREGADO', 'SE REALIZO EL PAGO DE LA ENCOMIENDA CON UN TOTAL DE:  30.00  SOLES', 30.00, 0.00, NULL, NULL, '2025-09-17 17:11:56', 1),
(24, 13, 'OBSERVADO', 'NO ACEPTA EL PRECIO', NULL, NULL, NULL, NULL, '2025-09-17 17:15:32', 1),
(25, 13, 'EN TRANSITO', 'SE AJUSTO EL PAGO Y MODIFICO ESTADO', 30.00, 25.00, NULL, NULL, '2025-09-17 17:15:55', 1),
(26, 18, 'OBSERVADO', 'NO ACEPTA PRECIO', NULL, NULL, NULL, NULL, '2025-09-17 17:17:18', 1),
(27, 18, 'EN TRANSITO', 'SE AJUSTO EL PAGO Y MODIFICO ESTADO', 30.00, 25.00, NULL, NULL, '2025-09-17 17:17:26', 1),
(29, 16, 'PENDIENTE', 'SE MODIFICO ESTE REGISTRO', NULL, NULL, NULL, NULL, '2025-09-20 11:02:42', 1),
(30, 16, 'PENDIENTE', 'SE MODIFICO ESTE REGISTRO', NULL, NULL, NULL, NULL, '2025-09-20 11:03:28', 1),
(31, 16, 'PENDIENTE', 'SE MODIFICO ESTE REGISTRO', NULL, NULL, NULL, NULL, '2025-09-20 11:05:36', 1),
(32, 16, 'PENDIENTE', 'SE MODIFICO ESTE REGISTRO', NULL, NULL, NULL, NULL, '2025-09-20 11:06:02', 1),
(33, 16, 'PENDIENTE', 'SE MODIFICO ESTE REGISTRO', NULL, NULL, NULL, NULL, '2025-09-20 11:06:29', 1),
(34, 16, 'PENDIENTE', 'SE MODIFICO ESTE REGISTRO', NULL, NULL, NULL, NULL, '2025-09-20 11:06:37', 1),
(35, 16, 'PENDIENTE', 'SE MODIFICO ESTE REGISTRO', NULL, NULL, NULL, NULL, '2025-09-20 11:11:10', 1),
(36, 16, 'PENDIENTE', 'SE MODIFICO ESTE REGISTRO', NULL, NULL, NULL, NULL, '2025-09-20 11:11:22', 1),
(37, 21, 'PENDIENTE', 'ES EL PRIMER REGISTRO', NULL, NULL, NULL, NULL, '2025-09-20 15:01:31', 1),
(38, 22, 'PENDIENTE', 'ES EL PRIMER REGISTRO', NULL, NULL, NULL, NULL, '2025-09-20 15:14:36', 1),
(54, 5, 'EN AGENCIA', 'LLEGO LA ENCOMIENDA ESTA EN AGENCIA', NULL, NULL, NULL, NULL, '2025-09-21 11:52:51', 1),
(55, 13, 'EN AGENCIA', 'LLEGO LA ENCOMIENDA ESTA EN AGENCIA', NULL, NULL, NULL, NULL, '2025-09-21 11:52:51', 1),
(57, 22, 'INCOMPLETO', 'SE MALOGRO EL CARRO', NULL, NULL, NULL, NULL, '2025-09-21 12:24:58', 1),
(58, 23, 'PENDIENTE', 'ES EL PRIMER REGISTRO', NULL, NULL, NULL, NULL, '2025-09-21 15:40:01', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_salidas_diarias`
--

CREATE TABLE `historial_salidas_diarias` (
  `id_historial_salida` int(11) NOT NULL,
  `id_salida` int(11) DEFAULT NULL,
  `estado` enum('EN TRANSITO','COMPLETADO','INCOMPLETO','ELIMINADO') DEFAULT NULL,
  `observacion` text DEFAULT NULL,
  `created` datetime(6) DEFAULT NULL,
  `usu` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `historial_salidas_diarias`
--

INSERT INTO `historial_salidas_diarias` (`id_historial_salida`, `id_salida`, `estado`, `observacion`, `created`, `usu`) VALUES
(6, 1, 'COMPLETADO', 'SE CULMINO EL VIAJE', '2025-09-21 11:52:51.000000', 2),
(8, 4, 'INCOMPLETO', 'SE MALOGRO EL CARRO', '2025-09-21 12:24:58.000000', 1);

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
(6, 'GASTOS', 'PAGO DE AGUA', 'SERVICIO DE PAGO DE AGUA', 'ACTIVO', '2025-07-29 09:39:21', '2025-07-29 09:39:59', 1),
(7, 'INGRESOS', 'ENCOMIENDAS', 'SE ENVIO ENCOMIENDA', 'ACTIVO', '2025-09-06 15:42:56', NULL, 1);

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

--
-- Volcado de datos para la tabla `ingresos`
--

INSERT INTO `ingresos` (`id_ingreso`, `id_encomiendas`, `id_salidas_diarias`, `id_indicador`, `monto`, `igv`, `monto_total`, `observacion`, `estado`, `id_usu`, `motivo_anulacion`, `fecha_anulacion`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 7, 50.00, NULL, 50.00, 'SE ENVIO ENCOMIENDA', 'ANULADO', 1, 'EQUIVOCACION', '2025-09-16', '2025-09-06 15:43:22', NULL),
(2, 16, NULL, 7, 65.00, 0.00, 65.00, 'ENVIO DE ENCOMIENDA - PAGO PRINCIPAL', 'VALIDO', 1, NULL, NULL, '2025-09-07 11:59:23', '2025-09-20 11:11:22'),
(3, 18, NULL, 7, 25.00, 0.00, 25.00, 'ENVIO DE ENCOMIENDA - SERVICIO A DOMICILIO', 'VALIDO', 1, NULL, NULL, '2025-09-07 12:02:30', NULL),
(4, 19, NULL, 7, 50.00, 0.00, 50.00, 'ENVIO DE ENCOMIENDA - PAGO PRINCIPAL', 'ANULADO', 1, 'SE EQUIVOCO YA NO ENVIARA', '2025-09-16', '2025-09-07 12:18:09', NULL),
(9, 14, NULL, 7, 50.00, 0.00, 50.00, 'ENVIO DE ENCOMIENDA - PAGO PRINCIPAL', 'VALIDO', 1, NULL, NULL, '2025-09-17 17:11:56', NULL),
(10, 21, NULL, 7, 50.00, 0.00, 50.00, 'ENVIO DE ENCOMIENDA - PAGO PRINCIPAL', 'VALIDO', 1, NULL, NULL, '2025-09-20 15:01:31', NULL),
(11, 23, NULL, 7, 50.00, 0.00, 50.00, 'ENVIO DE ENCOMIENDA - PAGO PRINCIPAL', 'VALIDO', 1, NULL, NULL, '2025-09-21 15:40:01', NULL);

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
(2, 'ASISTENTE', 'TIENE ACCESO SOLO A POCOS MóDULOS ', 'ACTIVO', '2025-07-26 12:03:15', '2025-09-21 16:46:45'),
(5, 'CONDUCTOR', 'ACCESO PARA CONDUCTORES', 'ACTIVO', '2025-09-21 00:00:00', NULL);

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
  `salida_nro` varchar(255) DEFAULT NULL,
  `id_conductor` int(11) NOT NULL,
  `monto` decimal(8,2) DEFAULT NULL,
  `fecha_hora` datetime DEFAULT NULL,
  `id_origen` int(11) DEFAULT NULL,
  `id_destino` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `observacion` text DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `estado` enum('EN TRANSITO','COMPLETADO','ELIMINADO','INCOMPLETO') DEFAULT NULL,
  `doc_nrocorrelativo` int(11) DEFAULT NULL,
  `total_pasajeros` int(11) DEFAULT NULL,
  `total_encomiendas` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `salidas_diarias`
--

INSERT INTO `salidas_diarias` (`id_salidas_diarias`, `salida_nro`, `id_conductor`, `monto`, `fecha_hora`, `id_origen`, `id_destino`, `created_at`, `updated_at`, `observacion`, `id_usuario`, `estado`, `doc_nrocorrelativo`, `total_pasajeros`, `total_encomiendas`) VALUES
(1, 'S-000001', 2, 3.00, '2025-09-20 15:55:40', 1, 2, '2025-09-20 15:55:46', '2025-09-21 11:52:51', 'Salio completo', 1, 'COMPLETADO', 1, 4, 2),
(3, 'S-000002', 3, 3.00, '2025-09-20 15:59:17', 1, 2, '2025-09-20 15:56:27', NULL, NULL, 1, 'INCOMPLETO', 2, 6, 3),
(4, 'S-000003', 2, 3.00, '2025-09-19 16:46:27', 2, 1, '2025-09-19 16:46:35', '2025-09-21 12:24:58', 'SE MALOGRO EL CARRO', 2, 'INCOMPLETO', 3, 4, 1),
(5, 'S-000004', 4, 3.00, '2025-09-21 15:53:16', 2, 1, '2025-09-21 15:53:24', NULL, NULL, 2, 'EN TRANSITO', 4, 4, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `salida_cliente`
--

CREATE TABLE `salida_cliente` (
  `id_cliente_salida` int(11) NOT NULL,
  `idsalida` int(11) DEFAULT NULL,
  `idcliente` int(11) DEFAULT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `observacion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `salida_encomienda`
--

CREATE TABLE `salida_encomienda` (
  `id_enco_salida` int(11) NOT NULL,
  `id_salida` int(11) DEFAULT NULL,
  `id_encomienda` int(11) DEFAULT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `observacion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `salida_encomienda`
--

INSERT INTO `salida_encomienda` (`id_enco_salida`, `id_salida`, `id_encomienda`, `created_at`, `updated_at`, `observacion`) VALUES
(1, 1, 5, '2025-09-21 11:41:44.000000', NULL, 'todo bien'),
(2, 1, 13, '2025-09-21 11:42:00.000000', NULL, 'todo bien'),
(4, 4, 22, '2025-09-21 12:24:34.000000', NULL, NULL);

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
(9, '72551154', 'ANABELL', 'CHAVEZ PEÑA', 'anabell12@gmail.com', '974484848', 'JR. CANADA', 'anabell2025', '$2y$12$dKiMgP1HQmJrePwHZPZVcOfrCA1Pu.hmJO5vIOVMepEcn/Qmjn7ae', 5, 'ACTIVO', 'controller/usuario/fotos/IMG27-7-2025-16-223.jpg', 1, '2025-07-27 16:28:34', '2025-09-21 16:51:27'),
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
-- Indices de la tabla `historial_estados`
--
ALTER TABLE `historial_estados`
  ADD PRIMARY KEY (`id_historial_estado`),
  ADD KEY `fk_enco_histo` (`id_encomienda`);

--
-- Indices de la tabla `historial_salidas_diarias`
--
ALTER TABLE `historial_salidas_diarias`
  ADD PRIMARY KEY (`id_historial_salida`),
  ADD KEY `dk_salida_diari12` (`id_salida`);

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
  ADD KEY `fk_usu_sali` (`id_usuario`),
  ADD KEY `fk_id_origin_idx` (`id_origen`),
  ADD KEY `fk_id_desti_idx` (`id_destino`);

--
-- Indices de la tabla `salida_cliente`
--
ALTER TABLE `salida_cliente`
  ADD PRIMARY KEY (`id_cliente_salida`),
  ADD KEY `fk_sali2` (`idsalida`),
  ADD KEY `dk_clie2` (`idcliente`);

--
-- Indices de la tabla `salida_encomienda`
--
ALTER TABLE `salida_encomienda`
  ADD PRIMARY KEY (`id_enco_salida`),
  ADD KEY `fk_sali3` (`id_salida`),
  ADD KEY `fk_enco2` (`id_encomienda`);

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
  MODIFY `id_chofer` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

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
  MODIFY `id_encomienda` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `gastos`
--
ALTER TABLE `gastos`
  MODIFY `id_gastos` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `historial_estados`
--
ALTER TABLE `historial_estados`
  MODIFY `id_historial_estado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT de la tabla `historial_salidas_diarias`
--
ALTER TABLE `historial_salidas_diarias`
  MODIFY `id_historial_salida` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `indicadores`
--
ALTER TABLE `indicadores`
  MODIFY `id_indicador` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `ingresos`
--
ALTER TABLE `ingresos`
  MODIFY `id_ingreso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_role` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `rutas`
--
ALTER TABLE `rutas`
  MODIFY `idrutas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `salidas_diarias`
--
ALTER TABLE `salidas_diarias`
  MODIFY `id_salidas_diarias` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `salida_cliente`
--
ALTER TABLE `salida_cliente`
  MODIFY `id_cliente_salida` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `salida_encomienda`
--
ALTER TABLE `salida_encomienda`
  MODIFY `id_enco_salida` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
-- Filtros para la tabla `historial_estados`
--
ALTER TABLE `historial_estados`
  ADD CONSTRAINT `fk_enco_histo` FOREIGN KEY (`id_encomienda`) REFERENCES `encomiendas` (`id_encomienda`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `historial_salidas_diarias`
--
ALTER TABLE `historial_salidas_diarias`
  ADD CONSTRAINT `dk_salida_diari12` FOREIGN KEY (`id_salida`) REFERENCES `salidas_diarias` (`id_salidas_diarias`) ON DELETE CASCADE ON UPDATE CASCADE;

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
  ADD CONSTRAINT `fk_conductor1` FOREIGN KEY (`id_conductor`) REFERENCES `choferes` (`id_chofer`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_id_desti` FOREIGN KEY (`id_destino`) REFERENCES `rutas` (`idrutas`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_id_origin` FOREIGN KEY (`id_origen`) REFERENCES `rutas` (`idrutas`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_usu_sali` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `salida_cliente`
--
ALTER TABLE `salida_cliente`
  ADD CONSTRAINT `dk_clie2` FOREIGN KEY (`idcliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sali2` FOREIGN KEY (`idsalida`) REFERENCES `salidas_diarias` (`id_salidas_diarias`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `salida_encomienda`
--
ALTER TABLE `salida_encomienda`
  ADD CONSTRAINT `fk_enco2` FOREIGN KEY (`id_encomienda`) REFERENCES `encomiendas` (`id_encomienda`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sali3` FOREIGN KEY (`id_salida`) REFERENCES `salidas_diarias` (`id_salidas_diarias`) ON DELETE CASCADE ON UPDATE CASCADE;

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
