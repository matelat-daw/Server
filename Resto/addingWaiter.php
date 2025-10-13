<?php
include "includes/conn.php";

// Verificar que se recibieron datos por POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: addWaiter.php?error=Método no permitido');
    exit;
}

// Variables para el control de errores
$errors = [];

try {
    // Validar y sanitizar datos de entrada
    $name = trim(htmlspecialchars($_POST['waiter_name'] ?? ''));

    // Validaciones básicas
    if (empty($name)) {
        $errors[] = 'El nombre del camarero es obligatorio';
    }
    
    if (strlen($name) < 3) {
        $errors[] = 'El nombre debe tener al menos 3 caracteres';
    }
    
    if (strlen($name) > 100) {
        $errors[] = 'El nombre no puede exceder 100 caracteres';
    }

    // Si hay errores, redirigir con mensaje de error
    if (!empty($errors)) {
        $errorMsg = implode('. ', $errors);
        header('Location: addWaiter.php?error=' . urlencode($errorMsg));
        exit;
    }

    // Verificar si ya existe un camarero con ese nombre
    $checkSql = "SELECT id FROM waiter WHERE name = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->execute([$name]);
    
    if ($checkStmt->rowCount() > 0) {
        header('Location: addWaiter.php?error=' . urlencode('Ya existe un camarero con ese nombre'));
        exit;
    }

    // Insertar el nuevo camarero
    $sql = "INSERT INTO waiter (name) VALUES (?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$name]);
    
    // Redirigir con mensaje de éxito
    header('Location: addWaiter.php?success=' . urlencode('Camarero "' . $name . '" agregado correctamente'));
    exit;

} catch (PDOException $e) {
    // Error en la base de datos
    error_log("Error al agregar camarero: " . $e->getMessage());
    header('Location: addWaiter.php?error=' . urlencode('Error al guardar en la base de datos'));
    exit;
} catch (Exception $e) {
    // Otro tipo de error
    error_log("Error general: " . $e->getMessage());
    header('Location: addWaiter.php?error=' . urlencode('Error inesperado al procesar la solicitud'));
    exit;
}
?>
