<?php // Script para autenticar a un cliente
include "../../includes/conn.php";

if (isset($_POST["email"]) && isset($_POST["pass"])) // Si se reciben email y contraseña
{
    $email = $_POST["email"];
    $pass = $_POST["pass"];
    
    // Consulta segura con prepared statement
    $sql = "SELECT * FROM client WHERE email = :email";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) // Si se encontró el email
    {
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        
        if (password_verify($pass, $row->pass)) // Verificar la contraseña
        {
            // Credenciales correctas - Crear sesión
            $_SESSION["client"] = $row->id;
            $_SESSION["name"] = $row->name;
            $_SESSION['success_message'] = 'Bienvenido/a ' . $row->name . '!';
            
            // Redirigir al perfil
            header('Location: /Barbery/app/client/profile.php');
            exit;
        }
        else // Contraseña incorrecta
        {
            $_SESSION['error_message'] = 'Las credenciales ingresadas no son correctas. Por favor, verifica tu correo y contraseña.';
            header('Location: /Barbery/app/auth/index.php#view3');
            exit;
        }
    }
    else // Email no encontrado
    {
        $_SESSION['error_message'] = 'No se encontró una cuenta con ese correo electrónico.';
        header('Location: /Barbery/app/auth/index.php#view3');
        exit;
    }
}
else // No se enviaron datos
{
    $_SESSION['error_message'] = 'Por favor, completa todos los campos.';
    header('Location: /Barbery/app/auth/index.php#view3');
    exit;
}
?>
