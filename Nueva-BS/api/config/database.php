<?php
// Conexión mínima, clara y con manejo básico de errores
session_start();

$host = 'localhost';
$db   = 'newapp';
$user = 'root';
$pass = getenv('MySQL');

// Usa tu variable de entorno preferida; de lo contrario, puedes poner la contraseña literal
// if ($pass === false) {
//     $pass = getenv('DB_PASS');
// }

// if ($pass === false || $pass === '') {
//     http_response_code(500);
//     header('Content-Type: application/json');
//     echo json_encode(['success' => false, 'message' => 'Password de BD no configurado']);
//     exit;
// }

try {
    $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos']);
    exit;
}
?>