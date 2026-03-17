<?php
// Configuración de conexión a base de datos
session_start();

$host = 'localhost';
$db   = 'energy';
$user = 'root';

// Obtener contraseña de variable de entorno MySQL
$pass = getenv('MySQL');

// Si la variable de entorno no está configurada, usar valor por defecto (solo para desarrollo)
if ($pass === false || empty($pass)) {
    // Descomentar y poner tu contraseña si no usas variable de entorno
    // $pass = 'tu_contraseña_aqui';
    
    // Si aún no hay contraseña, intentar sin contraseña (algunos servidores locales)
    $pass = '';
}

try {
    $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false, 
        'message' => 'Error de conexión a la base de datos',
        'error' => $e->getMessage() // Solo para desarrollo, quitar en producción
    ]);
    exit;
}
?>
