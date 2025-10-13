<?php
$title = "Arqueo de Caja";
include "includes/header.php";
?>

<section class="container-fluid pt-3">
<div id="pc"></div>
    <div id="mobile"></div>
    <div class="row">
        <div class="col-md-1"></div>
            <div class="col-md-10">
                <div id="view1">
                    <br><br><br><br>
					<h3>Por Favor selecciona la fecha para ver el arqueo</h3>
                        <br>
                        <h4>Selecciona el Día, el Mes y el Año a Consultar</h4>
                        <br>
                        <form action="individual.php" method="post" target="_blank">
                            <label><input type="date" name="date" required></label>
                            <br><br>
                            <input type="submit" value="Muestra esa Fecha" class="btn btn-info btn-lg">
                        </form>
					<br>
					<br>
				</div>
            </div>
        <div class="col-md-1"></div>
    </div>
</section>
<?php
include "includes/footer.html";
?>