<?php // Script que destruye la sesión.
session_start();
session_destroy(); // Cierra la sesión de Alumno, para poder cerrar la sesión hay que abrirla previamente con session_start().
$title = "Has Cerrado Sesión, Hasta Pronto";
include $_SERVER['DOCUMENT_ROOT'] . "/Barbery/includes/header.php";
include $_SERVER['DOCUMENT_ROOT'] . "/Barbery/includes/nav-start.html";
include $_SERVER['DOCUMENT_ROOT'] . "/Barbery/includes/nav-mob-start.html";
?>
<section class="container-fluid pt-3">
    <div class="row">
        <div class="col-md-10">
            <div id="view1">
                <br><br><br><br>
                <h1>Has Cerrado Sesión Vuelve a la Página de Inicio.</h1>
                <br>
                <button class="btn btn-primary" onclick="window.open('/Barbery/app/auth/index.php', '_self')">Vuelve al Inicio</button>
            </div>
        </div>
    </div>
</section>
<?php
include $_SERVER['DOCUMENT_ROOT'] . "/Barbery/includes/footer.html";
?>