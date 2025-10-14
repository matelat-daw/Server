<?php
include "includes/conn.php";
$title = "La Peluquería de Javier Borneo - Modificando los datos del Cliente";
include "includes/header.php";
include "includes/modal_index.html";

if (isset($_POST["username"])) // Si llegan datos por post.
{
    $ok = true; // Uso la variable $ok para verificar que no se repita el DNI ni el E-mail.
    $id = $_SESSION["client"]; // Asigno a la variable $id el valor de la sesión id.
    $name = htmlspecialchars($_POST["username"]); // Asigno a variables lo recibido por post.
    $address = htmlspecialchars($_POST["address"]);
    $phone = htmlspecialchars($_POST["phone"]);
    $email = htmlspecialchars($_POST["email"]);
    $bday = htmlspecialchars($_POST["bday"]);
    $pass = htmlspecialchars($_POST["pass"]);
    if ($pass != "")
    {
        $hash = password_hash($pass, PASSWORD_DEFAULT);
    }

    $sql = "SELECT id, email, phone FROM client"; // Preparo la consulta de la ID, Teléfono y E-mail de toda la tabla clients.
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    while($row = $stmt->fetch(PDO::FETCH_OBJ)) // Mientras reciba datos al asignar a la variable $row el resultado asociado a $stmt.
    {
        if ($id != $row->id)
        {
            if ($row->email == $email || $row->phone == $phone) // Si el email o el teléfono o el DNI enviados por post están en la tabla.
            {
                $ok = false; // $ok es false.
            }
        }
    }
    if ($ok) // Si $ok está a true, no hubo coincidencias.
    {
        $sql = "SELECT phone, email FROM client WHERE id='$id'"; // Preparo la consulta a la ID del cliente.
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_OBJ); // Asigno el resultado a la variable $row.
        if ($row->email != $email) // Verifico que el E-mail enviado por post es distinto que el de la tabla.
        {
            if ($row->phone != $phone)
            {
                if ($pass != "")
                {
                    $sql = "UPDATE client SET name='$name', address='$address', phone='$phone', email='$email', pass='$hash', bday='$bday' WHERE id=$id;"; // Preparo la consulta modificando todo.
                }
                else
                {
                    $sql = "UPDATE client SET name='$name', address='$address', phone='$phone', email='$email', bday='$bday' WHERE id=$id;"; // Preparo la consulta modificando todo.
                }
            }
            else
            {
                if ($pass != "")
                {
                    $sql = "UPDATE client SET name='$name', address='$address', email='$email', pass='$hash', bday='$bday' WHERE id=$id;"; // Preparo la consulta modificando todo.
                }
                else
                {
                    $sql = "UPDATE client SET name='$name', address='$address', email='$email', bday='$bday' WHERE id=$id;"; // Preparo la consulta modificando todo.
                }
            }
        }
        else // Si el E-mail está repetido, el cliente no cambió su E-mail.
        {
            if ($row->phone != $phone)
            {
                if ($pass != "")
                {
                    $sql = "UPDATE client SET name='$name', address='$address', phone='$phone', pass='$hash', bday='$bday' WHERE id=$id;"; // Preparo la consulta modificando todo.
                }
                else
                {
                    $sql = "UPDATE client SET name='$name', address='$address', phone='$phone', bday='$bday' WHERE id=$id;"; // Preparo la consulta modificando todo.
                }
            }
            else
            {
                if ($pass != "")
                {
                    $sql = "UPDATE client SET name='$name', address='$address', pass='$hash', bday='$bday' WHERE id=$id;"; // Preparo la consulta modificando todo menos los datos repetidos.
                }
                else
                {
                    $sql = "UPDATE client SET name='$name', address='$address', bday='$bday' WHERE id=$id;"; // Preparo la consulta modificando todo menos los datos repetidos.
                }
            }
        }

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        if ($stmt->rowCount() > 0) // Ejecuto la consulta y compruebo si se modifico el campo.
        {
            session_destroy();
            echo "<script>toast(0, 'Todo ha ido Bien', 'Se han Modificado tus Datos $name, Vuelve a Iniciar Sesión con tus Nuevos Datos.');</script>";
            // Muestro el aviso que todo ha ido bien.
        }
        else // Si hubo algún error.
        {
            echo "<script>toast(1, 'Algo Ha Fallado', 'No se Han Podido Modificar tus Datos $name.');</script>";
            // Muestro el error de mysql, algo ha fallado
        }
    }
    else
    {
        echo "<script>toast(1, 'Ya Registrado:', 'El E-mail $email o el Teléfono $phone, ya Están Registrados en Este Sitio. No puedes usar esos datos.');</script>"; // Muestro el error, el E-mail, etc. ya están registrados.
    }
}
?>
</body>
</html>