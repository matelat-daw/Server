<?php
/**
 * Gestión de Productos con Consultas Optimizadas
 * Aprovecha los índices creados en la base de datos
 */

class ProductManager {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    /**
     * Obtener productos por categoría (usa índice idx_food_kind)
     */
    public function getProductsByCategory($categoryId) {
        $stmt = $this->conn->prepare("
            SELECT f.id, f.name, f.price, f.kind, c.name as category_name, c.icon, c.color 
            FROM food f 
            JOIN categories c ON f.kind = c.id 
            WHERE f.kind = :category 
            ORDER BY f.name
        ");
        $stmt->execute([':category' => $categoryId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Buscar productos por nombre (usa índice idx_food_name)
     */
    public function searchProductsByName($searchTerm) {
        $stmt = $this->conn->prepare("
            SELECT f.id, f.name, f.price, f.kind, c.name as category_name, c.icon, c.color 
            FROM food f 
            JOIN categories c ON f.kind = c.id 
            WHERE f.name LIKE :search 
            ORDER BY f.name
        ");
        $stmt->execute([':search' => '%' . $searchTerm . '%']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener todos los productos con información de categoría
     */
    public function getAllProducts() {
        $stmt = $this->conn->prepare("
            SELECT f.id, f.name, f.price, f.kind, c.name as category_name, c.icon, c.color 
            FROM food f 
            JOIN categories c ON f.kind = c.id 
            ORDER BY c.id, f.name
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Agregar nuevo producto con mejor manejo de errores
     */
    public function addProduct($name, $price, $categoryId) {
        $stmt = $this->conn->prepare("
            INSERT INTO food (name, price, kind) 
            VALUES (:name, :price, :kind)
        ");
        $stmt->execute([
            ':name' => $name,
            ':price' => $price,
            ':kind' => $categoryId
        ]);
        return $this->conn->lastInsertId();
    }
    
    /**
     * Obtener estadísticas por categoría
     */
    public function getCategoryStats() {
        $stmt = $this->conn->prepare("
            SELECT 
                c.id,
                c.name,
                c.icon,
                c.color,
                COUNT(f.id) as total_products,
                COALESCE(AVG(f.price), 0) as avg_price,
                COALESCE(MIN(f.price), 0) as min_price,
                COALESCE(MAX(f.price), 0) as max_price
            FROM categories c 
            LEFT JOIN food f ON c.id = f.kind 
            GROUP BY c.id, c.name, c.icon, c.color 
            ORDER BY c.id
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Verificar si un producto existe por ID
     */
    public function productExists($id) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM food WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Verificar si un producto ya existe por nombre y categoría
     */
    public function productExistsByName($name, $categoryId) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM food WHERE name = :name AND kind = :category");
        $stmt->execute([':name' => $name, ':category' => $categoryId]);
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Obtener estadísticas de una categoría específica
     */
    public function getCategoryStatistics($categoryId) {
        $stmt = $this->conn->prepare("
            SELECT 
                COUNT(*) as total_products,
                COALESCE(AVG(price), 0) as avg_price,
                COALESCE(MIN(price), 0) as min_price,
                COALESCE(MAX(price), 0) as max_price
            FROM food 
            WHERE kind = :category
        ");
        $stmt->execute([':category' => $categoryId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

// Función helper para obtener el manager de productos
function getProductManager($conn) {
    static $manager = null;
    if ($manager === null) {
        $manager = new ProductManager($conn);
    }
    return $manager;
}
?>
