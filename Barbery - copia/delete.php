<?php // Script para eliminar un perfil de Cliente.
include "includes/conn.php";
$title = "Eliminando un Cliente";
include "includes/header.php";
include "includes/modal-dismiss.html";
if (isset($_POST["id"])) // Si se recibe la id del alumno.
{
    $id = $_POST["id"];
    $sql = "DELETE FROM client WHERE id=$id";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    if ($stmt->rowCount() > 0) // Lo borró de la base de datos.
    {
        session_destroy(); // Destruyo la sesión
        echo "<script>toast(2, 'Se Ha Eliminado Tu Perfil', 'Gracias por Haber Sido Parte de la Peluquería de Javier Borneo.');</script>";
    }
}
else // Si no llegaron datos por POST.
{
    echo "<script>toast(2, 'Llegaste Aquí por Error', 'No se ha Eliminado Ningún Perfil.');</script>";
}
?>