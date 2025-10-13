<?php
$title = "Buscar Facturas por Mesa y/o Fecha";
include "includes/header.php";
?>
<section class="container-fluid pt-3">
    <div class="row">
        <div class="col-md-1"></div>
            <div class="col-md-10">
                <div id="view1">
                    <br><br>
					<h1>Busqueda de Facturas por la Fecha, por la Mesa o por la Mesa y Fecha</h1>
					<form id="show" action="showtable.php" method="post" onsubmit="return verify(this.show)">
                    <label><input type="date" name="date"> Selecciona la Fecha</label>
					<br>
					<br>
					<label><select name="table">
                        <option value="">Selecciona una Mesa</option>
						<option value="Entrada 1">Entrada 1</option>
						<option value="Entrada 2">Entrada 2</option>
						<option value="Entrada 3">Entrada 3</option>
						<option value="Entrada 4">Entrada 4</option>
						<option value="Barra 1">Barra 1</option>
						<option value="Barra 2">Barra 2</option>
						<option value="Barra 3">Barra 3</option>
						<option value="Patio 1">Patio 1</option>
						<option value="Patio 2">Patio 2</option>
						<option value="Patio 3">Patio 3</option>
						<option value="Patio 4">Patio 4</option>
						<option value="Patio 5">Patio 5</option>
						<option value="Vereda 1">Vereda 1</option>
						<option value="Vereda 2">Vereda 2</option>
						<option value="Vereda 3">Vereda 3</option>
						<option value="Mesa 1">Mesa 1</option>
						<option value="Mesa 2">Mesa 2</option>
						<option value="Mesa 3">Mesa 3</option>
						<option value="Mesa 4">Mesa 4</option>
						<option value="Mesa 5">Mesa 5</option>
						<option value="Mesa 6">Mesa 6</option>
						<option value="Mesa 7">Mesa 7</option>
						<option value="Mesa 8">Mesa 8</option>
						<option value="Tablón 1">Tablón 1</option>
						<option value="Tablón 2">Tablón 2</option>
						<option value="Mesa 9">Mesa 9</option>
						<option value="Mesa 10">Mesa 10</option>
						<option value="Mesa 11">Mesa 11</option>
						<option value="Mesa 12">Mesa 12</option>
						<option value="Mesa 13">Mesa 13</option>
					<select> Selecciona la Mesa</label>
					<br><br>
					<input type="submit" value="Ver Facturas" class="btn btn-primary btn-lg">
                    </form>
				</div>
            </div>
        <div class="col-md-1"></div>
    </div>
</section>
<?php
include "includes/footer.html";
?>