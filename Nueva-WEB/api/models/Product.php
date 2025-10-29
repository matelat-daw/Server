<?php
class Product {
    private $db;

    public function __construct() {
        $this->db = new Database(); // Assuming Database is a class that handles DB connection
    }

    public function getAllProducts() {
        $query = "SELECT * FROM products";
        $stmt = $this->db->connect()->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductById($id) {
        $query = "SELECT * FROM products WHERE id = :id";
        $stmt = $this->db->connect()->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createProduct($data) {
        $query = "INSERT INTO products (name, description, price, stock) VALUES (:name, :description, :price, :stock)";
        $stmt = $this->db->connect()->prepare($query);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':price', $data['price']);
        $stmt->bindParam(':stock', $data['stock']);
        return $stmt->execute();
    }

    public function updateProduct($id, $data) {
        $query = "UPDATE products SET name = :name, description = :description, price = :price, stock = :stock WHERE id = :id";
        $stmt = $this->db->connect()->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':price', $data['price']);
        $stmt->bindParam(':stock', $data['stock']);
        return $stmt->execute();
    }

    public function deleteProduct($id) {
        $query = "DELETE FROM products WHERE id = :id";
        $stmt = $this->db->connect()->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>