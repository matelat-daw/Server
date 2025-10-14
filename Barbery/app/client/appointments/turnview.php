<?php
include $_SERVER['DOCUMENT_ROOT'] . "/Barbery/includes/conn.php";
$title = "Turnos de Clientes";
include $_SERVER['DOCUMENT_ROOT'] . "/Barbery/includes/header.php";
?>
<img alt="logo" src="/Barbery/assets/img/logo.jpg" height="300" width="100%"/>
<br>
<section class="container-fluid pt-3">
    <div class="row">
        <div class="col-md-1"></div>
            <div class="col-md-10">
                <div id="view1">
                    <br><br>
					<?php
					$date = $_POST['date'];
					$stmt = $conn->prepare("SELECT email, time FROM client WHERE date='$date' ORDER BY time;");
					$stmt->execute();
					if ($stmt->rowCount() > 0)
					{
						while ($row = $stmt->fetch(PDO::FETCH_OBJ))
						{
							echo '<h3>El Cliente: ' . $row->email . ' Tiene un Turno a las: ' . $row->time . ' Hs.</h3>';
						}
					}
					else
					{
						echo "<h4>No hay Turnos para ese día.</h4>";
					}
					?>
					<br><br>
					<input type="button" value="Cierra esta Ventana" onclick="window.close()">
				</div>
            </div>
        <div class="col-md-1"></div>
    </div>
</section>
</body>

</html>