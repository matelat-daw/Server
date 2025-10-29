<?php
class ProductController {
    private $productModel;

    public function __construct() {
        require_once '../models/Product.php';
        $this->productModel = new Product();
    }

    public function listProducts() {
        $products = $this->productModel->getAllProducts();
        echo json_encode($products);
    }

    public function getProduct($id) {
        $product = $this->productModel->getProductById($id);
        echo json_encode($product);
    }

    public function createProduct($data) {
        $result = $this->productModel->addProduct($data);
        echo json_encode($result);
    }

    public function updateProduct($id, $data) {
        $result = $this->productModel->updateProduct($id, $data);
        echo json_encode($result);
    }

    public function deleteProduct($id) {
        $result = $this->productModel->deleteProduct($id);
        echo json_encode($result);
    }
}
?>