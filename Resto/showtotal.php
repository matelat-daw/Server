<?php
include "includes/conn.php";
$final = 0;
$title = "Total Facturado Hasta Ahora en el Año";
include "includes/header.php";

$stmt = $conn->prepare('SELECT total FROM invoice');
$stmt->execute();
while($row = $stmt->fetch(PDO::FETCH_OBJ))
{
	$final += $row->total;
}
?>
<section class="container-fluid pt-3">
<div id="pc"></div>
    <div id="mobile"></div>
    <div class="row">
        <div class="col-md-1"></div>
            <div class="col-md-10">
                <div id="view1">
                    <br><br>
					<h1>La Facturación de todo el año hasta ahora es: <?php echo $final; ?> $.</h1>
                    <br><br>
                    <button class="btn btn-danger btn-lg" onclick="window.close()">Cierra Esta Ventana</button>
				</div>
            </div>
        <div class="col-md-1"></div>
    </div>
</section>
<?php
include "includes/footer.html";
?>