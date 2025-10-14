<?php
include "includes/conn.php";
$title = "La Peluquería de Javier Borneo - Recupera tu Contraseña";
include "includes/header.php";
include "includes/modal.html";
include "includes/nav_index.html";

if (isset($_POST["email"]))
{
    $email = htmlspecialchars($_POST["email"]);
    $hash = substr(md5(uniqid($email, true)), 8, 8);
    $pass = password_hash($hash, PASSWORD_DEFAULT);
    $ok = false;
    $sql = "SELECT email FROM client;"; // Preparo una consulta de todos los E-mail de espectadores de la base de datos.
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    while($row = $stmt->fetch(PDO::FETCH_OBJ)) // Asigno a la variable $row el contenido de la consulta.
    {
        if ($row->email == $email) // Si el E-mail está en la base de datos.
        {
            $ok = true; // $ok es true.
            break; // Rompo el bucle, si se encuentra el E-mail en los primeros resultados no hace falta seguir buscando, el E-mail es clave unica.
        }
    }
    if (!$ok) // Si $ok es false.
    {
        echo "<script>toast(2, 'Hay un Error', 'Lo Siento no Existe Ningún Cliente con E-mail: $email, Vuelve a Intentarlo con la Dirección con la que te Registrate.');</script>"; // Error.
    }
    else // Si el E-mail está en la base de datos.
    {
        $sql = "UPDATE client SET pass='$pass' WHERE email='$email'"; // Hago un update de la contraseña de ese E-mail.
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        echo "<script>toast(0, 'Todo ha Ido Bien', 'Se ha cambiado tu Contraseña a: $hash, Selecciónala y Cópiala, Después Vuelve a Iniciar Sesión con los Nuevos Datos. Te Recomendamos que Cambies la Contraseña, Gracias por Comprar tus Entradas Aquí.');</script>";
    }
}
?>
<section class="container-fluid pt-3">
    <div class="row">
        <div class="col-md-1"></div>
            <div class="col-md-10">
                <div id="view1">
                <br><br><br><br>
                    <h1>Vas a Modificar tu Contraseña por una Provisoria</h1>
                    <br><br>
                    <h2>Por Favor Después de Loguearte Modifícala Entrando en tu Perfil</h2>
                    <br><br>
                    <form action="" method="post">
                        <label><input type="text" name="email"> Danos el E-mail con el que te Registraste</label>
                        <br><br>
                        <input type="submit" value="Recuperar mi Contraseña">
                    </form>
                </div>
            </div>
        <div class="col-md-1"></div>
    </div>
</section>
<?php
include "includes/footer.html";
?>
<script>screenSize();</script>
</body>
</html>