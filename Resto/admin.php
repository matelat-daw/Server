<!-- Este HTML es para que el Administrador use todos los servicios del sitio, facturar, ver la facturación de cada día, ver la facturación por trimestres y sacar un informe para el gestor, ver la facturación total, hacer un backup de la base de datos, agregar/modificar/eliminar productos -->
<?php
include "includes/conn.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración y Facturación de XXXXX</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.2/css/bootstrap.min.css" integrity="sha512-CpIKUSyh9QX2+zSdfGP+eWLx23C8Dj9/XmHjZY2uDtfkdLGo0uY12jgcnkX9vXOgYajEKb/jiw67EYm+kBf+6g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
	<style>
		label, input, a, select, p
		{
			font-size: 1.3rem; /* Agrando las fuetes de las label, los input y los enlaces a 1.3 rem. */
		}
	</style>
</head>
<body>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.2/js/bootstrap.min.js" integrity="sha512-5BqtYqlWfJemW5+v+TZUs22uigI8tXeVah5S/1Z6qBLVO7gakAOtkOzUtgq6dsIo5c0NJdmGPs0H9I+2OHUHVQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<nav class="navbar fixed-top bg-white" id="pc">
		<div class="col-md-10">
			<!-- Columnas con el menú de navegación. -->
			<div class="nav nav-tabs" id="nav-tab" role="tablist">
				<a class="nav-link" aria-current="page" href="#view1" aria-selected="false" role="tab" aria-controls="nav-contact">Facturar</a></li>
				<a class="nav-link" aria-current="page" href="#view2" aria-selected="false" role="tab" aria-controls="nav-contact">Ver Facturación</a></li>
				<a class="nav-link" aria-current="page" href="#view3" aria-selected="false" role="tab" aria-controls="nav-contact">Agregar/Quitar Productos</a></li>
			</div>
		</div>
		<div class="col-md-2">
		</div>
	</nav>
	<nav class="navbar fixed-top bg-white" id="mobile">
		<div class="col-md-10">
			<!-- Columnas con el menú de navegación. -->
			<div class="nav nav-tabs" id="nav-tab" role="tablist">
				<select class="form-select" id="change" onchange="go()">
					<option value="">Selecciona Tu Opcion</option>
					<option value="view1">Facturar</option>
					<option value="view2">Ver Facturación</option>
					<option value="view3">Agregar/Quitar Productos</option>
				</select>
			</div>
		</div>
		<div class="col-md-2">
		</div>
	</nav>
	<img alt="logo" src="img/logo.webp" height="300" width="100%">
	<br>
	<section class="container-fluid pt-3">
		<div class="row">
			<div class="col-md-1"></div>
			<div class="col-md-10">
				<div id="view1">
					<br><br><br><br>
					<h1>XXXXX</h1>
					<br>
					<h2>Consola de Administración.</h2>
					<br>
					<h3>Sistema de Facturación</h3>
					<br>
					<h4>Factura a la Mesa: </h4>
					<form action="mesa.php" method="post" target="_blank">
                        <label><select name="table">
                            <option value="">Selecciona una Mesa</option>
                            <?php
                            $sql = "SELECT * FROM mesa";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute();
                            while ($row = $stmt->fetch(PDO::FETCH_OBJ))
                            {
                                echo '<option value="' . $row->id . '">' . $row->mesa . '</option>';
                            }
                            ?>
                        </select> Selecciona la Mesa a Facturar</label>
						<br><br>
						<input type="submit" value="Factura a la Mesa" class="btn btn-success">
					</form>
				</div>
				<div id="view2">
					<br><br><br><br><br>
					<div class="row">
						<div class="col-md-5">
						<h2>Ver Totales y Facturas</h2>
						<br>
						<h4>Selecciona el Trimestre y el Año para Descargar un Informe de las Facturas del Trimestre que Necesites y Haz Click en Ver Informe.</h4>
						<br>
						<form action="export.php" method="post" target="_blank">
							<label>
								<select name="date">
									<option value=1>1º Trimestre</option>
									<option value=2>2º Trimestre</option>
									<option value=3>3º Trimestre</option>
									<option value=4>4º Trimestre</option>
								</select> Selecciona el Trimestre a consultar
							</label>
							<br><br>
							<label><input type="number" id="year" name="year" min="2022" max="3000" step="1"> Selecciona el Año</label>
							<br><br>
							<input type="submit" value="Ver Informe" class="btn btn-info btn-lg">
						</form>
						<script>
							var date = document.getElementById("year");
							const d = new Date();
							let year = d.getFullYear();
							date.value = year;
						</script>
						<br>
						<br>
						<div>
							<button onclick="window.open('showtotal.php', '_blank')" class="btn btn-primary" style="height: 64px;">Mostrar el Total de Ventas del Año</button>&nbsp;&nbsp;&nbsp;&nbsp;
                            <button onclick="window.open('db-backup.php', '_blank')" class="btn btn-secondary" style="height: 64px;">Copia de Respaldo de la Base de Datos</button>
						</div>
						</div>
						<div class="col-md-1"></div>
						<div class="col-md-6">
							<h2>Ver/Editar Facturas por Día de Facturación</h2>
							<br>
							<h4>Selecciona la Fecha</h4>
							<br>
							<form action="individual.php" method="post" target="_blank">
								<label><input type="date" name="date"> Selecciona la Fecha</label>
									<br><br>
									<input type="submit" value="Busca esa fecha" class="btn btn-info btn-lg">
							</form>
							<br><br>
                            <h2>Ver Facturas por Fecha</h2>
                            <br>
                            <h4>Selecciona la fecha</h4>
                            <br>
                            <form action="showinvoices.php" method="post" target="_blank">
                                <label><input type="date" name="date"> Selecciona la Fecha</label>
                                <br><br>
                                <input class="btn btn-info btn-lg" type="submit" value="Muestrame las Facturas de esa Fecha">
                            </form>
						</div>
					</div>
				</div>
				<div id="view3">
					<br><br><br><br><br>
					<div class="row">
						<div class="col-md-6">
							<h2>Agregar Productos:</h2>
							<br>
							<form id="form" action="added.php" method="post" target="_blank">
								<label><input type="text" name="product" placeholder="Producto" required> Producto en Venta</label>
								<br><br>
								<label><select name="id" required>
									<option value="">Selecciona la Familia del artículo</option>
									<option value=0>Platos</option>
									<option value=1>Bebidas</option>
									<option value=2>Postres</option>
                                    <option value=3>Cafés</option>
                                    <option value=4>Vinos</option>
									</select> Grupo</label>
								<br><br>
								<label><input type="number" step=".05" name="price" placeholder="Precio" required> Precio del Producto</label>
								<br><br>
								<input type="button" value="Agrego Este Artículo" class="btn btn-info" onclick="sendClean();">
							</form>
						</div>
						<div class="col-md-1"></div>
						<div class="col-md-5">
							<h2>Modificar/Quitar Productos:</h2>
							<br>
							<br>
							<input type="button" value="Modificar/Quitar Servicio" onclick="window.open('modrem.php', '_blank')" class="btn btn-danger">
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-1"></div>
		</div>
	</section>
	<footer class="text-center text-lg-start bg-light text-muted">
		<div class="text-center p-4" style="background-color: rgba(0, 0, 0, 0.05);">
			WEB Design © 2022 César Osvaldo Matelat Borneo.
		</div>
	</footer>
	<script>
		function sendClean()
		{
			let form = document.getElementById("form");
			form.submit();
			form.reset();
		}

		function screen() // Función para dar el tamaño máximo de la pantalla a las vistas.
		{
			var view1 = document.getElementById("view1");
			var view2 = document.getElementById("view2");
			var view3 = document.getElementById("view3");

			var screenHeight = window.innerHeight; // Declaro la variable screenHeight y le asigno el tamaño interno disponible de la pantalla.

			view1.style.height = screenHeight + "px"; // Asigno el tamaño máximo de la pantalla a view1.
			view2.style.height = screenHeight + "px"; // Asigno el tamaño máximo de la pantalla a view2.
			view3.style.height = screenHeight - 200 + "px"; // Asigno el tamaño máximo de la pantalla a view3 menos 200PX.
		}

		function resolution() // Esta función comprueba si el ancho de la pantalla es de Ordenador o de Teléfono.
		{
			let mobile = document.getElementById("mobile");
			let pc = document.getElementById("pc");
			let width = innerWidth;
			if (width < 965) // Si el ancho es inferior a 965.
			{
				pc.style.visibility = "hidden"; // Oculta el menú de Ordenador
				mobile.style.visibility = "visible"; // Muestra el menú de Teléfono.
			}
			else // Si es mayor o igual a 965;
			{
				pc.style.visibility = "visible"; // Muestra el menú para Ordenador
				mobile.style.visibility = "hidden"; // Oculta el menú para Teléfono.
			}
		}

		function go() // Cuando cambia el selector del menú para Teléfono.
		{
			var change = document.getElementById("change").value; // Change obtiene el valor en el selector.
			switch (change)
			{
				case "view3":
					window.open("#view3", "_self");
					break;
				case "view2":
					window.open("#view2", "_self");
					break;
				default:
					window.open("#view1", "_self");
					break;
			}
		}
	</script>
	<script>screen();</script>
	<script>resolution();</script>
	<!-- Script para detectar si la pantalla modifica su tamaño horizontal -->
	<script>
		window.addEventListener('resize', resolution);
		resolution();
	</script>
</body>
</html>