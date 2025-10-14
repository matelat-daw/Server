-- Script para actualizar las rutas de imágenes en la tabla service
-- De 'img/archivo.jpg' a 'assets/img/archivo.jpg'

USE `barbery`;

UPDATE `service` SET `img` = 'assets/img/corte.jpg' WHERE `id` = 1;
UPDATE `service` SET `img` = 'assets/img/machine.jpg' WHERE `id` = 2;
UPDATE `service` SET `img` = 'assets/img/shave.jpg' WHERE `id` = 3;
UPDATE `service` SET `img` = 'assets/img/fix.jpg' WHERE `id` = 4;
UPDATE `service` SET `img` = 'assets/img/brush.jpg' WHERE `id` = 5;
UPDATE `service` SET `img` = 'assets/img/paint.jpg' WHERE `id` = 6;

-- Verificar los cambios
SELECT id, service, img FROM `service`;
