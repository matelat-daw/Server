<?php
include 'includes/conn.php';
include 'vendor/autoload.php';
include "getdata.php";

$qtty = [];
$service = [];
$service[] = [];
$price = [];
$price[] = [];
$index = 0;
$id = [];
$array = [];

function fillServices($conn, $result, $who)
{   
    global $service, $price, $qtty;
    global $index, $array; // Hago globales las variables ya declaradas $index, contiene el índice, $array y $qtty.

    $index = 0;
    $array[] = [];
    $qtty[] = [];
    $serv = [];
    $qtt = [];
    foreach ($result as $row)
    {
        $sql = "SELECT * FROM sold WHERE " . $row['id'] . "=invoice_id;"; // Selecciono el contenido de sold (vendidos) de las facturas solicitadas.
        $stmt_sold = $conn->prepare($sql);
        $stmt_sold->execute();
        while ($row_sold = $stmt_sold->fetch(PDO::FETCH_OBJ))
        {
            $id[$index] = $row_sold->invoice_id; // Pongo todas las ID en el array $id, esta vez van las repetidas también.
            $serv[$index] = $row_sold->service_id; // En $serv pongo las ID de los servicios de todas las facturas.
            if ($who == "excel") // Si es para mostrarlo en Excel.
            {
                $qtt[$index] = $row_sold->qtty . "\n"; // Meto en $qtt la cantidad de articulos vendidos con un salto de linea al final \n.
            }
            else // Si es para mostrarlo en HTML.
            {
                $qtt[$index] = $row_sold->qtty . "<br>"; // Meto en $qtt la cantidad de articulos vendidos con un break al final <br>.
            }
            $index++; // Incremento el $index.
        }
    }

    $size = 0; // Se Usará para contar la cantidad de Facturas que Hay en el Trimestre Seleccionado.
    for ($i = 0; $i < count($id) - 1; $i++) // Hago un Bucle Hasta la Cantidad de Facturas - 1(Varias están repetidas ya que una Factura Tiene Varios Servicios y Varios Servicios puede pertenecer a una Única Factura).
    {
        if ($id[$i] != $id[$i + 1]) // Si el Número de Factura Actual es Distinta que la Siguiente.
            $size++; // Incremento $size para saber cuantas facturas hay en total sin repetir.
    }
    $size++; // Vuelvo a Incrementar $size ya que Salió una Vuelta Antes y Queda una Factura por Contar.
    $index = 0; // Inicializo $Index a 0.
    $i = 0; // Inicializo $i a 0.
    for ($z = 0; $z < $size; $z++) // Hago un bucle a la cantidad de facturas distintas que hay.
    {
        recursive($index, $serv, $qtt, $id, $z); // Llamo a la función recursive que carga todos los servicios y cantidades de las facturas, pasandole $index y $i y el array $serv, $qtt y $id.
        $index++; // Incremento $index.
    }

    if ($who == "excel") // Para Mostrar en Excel.
    {
        getService($conn, $array, "excel"); // Llamo a getService y le paso $conn (La COnexión con la Base de Datos), $array que tiene las ID de los servicios y el texto excel, para obtener los nombres de los servicios y los precios.
    }
    else // Para mostrar en HTML
    {
        getService($conn, $array, "html"); // Llamo a getService y le paso $array que tiene las ID de los servicios y el texto html, para obtener los nombres de los servicios y los precios.
    }
}

	$date = $_POST['date']; // El Trimestre recibido desde admin.php.
	$year = $_POST['year']; // El Año recibido desde admin.php.
	
	switch($date)
	{
		case 1:
			$query = "SELECT *, DATE_FORMAT(inv_date,'%d %M %Y') as date FROM invoice WHERE inv_date BETWEEN CAST('" . $year . "-01-01' AS DATE) AND CAST('" . $year . "-03-31' AS DATE) ORDER BY id ASC"; // Para el 1º Trimestre desde el 1/1 al 31/3
		break;
		case 2:
			$query = "SELECT *, DATE_FORMAT(inv_date,'%d %M %Y') as date FROM invoice WHERE inv_date BETWEEN CAST('" . $year . "-04-01' AS DATE) AND CAST('" . $year . "-06-30' AS DATE) ORDER BY id ASC"; // Para el 2º Trimestre desde el 1/4 al 30/6
		break;
		case 3:
			$query = "SELECT *, DATE_FORMAT(inv_date,'%d %M %Y') as date FROM invoice WHERE inv_date BETWEEN CAST('" . $year . "-07-01' AS DATE) AND CAST('" . $year . "-09-30' AS DATE) ORDER BY id ASC"; // Para el 3º Trimestre desde el 1/7 al 30/9
		break;
		case 4:
			$query = "SELECT *, DATE_FORMAT(inv_date,'%d %M %Y') as date FROM invoice WHERE inv_date BETWEEN CAST('" . $year . "-10-01' AS DATE) AND CAST('" . $year . "-12-31' AS DATE) ORDER BY id ASC"; // Para el 4º Trimestre desde el 1/10 al 31/12
		break;
	}
	
	$stmt = $conn->prepare("SET lc_time_names = 'es_ES'");
	$stmt->execute();
	
	$statement = $conn->prepare($query);
	
	$statement->execute();
	
	$result = $statement->fetchAll();
	
