-- ============================================
-- SCRIPT DE REFACTORIZACIÓN DE BASE DE DATOS
-- Sistema de Gestión de Restaurante - Fonda 13
-- Fecha: 12 de octubre de 2025
-- ============================================

-- Este script renombra tablas y columnas para una nomenclatura consistente en inglés

-- IMPORTANTE: Hacer backup de la base de datos ANTES de ejecutar este script
-- Comando: mysqldump -u root -p resto > resto_backup_$(date +%Y%m%d).sql

USE resto;

-- ============================================
-- 1. RENOMBRAR TABLA: delivery → client
-- ============================================
-- La tabla 'delivery' contiene datos de clientes, no de entregas
-- Se renombra para mayor claridad

RENAME TABLE `delivery` TO `client`;

-- ============================================
-- 2. RENOMBRAR TABLA: mesa → tables
-- ============================================
-- Cambio de español a inglés para consistencia
-- 'tables' en plural porque es una palabra reservada en MySQL

RENAME TABLE `mesa` TO `tables`;

-- ============================================
-- 3. RENOMBRAR COLUMNA EN TABLA invoice
-- ============================================
-- Cambiar wait_id a waiter_id para mayor claridad

ALTER TABLE `invoice` 
CHANGE COLUMN `wait_id` `waiter_id` INT(11) NULL DEFAULT NULL;

-- Actualizar la clave foránea si existe
-- (Primero eliminar la constraint existente)
-- ALTER TABLE `invoice` DROP FOREIGN KEY `fk_invoice_waiter`;

-- Recrear la clave foránea con el nuevo nombre (si es necesario)
-- ALTER TABLE `invoice` 
-- ADD CONSTRAINT `fk_invoice_waiter` 
-- FOREIGN KEY (`waiter_id`) REFERENCES `waiter`(`id`) 
-- ON DELETE SET NULL ON UPDATE CASCADE;

-- ============================================
-- 4. RENOMBRAR COLUMNAS EN TABLA sold
-- ============================================
-- Cambiar food_id a product_id para mayor claridad
-- Cambiar qtty a quantity para nombre completo

ALTER TABLE `sold` 
CHANGE COLUMN `food_id` `product_id` INT(11) NOT NULL;

ALTER TABLE `sold` 
CHANGE COLUMN `qtty` `quantity` INT(11) NOT NULL DEFAULT 1;

-- Actualizar clave foránea si existe
-- ALTER TABLE `sold` DROP FOREIGN KEY `fk_sold_food`;
-- ALTER TABLE `sold` 
-- ADD CONSTRAINT `fk_sold_product` 
-- FOREIGN KEY (`product_id`) REFERENCES `food`(`id`) 
-- ON DELETE RESTRICT ON UPDATE CASCADE;

-- ============================================
-- 5. ACTUALIZAR REFERENCIAS A TABLAS
-- ============================================
-- Actualizar foreign keys que apuntan a las tablas renombradas

-- Para la tabla invoice -> tables
-- ALTER TABLE `invoice` DROP FOREIGN KEY `fk_invoice_mesa`;
-- ALTER TABLE `invoice` 
-- ADD CONSTRAINT `fk_invoice_table` 
-- FOREIGN KEY (`table_id`) REFERENCES `tables`(`id`) 
-- ON DELETE RESTRICT ON UPDATE CASCADE;

-- Para la tabla invoice -> client
-- ALTER TABLE `invoice` DROP FOREIGN KEY `fk_invoice_delivery`;
-- ALTER TABLE `invoice` 
-- ADD CONSTRAINT `fk_invoice_client` 
-- FOREIGN KEY (`client_id`) REFERENCES `client`(`id`) 
-- ON DELETE SET NULL ON UPDATE CASCADE;

-- ============================================
-- VERIFICACIÓN DE CAMBIOS
-- ============================================

-- Verificar estructura de tablas renombradas
SHOW COLUMNS FROM `client`;
SHOW COLUMNS FROM `tables`;
SHOW COLUMNS FROM `invoice`;
SHOW COLUMNS FROM `sold`;

-- Verificar que no haya tablas antiguas
-- SHOW TABLES LIKE 'delivery';  -- No debería existir
-- SHOW TABLES LIKE 'mesa';      -- No debería existir

-- Ver todas las tablas
SHOW TABLES;

-- ============================================
-- NOTAS IMPORTANTES
-- ============================================
/*
DESPUÉS DE EJECUTAR ESTE SCRIPT:

1. Verificar que la aplicación PHP funcione correctamente
2. Probar operaciones CRUD en todas las entidades:
   - Crear nueva factura
   - Modificar camarero
   - Eliminar cliente
   - Exportar facturas

3. Si hay errores, restaurar desde el backup:
   mysql -u root -p resto < resto_backup_YYYYMMDD.sql

4. Actualizar la aplicación Android para reflejar:
   - Campo "waiter" en lugar de "wait" en JSON de facturas
   - Campos "product_id" y "quantity" en lugar de "food_id" y "qtty"

5. TABLAS CAMBIADAS:
   - delivery → client
   - mesa → tables

6. COLUMNAS CAMBIADAS:
   - invoice.wait_id → invoice.waiter_id
   - sold.food_id → sold.product_id
   - sold.qtty → sold.quantity

7. Este script NO renombra la tabla 'food' porque:
   - Sería un cambio muy grande
   - 'food' es descriptivo en contexto de restaurante
   - product_id hace referencia conceptual, no necesariamente al nombre de tabla
*/

-- ============================================
-- FIN DEL SCRIPT
-- ============================================
