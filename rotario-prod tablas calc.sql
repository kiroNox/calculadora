-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 21-09-2024 a las 20:17:25
-- Versión del servidor: 10.1.38-MariaDB
-- Versión de PHP: 7.3.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `rotario-produccion`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalles_formulas`
--

CREATE TABLE `detalles_formulas` (
  `id_formula` int(11) NOT NULL,
  `formula` text NOT NULL,
  `variables` text,
  `condicional` text,
  `orden` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `detalles_formulas`
--

INSERT INTO `detalles_formulas` (`id_formula`, `formula`, `variables`, `condicional`, `orden`) VALUES
(52, 'SUELDO_BASE*0.5599', NULL, 'TIEMPO_TRABAJADOR>=33', 1),
(52, 'SUELDO_BASE*0.5133', NULL, 'TIEMPO_TRABAJADOR>=30', 2),
(52, 'SUELDO_BASE*0.4666', NULL, 'TIEMPO_TRABAJADOR>=27', 3),
(52, 'SUELDO_BASE*0.42', NULL, 'TIEMPO_TRABAJADOR>=24', 4),
(52, 'SUELDO_BASE*0.3733', NULL, 'TIEMPO_TRABAJADOR>=21', 5),
(52, 'SUELDO_BASE*0.3266', NULL, 'TIEMPO_TRABAJADOR>=18', 6),
(52, 'SUELDO_BASE*0.28', NULL, 'TIEMPO_TRABAJADOR>=15', 7),
(52, 'SUELDO_BASE*0.2333', NULL, 'TIEMPO_TRABAJADOR>=12', 8),
(52, 'SUELDO_BASE*0.1866', NULL, 'TIEMPO_TRABAJADOR>=9', 9),
(52, 'SUELDO_BASE*0.14', NULL, 'TIEMPO_TRABAJADOR>=6', 10),
(52, 'SUELDO_BASE*0.933', NULL, 'TIEMPO_TRABAJADOR>=3', 11),
(52, 'SUELDO_BASE*0.467', NULL, 'TIEMPO_TRABAJADOR>=1', 12),
(53, 'TABLA_ESCALAFON', NULL, 'MEDICO', 0),
(54, 'SUELDO_BASE*0.10', NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `formulas`
--

CREATE TABLE `formulas` (
  `id_formula` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `formulas`
--

INSERT INTO `formulas` (`id_formula`, `nombre`, `descripcion`) VALUES
(52, 'TABLA_ESCALAFON', 'calcula el escalafón de un trabajador sin importar su cargo'),
(53, 'ESCALAFON', 'Calcula el escalafón del medico '),
(54, 'SALUD_DEDICACION', 'Prima por dedicación a la actividad del sistema publico único nacional de salud');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usando`
--

CREATE TABLE `usando` (
  `id_formula_uno` int(11) NOT NULL,
  `id_formula_dos` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `detalles_formulas`
--
ALTER TABLE `detalles_formulas`
  ADD KEY `id_formula` (`id_formula`);

--
-- Indices de la tabla `formulas`
--
ALTER TABLE `formulas`
  ADD PRIMARY KEY (`id_formula`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `usando`
--
ALTER TABLE `usando`
  ADD KEY `id_formula_uno` (`id_formula_uno`),
  ADD KEY `id_formula_dos` (`id_formula_dos`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `formulas`
--
ALTER TABLE `formulas`
  MODIFY `id_formula` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalles_formulas`
--
ALTER TABLE `detalles_formulas`
  ADD CONSTRAINT `detalles_formulas_ibfk_1` FOREIGN KEY (`id_formula`) REFERENCES `formulas` (`id_formula`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usando`
--
ALTER TABLE `usando`
  ADD CONSTRAINT `usando_ibfk_1` FOREIGN KEY (`id_formula_uno`) REFERENCES `formulas` (`id_formula`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `usando_ibfk_2` FOREIGN KEY (`id_formula_dos`) REFERENCES `formulas` (`id_formula`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
