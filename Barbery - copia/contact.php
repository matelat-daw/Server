<?php
$title = "Página de Contacto - Peluquería de Javier Borneo"; // Pagina de contacto, con un formulario para seleccionar la forma de contacto.
include "includes/header.php";
include "includes/nav_index.html";
include "includes/nav-mob-index.html";
?>
<section class='container-fluid pt-3'>
    <div class='row'>
        <div class='col-sm-1'></div>
            <div class='col-sm-10'>
                <div id='view1'>
                    <br><br><br>
                    <h1>Página de Contacto de la Peluquería de Javier Borneo</h1>
                    <br>
                    <h3>Selecciona la Forma de Contacto, Nosotros nos Pondremos en Contacto Contigo</h3>
                    <br>
                    <h4>También puedes escribirnos a:</h4>
                    <small><a href="mailto:juborneo2001@hotmail.com">La Peluquería de Javier Borneo</a></small>
                    <h3>O <img src="img/qr.webp" alt="Whatsapp" width="480"></h3>
                    <br><br>
                    <form action="connect.php" method="post">
                        <label><select name="callme" id="contact" onchange="changeit()" required>
                            <option value="">Selecciona</option>
                            <option value="Teléfono">Teléfono</option>
                            <option value="Whatsapp">Whatsapp</option>
                            <option value="E-mail">E-mail</option>
                        </select> Selecciona Modo de Contacto</label>
                        <br><br>
                        <label><input type="date" name="date" required> Fecha de Contacto</label>
                        <br><br>
                        <label><input type="time" name="starthour" required> Hora Aproximada de Contacto Desde</label>
                        <br><br>
                        <label><input type="time" name="endhour" required> Hora Aproximada de Contacto Hasta</label>
                        <br><br>
                        <label id="phone" style="visibility: hidden;"><input type="text" id="ph" name="phone"> Dame tu Número</label>
                        <br>
                        <label id="email" style="visibility: hidden;"><input type="email" id="em" name="email"> Dame tu E-mail</label>
                        <br><br>
                        <input type="submit" id="change" value="Contacto" class="btn btn-primary">
                        <br><br>
                    </form>
                </div>
            </div>
        <div class='col-sm-1'></div>
    </div>
</section>
<?php
include "includes/footer.html";
?>