if(isset($_POST["export"]))
{
	$file = new PhpOffice\PhpSpreadsheet\Spreadsheet(); // Hay que usarlo así en Wordpress, también funciona en cualquier script de PHP.

	$active_sheet = $file->getActiveSheet();

	$active_sheet->setCellValue('A1', 'Nº de factura');
	$active_sheet->setCellValue('B1', 'Cliente');
	$active_sheet->setCellValue('C1', 'Servicio');
	$active_sheet->setCellValue('D1', 'Precio');
	$active_sheet->setCellValue('E1', 'Cantidad');
    $active_sheet->getStyle('E1')->getAlignment()->setHorizontal("center");
	$active_sheet->setCellValue('F1', 'Parcial');
    $active_sheet->setCellValue('G1', 'Día');
    $active_sheet->getStyle('G1')->getAlignment()->setHorizontal("center");
    $active_sheet->setCellValue('H1', 'Hora');
    $active_sheet->getStyle('H1')->getAlignment()->setHorizontal("center");
	$active_sheet->setCellValue('I1', 'Base Imponible');
	$active_sheet->setCellValue('J1', 'I.V.A.');
    $active_sheet->getStyle('J1')->getAlignment()->setHorizontal("center");
	$active_sheet->setCellValue('K1', 'Total + I.V.A.');

	$count = 2;
	$total = 0;
    fillServices($conn, $result, "excel");

    $servi = [];
    $pric = [];
    $qtti = [];
    $partial = [];
    for ($i = 0; $i < count($service); $i++) // Hago un bucle del tamaño del array que contiene las ID de las facturas.
    {
        $servi[$i] = ""; // Declaro las varibles vacias para poder concatenar los resultados.
        $pric[$i] = "";
        $qtti[$i] = "";
        $partial[$i] = "";
        for ($j = 0; $j < count($service[$i]); $j++) // Hago el segundo bucle del tamaño del array interno, el segundo array contiene las ID de los servicios.
        {
            $servi[$i] .= $service[$i][$j]; // Concateno en $servi en la posición de la ID de la factura todos los servicios que tiene la factura.
            $pric[$i] .= $price[$i][$j]; // Lo mismo con los precios en la variable $pric.
            $qtti[$i] .= $qtty[$i][$j]; // Lo mismo con las cantidades en la variable $qtti.
            $partial[$i] .= number_format((floatval($price[$i][$j]) * intval($qtty[$i][$j])), 2) . " $\n"; // Calculo el precio parcial multiplicando las cantidades por los precios.
        }
    }
    $i = 0;
	foreach($result as $row)
	{
		if ($row["client_id"] !== null)
		{
			$client = getClient($conn, $row["client_id"]);
		}
		else
		{
			$client = "Consumidor Final";
		}

		$active_sheet->setCellValue('A' . $count, $row["id"]);
		$active_sheet->setCellValue('B' . $count, $client);
        $active_sheet->setCellValue('C' . $count, $servi[$i]);
        $active_sheet->setCellValue('D' . $count, $pric[$i]);
        $active_sheet->setCellValue('E' . $count, $qtti[$i]);
		$active_sheet->getStyle('E' . $count)->getAlignment()->setHorizontal("center"); // Alineación del texto con la cadena 'right', Alinea a la Derecha.
        $active_sheet->setCellValue('F' . $count, $partial[$i]);
        $active_sheet->getStyle('F' . $count)->getAlignment()->setHorizontal("right");
		$active_sheet->setCellValue('G' . $count, $row["inv_date"]);
        $active_sheet->getStyle('G' . $count)->getAlignment()->setHorizontal("center");
		$active_sheet->setCellValue('H' . $count, $row["inv_time"]);
        $active_sheet->getStyle('H' . $count)->getAlignment()->setHorizontal("center");
		$active_sheet->setCellValue('I' . $count, $row["total"] * 100 / 121);
		$active_sheet->getStyle('I' . $count)->getNumberFormat()->setFormatCode('#,##0.00 $');
		$active_sheet->setCellValue('J' . $count, $row["total"] * 100 / 121 * .21);
        $active_sheet->getStyle('J' . $count)->getAlignment()->setHorizontal("center");
		$active_sheet->getStyle('J' . $count)->getNumberFormat()->setFormatCode('#,##0.00 $');
		$active_sheet->setCellValue('K' . $count, $row["total"]);
		$active_sheet->getStyle('K' . $count)->getNumberFormat()->setFormatCode('#,##0.00 $');

		$count++;
        $i++;
	}

	$active_sheet->setCellValue('J' . ($count + 2), "Total:");
	$active_sheet->setCellValue('K' . ($count + 2), "=SUM(K2:K" . ($count - 1) . ")");
	$active_sheet->getStyle('K' . ($count + 2))->getNumberFormat()->setFormatCode('#,##0.00 $');
	$active_sheet->setCellValue('A' . ($count + 4), "La Peluquería de Javier Borneo - C.U.I.T 20-22506157-3");

    $active_sheet->getRowDimension(1)->setRowHeight(20); // Cambia el tamaño Vertical de las filas usadas en la planilla.
    $active_sheet->getColumnDimension(chr(65))->setWidth(15);
    $active_sheet->getColumnDimension(chr(66))->setWidth(40); // Si es la Letra C le da el tamaño horizontal 40.
    $active_sheet->getColumnDimension(chr(67))->setWidth(50); // Si es la Letra D le da el tamaño horizontal 50.
    for ($i = 68; $i < 75; $i++)
    {
        $active_sheet->getColumnDimension(chr($i))->setWidth(15); // Si es la Letra E le da el tamaño horizontal 15.
    }
    $active_sheet->getColumnDimension(chr(75))->setWidth(20);

    $active_sheet->getRowDimension($count + 2)->setRowHeight(40); // Cambia el tamaño Vertical de las filas usadas en la planilla.
    $active_sheet->getRowDimension($count + 4)->setRowHeight(40); // Cambia el tamaño Vertical de las filas usadas en la planilla.
		
	$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($file, $_POST["file_type"]);

	$file_name = $date . 'º Trimestre de ' . $year . "." . $_POST["file_type"];

	$writer->save($file_name);

	header('Content-Type: application/x-www-form-urlencoded');

	header('Content-Transfer-Encoding: Binary');

	header("Content-disposition: attachment; filename=\"".$file_name."\"");

	readfile($file_name);

	unlink($file_name);

	exit;
}

