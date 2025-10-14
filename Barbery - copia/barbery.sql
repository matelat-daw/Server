-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 06-10-2023 a las 18:23:31
-- Versión del servidor: 10.9.7-MariaDB
-- Versión de PHP: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `barbery`
--
CREATE DATABASE IF NOT EXISTS `barbery` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `barbery`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `client`
--

CREATE TABLE `client` (
  `id` int(11) NOT NULL,
  `name` varchar(32) NOT NULL,
  `surname` varchar(32) NOT NULL,
  `surname2` varchar(32) DEFAULT NULL,
  `address` varchar(255) NOT NULL,
  `phone` varchar(12) NOT NULL,
  `email` varchar(48) NOT NULL,
  `pass` varchar(128) NOT NULL,
  `bday` date NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `client`
--

INSERT INTO `client` (`id`, `name`, `surname`, `surname2`, `address`, `phone`, `email`, `pass`, `bday`, `date`, `time`) VALUES
(1, 'César', 'Matelat', 'Borneo', 'Calle Fermín Morín Nº 2, Portal 4, 7º B', '664774821', 'matelat@gmail.com', '$2y$10$wE8iJkd0JOkVBKvDulTFkuig7GuC2RfgJYogQz.fL2eWkYaLmFxB6', '1968-04-05', '2023-02-06', '11:30:00'),
(2, 'César Matelat', '', '', 'Calle Fermín Morín Nº 2, portal 4, 7º B.', '664774822', 'orions68@gmail.com', '$2y$10$qWsuo7BpUr83HotXPUIHk.8aYoRJEiYUa5XyJMI5iI7OE2CnrXcvu', '1968-04-05', '2022-11-23', '11:00:00'),
(3, 'César Matelat', '', '', 'Calle Fermín Morín Nº 2, portal 4, 7º B.', '664774823', 'orions@gmx.net', '$2y$10$MESgIzXHC8unYvQysr/SnuosmhD0uEzyeiRrXYtypErWuOuJqj5X2', '1968-04-05', '2022-11-21', '10:00:00'),
(4, 'Pepe', '', '', 'Elve', 'Cino', '1@1.com', '$2y$10$UxpHYGLpjjuhShnyyxBXYuhIVw.kQCiL8HlUCWJwRz9CtGeHBvaVS', '1970-01-01', '0000-00-00', '00:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `invoice`
--

CREATE TABLE `invoice` (
  `id` int(11) NOT NULL,
  `client_id` int(11) DEFAULT NULL,
  `total` decimal(11,2) NOT NULL,
  `inv_date` date NOT NULL,
  `inv_time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `invoice`
--

INSERT INTO `invoice` (`id`, `client_id`, `total`, `inv_date`, `inv_time`) VALUES
(1, 1, 2800.00, '2023-03-03', '07:23:31'),
(2, 1, 4800.00, '2023-03-03', '19:25:36'),
(3, NULL, 4651.50, '2023-03-08', '05:26:59'),
(4, NULL, 1550.50, '2023-03-08', '06:12:09'),
(5, 1, 2000.00, '2023-03-12', '11:27:59'),
(6, 1, 2300.00, '2023-03-12', '11:55:07');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `service`
--

CREATE TABLE `service` (
  `id` int(11) NOT NULL,
  `service` varchar(128) NOT NULL,
  `price` decimal(11,2) NOT NULL,
  `img` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `service`
--

INSERT INTO `service` (`id`, `service`, `price`, `img`) VALUES
(1, 'Corte a Tijera', 1000.00, 'img/corte.jpg'),
(2, 'Corte a Máquina', 800.00, 'img/machine.jpg'),
(3, 'Afeitado con Navaja', 700.00, 'img/shave.jpg'),
(4, 'Arreglo de Barba', 600.00, 'img/fix.jpg'),
(5, 'Peinado', 500.00, 'img/brush.jpg'),
(6, 'Pinceladas de Color', 1000.00, 'img/paint.jpg'),
(7, 'Tintura', 1500.00, 'img/dye.jpg'),
(8, 'Mechas', 1750.00, 'img/reflex.jpg'),
(9, 'Perfilado Cejas y Barba', 750.00, 'img/cejas.jpg'),
(10, 'Cortes Tribales', 1500.00, 'img/draw.jpeg'),
(11, 'Alisado con Keratina', 2000.00, 'img/keratina.jpg'),
(13, 'Corte Laser', 1250.00, 'img/laser.jpg'),
(16, 'Con Decimales', 1550.50, 'img/perfil.jpeg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sold`
--

CREATE TABLE `sold` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `qtty` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sold`
--

INSERT INTO `sold` (`id`, `invoice_id`, `service_id`, `qtty`) VALUES
(1, 1, 2, 1),
(2, 1, 9, 2),
(3, 1, 5, 1),
(4, 2, 1, 1),
(5, 2, 6, 2),
(6, 2, 4, 3),
(7, 3, 16, 3),
(8, 4, 16, 1),
(9, 5, 1, 1),
(10, 5, 5, 2),
(11, 6, 2, 1),
(12, 6, 9, 2);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `phone` (`phone`,`email`);

--
-- Indices de la tabla `invoice`
--
ALTER TABLE `invoice`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indices de la tabla `service`
--
ALTER TABLE `service`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sold`
--
ALTER TABLE `sold`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_id` (`invoice_id`),
  ADD KEY `service_id` (`service_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `client`
--
ALTER TABLE `client`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `invoice`
--
ALTER TABLE `invoice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `service`
--
ALTER TABLE `service`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `sold`
--
ALTER TABLE `sold`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `invoice`
--
ALTER TABLE `invoice`
  ADD CONSTRAINT `invoice_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `client` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `sold`
--
ALTER TABLE `sold`
  ADD CONSTRAINT `sold_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoice` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `sold_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
