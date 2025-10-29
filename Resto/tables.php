<?php
// test_tables.php - Versión con PDO y variable de entorno para la contraseña

// 1. Configuración de la respuesta JSON
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// 2. Credenciales de la base de datos (¡AJUSTA ESTO!)
$dbHost = 'localhost';
$dbName = 'resto'; // <-- REEMPLAZA ESTO
$dbUser = 'root';       // <-- REEMPLAZA ESTO
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
    // Verifica si la variable de entorno fue leída correctamente
    if ($dbPass === false) {
        throw new \RuntimeException("La variable de entorno 'MySQL' no está definida o no es accesible por el servidor web.");
    }

    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);

} catch (\Throwable $e) { // Atrapa tanto PDOException como RuntimeException
    http_response_code(500);
    error_log("Error de conexión a la base de datos (PDO): " . $e->getMessage());
    echo json_encode(['error' => 'Fallo al conectar con la base de datos. Revisa los logs del servidor.']);
    exit;
}

// 4. Consulta y procesamiento de datos
try {
    $stmt = $pdo->query("SELECT id, name FROM tables ORDER BY id");
    $tables = $stmt->fetchAll();

    // 5. Devolver el resultado como JSON
    if (ob_get_length()) {
        ob_clean();
    }
    echo json_encode($tables);

} catch (\PDOException $e) {
    http_response_code(500);
    error_log("Error en la consulta SQL (PDO): " . $e->getMessage());
    echo json_encode(['error' => 'Fallo al consultar los datos.']);
    exit;
}