$title = "Exportando Facturas";
include "includes/header.php";
?>
  	<img alt="logo" src="img/logo.jpg" height="300" width="100%"/>
	<br>
    	<section class="container-fluid pt-3">
        <div id="pc"></div>
	    <div id="mobile"></div>
    		<br>
    		<h3 style="text-align: center;">Exporta las Facturas a Excel o CSV</h3>
    		<br>
          	<div class="row">
		  		<div class="col-md-1" style="width:3%;"></div>
            		<div class="col-md-10">
						<div class="row">
							<div class="col-md-7">
								Facturas: <?php echo " " . $date; ?>º Trimestre del año: <?php echo " " . $year; ?> La Peluquería de Javier Borneo - C.U.I.T. 20-22506157-3
							</div>
							<div class="col-md-2">
							<form method="post">
								<input type="hidden" name="date" value="<?php echo $date; ?>">
								<input type="hidden" name="year" value="<?php echo $year; ?>">
								<select name="file_type" class="form-control input-sm">
									<option value="Xlsx">Xlsx</option>
									<option value="Csv">Csv</option>
								</select>
							</div>
							<div class="col-md-3">
								<input type="submit" name="export" class="btn btn-primary btn-lg" value="Descarga el Informe" />
							</div>
						</div>
						</form>
						<br><br>
						<table>
							<tr>
							<th>Nº de factura</th>
							<th>Cliente</th>
							<th>Servicio</th>
							<th>Precio</th>
							<th>Cantidad</th>
							<th>Día</th>
                            <th>Hora</th>
							<th>Base Imponible</th>
							<th>I.V.A.</th>
							<th>Total + I.V.A.</th>
							</tr>
						<?php
                            // $sql = "SELECT invoice.id, sold.invoice_id FROM invoice JOIN sold ON invoice.id=sold.invoice_id WHERE YEAR(inv_date)=$year AND MONTH(inv_date)>$date * 3 - 2;";
                            fillServices($conn, $result, "html");
                            $i = 0;
                            foreach($result as $row)
                            {            	
                                if ($row["client_id"] !== null)
                                {
                                    $client = getClient($conn, $row["client_id"]);
                                }
                                else
                                {
                                    $client = "Consumidor Final";
                                }

                                echo '<tr><td style="width: 70px;">' . $row["id"] . '</td>
                                <td style="width: 100px;">' . $client . '</td><td style="width: 180px;">';
                                for ($j = 0; $j < count($service[$i]); $j++)
                                {
                                    echo $service[$i][$j];
                                }
                                echo '</td><td style="text-align: right; width: 40px;">';
                                for ($j = 0; $j < count($service[$i]); $j++)
                                {
                                    echo $price[$i][$j];
                                }
                                echo '</td><td style="text-align: right; width: 40px;">';
                                for ($j = 0; $j < count($service[$i]); $j++)
                                {
                                    echo $qtty[$i][$j];
                                }
                                echo '</td><td style="text-align: right; width: 40px;">' . $row["inv_date"] . '</td>
                                <td style="text-align: right; width: 40px;">' . $row["inv_time"] . '</td>
                                <td style="text-align: right; width: 80px;">' . number_format((float)$row["total"] * 100 / 121, 2, ',', '.') . ' $</td>
                                <td style="text-align: right; width: 80px;">' . number_format((float)$row["total"] * 100 / 121 * .21, 2, ',', '.') . ' $</td>
                                <td style="text-align: right; width: 100px;">' . number_format((float)$row["total"], 2, ',', '.') . ' $</td>
                                </tr>';
                                $i++;
                            }
						?>
					 </table>
                     <br><br><br>
                    <button class="btn btn-danger btn-lg" onclick="window.close()">Cierra Esta Ventana</button>
                    <br><br><br><br><br><br>
					</div>
				<div class="col-md-1" style="width:3%;"></div>
			</div>
    	</section>
<?php
include "includes/footer.html";
?>