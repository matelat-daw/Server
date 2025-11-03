<?php
class ProductController {
    private $productModel;

    public function __construct() {
        require_once __DIR__ . '/../config/database.php';
        require_once __DIR__ . '/../models/Product.php';
        global $conn;
        $this->productModel = new Product($conn);
        header('Content-Type: application/json');
    }

    // GET /products
    public function index() {
        $products = $this->productModel->findAll();
        http_response_code(200);
        echo json_encode(['success' => true, 'products' => $products]);
    }

    // GET /products/{id}
    public function show($id) {
        $product = $this->productModel->findById($id);
        if (!$product) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
            return;
        }
        http_response_code(200);
        echo json_encode(['success' => true, 'product' => $product]);
    }

    // POST /products (JSON)
    public function store($data) {
        $ok = $this->productModel->create($data);
        if ($ok) {
            http_response_code(201);
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error al crear producto']);
        }
    }

    // PUT /products/{id} (JSON)
    public function update($id, $data) {
        $ok = $this->productModel->update($id, $data);
        if ($ok) {
            http_response_code(200);
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error al actualizar producto']);
        }
    }

    // DELETE /products/{id}
    public function destroy($id) {
        $ok = $this->productModel->delete($id);
        if ($ok) {
            http_response_code(200);
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error al eliminar producto']);
        }
    }
}
?>