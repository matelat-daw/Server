<?php
include "includes/conn.php";

// Verificar que se recibieron datos por POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: addUser.php?error=Método no permitido');
    exit;
}

// Variables para el control de errores y mensajes
$errors = [];
$success = false;

try {
    // Validar y sanitizar datos de entrada
    $name = trim(htmlspecialchars($_POST['client'] ?? ''));
    $surname1 = trim(htmlspecialchars($_POST['surname1'] ?? ''));
    $surname2 = trim(htmlspecialchars($_POST['surname2'] ?? ''));
    $email = trim(filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL));
    $pass = $_POST['pass'] ?? '';
    $phone = trim(htmlspecialchars($_POST['phone'] ?? ''));
    $address = trim(htmlspecialchars($_POST['address'] ?? ''));

    // Validaciones básicas
    if (empty($name)) {
        $errors[] = 'El nombre es obligatorio';
    }
    
    if (empty($surname1)) {
        $errors[] = 'El apellido es obligatorio';
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email no válido';
    }
    
    if (empty($pass) || strlen($pass) < 6) {
        $errors[] = 'La contraseña debe tener al menos 6 caracteres';
    }
    
    if (empty($phone)) {
        $errors[] = 'El teléfono es obligatorio';
    }
    
    if (empty($address)) {
        $errors[] = 'La dirección es obligatoria';
    }

    // Si hay errores, redirigir con mensaje de error
    if (!empty($errors)) {
        $errorMsg = implode(', ', $errors);
        header('Location: addUser.php?error=' . urlencode($errorMsg));
        exit;
    }

    // Verificar duplicados
    $checkSql = "SELECT id FROM client WHERE phone = ? OR email = ?";
    $checkParams = [$phone, $email];
    
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->execute($checkParams);
    
    if ($checkStmt->rowCount() > 0) {
        header('Location: addUser.php?error=Ya existe un cliente con ese teléfono o email');
        exit;
    }

    // Insertar nuevo cliente
    $hash = password_hash($pass, PASSWORD_DEFAULT);
    $insertSql = "INSERT INTO client (name, surname1, surname2, email, pass, phone, address) VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $insertStmt = $conn->prepare($insertSql);
    $insertStmt->execute([
        $name, 
        $surname1, 
        $surname2 ?: null, 
        $email, 
        $hash, 
        $phone, 
        $address
    ]);

    if ($insertStmt->rowCount() > 0) {
        $success = true;
        header('Location: addUser.php?success=Cliente agregado correctamente');
    } else {
        header('Location: addUser.php?error=Error al agregar el cliente');
    }

} catch (PDOException $e) {
    error_log("Error en inserción de cliente: " . $e->getMessage());
    header('Location: addUser.php?error=Error en la base de datos');
} catch (Exception $e) {
    error_log("Error general en inserción de cliente: " . $e->getMessage());
    header('Location: addUser.php?error=Error inesperado');
}

exit;
