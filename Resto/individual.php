<?php
include "includes/conn.php";
$title = "Facturas por Días";
include "includes/function.php";
include "includes/header.php";
include "includes/modal.html";

if (isset($_POST["date"]))
{
    $date = $_POST["date"];
    $mydate = explode("-", $date);
    $mdate = $mydate[2] . "/" . $mydate[1] . "/" . $mydate[0];
    $product = "";
	$price = "";
	$quantity = "";

    $stmt = $conn->prepare("SET lc_time_names = 'es_ES'");
	$stmt->execute();

    $sql = "SELECT *, DATE_FORMAT(inv_date,'%d %M %Y') as date FROM invoice WHERE inv_date='$date'";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll();
    ?>
<section class="container-fluid pt-3">
<div id="pc"></div>
<div id="mobile"></div>
    <div class="row">
        <div class="col-md-1" style="width:3%;"></div>
            <div class="col-md-10">
                <div id="view1">
                <br><br>
                <h3 style="text-align: center;">Facturas del Día <?php echo $mdate; ?></h3>
                <br>
                <table>
                    <tr>
                    <th>Nº de factura</th>
                    <th>Camarero</th>
                    <th>Mesa</th>
                    <th>Cliente</th>
                    <th>Producto</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Hora</th>
                    <th>Día</th>
                    <th>Base Imponible</th>
                    <th>Pago de I.V.A. 10%</th>
                    <th>Total + I.V.A.</th>
                    <th style="color: red;">BORRAR</th>
                    </tr>
                    
<?php
$table_name = "";
$client = "";
$waiter = "";
$price = "";
$partial = "";
$product = "";
$quantity = "";

    foreach($result as $row)
	{
        result($conn, $row, 1, 0); // Llama a la función result, le pasa la conexión, el resultado de la base de datos y un 0.

        echo '<tr>
        <td>' . $row["id"] . '</td>
        <td>' . $waiter . '</td>
        <td>' . $table_name . '</td>
        <td>' . $client . '</td>
        <td>' . $product . '</td>
        <td>' . $price . '</td>
        <td>' . $quantity . '</td>
        <td>' . $row["inv_time"] . '</td>
        <td>' . $row["inv_date"] . '</td>
        <td>' . number_format((float)$row["total"] * 100 / 110, 2, ',', '.') . ' $</td>
        <td>' . number_format((float)$row["total"] * 100 / 110 * .1, 2, ',', '.') . ' $</td>
        <td>' . number_format((float)$row["total"], 2, ',', '.') . ' $</td>
        <td><form action="delinvoice.php" method="post">
            <input type="hidden" name="id" value="' . $row["id"] . '">
            <input type="submit" value="Quitar" class="btn btn-danger">
            </form>
        </td>
        </tr>';
        $product = "";
		$price = "";
		$quantity = "";
	}
    ?>
                </table>
                <br><br><br>
                    <button class="btn btn-danger" style="width:160px; height:80px;" onclick="window.close()">Cierra Esta Ventana</button>
                </div>
            </div>
        <div class="col-md-1" style="width:3%;"></div>
    </div>
</section>
<?php
}
include "includes/footer.html";
?>