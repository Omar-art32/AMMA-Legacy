-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 19-08-2026 a las 15:13:51
-- Versión del servidor: 10.1.13-MariaDB
-- Versión de PHP: 5.6.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `amma`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rv_produccion_ensamble`
--

CREATE TABLE `rv_produccion_ensamble` (
  `id_ensamble` int(11) NOT NULL,
  `id_ensamble_union` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `id_produccion_entrada` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `id_agave_sobrante` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `id_agave_cocido_sobrante` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `id_predio` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `id_planta` int(11) NOT NULL,
  `no_guia` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `no_pinas_agave` int(11) NOT NULL,
  `agave_kg` double NOT NULL,
  `agave_coccion_kg` double NOT NULL,
  `porcentaje_art` double NOT NULL,
  `tipo` int(1) NOT NULL,
  `id_verificador` int(11) NOT NULL,
  `actualizado` int(1) NOT NULL,
  `fecha_subio` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `rv_produccion_ensamble`
--

INSERT INTO `rv_produccion_ensamble` (`id_ensamble`, `id_ensamble_union`, `id_produccion_entrada`, `id_agave_sobrante`, `id_agave_cocido_sobrante`, `id_predio`, `id_planta`, `no_guia`, `no_pinas_agave`, `agave_kg`, `agave_coccion_kg`, `porcentaje_art`, `tipo`, `id_verificador`, `actualizado`, `fecha_subio`) VALUES
(1, '01-1', '01-3', '', '', 'P1', 133, 'g180', 10, 10, 10, 8.33, 1, 1, 1, '2021-08-25 18:21:23'),
(2, '01-2', '01-3', '', '', 'P1', 133, 'g181', 50, 20, 20, 8.33, 1, 1, 1, '2021-08-25 18:21:23');

--
-- Disparadores `rv_produccion_ensamble`
--
DELIMITER $$
CREATE TRIGGER `Bitácora_Editar_Ensamble` AFTER UPDATE ON `rv_produccion_ensamble` FOR EACH ROW BEGIN 

INSERT INTO siig.rv_actualizaciones(tabla, id_tabla, verificador_id, accion, estado, estatus) 

 VALUES('produccion_ensamble',NEW.id_ensamble_union, NEW.id_verificador, 'Update', '0', '1') ;

 END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `Bitácora_Insertar_Ensamble` AFTER INSERT ON `rv_produccion_ensamble` FOR EACH ROW BEGIN 

INSERT INTO siig.rv_actualizaciones(tabla, id_tabla, verificador_id, accion, estado, estatus) 

 VALUES('produccion_ensamble',NEW.id_ensamble_union, NEW.id_verificador, 'Insert', '0', '1') ;

END
$$
DELIMITER ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `rv_produccion_ensamble`
--
ALTER TABLE `rv_produccion_ensamble`
  ADD PRIMARY KEY (`id_ensamble`),
  ADD KEY `id_ensamble_union` (`id_ensamble_union`),
  ADD KEY `id_produccion_entrada` (`id_produccion_entrada`),
  ADD KEY `id_agave_sobrante` (`id_agave_sobrante`),
  ADD KEY `id_agave_cocido_sobrante` (`id_agave_cocido_sobrante`),
  ADD KEY `id_predio` (`id_predio`),
  ADD KEY `id_planta` (`id_planta`),
  ADD KEY `id_verificador` (`id_verificador`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `rv_produccion_ensamble`
--
ALTER TABLE `rv_produccion_ensamble`
  MODIFY `id_ensamble` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
