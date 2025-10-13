<?php
/**
 * API Endpoint: Lista de Camareros
 * Proporciona el listado de todos los camareros del restaurante para la App Android
 * 
 * Método: GET o POST (con JSON)
 * 
 * Respuesta JSON:
 * [
 *   {
 *     "id": "1",
 *     "name": "Juan Pérez"
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
    // Consultar todos los camareros
    $sql = "SELECT id, name FROM waiter ORDER BY name ASC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    
    // Construir array de respuesta
    $waiters = array();
    
    while($row = $stmt->fetch(PDO::FETCH_OBJ))
    {
        $temp = array();
        $temp['id'] = $row->id;
        $temp['name'] = $row->name;
        array_push($waiters, $temp);
    }
    
    // Enviar respuesta JSON
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($waiters, JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    // En caso de error, enviar respuesta de error
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(500);
    echo json_encode(array(
        'error' => true,
        'message' => 'Error al consultar los camareros'
    ));
}
?>
