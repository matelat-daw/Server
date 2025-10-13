<?php
include "includes/conn.php";

// Verificar que se recibieron datos por POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: addUser.php?error=method');
    exit;
}

// Variables para el control de errores y mensajes
$errors = [];
$success = false;

try {
    // Validar y sanitizar datos de entrada
    $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
    $name = trim(htmlspecialchars($_POST['client'] ?? ''));
    $surname1 = trim(htmlspecialchars($_POST['surname1'] ?? ''));
    $surname2 = trim(htmlspecialchars($_POST['surname2'] ?? ''));
    $dni = trim(htmlspecialchars($_POST['cuit'] ?? ''));
    $email = trim(filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL));
    $pass = $_POST['pass'] ?? '';
    $pass2 = $_POST['pass2'] ?? '';
    $phone = trim(htmlspecialchars($_POST['phone'] ?? ''));
    $address = trim(htmlspecialchars($_POST['address'] ?? ''));
    $kind = filter_var($_POST['kind'] ?? 0, FILTER_VALIDATE_INT);

    // Validaciones básicas
    if (!$id) {
        $errors[] = 'ID de cliente no válido';
    }
    
    if (empty($name)) {
        $errors[] = 'El nombre es obligatorio';
    }
    
    if (empty($surname1)) {
        $errors[] = 'El apellido es obligatorio';
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email no válido';
    }
    
    if (empty($phone)) {
        $errors[] = 'El teléfono es obligatorio';
    }
    
    if (empty($address)) {
        $errors[] = 'La dirección es obligatoria';
    }

    // Validar contraseñas si se proporcionaron
    if (!empty($pass)) {
        if ($pass !== $pass2) {
            $errors[] = 'Las contraseñas no coinciden';
        }
        if (strlen($pass) < 6) {
            $errors[] = 'La contraseña debe tener al menos 6 caracteres';
        }
    }

    // Si hay errores, redirigir con mensaje de error
    if (!empty($errors)) {
        $errorMsg = implode(', ', $errors);
        header('Location: addUser.php?error=' . urlencode($errorMsg));
        exit;
    }

    // Verificar que el cliente existe
    $checkSql = "SELECT id FROM client WHERE id = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->execute([$id]);
    
    if ($checkStmt->rowCount() === 0) {
        header('Location: addUser.php?error=Cliente no encontrado');
        exit;
    }

    // Verificar duplicados (excluyendo el cliente actual)
    $duplicateSql = "SELECT id FROM client WHERE (phone = ? OR email = ?" . 
                   ($dni ? " OR cuit = ?" : "") . ") AND id != ?";
    
    $duplicateParams = [$phone, $email];
    if ($dni) {
        $duplicateParams[] = $dni;
    }
    $duplicateParams[] = $id;
    
    $duplicateStmt = $conn->prepare($duplicateSql);
    $duplicateStmt->execute($duplicateParams);
    
    if ($duplicateStmt->rowCount() > 0) {
        header('Location: addUser.php?error=Ya existe un cliente con ese teléfono, email o DNI');
        exit;
    }

    // Preparar la consulta de actualización
    if (!empty($pass)) {
        // Actualizar con nueva contraseña
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $updateSql = "UPDATE client SET name = ?, surname1 = ?, surname2 = ?, email = ?, pass = ?, phone = ?, address = ? WHERE id = ?";
        $updateParams = [$name, $surname1, $surname2 ?: null, $email, $hash, $phone, $address, $id];
    } else {
        // Actualizar sin cambiar contraseña
        $updateSql = "UPDATE client SET name = ?, surname1 = ?, surname2 = ?, email = ?, phone = ?, address = ? WHERE id = ?";
        $updateParams = [$name, $surname1, $surname2 ?: null, $email, $phone, $address, $id];
    }

    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->execute($updateParams);

    if ($updateStmt->rowCount() > 0) {
        $success = true;
        header('Location: addUser.php?success=Cliente modificado correctamente');
    } else {
        header('Location: addUser.php?error=No se realizaron cambios en los datos');
    }

} catch (PDOException $e) {
    error_log("Error en modificación de cliente: " . $e->getMessage());
    header('Location: addUser.php?error=Error en la base de datos');
} catch (Exception $e) {
    error_log("Error general en modificación de cliente: " . $e->getMessage());
    header('Location: addUser.php?error=Error inesperado');
}

exit;
?>
