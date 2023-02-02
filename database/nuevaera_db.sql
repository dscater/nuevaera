-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 02-02-2023 a las 17:05:46
-- Versión del servidor: 5.7.33
-- Versión de PHP: 7.4.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `nuevaera_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `almacens`
--

CREATE TABLE `almacens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `producto_id` bigint(20) UNSIGNED NOT NULL,
  `stock_actual` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cajas`
--

CREATE TABLE `cajas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `codigo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_registro` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cajas`
--

INSERT INTO `cajas` (`id`, `codigo`, `nombre`, `descripcion`, `fecha_registro`, `created_at`, `updated_at`) VALUES
(1, 'C1', 'CAJA 1', '', '2023-01-12', '2023-01-12 15:39:05', '2023-01-30 15:24:55'),
(2, 'C2', 'CAJA 2', 'CAJA 2', '2023-01-12', '2023-01-12 15:39:48', '2023-01-30 15:25:01'),
(3, 'C3', 'CAJA 3', '', '2023-01-12', '2023-01-12 15:40:56', '2023-01-30 15:25:07'),
(4, 'C4', 'CAJA 4', 'DESC 4', '2023-02-02', '2023-02-02 17:03:46', '2023-02-02 17:03:46');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `caja_usuarios`
--

CREATE TABLE `caja_usuarios` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `caja_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `caja_usuarios`
--

INSERT INTO `caja_usuarios` (`id`, `user_id`, `caja_id`, `created_at`, `updated_at`) VALUES
(1, 3, 1, NULL, '2023-01-30 15:27:54');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ci` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ci_exp` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nit` varchar(155) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fono` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dir` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_registro` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `nombre`, `ci`, `ci_exp`, `nit`, `fono`, `dir`, `fecha_registro`, `created_at`, `updated_at`) VALUES
(2, 'JUAN PERES', '452323', 'LP', '1111', '777777; 66666', 'LOS OLIVOS', '2023-01-19', '2023-01-19 14:10:06', '2023-01-19 14:10:16'),
(3, 'JOSE PAREDES', '4343', 'LP', '22', '222', '', '2023-01-20', '2023-01-20 18:26:13', '2023-01-20 18:26:13'),
(4, 'CARLOS MARTINEZ', '4343', 'LP', '2323', '222', '', '2023-01-25', '2023-01-25 16:54:54', '2023-01-25 16:54:54'),
(5, 'MARCELO CONDORI CONDORI', '23232', 'LP', '', '3333', '', '2023-01-29', '2023-01-29 15:58:08', '2023-01-29 15:58:08'),
(6, 'MARCOS LIMACHI', '4332', 'CB', '3322', '222', '', '2023-01-29', '2023-01-29 15:59:04', '2023-01-29 15:59:04');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracions`
--

CREATE TABLE `configuracions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre_sistema` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alias` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `razon_social` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ciudad` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dir` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fono` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `web` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `actividad` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `correo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `configuracions`
--

