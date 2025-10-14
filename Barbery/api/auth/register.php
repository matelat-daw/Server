<?php
include "../../includes/conn.php";

if (isset($_POST["username"])) // Si se reciben los datos por POST.
{
    $already = false; // Uso esta variable para comprobar que no se repita ni el E-mail ni el Teléfono.
    $name = htmlspecialchars($_POST["username"]);
    // Los apellidos se concatenan al nombre ya que la tabla no tiene columnas separadas para apellidos
    if (!empty($_POST["surname"])) {
        $name .= " " . htmlspecialchars($_POST["surname"]);
    }
    if (!empty($_POST["surname2"])) {
        $name .= " " . htmlspecialchars($_POST["surname2"]);
    }
    $address = htmlspecialchars($_POST["address"]);
    $phone = htmlspecialchars($_POST["phone"]);
    $email = htmlspecialchars($_POST["email"]);
    $pass = htmlspecialchars($_POST["pass"]);
    $bday = htmlspecialchars($_POST["bday"]);
    // Los campos date y time se dejan NULL hasta que el cliente reserve una cita
    $hash = password_hash($pass, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("SELECT phone, email FROM client"); // Busco los E-mail y Teléfonos
    $stmt->execute();
    if ($stmt->rowCount() > 0)
    {
        while($row = $stmt->fetch(PDO::FETCH_OBJ))
        {
            if ($phone == $row->phone || $email == $row->email) // Si está cuaquiera de los tres.
            {
                $already = true; // Pongo $already a true.
                break; // Salgo de la busqueda.
            }
        }
    }

    if (!$already) // Si no están en la base de datos ni E-mail ni Teléfono.
    {
        $stmt = $conn->prepare("INSERT INTO client (name, address, phone, email, pass, bday, date, time) VALUES (:name, :address, :phone, :email, :pass, :bday, NULL, NULL)");

        $stmt->execute(array(
            ':name' => $name, 
            ':address' => $address, 
            ':phone' => $phone, 
            ':email' => $email, 
            ':pass' => $hash, 
            ':bday' => $bday
        ));

        $conn = null;
        $_SESSION['success_message'] = "Te damos la Bienvenida $name, Gracias por Registarte como Cliente en la Peluquería de Javier Borneo.";
        header('Location: /Barbery/app/auth/index.php#view3');
        exit;
    }
    else // Si hay alguna repetido.
    {
        $conn = null;
        $_SESSION['warning_message'] = 'Alguno de tus Datos de Cliente ya Están Registrados, si Tienes Cualquier Duda con tu Cuenta no Dudes en Contactarnos.';
        header('Location: /Barbery/app/auth/index.php#view1');
        exit;
    }
}
?>