-- Script de migración para actualizar usuarios existentes con campos nuevos
-- Este script agrega los campos first_name, last_name, gender y profile_img si no existen
-- y asigna valores por defecto a usuarios existentes

-- Verificar y agregar columnas si no existen (para bases de datos ya creadas)
-- Si la base de datos se crea desde cero con newapp_schema.sql, esto no es necesario

-- Agregar first_name si no existe
SET @col_exists = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'users'
    AND COLUMN_NAME = 'first_name'
);

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE users ADD COLUMN first_name VARCHAR(50) AFTER password',
    'SELECT "Column first_name already exists" AS message'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Agregar last_name si no existe
SET @col_exists = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'users'
    AND COLUMN_NAME = 'last_name'
);

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE users ADD COLUMN last_name VARCHAR(50) AFTER first_name',
    'SELECT "Column last_name already exists" AS message'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Agregar gender si no existe
SET @col_exists = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'users'
    AND COLUMN_NAME = 'gender'
);

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE users ADD COLUMN gender ENUM("male", "female", "other") DEFAULT "other" AFTER last_name',
    'SELECT "Column gender already exists" AS message'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Agregar profile_img si no existe
SET @col_exists = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'users'
    AND COLUMN_NAME = 'profile_img'
);

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE users ADD COLUMN profile_img VARCHAR(255) DEFAULT NULL AFTER gender',
    'SELECT "Column profile_img already exists" AS message'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Actualizar usuarios existentes sin profile_img
-- Nota: Este script solo actualiza la base de datos
-- Las imágenes físicas deben copiarse manualmente desde media/ a uploads/users/{ID}/profile.png
-- O ejecutar el script PHP complementario que se encargará de eso

UPDATE users 
SET profile_img = CONCAT('users/', id, '/profile.png')
WHERE profile_img IS NULL OR profile_img = '';

-- Nota: Después de ejecutar este script SQL, ejecuta el script PHP de migración
-- para copiar físicamente las imágenes de media/ a las carpetas de usuarios
