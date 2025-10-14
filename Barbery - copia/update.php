<?php
include "includes/conn.php";
$title = "Modificando un Servicio - La Peluquería de Javier Borneo";
include "includes/header.php";
include "includes/modal-update.html";
?>
<section class="container-fluid pt-3">
    <div class="row">
        <div class="col-md-1"></div>
            <div class="col-md-10">
                <div id="view1">
                    <br><br><br><br>
					<?php
						$service = $_POST['service'];
						$price = $_POST['price'];
						$id = $_POST['id'];
						$stmt = $conn->prepare("UPDATE service SET service='$service', price='$price' WHERE id=$id;");
						$stmt->execute();
						
						echo "<script>toast(0, 'Todo Ha Ido Bien:', 'Servicio : " . $service . " Modificado correctamente.');</script>";
					?>
				</div>
            </div>
        <div class="col-md-1"></div>
    </div>
</section>
</body>
</html>