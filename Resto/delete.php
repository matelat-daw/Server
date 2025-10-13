<?php
$table = $_GET["table"];
if (file_exists($table . ".txt"))
{
	unlink($table . ".txt");
	echo "<script>if (!alert('La Factura de la mesa : " . $table . " ha sido Cancelada correctamente.')) window.close('_self')</script>";
}
else
{
	echo "<script>window.close('_self')</script>";
}
$title = "Cancelando Factura";
include "includes/header.php";
?>
<section class="container-fluid pt-3">
<div id="pc"></div>
    <div id="mobile"></div>
    <div class="row">
        <div class="col-md-1"></div>
            <div class="col-md-10">
                <div id="view1">
                </div>
            </div>
        <div class="col-md-1"></div>
    </div>
</section>
<?php
include "includes/footer.html";
?>