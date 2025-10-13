<?php
include "includes/conn.php";
$title = "Facturas por Días";
include "includes/header.php";
include "includes/modal.html";
include "getdata.php";

if (isset($_POST["date"]))
{
    $date = $_POST["date"];
    $i = 0;

    $stmt = $conn->prepare("SET lc_time_names = 'es_ES'");
	$stmt->execute();

    $sql = "SELECT *, DATE_FORMAT(inv_date,'%d %M %Y') as date FROM invoice WHERE inv_date='$date' ORDER BY inv_time DESC;";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    if ($stmt->rowCount() > 0)
    {
        while ($row = $stmt->fetch(PDO::FETCH_OBJ))
        {
            $id[$i] = $row->id;
            $client_id[$i] = $row->client_id;
            $client[$i] = getClient($conn, $client_id[$i]);
            $total[$i] = $row->total;
            $time[$i] = $row->inv_time;
            $i++;
        }
        $i = 0;
        $sql = "SELECT * FROM sold INNER JOIN invoice ON sold.invoice_id=invoice.id WHERE invoice.inv_date='$date' ORDER BY invoice.inv_time DESC;";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_OBJ))
        {
            $ids[$i] = $row->invoice_id;
            $service_id[$i] = $row->food_id;
            $qtt[$i] = $row->qtty . "<br>";
            $i++;
        }
        $i = 0;
        $index = 0;
        for ($z = 0; $z < count($id); $z++)
        {
            recursive($index, $service_id, $qtt, $ids, $i, 0);
            $i++;
            $index++;
        }
        getService($conn, $array, "html");

        $servi = [];
        $pric = [];
        $qtti = [];
        $partial = [];

        for ($i = 0; $i < count($service); $i++)
        {
            $servi[$i] = "";
            $pric[$i] = "";
            $qtti[$i] = "";
            $partial[$i] = "";
            for ($j = 0; $j < count($service[$i]); $j++)
            {
                $servi[$i] .= $service[$i][$j];
                $pric[$i] .= $price[$i][$j];
                $qtti[$i] .= $qtty[$i][$j];
                $partial[$i] .= number_format((floatval($price[$i][$j]) * intval($qtty[$i][$j])), 2) . " $\n";
            }
        }
    }
}
?>
<section class="container-fluid pt-3">
    <div class="row">
    <div id="pc"></div>
	<div id="mobile"></div>
        <div class="col-md-1" style="width:3%;"></div>
            <div class="col-md-10">
                <div id="view1">
                    <h1>Facturas del Día: <?php echo $date; ?></h1>
                    <br><br>
                <?php
                for ($j = 0; $j < $i; $j++)
                {
                    echo '<div id="printable' . $j . '">
                        <h3>Restaurante Fonda 13 - C.U.I.T 20-22506157-3</h3>
                        <h4>Factura  Nº: ' . $id[$j] . ', a ' . $client[$j] . ' Fecha: ' . $date . '</h4>
                        <div class="row">
                        <div style="width: 1px;"></div>
                            <div class="column left" style="background-color:#d0d0d0;">
                            Artículo
                            </div>
                            <div class="column right" style="background-color:#d8d8d8;">
                            Precio
                            </div>
                            <div class="column middle" style="background-color:#dfdfdf;">
                            Cantidad
                            </div>
                            <div class="column right" style="background-color:#e0e0e0;">
                            Parcial
                            </div>
                            <div class="column middle" style="background-color:#e8e8e8;">
                            Base Imponible
                            </div>
                            <div class="column middle" style="background-color:#efefef">
                            Pago de I.V.A. 10%
                            </div>
                            <div class="column middle" style="background-color:#f8f8f8;">
                            Total + I.V.A.
                            </div>
                        </div>
                        <div class="row">
                        <div style="width: 1px;"></div>
                            <div class="column left" style="background-color:#d0d0d0;">' . $servi[$j] . '
                            </div>
                            <div class="column right" style="background-color:#d8d8d8;">' . $pric[$j] . '
                            </div>
                            <div class="column middle" style="background-color:#dfdfdf;">' . $qtti[$j] . '
                            </div>
                            <div class="column right" style="background-color:#e0e0e0;">' . $partial[$j] . '
                            </div>
                            <div class="column middle" style="background-color:#e8e8e8;">' . number_format((float)$total[$j] * 100 / 110, 2, ',', '.') . ' $
                            </div>
                            <div class="column middle" style="background-color:#efefef;">' . number_format((float)$total[$j] * .1, 2, ',', '.') . ' $
                            </div>
                            <div class="column middle" style="background-color:#f8f8f8;">' . number_format((float)$total[$j], 2, ',', '.') . ' $
                            </div>
                        </div>
                        <div class="row">
                            <div class="column total">Total I.V.A. Incluido: ' . number_format((float)$total[$j], 2, ",", ".") . ' $</div>
                        </div>
                    </div>
                    <a id="image' . $j . '" download="Factura Nº ' . $id[$j] . '.png"></a>
					<br><br><br><br>
                    <div class="row">
                        <div class="col-md-3">
                            <button onclick="printIt(' . $j . ')" style="width:160px; height:80px;" class="btn btn-primary">Imprime la Factura</button>
                        </div>
                        <div class="col-md-4">
                            <button onclick="pdfDown(' . $j . ')" class="btn btn-secondary btn-lg">Descarga la Factura en PDF</button>
                        </div>
                        <div class="col-md-3">
                        <button onclick="window.open(\'saveIt.php?id=' . $id[$j] . '\', \'_blank\')" style="width:160px; height:80px;" class="btn btn-info">Guarda la Factura en Exel</button>
                            <script>capture(' . $j . ');</script>
                        </div>
                    </div><hr><br><br><br>';
                }
				?>
                <br><br>
                <button class="btn btn-danger btn-lg" onclick="window.close()">Cierra Esta Ventana</button>
                    <br><br><br><br><br>
				</div>
            </div>
        <div class="col-md-1"></div>
    </div>
</section>
<?php
include "includes/footer.html";
?>