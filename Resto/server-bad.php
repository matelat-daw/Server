<?php
// server.php - Versión final y corregida

// 1. Configuración de la respuesta JSON y errores
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// 2. Credenciales de la base de datos (Ajusta si es necesario)
$dbHost = 'localhost';
$dbName = 'resto'; // El log de error indica que la base de datos se llama 'resto'
$dbUser = 'root';       // <-- REEMPLAZA CON TU USUARIO
$charset = 'utf8mb4';

// Lee la contraseña desde la variable de entorno 'MySQL'
$dbPass = getenv('MySQL');

// 3. Conexión a la base de datos con PDO
$dsn = "mysql:host=$dbHost;dbname=$dbName;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    if ($dbPass === false) {
        throw new \RuntimeException("La variable de entorno 'MySQL' no está definida.");
    }
    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
} catch (\Throwable $e) {
    http_response_code(500);
    error_log("Error de conexión a la base de datos (PDO): " . $e->getMessage());
    echo json_encode(['error' => 'Fallo al conectar con la base de datos.']);
    exit;
}

// 4. Obtener el ID de la categoría
if (isset($_POST['id']) && is_numeric($_POST['id'])) {
    $categoryId = (int)$_POST['id'];
} else {
    echo json_encode([]);
    exit;
}

// 5. Consulta y procesamiento de datos
try {
    // ==================================================================
    // CORRECCIÓN: Se ha cambiado el nombre de la tabla de 'products' a 'food'.
    // ==================================================================
    $sql = "SELECT id, name, price FROM food WHERE kind = :kind ORDER BY name";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['kind' => $categoryId]);
    $products = $stmt->fetchAll();

    // 6. Devolver el resultado como JSON
    if (ob_get_length()) {
        ob_clean();
    }
    echo json_encode($products);

} catch (\PDOException $e) {
    http_response_code(500);
    error_log("Error en la consulta SQL (PDO): " . $e->getMessage());
    echo json_encode(['error' => 'Fallo al consultar los datos.']);
    exit;
}
