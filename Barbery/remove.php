<?php
include "includes/conn.php";
$title = "Quitar Servicio";
include "includes/header.php";
include "includes/modal.html";
?>

<img alt="logo" src="img/logo.jpg" height="300" width="100%"/>
<br>
<section class="container-fluid pt-3">
    <div class="row">
        <div class="col-md-1"></div>
            <div class="col-md-10">
                <div id="view1">
                <br><br><br><br>
                <?php
                $id = $_POST['id'];
                $service = $_POST['service'];
                $stmt = $conn->prepare("DELETE FROM service WHERE id=$id");
                $stmt->execute();
                echo "<script>toast(0, 'Servicio Quitado:', 'El Servicio " . $service . " ha Sido Quitado Correctamente.');</script>";
                ?>
                </div>
            </div>
        <div class="col-md-1"></div>
    </div>
</section>
</body>

</html>