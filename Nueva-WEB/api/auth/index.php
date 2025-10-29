<?php
// Desactivar la salida de errores en pantalla y activar log de errores
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/php_error.log');
// filepath: c:\Server\html\Nueva-WEB\api\auth\index.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");

require_once __DIR__ . '/../controllers/AuthController.php';

$authController = new AuthController();

// Obtener método de la petición
$request_method = $_SERVER["REQUEST_METHOD"];

// Obtener la ruta
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);
$path = str_replace('/Nueva-WEB/api/auth/', '', $path);

switch($request_method) {
    case 'POST':
        if ($path === 'register') {
            // Permitir JSON o form-data
            $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
            if (stripos($contentType, 'application/json') !== false) {
                $data = json_decode(file_get_contents("php://input"), true);
            } else {
                $data = $_POST;
            }
            $authController->register($data);
        } elseif ($path === 'login') {
            $data = json_decode(file_get_contents("php://input"), true);
            $authController->login($data);
        } elseif ($path === 'logout') {
            $authController->logout();
        } else {
            http_response_code(404);
            echo json_encode(["success" => false, "message" => "Endpoint no encontrado"]);
        }
        break;
        
    case 'GET':
        if ($path === 'validate') {
            $authController->validateToken();
        } else {
            http_response_code(404);
            echo json_encode(["success" => false, "message" => "Endpoint no encontrado"]);
        }
        break;
        
    case 'PUT':
        if ($path === 'profile') {
            $data = $_POST;
            $authController->updateProfile($data);
        } else {
            http_response_code(404);
            echo json_encode(["success" => false, "message" => "Endpoint no encontrado"]);
        }
        break;
        
    case 'DELETE':
        if ($path === 'profile') {
            $authController->deleteProfile();
        } else {
            http_response_code(404);
            echo json_encode(["success" => false, "message" => "Endpoint no encontrado"]);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(["success" => false, "message" => "Método no permitido"]);
        break;
}
?>