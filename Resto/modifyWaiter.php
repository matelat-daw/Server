<?php
include "includes/conn.php";

// Verificar que se recibieron datos por POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: addWaiter.php?error=Método no permitido');
    exit;
}

// Validar que se recibió el ID del camarero
if (!isset($_POST["waiter_id"]) || !filter_var($_POST["waiter_id"], FILTER_VALIDATE_INT)) {
    header('Location: addWaiter.php?error=Camarero no válido');
    exit;
}

$id = $_POST["waiter_id"];
$errors = [];

try {
    // Verificar que el camarero existe
    $checkSql = "SELECT name FROM waiter WHERE id = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->execute([$id]);
    $waiter = $checkStmt->fetch(PDO::FETCH_OBJ);
    
    if (!$waiter) {
        header('Location: addWaiter.php?error=Camarero no encontrado');
        exit;
    }

    // Validar y sanitizar el nuevo nombre
    $new_name = trim(htmlspecialchars($_POST['new_name'] ?? ''));
    
    if (empty($new_name)) {
        $errors[] = 'El nuevo nombre es obligatorio';
    }
    
    if (strlen($new_name) < 3) {
        $errors[] = 'El nombre debe tener al menos 3 caracteres';
    }
    
    if (strlen($new_name) > 100) {
        $errors[] = 'El nombre no puede exceder 100 caracteres';
    }

    // Si hay errores, redirigir
    if (!empty($errors)) {
        $errorMsg = implode('. ', $errors);
        header('Location: addWaiter.php?error=' . urlencode($errorMsg));
        exit;
    }

    // Verificar si el nuevo nombre ya existe (en otro camarero)
    $duplicateSql = "SELECT id FROM waiter WHERE name = ? AND id != ?";
    $duplicateStmt = $conn->prepare($duplicateSql);
    $duplicateStmt->execute([$new_name, $id]);
    
    if ($duplicateStmt->rowCount() > 0) {
        header('Location: addWaiter.php?error=' . urlencode('Ya existe otro camarero con ese nombre'));
        exit;
    }

    // Actualizar el nombre del camarero
    $sql = "UPDATE waiter SET name = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$new_name, $id]);
    
    // Redirigir con mensaje de éxito
    $successMsg = 'Camarero actualizado correctamente de "' . $waiter->name . '" a "' . $new_name . '"';
    header('Location: addWaiter.php?success=' . urlencode($successMsg));
    exit;

} catch (PDOException $e) {
    error_log("Error al modificar camarero: " . $e->getMessage());
    header('Location: addWaiter.php?error=' . urlencode('Error al actualizar la base de datos'));
    exit;
} catch (Exception $e) {
    error_log("Error general: " . $e->getMessage());
    header('Location: addWaiter.php?error=' . urlencode('Error inesperado al procesar la solicitud'));
    exit;
}
?>
