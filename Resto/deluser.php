<?php
include "includes/conn.php";

// Verificar que se recibió el ID del cliente
if (!isset($_POST["client"]) || !filter_var($_POST["client"], FILTER_VALIDATE_INT)) {
    header('Location: addUser.php?error=Cliente no válido');
    exit;
}

$id = $_POST["client"];

try {
    // Verificar que el cliente existe antes de eliminarlo
    $checkSql = "SELECT name, surname1 FROM client WHERE id = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->execute([$id]);
    $client = $checkStmt->fetch(PDO::FETCH_OBJ);
    
    if (!$client) {
        header('Location: addUser.php?error=Cliente no encontrado');
        exit;
    }

    // Verificar si el cliente tiene facturas o pedidos asociados
    $invoiceCheckSql = "SELECT COUNT(*) as count FROM invoice WHERE client_id = ?";
    $invoiceStmt = $conn->prepare($invoiceCheckSql);
    $invoiceStmt->execute([$id]);
    $invoiceCount = $invoiceStmt->fetch(PDO::FETCH_OBJ)->count;

    if ($invoiceCount > 0) {
        $clientName = htmlspecialchars($client->name . ' ' . $client->surname1);
        header('Location: addUser.php?error=' . urlencode('No se puede eliminar a "' . $clientName . '" porque tiene ' . $invoiceCount . ' factura(s) asociada(s)'));
        exit;
    }

    // Eliminar el cliente
    $deleteSql = "DELETE FROM client WHERE id = ?";
    $deleteStmt = $conn->prepare($deleteSql);
    $deleteStmt->execute([$id]);
    
    if ($deleteStmt->rowCount() > 0) {
        $clientName = htmlspecialchars($client->name . ' ' . $client->surname1);
        header('Location: addUser.php?success=' . urlencode('Cliente "' . $clientName . '" eliminado correctamente del sistema'));
    } else {
        header('Location: addUser.php?error=Error al eliminar el cliente');
    }

} catch (PDOException $e) {
    error_log("Error al eliminar cliente: " . $e->getMessage());
    
    // Verificar si es un error de integridad referencial
    if (strpos($e->getMessage(), 'foreign key constraint') !== false) {
        header('Location: addUser.php?error=No se puede eliminar el cliente porque tiene registros asociados');
    } else {
        header('Location: addUser.php?error=Error en la base de datos');
    }
} catch (Exception $e) {
    error_log("Error general al eliminar cliente: " . $e->getMessage());
    header('Location: addUser.php?error=Error inesperado');
}

exit;