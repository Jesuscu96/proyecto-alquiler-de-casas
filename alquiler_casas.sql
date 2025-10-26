-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 26-10-2025 a las 21:54:02
-- Versión del servidor: 9.1.0
-- Versión de PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `alquiler_casas`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `casas_vacacionales`
--

DROP TABLE IF EXISTS `casas_vacacionales`;
CREATE TABLE IF NOT EXISTS `casas_vacacionales` (
  `id_casa` int NOT NULL AUTO_INCREMENT,
  `id_propietario` int NOT NULL,
  `id_comunidad` int NOT NULL,
  `id_provincia` int NOT NULL,
  `id_ciudad` int NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `capacidad` int NOT NULL,
  `precio_noche` decimal(10,2) NOT NULL,
  `disponible` tinyint(1) DEFAULT '1',
  `num_banos` int DEFAULT '1',
  `num_cocinas` int DEFAULT '1',
  `num_hab_individuales` int DEFAULT '0',
  `num_hab_familiares` int DEFAULT '0',
  `num_aparcamientos` int DEFAULT '0',
  `num_lavadora` int DEFAULT '0',
  `num_secadora` int DEFAULT '0',
  `num_lavavajillas` int DEFAULT '0',
  `num_horno` int DEFAULT '0',
  `num_microondas` int DEFAULT '0',
  `num_nevera` int DEFAULT '0',
  `num_congelador` int DEFAULT '0',
  `tiene_wifi` int DEFAULT '0',
  `num_ascensores` int DEFAULT '0',
  `tiene_calefaccion` int DEFAULT '0',
  `tiene_aire_acondicionado` int DEFAULT '0',
  `tiene_piscina` int DEFAULT '0',
  `tiene_banera` int DEFAULT '0',
  `tiene_barbacoa` int DEFAULT '0',
  `tiene_chimenea` int DEFAULT '0',
  `tiene_adaptacion_discapacitados` int DEFAULT '0',
  `tiene_jardin` int DEFAULT '0',
  `tiene_patio` int DEFAULT '0',
  `tiene_sala_cine` int DEFAULT '0',
  `tiene_secador_pelo` int DEFAULT '0',
  `imagen_principal` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id_casa`),
  KEY `id_propietario` (`id_propietario`),
  KEY `id_comunidad` (`id_comunidad`),
  KEY `id_provincia` (`id_provincia`),
  KEY `id_ciudad` (`id_ciudad`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ciudades`
--

DROP TABLE IF EXISTS `ciudades`;
CREATE TABLE IF NOT EXISTS `ciudades` (
  `id_ciudad` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `id_provincia` int NOT NULL,
  PRIMARY KEY (`id_ciudad`),
  KEY `id_provincia` (`id_provincia`)
) ENGINE=InnoDB AUTO_INCREMENT=276 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ciudades`
--

INSERT INTO `ciudades` (`id_ciudad`, `nombre`, `id_provincia`) VALUES
(1, 'Almería', 1),
(2, 'Roquetas de Mar', 1),
(3, 'El Ejido', 1),
(4, 'Níjar', 1),
(5, 'Adra', 1),
(6, 'Cádiz', 2),
(7, 'Jerez de la Frontera', 2),
(8, 'Algeciras', 2),
(9, 'San Fernando', 2),
(10, 'El Puerto de Santa María', 2),
(11, 'Córdoba', 3),
(12, 'Lucena', 3),
(13, 'Puente Genil', 3),
(14, 'Montilla', 3),
(15, 'Priego de Córdoba', 3),
(16, 'Granada', 4),
(17, 'Motril', 4),
(18, 'Almuñécar', 4),
(19, 'Loja', 4),
(20, 'Guadix', 4),
(21, 'Huelva', 5),
(22, 'Lepe', 5),
(23, 'Ayamonte', 5),
(24, 'Isla Cristina', 5),
(25, 'Almonte', 5),
(26, 'Jaén', 6),
(27, 'Linares', 6),
(28, 'Úbeda', 6),
(29, 'Andújar', 6),
(30, 'Martos', 6),
(31, 'Málaga', 7),
(32, 'Marbella', 7),
(33, 'Mijas', 7),
(34, 'Fuengirola', 7),
(35, 'Vélez-Málaga', 7),
(36, 'Sevilla', 8),
(37, 'Dos Hermanas', 8),
(38, 'Alcalá de Guadaíra', 8),
(39, 'Utrera', 8),
(40, 'Écija', 8),
(41, 'Huesca', 9),
(42, 'Monzón', 9),
(43, 'Barbastro', 9),
(44, 'Binéfar', 9),
(45, 'Jaca', 9),
(46, 'Teruel', 10),
(47, 'Alcañiz', 10),
(48, 'Calamocha', 10),
(49, 'Andorra', 10),
(50, 'Utrillas', 10),
(51, 'Zaragoza', 11),
(52, 'Calatayud', 11),
(53, 'Ejea de los Caballeros', 11),
(54, 'Tarazona', 11),
(55, 'Utebo', 11),
(56, 'Oviedo', 12),
(57, 'Gijón', 12),
(58, 'Avilés', 12),
(59, 'Langreo', 12),
(60, 'Siero', 12),
(61, 'Palma de Mallorca', 13),
(62, 'Ibiza', 13),
(63, 'Maó (Mahón)', 13),
(64, 'Manacor', 13),
(65, 'Inca', 13),
(66, 'Las Palmas de Gran Canaria', 14),
(67, 'Telde', 14),
(68, 'Arucas', 14),
(69, 'Gáldar', 14),
(70, 'Agüimes', 14),
(71, 'Santa Cruz de Tenerife', 15),
(72, 'San Cristóbal de La Laguna', 15),
(73, 'Arona', 15),
(74, 'Adeje', 15),
(75, 'Granadilla de Abona', 15),
(76, 'Santander', 16),
(77, 'Torrelavega', 16),
(78, 'Castro Urdiales', 16),
(79, 'Camargo', 16),
(80, 'Piélagos', 16),
(81, 'Albacete', 17),
(82, 'Hellín', 17),
(83, ' Villarrobledo', 17),
(84, 'La Roda', 17),
(85, 'Tobarra', 17),
(86, 'Ciudad Real', 18),
(87, 'Puertollano', 18),
(88, 'Tomelloso', 18),
(89, 'Manzanares', 18),
(90, 'Valdepeñas', 18),
(91, 'Cuenca', 19),
(92, 'Tarancón', 19),
(93, 'San Clemente', 19),
(94, 'Motilla del Palancar', 19),
(95, 'Las Pedroñeras', 19),
(96, 'Guadalajara', 20),
(97, 'Azuqueca de Henares', 20),
(98, 'Marchamalo', 20),
(99, 'Cabanillas del Campo', 20),
(100, 'El Casar', 20),
(101, 'Toledo', 21),
(102, 'Talavera de la Reina', 21),
(103, 'Illescas', 21),
(104, 'Seseña', 21),
(105, 'Guadamur', 21),
(116, 'Albacete', 17),
(117, 'Hellín', 17),
(118, ' Villarrobledo', 17),
(119, 'La Roda', 17),
(120, 'Tobarra', 17),
(121, 'Ciudad Real', 18),
(122, 'Puertollano', 18),
(123, 'Tomelloso', 18),
(124, 'Manzanares', 18),
(125, 'Valdepeñas', 18),
(126, 'Cuenca', 19),
(127, 'Tarancón', 19),
(128, 'San Clemente', 19),
(129, 'Motilla del Palancar', 19),
(130, 'Las Pedroñeras', 19),
(131, 'Guadalajara', 20),
(132, 'Azuqueca de Henares', 20),
(133, 'Marchamalo', 20),
(134, 'Cabanillas del Campo', 20),
(135, 'El Casar', 20),
(136, 'Toledo', 21),
(137, 'Talavera de la Reina', 21),
(138, 'Illescas', 21),
(139, 'Seseña', 21),
(140, 'Guadamur', 21),
(146, 'Burgos', 23),
(147, 'Miranda de Ebro', 23),
(148, 'Aranda de Duero', 23),
(149, 'Briviesca', 23),
(150, 'Medina de Pomar', 23),
(151, 'León', 24),
(152, 'Ponferrada', 24),
(153, 'San Andrés del Rabanedo', 24),
(154, 'Villaquilambre', 24),
(155, 'La Bañeza', 24),
(156, 'Salamanca', 25),
(157, 'Béjar', 25),
(158, 'Ciudad Rodrigo', 25),
(159, 'Santa Marta de Tormes', 25),
(160, 'Moraleja de Enmedio', 25),
(161, 'Segovia', 26),
(162, 'Cuéllar', 26),
(163, 'San Ildefonso', 26),
(164, 'Carbonero el Mayor', 26),
(165, 'Palazuelos de Eresma', 26),
(166, 'Soria', 27),
(167, 'Almazán', 27),
(168, 'Ólvega', 27),
(169, 'Golmayo', 27),
(170, 'San Esteban de Gormaz', 27),
(171, 'Valladolid', 28),
(172, 'Medina del Campo', 28),
(173, 'Tordesillas', 28),
(174, 'Íscar', 28),
(175, 'La Cistérniga', 28),
(176, 'Zamora', 29),
(177, 'Benavente', 29),
(178, 'Toro', 29),
(179, 'La Agencia', 29),
(180, 'Sanabria', 29),
(186, 'Barcelona', 30),
(187, 'Hospitalet de Llobregat', 30),
(188, 'Badalona', 30),
(189, 'Sabadell', 30),
(190, 'Terrassa', 30),
(191, 'Girona', 31),
(192, 'Figueres', 31),
(193, 'Blanes', 31),
(194, 'Lloret de Mar', 31),
(195, 'Salt', 31),
(196, 'Lleida', 32),
(197, 'Tàrrega', 32),
(198, 'Balaguer', 32),
(199, 'Mollerussa', 32),
(200, 'Cervera', 32),
(201, 'Tarragona', 33),
(202, 'Reus', 33),
(203, 'Vendrell', 33),
(204, 'Salou', 33),
(205, 'Cambrils', 33),
(211, 'Madrid', 34),
(212, 'Alcalá de Henares', 34),
(213, 'Móstoles', 34),
(214, 'Fuenlabrada', 34),
(215, 'Getafe', 34),
(221, 'Alicante', 35),
(222, 'Elche', 35),
(223, 'Benidorm', 35),
(224, 'Orihuela', 35),
(225, 'Alcoy', 35),
(226, 'Castellón de la Plana', 36),
(227, 'Villarreal', 36),
(228, 'Burriana', 36),
(229, 'Benicàssim', 36),
(230, 'Onda', 36),
(231, 'Valencia', 37),
(232, 'Torrent', 37),
(233, 'Gandia', 37),
(234, 'Paterna', 37),
(235, 'Mislata', 37),
(241, 'Badajoz', 38),
(242, 'Mérida', 38),
(243, 'Don Benito', 38),
(244, 'Almendralejo', 38),
(245, 'Villanueva de la Serena', 38),
(246, 'Logroño', 44),
(247, 'Calahorra', 44),
(248, 'Arnedo', 44),
(249, 'Haro', 44),
(250, 'Nájera', 44),
(251, 'Pamplona', 45),
(252, 'Tudela', 45),
(253, 'Barañáin', 45),
(254, 'Buñuel', 45),
(255, 'Estella', 45),
(256, 'Vitoria-Gasteiz', 46),
(257, 'Amurrio', 46),
(258, 'Llodio', 46),
(259, 'Laudio', 46),
(260, 'Alegría-Dulantzi', 46),
(261, 'San Sebastián', 47),
(262, 'Irún', 47),
(263, 'Tolosa', 47),
(264, 'Eibar', 47),
(265, 'Mutriku', 47),
(266, 'Bilbao', 48),
(267, 'Barakaldo', 48),
(268, 'Getxo', 48),
(269, 'Portugalete', 48),
(270, 'Santurtzi', 48),
(271, 'Murcia', 49),
(272, 'Cartagena', 49),
(273, 'Lorca', 49),
(274, 'Molina de Segura', 49),
(275, 'San Javier', 49);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comunidades`
--

DROP TABLE IF EXISTS `comunidades`;
CREATE TABLE IF NOT EXISTS `comunidades` (
  `id_comunidad` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_comunidad`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `comunidades`
--

INSERT INTO `comunidades` (`id_comunidad`, `nombre`) VALUES
(1, 'Andalucía'),
(2, 'Aragón'),
(3, 'Asturias'),
(5, 'Canarias'),
(6, 'Cantabria'),
(8, 'Castilla y León'),
(7, 'Castilla-La Mancha'),
(9, 'Cataluña'),
(10, 'Comunidad de Madrid'),
(11, 'Comunidad Valenciana'),
(12, 'Extremadura'),
(13, 'Galicia'),
(4, 'Islas Baleares'),
(14, 'La Rioja'),
(15, 'Navarra'),
(16, 'País Vasco'),
(17, 'Región de Murcia');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `imagenes_casas`
--

DROP TABLE IF EXISTS `imagenes_casas`;
CREATE TABLE IF NOT EXISTS `imagenes_casas` (
  `id_casa` int NOT NULL,
  `imagen_1` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `imagen_2` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `imagen_3` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `imagen_4` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `imagen_5` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `imagen_6` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `imagen_7` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `imagen_8` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `imagen_9` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `imagen_10` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id_casa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `provincias`
--

DROP TABLE IF EXISTS `provincias`;
CREATE TABLE IF NOT EXISTS `provincias` (
  `id_provincia` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `id_comunidad` int NOT NULL,
  PRIMARY KEY (`id_provincia`),
  KEY `id_comunidad` (`id_comunidad`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `provincias`
--

INSERT INTO `provincias` (`id_provincia`, `nombre`, `id_comunidad`) VALUES
(1, 'Almería', 1),
(2, 'Cádiz', 1),
(3, 'Córdoba', 1),
(4, 'Granada', 1),
(5, 'Huelva', 1),
(6, 'Jaén', 1),
(7, 'Málaga', 1),
(8, 'Sevilla', 1),
(9, 'Huesca', 2),
(10, 'Teruel', 2),
(11, 'Zaragoza', 2),
(12, 'Asturias', 3),
(13, 'Illes Balears', 4),
(14, 'Las Palmas', 5),
(15, 'Santa Cruz de Tenerife', 5),
(16, 'Cantabria', 6),
(17, 'Albacete', 7),
(18, 'Ciudad Real', 7),
(19, 'Cuenca', 7),
(20, 'Guadalajara', 7),
(21, 'Toledo', 7),
(22, 'Ávila', 8),
(23, 'Burgos', 8),
(24, 'León', 8),
(25, 'Salamanca', 8),
(26, 'Segovia', 8),
(27, 'Soria', 8),
(28, 'Valladolid', 8),
(29, 'Zamora', 8),
(30, 'Barcelona', 9),
(31, 'Girona', 9),
(32, 'Lleida', 9),
(33, 'Tarragona', 9),
(34, 'Madrid', 10),
(35, 'Alicante', 11),
(36, 'Castellón', 11),
(37, 'Valencia', 11),
(38, 'Badajoz', 12),
(39, 'Cáceres', 12),
(40, 'A Coruña', 13),
(41, 'Lugo', 13),
(42, 'Ourense', 13),
(43, 'Pontevedra', 13),
(44, 'La Rioja', 14),
(45, 'Navarra', 15),
(46, 'Álava', 16),
(47, 'Gipuzkoa', 16),
(48, 'Bizkaia', 16),
(49, 'Murcia', 17);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservas`
--

DROP TABLE IF EXISTS `reservas`;
CREATE TABLE IF NOT EXISTS `reservas` (
  `id_reserva` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `id_casa` int NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `num_huespedes` int NOT NULL,
  `total_precio` decimal(10,2) NOT NULL,
  `estado` enum('pendiente','confirmada','cancelada') COLLATE utf8mb4_general_ci DEFAULT 'pendiente',
  PRIMARY KEY (`id_reserva`),
  KEY `id_usuario` (`id_usuario`),
  KEY `id_casa` (`id_casa`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id_usuario` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `apellidos` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `edad` int DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `rol` enum('admin','cliente','propietario') COLLATE utf8mb4_general_ci NOT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `nombre_usuario` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `username`, `nombre`, `apellidos`, `edad`, `email`, `password`, `rol`, `telefono`) VALUES
(1, 'admin01', 'Ana', 'García', 35, 'ana.admin1@mail.com', 'adminpass1', 'admin', '600111111'),
(2, 'admin02', 'Luis', 'Martínez', 42, 'luis.admin2@mail.com', 'adminpass2', 'admin', '600111112'),
(3, 'admin03', 'Marta', 'Ruiz', 38, 'marta.admin3@mail.com', 'adminpass3', 'admin', '600111113'),
(4, 'admin04', 'Carlos', 'Sánchez', 29, 'carlos.admin4@mail.com', 'adminpass4', 'admin', '600111114'),
(5, 'admin05', 'Elena', 'Fernández', 33, 'elena.admin5@mail.com', 'adminpass5', 'admin', '600111115'),
(6, 'admin06', 'Javier', 'Romero', 40, 'javier.admin6@mail.com', 'adminpass6', 'admin', '600111116'),
(7, 'admin07', 'Patricia', 'Muñoz', 27, 'patricia.admin7@mail.com', 'adminpass7', 'admin', '600111117'),
(8, 'admin08', 'David', 'Alonso', 31, 'david.admin8@mail.com', 'adminpass8', 'admin', '600111118'),
(9, 'cliente01', 'Sergio', 'López', 24, 'sergio.cliente1@mail.com', 'clientepass1', 'cliente', '600222221'),
(10, 'cliente02', 'Cristina', 'Pérez', 36, 'cristina.cliente2@mail.com', 'clientepass2', 'cliente', '600222222'),
(11, 'cliente03', 'Manuel', 'Giménez', 40, 'manuel.cliente3@mail.com', 'clientepass3', 'cliente', '600222223'),
(12, 'cliente04', 'Belén', 'Castro', 29, 'belen.cliente4@mail.com', 'clientepass4', 'cliente', '600222224'),
(13, 'cliente05', 'Miguel', 'Vega', 27, 'miguel.cliente5@mail.com', 'clientepass5', 'cliente', '600222225'),
(14, 'cliente06', 'Lucas', 'Santos', 21, 'lucas.cliente6@mail.com', 'clientepass6', 'cliente', '600222226'),
(15, 'cliente07', 'Sofía', 'Aguirre', 33, 'sofia.cliente7@mail.com', 'clientepass7', 'cliente', '600222227'),
(16, 'cliente08', 'Noelia', 'Díaz', 28, 'noelia.cliente8@mail.com', 'clientepass8', 'cliente', '600222228'),
(17, 'propietario01', 'Rubén', 'Moreno', 44, 'ruben.prop1@mail.com', 'proppass1', 'propietario', '600333331'),
(18, 'propietario02', 'Sara', 'Navarro', 38, 'sara.prop2@mail.com', 'proppass2', 'propietario', '600333332'),
(19, 'propietario03', 'Antonio', 'Serrano', 54, 'antonio.prop3@mail.com', 'proppass3', 'propietario', '600333333'),
(20, 'propietario04', 'Laura', 'Torres', 47, 'laura.prop4@mail.com', 'proppass4', 'propietario', '600333334'),
(21, 'propietario05', 'Isabel', 'Ibáñez', 31, 'isabel.prop5@mail.com', 'proppass5', 'propietario', '600333335'),
(22, 'propietario06', 'Alberto', 'Reyes', 39, 'alberto.prop6@mail.com', 'proppass6', 'propietario', '600333336'),
(23, 'propietario07', 'Paula', 'Ortega', 53, 'paula.prop7@mail.com', 'proppass7', 'propietario', '600333337'),
(24, 'propietario08', 'Tomás', 'Herrera', 45, 'tomas.prop8@mail.com', 'proppass8', 'propietario', '600333338');

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `casas_vacacionales`
--
ALTER TABLE `casas_vacacionales`
  ADD CONSTRAINT `casas_vacacionales_ibfk_1` FOREIGN KEY (`id_propietario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `casas_vacacionales_ibfk_2` FOREIGN KEY (`id_comunidad`) REFERENCES `comunidades` (`id_comunidad`) ON DELETE RESTRICT,
  ADD CONSTRAINT `casas_vacacionales_ibfk_3` FOREIGN KEY (`id_provincia`) REFERENCES `provincias` (`id_provincia`) ON DELETE RESTRICT,
  ADD CONSTRAINT `casas_vacacionales_ibfk_4` FOREIGN KEY (`id_ciudad`) REFERENCES `ciudades` (`id_ciudad`) ON DELETE RESTRICT;

--
-- Filtros para la tabla `ciudades`
--
ALTER TABLE `ciudades`
  ADD CONSTRAINT `ciudades_ibfk_1` FOREIGN KEY (`id_provincia`) REFERENCES `provincias` (`id_provincia`);

--
-- Filtros para la tabla `imagenes_casas`
--
ALTER TABLE `imagenes_casas`
  ADD CONSTRAINT `imagenes_casas_ibfk_1` FOREIGN KEY (`id_casa`) REFERENCES `casas_vacacionales` (`id_casa`) ON DELETE CASCADE;

--
-- Filtros para la tabla `provincias`
--
ALTER TABLE `provincias`
  ADD CONSTRAINT `provincias_ibfk_1` FOREIGN KEY (`id_comunidad`) REFERENCES `comunidades` (`id_comunidad`);

--
-- Filtros para la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD CONSTRAINT `reservas_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservas_ibfk_2` FOREIGN KEY (`id_casa`) REFERENCES `casas_vacacionales` (`id_casa`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
