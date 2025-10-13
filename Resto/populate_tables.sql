-- ============================================
-- SCRIPT: Poblar tabla TABLES con todas las mesas
-- Sistema de Gestión de Restaurante - Fonda 13
-- Fecha: 12 de octubre de 2025
-- ============================================

USE resto;

-- Primero, verificar si la tabla existe y crearla si no existe
-- (Esto es por si no ejecutaste database_refactoring.sql aún)
CREATE TABLE IF NOT EXISTS `tables` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Limpiar tabla (OPCIONAL - quitar comentario si quieres empezar desde cero)
-- TRUNCATE TABLE `tables`;

-- ============================================
-- INSERTAR TODAS LAS MESAS
-- ============================================

-- Zona de Entrada (4 mesas)
INSERT IGNORE INTO `tables` (`id`, `name`) VALUES
(NULL, 'Entrada 1'),
(NULL, 'Entrada 2'),
(NULL, 'Entrada 3'),
(NULL, 'Entrada 4');

-- Zona de Barra (3 mesas)
INSERT IGNORE INTO `tables` (`id`, `name`) VALUES
(NULL, 'Barra 1'),
(NULL, 'Barra 2'),
(NULL, 'Barra 3');

-- Zona de Patio (5 mesas)
INSERT IGNORE INTO `tables` (`id`, `name`) VALUES
(NULL, 'Patio 1'),
(NULL, 'Patio 2'),
(NULL, 'Patio 3'),
(NULL, 'Patio 4'),
(NULL, 'Patio 5');

-- Zona de Vereda (3 mesas)
INSERT IGNORE INTO `tables` (`id`, `name`) VALUES
(NULL, 'Vereda 1'),
(NULL, 'Vereda 2'),
(NULL, 'Vereda 3');

-- Mesas Principales (13 mesas)
INSERT IGNORE INTO `tables` (`id`, `name`) VALUES
(NULL, 'Mesa 1'),
(NULL, 'Mesa 2'),
(NULL, 'Mesa 3'),
(NULL, 'Mesa 4'),
(NULL, 'Mesa 5'),
(NULL, 'Mesa 6'),
(NULL, 'Mesa 7'),
(NULL, 'Mesa 8'),
(NULL, 'Mesa 9'),
(NULL, 'Mesa 10'),
(NULL, 'Mesa 11'),
(NULL, 'Mesa 12'),
(NULL, 'Mesa 13');

-- Tablones (2 mesas)
INSERT IGNORE INTO `tables` (`id`, `name`) VALUES
(NULL, 'Tablón 1'),
(NULL, 'Tablón 2');

-- ============================================
-- VERIFICACIÓN
-- ============================================

-- Mostrar todas las mesas insertadas
SELECT 
    id,
    name,
    'Zona de Entrada' as zona
FROM tables 
WHERE name LIKE 'Entrada%'

UNION ALL

SELECT 
    id,
    name,
    'Zona de Barra' as zona
FROM tables 
WHERE name LIKE 'Barra%'

UNION ALL

SELECT 
    id,
    name,
    'Zona de Patio' as zona
FROM tables 
WHERE name LIKE 'Patio%'

UNION ALL

SELECT 
    id,
    name,
    'Zona de Vereda' as zona
FROM tables 
WHERE name LIKE 'Vereda%'

UNION ALL

SELECT 
    id,
    name,
    'Mesas Principales' as zona
FROM tables 
WHERE name LIKE 'Mesa%'

UNION ALL

SELECT 
    id,
    name,
    'Tablones' as zona
FROM tables 
WHERE name LIKE 'Tablón%'

ORDER BY zona, name;

-- Contar total de mesas
SELECT COUNT(*) as total_mesas FROM tables;

-- ============================================
-- NOTAS
-- ============================================
/*
Este script inserta 33 mesas en total:
- 4 mesas de Entrada
- 3 mesas de Barra
- 5 mesas de Patio
- 3 mesas de Vereda
- 13 Mesas principales (numeradas 1-13)
- 2 Tablones

Usa INSERT IGNORE para evitar duplicados si ejecutas el script múltiples veces.

Para ejecutar este script:
1. Desde la terminal: mysql -u root -p resto < populate_tables.sql
2. Desde phpMyAdmin: Copiar y pegar el contenido
3. Desde MySQL Workbench: Abrir y ejecutar
4. Desde el script PHP: setup_tables.php (archivo adjunto)
*/
