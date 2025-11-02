-- =====================================================
-- Base de Datos: Economía Circular Canarias - MySQL
-- =====================================================

-- Crear base de datos
CREATE DATABASE IF NOT EXISTS canarias_ec 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE canarias_ec;

-- =====================================================
-- TABLA: users (Usuarios del sistema)
-- =====================================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    phone VARCHAR(20),
    avatar_url VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,
    is_verified TINYINT(1) DEFAULT 0,
    role ENUM('user', 'admin', 'seller') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    INDEX idx_email (email),
    INDEX idx_username (username),
    INDEX idx_role (role),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: products (Productos de la economía circular)
-- =====================================================
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    seller_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    category VARCHAR(50) NOT NULL,
    condition_state ENUM('nuevo', 'como_nuevo', 'bueno', 'aceptable', 'para_reparar') DEFAULT 'bueno',
    price DECIMAL(10, 2) NOT NULL,
    original_price DECIMAL(10, 2),
    location VARCHAR(100),
    images JSON, -- Array de URLs de imágenes
    is_available TINYINT(1) DEFAULT 1,
    views INT DEFAULT 0,
    favorites INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_category (category),
    INDEX idx_seller (seller_id),
    INDEX idx_available (is_available),
    INDEX idx_price (price),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: orders (Pedidos/Transacciones)
-- =====================================================
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    buyer_id INT NOT NULL,
    seller_id INT NOT NULL,
    product_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'confirmed', 'shipped', 'delivered', 'cancelled', 'refunded') DEFAULT 'pending',
    payment_method VARCHAR(50),
    payment_status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    shipping_address TEXT,
    tracking_number VARCHAR(100),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_buyer (buyer_id),
    INDEX idx_seller (seller_id),
    INDEX idx_product (product_id),
    INDEX idx_status (status),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: categories (Categorías de productos)
-- =====================================================
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    icon VARCHAR(50),
    parent_id INT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_parent (parent_id),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: favorites (Productos favoritos de usuarios)
-- =====================================================
CREATE TABLE IF NOT EXISTS favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_favorite (user_id, product_id),
    INDEX idx_user (user_id),
    INDEX idx_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: messages (Mensajes entre usuarios)
-- =====================================================
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    product_id INT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL,
    INDEX idx_sender (sender_id),
    INDEX idx_receiver (receiver_id),
    INDEX idx_product (product_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: reviews (Reseñas de vendedores/productos)
-- =====================================================
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reviewer_id INT NOT NULL,
    reviewed_user_id INT NOT NULL,
    order_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (reviewer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    UNIQUE KEY unique_review (reviewer_id, order_id),
    INDEX idx_reviewer (reviewer_id),
    INDEX idx_reviewed (reviewed_user_id),
    INDEX idx_rating (rating)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- DATOS INICIALES
-- =====================================================

-- Categorías principales
INSERT INTO categories (name, description, icon) VALUES
('Electrónica', 'Dispositivos electrónicos y gadgets', '📱'),
('Hogar', 'Muebles y artículos del hogar', '🏠'),
('Ropa', 'Ropa y accesorios de segunda mano', '👕'),
('Deportes', 'Equipamiento deportivo', '⚽'),
('Libros', 'Libros usados y material educativo', '📚'),
('Juguetes', 'Juguetes y juegos infantiles', '🧸'),
('Herramientas', 'Herramientas y equipamiento', '🔧'),
('Jardín', 'Artículos de jardinería', '🌱'),
('Vehículos', 'Bicicletas, patinetes, etc.', '🚲'),
('Otros', 'Otros productos', '📦');

-- Usuario administrador por defecto (password: admin123)
INSERT INTO users (username, email, password_hash, first_name, last_name, role, is_verified) VALUES
('admin', 'admin@canarias-ec.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'Sistema', 'admin', 1);

-- =====================================================
-- VISTAS ÚTILES
-- =====================================================

-- Vista de productos con información del vendedor
CREATE OR REPLACE VIEW v_products_with_seller AS
SELECT 
    p.*,
    u.username AS seller_username,
    u.email AS seller_email,
    u.avatar_url AS seller_avatar,
    (SELECT COUNT(*) FROM favorites WHERE product_id = p.id) AS favorite_count
FROM products p
INNER JOIN users u ON p.seller_id = u.id;

-- Vista de pedidos con información completa
CREATE OR REPLACE VIEW v_orders_complete AS
SELECT 
    o.*,
    buyer.username AS buyer_username,
    buyer.email AS buyer_email,
    seller.username AS seller_username,
    seller.email AS seller_email,
    p.title AS product_title,
    p.category AS product_category
FROM orders o
INNER JOIN users buyer ON o.buyer_id = buyer.id
INNER JOIN users seller ON o.seller_id = seller.id
INNER JOIN products p ON o.product_id = p.id;

-- =====================================================
-- PROCEDIMIENTOS ALMACENADOS
-- =====================================================

DELIMITER //

-- Procedimiento para crear un pedido
CREATE PROCEDURE sp_create_order(
    IN p_buyer_id INT,
    IN p_product_id INT,
    IN p_shipping_address TEXT,
    OUT p_order_id INT
)
BEGIN
    DECLARE v_seller_id INT;
    DECLARE v_price DECIMAL(10,2);
    
    -- Obtener información del producto
    SELECT seller_id, price INTO v_seller_id, v_price
    FROM products
    WHERE id = p_product_id AND is_available = 1;
    
    -- Crear el pedido
    INSERT INTO orders (buyer_id, seller_id, product_id, total_amount, shipping_address)
    VALUES (p_buyer_id, v_seller_id, p_product_id, v_price, p_shipping_address);
    
    SET p_order_id = LAST_INSERT_ID();
    
    -- Marcar producto como no disponible
    UPDATE products SET is_available = 0 WHERE id = p_product_id;
END //

DELIMITER ;

-- =====================================================
-- TRIGGERS
-- =====================================================

DELIMITER //

-- Trigger para actualizar contador de favoritos
CREATE TRIGGER trg_update_product_favorites
AFTER INSERT ON favorites
FOR EACH ROW
BEGIN
    UPDATE products 
    SET favorites = (SELECT COUNT(*) FROM favorites WHERE product_id = NEW.product_id)
    WHERE id = NEW.product_id;
END //

CREATE TRIGGER trg_delete_product_favorites
AFTER DELETE ON favorites
FOR EACH ROW
BEGIN
    UPDATE products 
    SET favorites = (SELECT COUNT(*) FROM favorites WHERE product_id = OLD.product_id)
    WHERE id = OLD.product_id;
END //

DELIMITER ;

-- =====================================================
-- ÍNDICES ADICIONALES PARA RENDIMIENTO
-- =====================================================

-- Índices de texto completo para búsquedas
ALTER TABLE products ADD FULLTEXT INDEX idx_search (title, description);
ALTER TABLE users ADD FULLTEXT INDEX idx_user_search (username, first_name, last_name);

-- =====================================================
-- GRANT PERMISSIONS (Opcional - para usuario específico)
-- =====================================================

-- Si quieres crear un usuario específico para la app:
-- CREATE USER 'canarias_user'@'localhost' IDENTIFIED BY 'tu_password_seguro';
-- GRANT ALL PRIVILEGES ON canarias_ec.* TO 'canarias_user'@'localhost';
-- FLUSH PRIVILEGES;

-- =====================================================
-- FIN DEL SCRIPT
-- =====================================================

SELECT 'Base de datos canarias_ec creada exitosamente!' AS mensaje;
