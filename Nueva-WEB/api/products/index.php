<?php
// filepath: c:\Server\html\Nueva-WEB\api\products\index.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once __DIR__ . '/../config/database.php';

$database = new Database();
$db = $database->getConnection();

$request_method = $_SERVER["REQUEST_METHOD"];
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);
$path = str_replace('/Nueva-WEB/api/products/', '', $path);

switch($request_method) {
    case 'GET':
        if ($path === 'featured' || $path === 'featured/') {
            // Obtener productos destacados
            $query = "SELECT id, name, description, price, stock, category, image 
                      FROM products 
                      WHERE featured = 1 
                      ORDER BY created_at DESC 
                      LIMIT 6";
            
            $stmt = $db->prepare($query);
            $stmt->execute();
            
            $products = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $products[] = $row;
            }
            
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Productos destacados obtenidos exitosamente',
                'products' => $products
            ]);
        } else {
            // Obtener todos los productos
            $query = "SELECT id, name, description, price, stock, category, image 
                      FROM products 
                      ORDER BY created_at DESC";
            
            $stmt = $db->prepare($query);
            $stmt->execute();
            
            $products = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $products[] = $row;
            }
            
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Productos obtenidos exitosamente',
                'products' => $products
            ]);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'message' => 'Método no permitido'
        ]);
        break;
}
?>