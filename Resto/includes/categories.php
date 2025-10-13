<?php
/**
 * Gestión de Categorías de Productos
 * Este archivo centraliza la lógica de categorías usando la tabla categories
 */

class CategoryManager {
    private $conn;
    private $categories = [];
    
    public function __construct($connection) {
        $this->conn = $connection;
        $this->loadCategories();
    }
    
    /**
     * Cargar todas las categorías desde la base de datos
     */
    private function loadCategories() {
        try {
            $stmt = $this->conn->prepare("SELECT id, name, icon, color FROM categories ORDER BY id");
            $stmt->execute();
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $this->categories[$row['id']] = [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'icon' => $row['icon'],
                    'color' => $row['color']
                ];
            }
        } catch (PDOException $e) {
            // Fallback a categorías hardcodeadas si hay error
            $this->categories = [
                0 => ['id' => 0, 'name' => 'Plato Principal', 'icon' => 'egg-fried', 'color' => 'primary'],
                1 => ['id' => 1, 'name' => 'Bebida', 'icon' => 'cup-straw', 'color' => 'info'],
                2 => ['id' => 2, 'name' => 'Postre', 'icon' => 'cake2', 'color' => 'warning'],
                3 => ['id' => 3, 'name' => 'Café', 'icon' => 'cup-hot', 'color' => 'dark'],
                4 => ['id' => 4, 'name' => 'Vino', 'icon' => 'wine', 'color' => 'danger']
            ];
        }
    }
    
    /**
     * Obtener información de una categoría específica
     */
    public function getCategory($id) {
        return $this->categories[$id] ?? null;
    }
    
    /**
     * Obtener todas las categorías
     */
    public function getAllCategories() {
        return $this->categories;
    }
    
    /**
     * Verificar si una categoría existe
     */
    public function categoryExists($id) {
        return isset($this->categories[$id]);
    }
    
    /**
     * Obtener IDs válidos de categorías
     */
    public function getValidCategoryIds() {
        return array_keys($this->categories);
    }
    
    /**
     * Obtener placeholder de ejemplo para una categoría
     */
    public function getPlaceholder($id) {
        $placeholders = [
            0 => 'Milanesa con papas fritas',
            1 => 'Coca Cola 500ml',
            2 => 'Flan con dulce de leche',
            3 => 'Café cortado',
            4 => 'Vino Malbec copa'
        ];
        
        return $placeholders[$id] ?? 'Nombre del producto';
    }
}

// Función helper para obtener el manager de categorías
function getCategoryManager($conn) {
    static $manager = null;
    if ($manager === null) {
        $manager = new CategoryManager($conn);
    }
    return $manager;
}
?>
