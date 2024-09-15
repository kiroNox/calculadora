-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 15-09-2024 a las 04:13:42
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
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `formulas`
--
ALTER TABLE `formulas`
  MODIFY `id_formula` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalles_formulas`
--
ALTER TABLE `detalles_formulas`
  ADD CONSTRAINT `detalles_formulas_ibfk_1` FOREIGN KEY (`id_formula`) REFERENCES `formulas` (`id_formula`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
