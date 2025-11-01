<?php
require_once __DIR__ . '/../api/config/database.php';

try {
    $stmt = $conn->query('SELECT id, name, description, price, stock, image, featured FROM products LIMIT 10');
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Total productos: " . count($products) . "\n\n";
    
    foreach ($products as $product) {
        echo "ID: " . $product['id'] . "\n";
        echo "Nombre: " . $product['name'] . "\n";
        echo "Precio: " . $product['price'] . "€\n";
        echo "Stock: " . $product['stock'] . "\n";
        echo "Imagen: " . ($product['image'] ?? 'NULL') . "\n";
        echo "Destacado: " . ($product['featured'] ? 'Sí' : 'No') . "\n";
        echo "---\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
