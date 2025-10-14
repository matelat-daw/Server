

CREATE TABLE `client` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(48) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pass` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bday` date NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `phone` (`phone`,`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO client VALUES("1","César Matelat","Calle Fermín Morín Nº 2, Portal 4, 7º B","664774821","matelat@gmail.com","$2y$10$wE8iJkd0JOkVBKvDulTFkuig7GuC2RfgJYogQz.fL2eWkYaLmFxB6","1968-04-05","2023-02-06","11:30:00");
INSERT INTO client VALUES("2","César Matelat","Calle Fermín Morín Nº 2, portal 4, 7º B.","664774822","orions68@gmail.com","$2y$10$qWsuo7BpUr83HotXPUIHk.8aYoRJEiYUa5XyJMI5iI7OE2CnrXcvu","1968-04-05","2022-11-23","11:00:00");
INSERT INTO client VALUES("3","César Matelat","Calle Fermín Morín Nº 2, portal 4, 7º B.","664774823","orions@gmx.net","$2y$10$MESgIzXHC8unYvQysr/SnuosmhD0uEzyeiRrXYtypErWuOuJqj5X2","1968-04-05","2022-11-21","10:00:00");



CREATE TABLE `invoice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) DEFAULT NULL,
  `total` decimal(11,2) NOT NULL,
  `inv_date` date NOT NULL,
  `inv_time` time NOT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  CONSTRAINT `invoice_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `client` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO invoice VALUES("1","1","2800.00","2023-03-03","07:23:31");
INSERT INTO invoice VALUES("2","1","4800.00","2023-03-03","19:25:36");
INSERT INTO invoice VALUES("3","","4651.50","2023-03-08","05:26:59");
INSERT INTO invoice VALUES("4","","1550.50","2023-03-08","06:12:09");



CREATE TABLE `service` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(11,2) NOT NULL,
  `img` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO service VALUES("1","Corte a Tijera","1000.00","img/corte.jpg");
INSERT INTO service VALUES("2","Corte a Máquina","800.00","img/machine.jpg");
INSERT INTO service VALUES("3","Afeitado con Navaja","700.00","img/shave.jpg");
INSERT INTO service VALUES("4","Arreglo de Barba","600.00","img/fix.jpg");
INSERT INTO service VALUES("5","Peinado","500.00","img/brush.jpg");
INSERT INTO service VALUES("6","Pinceladas de Color","1000.00","img/paint.jpg");
INSERT INTO service VALUES("7","Tintura","1500.00","img/dye.jpg");
INSERT INTO service VALUES("8","Mechas","1750.00","img/reflex.jpg");
INSERT INTO service VALUES("9","Perfilado Cejas y Barba","750.00","img/cejas.jpg");
INSERT INTO service VALUES("10","Cortes Tribales","1500.00","img/draw.jpeg");
INSERT INTO service VALUES("11","Alisado con Keratina","2000.00","img/keratina.jpg");
INSERT INTO service VALUES("13","Corte Laser","1250.00","img/laser.jpg");
INSERT INTO service VALUES("16","Con Decimales","1550.50","img/perfil.jpeg");



CREATE TABLE `sold` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `qtty` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_id` (`invoice_id`),
  KEY `service_id` (`service_id`),
  CONSTRAINT `sold_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoice` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `sold_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sold VALUES("1","1","2","1");
INSERT INTO sold VALUES("2","1","9","2");
INSERT INTO sold VALUES("3","1","5","1");
INSERT INTO sold VALUES("4","2","1","1");
INSERT INTO sold VALUES("5","2","6","2");
INSERT INTO sold VALUES("6","2","4","3");
INSERT INTO sold VALUES("7","3","16","3");
INSERT INTO sold VALUES("8","4","16","1");