INSERT INTO `configuracions` (`id`, `nombre_sistema`, `alias`, `razon_social`, `nit`, `ciudad`, `dir`, `fono`, `web`, `actividad`, `correo`, `logo`, `created_at`, `updated_at`) VALUES
(1, 'SISTEMA DE INVENTARIO Y VENTAS', 'NUEVAERA', 'EMPRESA NUEVAERA', '10000000000', 'LA PAZ', 'LA PAZ', '222222', '', 'ACTIVIDAD', '', 'logo.png', NULL, '2023-01-13 18:32:05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `creditos`
--

CREATE TABLE `creditos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `orden_id` bigint(20) UNSIGNED NOT NULL,
  `estado` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_ordens`
--

CREATE TABLE `detalle_ordens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `orden_id` bigint(20) UNSIGNED NOT NULL,
  `producto_id` bigint(20) UNSIGNED NOT NULL,
  `sucursal_stock_id` bigint(20) UNSIGNED NOT NULL,
  `cantidad` int(11) NOT NULL,
  `venta_mayor` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `precio` decimal(24,2) NOT NULL,
  `subtotal` decimal(24,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `devolucions`
--

CREATE TABLE `devolucions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `orden_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `fecha_registro` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `devolucion_detalles`
--

CREATE TABLE `devolucion_detalles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `devolucion_id` bigint(20) UNSIGNED NOT NULL,
  `detalle_orden_id` bigint(20) UNSIGNED NOT NULL,
  `producto_id` bigint(20) UNSIGNED NOT NULL,
  `sucursal_stock_id` bigint(20) UNSIGNED NOT NULL,
  `cantidad` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupos`
--

CREATE TABLE `grupos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_registro` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `grupos`
--

INSERT INTO `grupos` (`id`, `nombre`, `descripcion`, `fecha_registro`, `created_at`, `updated_at`) VALUES
(2, 'GRUPO 1', '', '2023-01-13', '2023-01-13 18:08:22', '2023-01-13 18:08:22'),
(11, '001- ORING SERIE 000 1/16', NULL, '2023-01-19', '2023-01-19 23:01:05', '2023-01-19 23:01:05'),
(12, '002- ORING SERIE 100 3/32', NULL, '2023-01-19', '2023-01-19 23:01:06', '2023-01-19 23:01:06'),
(13, '003- ORING SERIE 200 1/8', NULL, '2023-01-19', '2023-01-19 23:01:06', '2023-01-19 23:01:06'),
(14, '004- ORING SERIE 300 3/16', NULL, '2023-01-19', '2023-01-19 23:01:06', '2023-01-19 23:01:06'),
(15, '005- ORING SERIE 400 1/4', NULL, '2023-01-19', '2023-01-19 23:01:06', '2023-01-19 23:01:06'),
(16, '006- ORING MILIMETRICO - 2MM', NULL, '2023-01-19', '2023-01-19 23:01:06', '2023-01-19 23:01:06'),
(17, '007- ORING MILIMETRICO - 3MM', NULL, '2023-01-19', '2023-01-19 23:01:07', '2023-01-19 23:01:07'),
(18, '008- ORING MILIMETRICO - 4MM', NULL, '2023-01-19', '2023-01-19 23:01:07', '2023-01-19 23:01:07'),
(19, '009- ORING MILIMETRICO - 5MM', NULL, '2023-01-19', '2023-01-19 23:01:07', '2023-01-19 23:01:07'),
(20, '010- ORING MILIMETRICO - 5,5MM', NULL, '2023-01-19', '2023-01-19 23:01:07', '2023-01-19 23:01:07'),
(21, '011- ORING MILIMETRICO - 6MM', NULL, '2023-01-19', '2023-01-19 23:01:07', '2023-01-19 23:01:07'),
(22, '012- ORING POR METRO', NULL, '2023-01-19', '2023-01-19 23:01:07', '2023-01-19 23:01:07'),
(23, '013- KITS DE ORINGS', NULL, '2023-01-19', '2023-01-19 23:01:07', '2023-01-19 23:01:07'),
(24, '014- POLYPACK 1/8 - 125', NULL, '2023-01-19', '2023-01-19 23:01:07', '2023-01-19 23:01:07'),
(25, '015- POLYPACK 3/16 - 187', NULL, '2023-01-19', '2023-01-19 23:01:07', '2023-01-19 23:01:07'),
(26, '016- POLYPACK 1/4 - 250', NULL, '2023-01-19', '2023-01-19 23:01:07', '2023-01-19 23:01:07'),
(27, '017- POLYPACK 5/16 - 312', NULL, '2023-01-19', '2023-01-19 23:01:08', '2023-01-19 23:01:08'),
(28, '018- POLYPACK 3/8 - 375', NULL, '2023-01-19', '2023-01-19 23:01:08', '2023-01-19 23:01:08'),
(29, '019- POLYPACK 1/2 - 500', NULL, '2023-01-19', '2023-01-19 23:01:08', '2023-01-19 23:01:08'),
(30, '020- CUBETAS POLYPACK AGEL', NULL, '2023-01-19', '2023-01-19 23:01:08', '2023-01-19 23:01:08'),
(31, '021- CUBETAS MILIMETRICAS', NULL, '2023-01-19', '2023-01-19 23:01:08', '2023-01-19 23:01:08'),
(32, '022- CUBETAS MILIMETRICAS AGEL', NULL, '2023-01-19', '2023-01-19 23:01:08', '2023-01-19 23:01:08'),
(33, '023- CUBETAS PLANAS HALLITE 616', NULL, '2023-01-19', '2023-01-19 23:01:09', '2023-01-19 23:01:09'),
(34, '023.1- CUBETAS PLANAS AGEL', NULL, '2023-01-19', '2023-01-19 23:01:09', '2023-01-19 23:01:09'),
(35, '024- CUBETAS PLANAS TIPO 416', NULL, '2023-01-19', '2023-01-19 23:01:09', '2023-01-19 23:01:09'),
(36, '025- ANTICHOQUES CONJUNTOS', NULL, '2023-01-19', '2023-01-19 23:01:09', '2023-01-19 23:01:09'),
(37, '026- ANTICHOQUES ARRUELA DE PU', NULL, '2023-01-19', '2023-01-19 23:01:09', '2023-01-19 23:01:09'),
(38, '027- CONJUNTO PTFE CON BRONZE - ORING EXTERNO', NULL, '2023-01-19', '2023-01-19 23:01:09', '2023-01-19 23:01:09'),
(39, '028- CONJUNTO PTFE CON BRONZE - ORING INTERNO', NULL, '2023-01-19', '2023-01-19 23:01:09', '2023-01-19 23:01:09'),
(40, '029.1- RESPALDO DE CUBETAS', NULL, '2023-01-19', '2023-01-19 23:01:09', '2023-01-19 23:01:09'),
(41, '030- CUBETAS DOBLE ACCION PARA PISTON 715', NULL, '2023-01-19', '2023-01-19 23:01:09', '2023-01-19 23:01:09'),
(42, '030.1 - CUBETAS DOBLE ACCION PARA PISTON CAP AGEL', NULL, '2023-01-19', '2023-01-19 23:01:09', '2023-01-19 23:01:09'),
(43, '031- HALLITE 53 PISTON SEALS PULGADAS', NULL, '2023-01-19', '2023-01-19 23:01:09', '2023-01-19 23:01:09'),
(44, '032- HALLITE 53 PISTON SEAL MILIMETRICAS', NULL, '2023-01-19', '2023-01-19 23:01:09', '2023-01-19 23:01:09'),
(45, '033- PS STYLE', NULL, '2023-01-19', '2023-01-19 23:01:09', '2023-01-19 23:01:09'),
(46, '034- CONJUNTO PTFE - ATF', NULL, '2023-01-19', '2023-01-19 23:01:09', '2023-01-19 23:01:09'),
(47, '034.1- CONJUNTO PTFE - ATF', NULL, '2023-01-19', '2023-01-19 23:01:09', '2023-01-19 23:01:09'),
(48, '035- CONJUNTOS ZO', NULL, '2023-01-19', '2023-01-19 23:01:09', '2023-01-19 23:01:09'),
(49, '036- VEE PACKING 1501', NULL, '2023-01-19', '2023-01-19 23:01:09', '2023-01-19 23:01:09'),
(50, '036.1- VEEPACKINGS AGEL', NULL, '2023-01-19', '2023-01-19 23:01:09', '2023-01-19 23:01:09'),
(51, '037- VEE PACKING SETS', NULL, '2023-01-19', '2023-01-19 23:01:09', '2023-01-19 23:01:09'),
(52, '038- LIMPIADOR METALICO M.M. HALLITE - 860', NULL, '2023-01-19', '2023-01-19 23:01:09', '2023-01-19 23:01:09'),
(53, '039- LIMPIADOR METALICO PULGADA HALLITE - 860', NULL, '2023-01-19', '2023-01-19 23:01:09', '2023-01-19 23:01:09'),
(54, '040- LIMPIADOR METALICO PULGADA - 862', NULL, '2023-01-19', '2023-01-19 23:01:09', '2023-01-19 23:01:09'),
(55, '041- LIMPIADORES METALICOS M.M. AGEL', NULL, '2023-01-19', '2023-01-19 23:01:09', '2023-01-19 23:01:09'),
(56, '041.1- LIMPIADOR METALICO PULGADA TIPO 7K AGEL', NULL, '2023-01-19', '2023-01-19 23:01:09', '2023-01-19 23:01:09'),
(57, '041.2- LIMPIADOR METALICO PULPAGA TIPO 5P AGEL', NULL, '2023-01-19', '2023-01-19 23:01:09', '2023-01-19 23:01:09'),
(58, '041.3- RETENES DE GRASA', NULL, '2023-01-19', '2023-01-19 23:01:09', '2023-01-19 23:01:09'),
(59, '042- LIMPIADORES DE GOMA PULGADA - ST', NULL, '2023-01-19', '2023-01-19 23:01:09', '2023-01-19 23:01:09'),
(60, '042.1- LIMPIADORES DE GOMA AGEL - ST', NULL, '2023-01-19', '2023-01-19 23:01:10', '2023-01-19 23:01:10'),
(61, '042.2- LIMPIADORES DE GOMA ARCA', NULL, '2023-01-19', '2023-01-19 23:01:10', '2023-01-19 23:01:10'),
(62, '043- RASPADORES LIMPIADORES ARCA', NULL, '2023-01-19', '2023-01-19 23:01:10', '2023-01-19 23:01:10'),
(63, '044- LIMPIADORES AN', NULL, '2023-01-19', '2023-01-19 23:01:10', '2023-01-19 23:01:10'),
(64, '045- LIMPIADORES DE GOMA AGEL', NULL, '2023-01-19', '2023-01-19 23:01:10', '2023-01-19 23:01:10'),
(65, '046- LIMPIADOR TIPO RP-28', NULL, '2023-01-19', '2023-01-19 23:01:10', '2023-01-19 23:01:10'),
(66, '047- GUIAS HALLITE 533', NULL, '2023-01-19', '2023-01-19 23:01:10', '2023-01-19 23:01:10'),
(67, '047.1- GUIAS HALLITE 533', NULL, '2023-01-19', '2023-01-19 23:01:10', '2023-01-19 23:01:10'),
(68, '047.2 GUIAS ANG', NULL, '2023-01-19', '2023-01-19 23:01:10', '2023-01-19 23:01:10'),
(69, '049- GUIAS POR METRO', NULL, '2023-01-19', '2023-01-19 23:01:10', '2023-01-19 23:01:10'),
(70, '050- GUIAS ARCA', NULL, '2023-01-19', '2023-01-19 23:01:10', '2023-01-19 23:01:10'),
(71, '050.1- GUIA W2 AGEL', NULL, '2023-01-19', '2023-01-19 23:01:10', '2023-01-19 23:01:10'),
(72, '051- GUIAS AGEL 1.5MM AGEL', NULL, '2023-01-19', '2023-01-19 23:01:10', '2023-01-19 23:01:10'),
(73, '052- GUIAS AGEL 2MM AGEL', NULL, '2023-01-19', '2023-01-19 23:01:10', '2023-01-19 23:01:10'),
(74, '053- GUIAS AGEL 2.5MM AGEL', NULL, '2023-01-19', '2023-01-19 23:01:10', '2023-01-19 23:01:10'),
(75, '054- GUIAS AGEL 3MM AGEL', NULL, '2023-01-19', '2023-01-19 23:01:10', '2023-01-19 23:01:10'),
(76, '055- GUIAS AGEL 4MM Y 4.5MM AGEL', NULL, '2023-01-19', '2023-01-19 23:01:10', '2023-01-19 23:01:10'),
(77, '056- GUIAS MARUCCI - BAS AGEL', NULL, '2023-01-19', '2023-01-19 23:01:10', '2023-01-19 23:01:10'),
(78, '057- GUIAS MARUCCI - HYV AGEL', NULL, '2023-01-19', '2023-01-19 23:01:10', '2023-01-19 23:01:10'),
(79, '058- GUIAS ESPECIALES AGEL', NULL, '2023-01-19', '2023-01-19 23:01:10', '2023-01-19 23:01:10'),
(80, '059- HYVA JUEGOS AGEL', NULL, '2023-01-19', '2023-01-19 23:01:10', '2023-01-19 23:01:10'),
(81, '059.1- HYM JUEGOS DE BOTELLON', NULL, '2023-01-19', '2023-01-19 23:01:10', '2023-01-19 23:01:10'),
(82, '060- JUEGO KOMATSU AGEL', NULL, '2023-01-19', '2023-01-19 23:01:10', '2023-01-19 23:01:10'),
(83, '061- JUEGO VOLVO HALLITE', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(84, '062- JUEGO VOLVO AGEL', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(85, '063- JUEGOS CAT AGEL', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(86, '064- JUEGO  CASE AGEL', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(87, '065- JUEGO JCB AGEL', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(88, '066- GOMAS CUADRADAS - AQB AGEL', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(89, '067- JUEGO JHON DEERE', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(90, '069- PRODUCTOS VARIOS', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(91, '070- PRODUCTOS VARIOS RETENES', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(92, '070.1- PRODUCTOS VARIOS RETENES', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(93, '070.2- PRODUCTOS VARIOS RETENES DE GRASA', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(94, '071- MANGUERAS', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(95, '072- MANGUERA R2', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(96, '073- MANGUERA R12', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(97, '074- MANGUERA 4SP', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(98, '075- MANGUERA 4SH', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(99, '076- MANGUERA R13', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(100, '077- MANGUERA R15', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(101, '078- MANGUERA R6 FLUIDOS HIDRAULICOS LONA', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(102, '079- MANGUERA R5', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(103, '080- MANGUERA R7', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(104, '081- MANGUERA R14', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(105, '082- MANGUERA AIRMASTER', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(106, '083- MANGUERA ACQUATANK', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(107, '084- PROTECTOR DE MANGUERA', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(108, '085- CASQUILLOS PARA MANGUERA IG1 - LATON', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(109, '086- SALVAVIDAS', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(110, '087- UNION', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(111, '088- CASQUILLO PARA R1A', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(112, '089- CASQUILLO PARA R1AT', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(113, '090- CASQUILLO PARA R6/R7/R8', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(114, '091- CASQUILLO PARA R2A (R2/R12)', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(115, '092- CASQUILLO PARA R2AT', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(116, '093- CASQUILLOS PARA R1AT / R3', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(117, '094- CASQUILLOS PARA  R1AT/R2AT/R17', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(118, '095- CASQUILLOS PARA R17 / R1', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(119, '096- CASQUILLOS PARA R12 (4SH - 4SP)', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(120, '097- CASQUILLO PARA SAE 100 R12', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(121, '098- CASQUILLOS PARA 4SH', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(122, '099- CASQUILLOS PARA 4SH R13 / R15', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(123, '100- NIPLES ASIENTO PLANO HEMBRA', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(124, '101- NIPLES ASIENTO PLANO HEMBRA HEXAGONO', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(125, '102- ASIENTO PLANO HEMBRA 45º', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(126, '103- ASIENTO PLANO H. 45º EXTREMA PRESION', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(127, '104- ASIENTO PLANO HEMBRA 90º', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(128, '105- ASIENTO PLANO H. 90º EXTREMA PRESION', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(129, '106- ASIENTO PLANO MACHO', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(130, '107- JIC HEMBRA RECTO', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(131, '108- SPIRAL JIC HEMBRA', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(132, '109- JIC HEMBRA 45º', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(133, '110- JIC HEMBRA 90º', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(134, '111- JIC MACHO', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(135, '112- NPS - BSP HEMBRA RECTO', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(136, '113- NPS- BSP 45º', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(137, '114- NPS - BSP 90º', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(138, '115- NPT MACHO', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(139, '116- ACOPLES RAPIDOS', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(140, '117- CODE 61 RECTO', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(141, '118- CODE 61 SPIRAL 45º', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(142, '119- CODE 61 SPIRAL 90º', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(143, '120- CODE 61 RECTO LARGO', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(144, '121- CODE 61 LARGO 45º', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(145, '122- CODE 61 LARGO 90º', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(146, '123- CODE 62 RECTO', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(147, '124- CODE 62 45º', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(148, '125- CODE 62 90º', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(149, '126- CODE 62 RECTO LARGO', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(150, '127- CODE 62 LARGO 45º', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(151, '128- CODE 62 LARGO 90º', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(152, '129- SUPERCAT RECTO', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(153, '130- SUPERCAT 45º', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(154, '131- SUPERCAT 90º', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(155, '132- NIPLE MM HEMBRA RECTO', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(156, '133- NIPLE MM HEMBRA 45º', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(157, '134- NIPLE MM HEMBRA 90º', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(158, '135- NIPLE MM MACHO', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(159, '136- JIC JAPANESE 60º CONE METRIC', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(160, '137- ESPIGA AST FIT A 90º H.G. METRICA', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(161, '138- ESPIGA AST FIT MACHO BSP 600.600', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(162, '139- ESPIGA AST FIT HEMBRA G.BSP', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(163, '140- ESPIGA AST FIT A 45º HEMBRA G. BSPO', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(164, '141- ADAPTADOR MACHO NPT - NPT IOC', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(165, '142- ADAPTADOR MACHO NTP - JIC IOA', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(166, '143- ADAPTADOR 90º M. NTP - JIC (I2A)', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(167, '144- ADAPTADOR 90º MACHO JIC (I2D)', NULL, '2023-01-19', '2023-01-19 23:01:11', '2023-01-19 23:01:11'),
(168, '145- TEE MACHO JIC', NULL, '2023-01-19', '2023-01-19 23:01:12', '2023-01-19 23:01:12'),
(169, '146- ADAPTADOR M. NPT H. G. JIC (IOX)', NULL, '2023-01-19', '2023-01-19 23:01:12', '2023-01-19 23:01:12'),
(170, '147- ADAPTADOR M.  JIC - JIC (IOB)', NULL, '2023-01-19', '2023-01-19 23:01:12', '2023-01-19 23:01:12'),
(171, '148- ADAPTADOR H. G. NPS - M. NPT (IOC)', NULL, '2023-01-19', '2023-01-19 23:01:12', '2023-01-19 23:01:12'),
(172, '149- ADAPTADOR 90º H. G. NPS - M. NPT (I2G)', NULL, '2023-01-19', '2023-01-19 23:01:12', '2023-01-19 23:01:12'),
(173, '150- ADAPTADOR 90º M. NPT - MACHO NPT (I2C)', NULL, '2023-01-19', '2023-01-19 23:01:12', '2023-01-19 23:01:12'),
(174, '152- ADAPTADOR 90º H. NPT - H. NPT', NULL, '2023-01-19', '2023-01-19 23:01:12', '2023-01-19 23:01:12'),
(175, '154- TEE H. NPT - H. NPT - H.NPT', NULL, '2023-01-19', '2023-01-19 23:01:12', '2023-01-19 23:01:12'),
(176, '155- TEE M. NPT - M. NPT - M. NTP', NULL, '2023-01-19', '2023-01-19 23:01:12', '2023-01-19 23:01:12'),
(177, '156- ADAPTADOR MACHO ORING BOSS-JIC', NULL, '2023-01-19', '2023-01-19 23:01:12', '2023-01-19 23:01:12'),
(178, '157- ADAPTADOR  MACHO ASIENTO PLANO', NULL, '2023-01-19', '2023-01-19 23:01:12', '2023-01-19 23:01:12'),
(179, '160- ESPIGA AST FIT ANULAR 299.920', NULL, '2023-01-19', '2023-01-19 23:01:12', '2023-01-19 23:01:12'),
(180, '161- RECORES AST FIT', NULL, '2023-01-19', '2023-01-19 23:01:12', '2023-01-19 23:01:12'),
(181, '162- LUBRICANTES CAM2', NULL, '2023-01-19', '2023-01-19 23:01:12', '2023-01-19 23:01:12'),
(182, '163 - JUEGOS DE BOMBA', NULL, '2023-01-19', '2023-01-19 23:01:12', '2023-01-19 23:01:12');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_accions`
--

CREATE TABLE `historial_accions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `accion` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `datos_original` text COLLATE utf8mb4_unicode_ci,
  `datos_nuevo` text COLLATE utf8mb4_unicode_ci,
  `modulo` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `importancion_aperturas`
--

CREATE TABLE `importancion_aperturas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `lugar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_registros` bigint(20) NOT NULL,
  `cambio_stock` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingreso_productos`
--

CREATE TABLE `ingreso_productos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `lugar` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `producto_id` bigint(20) UNSIGNED NOT NULL,
  `proveedor_id` bigint(20) UNSIGNED NOT NULL,
  `precio_compra` decimal(8,2) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `tipo_ingreso_id` bigint(20) UNSIGNED NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `fecha_registro` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `kardex_productos`
--

CREATE TABLE `kardex_productos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `lugar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo_registro` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `registro_id` bigint(20) UNSIGNED DEFAULT NULL,
  `producto_id` bigint(20) UNSIGNED NOT NULL,
  `detalle` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `precio` decimal(24,2) NOT NULL,
  `tipo_is` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cantidad_ingreso` double DEFAULT NULL,
  `cantidad_salida` double DEFAULT NULL,
  `cantidad_saldo` double NOT NULL,
  `cu` decimal(24,2) NOT NULL,
  `monto_ingreso` decimal(24,2) DEFAULT NULL,
  `monto_salida` decimal(24,2) DEFAULT NULL,
  `monto_saldo` decimal(24,2) NOT NULL,
  `fecha` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000002_create_users_table', 1),
(2, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(3, '2022_10_13_132625_create_configuracions_table', 1),
(4, '2023_01_11_140318_create_sucursals_table', 1),
(5, '2023_01_11_140319_create_cajas_table', 1),
(6, '2023_01_11_140426_create_sucursal_usuarios_table', 1),
(7, '2023_01_11_140527_create_proveedors_table', 1),
(8, '2023_01_11_140549_create_grupos_table', 1),
(9, '2023_01_11_140550_create_productos_table', 1),
(10, '2023_01_11_140656_create_tipo_ingresos_table', 1),
(11, '2023_01_11_140657_create_ingreso_productos_table', 1),
(12, '2023_01_11_140668_create_almacens_table', 1),
(13, '2023_01_11_140736_create_tipo_salidas_table', 1),
(14, '2023_01_11_140748_create_salida_productos_table', 1),
(15, '2023_01_11_140850_create_transferencia_productos_table', 1),
(16, '2023_01_11_141105_create_clientes_table', 1),
(17, '2023_01_11_141119_create_orden_ventas_table', 1),
(18, '2023_01_11_141136_create_devolucions_table', 1),
(19, '2023_01_11_141148_create_detalle_ordens_table', 1),
(20, '2023_01_11_141236_create_importancion_aperturas_table', 1),
(21, '2023_01_11_144534_create_kardex_productos_table', 1),
(22, '2023_01_11_144623_create_sucursal_stocks_table', 1),
(23, '2023_01_11_145555_create_devolucion_detalles_table', 1),
(24, '2023_01_26_144253_create_historial_accions_table', 2),
(25, '2023_01_30_105000_create_creditos_table', 3),
(26, '2023_01_30_111547_create_caja_usuarios_table', 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `orden_ventas`
--

CREATE TABLE `orden_ventas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `caja_id` bigint(20) UNSIGNED NOT NULL,
  `cliente_id` bigint(20) UNSIGNED NOT NULL,
  `nit` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total` decimal(24,2) NOT NULL,
  `tipo_venta` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_registro` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `codigo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `medida` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `grupo_id` bigint(20) UNSIGNED NOT NULL,
  `precio` decimal(24,2) NOT NULL,
  `precio_mayor` decimal(24,2) NOT NULL,
  `stock_min` int(11) NOT NULL,
  `descontar_stock` enum('SI','NO') COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_registro` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedors`
--

CREATE TABLE `proveedors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dir` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fono` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre_contacto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_registro` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `proveedors`
--

INSERT INTO `proveedors` (`id`, `nombre`, `nit`, `dir`, `fono`, `nombre_contacto`, `descripcion`, `fecha_registro`, `created_at`, `updated_at`) VALUES
(1, 'PROVEEDOR 1', '100000001', 'LOS OLIVOS #333', '22222; 777777; 66666', 'JUAN PERES SOLIZ', 'DESCRIPCION PROVEEDOR', '2023-01-13', '2023-01-13 18:24:11', '2023-01-13 18:24:33');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `salida_productos`
--

CREATE TABLE `salida_productos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `lugar` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `producto_id` bigint(20) UNSIGNED NOT NULL,
  `cantidad` int(11) NOT NULL,
  `tipo_salida_id` bigint(20) UNSIGNED NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `fecha_registro` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sucursal_stocks`
--

CREATE TABLE `sucursal_stocks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `producto_id` bigint(20) UNSIGNED NOT NULL,
  `stock_actual` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_ingresos`
--

CREATE TABLE `tipo_ingresos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tipo_ingresos`
--

INSERT INTO `tipo_ingresos` (`id`, `nombre`, `descripcion`, `created_at`, `updated_at`) VALUES
(1, 'INGRESO TIPO 1', '', '2023-01-16 14:46:18', '2023-01-16 14:46:28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_salidas`
--

CREATE TABLE `tipo_salidas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tipo_salidas`
--

INSERT INTO `tipo_salidas` (`id`, `nombre`, `descripcion`, `created_at`, `updated_at`) VALUES
(1, 'SALIDA TIPO 1', '', '2023-01-30 16:40:54', '2023-01-30 16:40:54');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `transferencia_productos`
--

CREATE TABLE `transferencia_productos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `origen` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `destino` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `producto_id` bigint(20) UNSIGNED NOT NULL,
  `cantidad` int(11) NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `fecha_registro` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `usuario` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `paterno` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `materno` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ci` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ci_exp` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dir` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `correo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fono` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` enum('ADMINISTRADOR','SUPERVISOR','CAJA') COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `acceso` int(11) NOT NULL,
  `fecha_registro` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `usuario`, `nombre`, `paterno`, `materno`, `ci`, `ci_exp`, `dir`, `correo`, `fono`, `tipo`, `foto`, `password`, `acceso`, `fecha_registro`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin', 'admin', NULL, '', '', '', NULL, '', 'ADMINISTRADOR', NULL, '$2y$10$RrCZZySOwPej2gMFWsrjMe6dLzfaL5Q88h4J75I1FesEBRNPwq1x.', 1, '2023-01-11', NULL, NULL),
(2, 'JPERES', 'JUAN', 'PERES', 'PERES', '2222', 'LP', 'LOS OLIVOS', '', '77777; 66666', 'SUPERVISOR', 'default.png', '$2y$10$cAN6ZRMeN.srpdKQAjou2e96/34TzhbJcpBjsfTfMU2evwNXSIa9G', 1, '2023-01-12', '2023-01-12 16:08:02', '2023-01-12 16:27:10'),
(3, 'MGONZALES', 'MARIA', 'GONZALES', '', '3333', 'CB', 'LOS OLIVOS', '', '666666', 'CAJA', '1674665167_MGONZALES.jpg', '$2y$10$xnFmi0gR0B.pPkCRJSeaQ.FIG82P/Jr6x5IGZqBteP5q0nKoa5C2q', 1, '2023-01-12', '2023-01-12 16:12:35', '2023-01-25 16:46:07');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `almacens`
--
ALTER TABLE `almacens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `cajas`
--
ALTER TABLE `cajas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `caja_usuarios`
--
ALTER TABLE `caja_usuarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `caja_usuarios_user_id_foreign` (`user_id`),
  ADD KEY `caja_usuarios_caja_id_foreign` (`caja_id`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `configuracions`
--
ALTER TABLE `configuracions`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `creditos`
--
ALTER TABLE `creditos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `detalle_ordens`
--
ALTER TABLE `detalle_ordens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `detalle_ordens_orden_id_foreign` (`orden_id`),
  ADD KEY `detalle_ordens_producto_id_foreign` (`producto_id`);

--
-- Indices de la tabla `devolucions`
--
ALTER TABLE `devolucions`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `devolucion_detalles`
--
ALTER TABLE `devolucion_detalles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `devolucion_detalles_devolucion_id_foreign` (`devolucion_id`),
  ADD KEY `devolucion_detalles_producto_id_foreign` (`producto_id`);

--
-- Indices de la tabla `grupos`
--
ALTER TABLE `grupos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `historial_accions`
--
ALTER TABLE `historial_accions`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `importancion_aperturas`
--
ALTER TABLE `importancion_aperturas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ingreso_productos`
--
ALTER TABLE `ingreso_productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ingreso_productos_producto_id_foreign` (`producto_id`),
  ADD KEY `ingreso_productos_proveedor_id_foreign` (`proveedor_id`),
  ADD KEY `ingreso_productos_tipo_ingreso_id_foreign` (`tipo_ingreso_id`);

--
-- Indices de la tabla `kardex_productos`
--
ALTER TABLE `kardex_productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kardex_productos_producto_id_foreign` (`producto_id`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `orden_ventas`
--
ALTER TABLE `orden_ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orden_ventas_cliente_id_foreign` (`cliente_id`);

--
-- Indices de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `productos_grupo_id_foreign` (`grupo_id`);

--
-- Indices de la tabla `proveedors`
--
ALTER TABLE `proveedors`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `salida_productos`
--
ALTER TABLE `salida_productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `salida_productos_producto_id_foreign` (`producto_id`),
  ADD KEY `salida_productos_tipo_salida_id_foreign` (`tipo_salida_id`);

--
-- Indices de la tabla `sucursal_stocks`
--
ALTER TABLE `sucursal_stocks`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tipo_ingresos`
--
ALTER TABLE `tipo_ingresos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tipo_salidas`
--
ALTER TABLE `tipo_salidas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `transferencia_productos`
--
ALTER TABLE `transferencia_productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transferencia_productos_producto_id_foreign` (`producto_id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_usuario_unique` (`usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `almacens`
--
ALTER TABLE `almacens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cajas`
--
ALTER TABLE `cajas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `caja_usuarios`
--
ALTER TABLE `caja_usuarios`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `configuracions`
--
ALTER TABLE `configuracions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `creditos`
--
ALTER TABLE `creditos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_ordens`
--
ALTER TABLE `detalle_ordens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `devolucions`
--
ALTER TABLE `devolucions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `devolucion_detalles`
--
ALTER TABLE `devolucion_detalles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `grupos`
--
ALTER TABLE `grupos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=183;

--
-- AUTO_INCREMENT de la tabla `historial_accions`
--
ALTER TABLE `historial_accions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `importancion_aperturas`
--
ALTER TABLE `importancion_aperturas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ingreso_productos`
--
ALTER TABLE `ingreso_productos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `kardex_productos`
--
ALTER TABLE `kardex_productos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `orden_ventas`
--
ALTER TABLE `orden_ventas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `proveedors`
--
ALTER TABLE `proveedors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `salida_productos`
--
ALTER TABLE `salida_productos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sucursal_stocks`
--
ALTER TABLE `sucursal_stocks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tipo_ingresos`
--
ALTER TABLE `tipo_ingresos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `tipo_salidas`
--
ALTER TABLE `tipo_salidas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `transferencia_productos`
--
ALTER TABLE `transferencia_productos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `almacens`
--
ALTER TABLE `almacens`
  ADD CONSTRAINT `almacens_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `caja_usuarios`
--
ALTER TABLE `caja_usuarios`
  ADD CONSTRAINT `caja_usuarios_caja_id_foreign` FOREIGN KEY (`caja_id`) REFERENCES `cajas` (`id`),
  ADD CONSTRAINT `caja_usuarios_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
