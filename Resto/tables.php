<?php
/**
 * API Endpoint: Lista de Mesas
 * Proporciona el listado de todas las mesas del restaurante para la App Android
 * 
 * Método: GET o POST (con JSON)
 * 
 * Respuesta JSON:
 * [
 *   {
 *     "id": "1",
 *     "name": "Entrada 1"
 *   },
 *   ...
 * ]
 */

// Habilitar recepción de JSON
if (json_decode(file_get_contents('php://input'), true))
{
    $_POST = json_decode(file_get_contents('php://input'), true);
}

// Conectar a la base de datos
include "includes/conn.php";

try {
    // Consultar todas las mesas
    $sql = "SELECT id, name FROM tables ORDER BY id ASC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    
    // Construir array de respuesta
    $tables = array();
    
    while($row = $stmt->fetch(PDO::FETCH_OBJ))
    {
        $temp = array();
        $temp['id'] = $row->id;
        $temp['name'] = $row->name;
        array_push($tables, $temp);
    }
    
    // Enviar respuesta JSON
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($tables, JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    // En caso de error, enviar respuesta de error
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(500);
    echo json_encode(array(
        'error' => true,
        'message' => 'Error al consultar las mesas'
    ));
}
?>
