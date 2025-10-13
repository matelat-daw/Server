<?php
include "includes/conn.php";

// Verificar que se recibió el ID del camarero
if (!isset($_POST["waiter_id"]) || !filter_var($_POST["waiter_id"], FILTER_VALIDATE_INT)) {
    header('Location: addWaiter.php?error=Camarero no válido');
    exit;
}

$id = $_POST["waiter_id"];

try {
    // Verificar que el camarero existe antes de eliminarlo
    $checkSql = "SELECT name FROM waiter WHERE id = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->execute([$id]);
    $waiter = $checkStmt->fetch(PDO::FETCH_OBJ);
    
    if (!$waiter) {
        header('Location: addWaiter.php?error=Camarero no encontrado');
        exit;
    }

    // Verificar si el camarero tiene facturas asociadas
    $invoiceCheckSql = "SELECT COUNT(*) as count FROM invoice WHERE waiter_id = ?";
    $invoiceStmt = $conn->prepare($invoiceCheckSql);
    $invoiceStmt->execute([$id]);
    $invoiceCount = $invoiceStmt->fetch(PDO::FETCH_OBJ);
    
    if ($invoiceCount->count > 0) {
        $errorMsg = 'No se puede eliminar al camarero "' . $waiter->name . '" porque tiene ' . 
                    $invoiceCount->count . ' factura(s) asociada(s). ' .
                    'Primero elimina o reasigna las facturas.';
        header('Location: addWaiter.php?error=' . urlencode($errorMsg));
        exit;
    }

    // Eliminar el camarero
    $deleteSql = "DELETE FROM waiter WHERE id = ?";
    $deleteStmt = $conn->prepare($deleteSql);
    $deleteStmt->execute([$id]);
    
    // Redirigir con mensaje de éxito
    $successMsg = 'Camarero "' . $waiter->name . '" eliminado correctamente';
    header('Location: addWaiter.php?success=' . urlencode($successMsg));
    exit;

} catch (PDOException $e) {
    error_log("Error al eliminar camarero: " . $e->getMessage());
    
    // Verificar si es un error de clave foránea
    if (strpos($e->getMessage(), 'foreign key constraint') !== false || 
        strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
        header('Location: addWaiter.php?error=' . urlencode('No se puede eliminar el camarero porque tiene facturas asociadas'));
    } else {
        header('Location: addWaiter.php?error=' . urlencode('Error al eliminar de la base de datos'));
    }
    exit;
} catch (Exception $e) {
    error_log("Error general: " . $e->getMessage());
    header('Location: addWaiter.php?error=' . urlencode('Error inesperado al procesar la solicitud'));
    exit;
}
?>
