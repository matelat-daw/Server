<?php
include "includes/conn.php";
include "getdata.php";
$title = "Facturación de La Peluquería de Javier Borneo";
include "includes/header.php";

$stmt = $conn->prepare("SET lc_time_names = 'es_ES'");
$stmt->execute();
$sql = "SELECT *, DATE_FORMAT(inv_date,'%d %M %Y') as date FROM invoice ORDER BY id DESC LIMIT 1;";
$stmt = $conn->prepare($sql);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_OBJ);
$id = $row->id;
$client = getClient($conn, $row->client_id);
$total = $row->total;
$date = $row->inv_date;
$time = $row->inv_time;

$i = 0;
$qtty = "";
$service = [];
$price = [];
$sql = "SELECT * FROM sold WHERE invoice_id=$id;";
$stmt = $conn->prepare($sql);
$stmt->execute();
while ($row = $stmt->fetch(PDO::FETCH_OBJ))
{
    $service[$i] = [];
    $price[$i] = [];
    $services[$i] = $row->service_id;
    $qtty .= $row->qtty . "<br>";
    $i++;
}
getService($conn, $services, "html");
?>
<img alt="logo" src="img/logo.jpg" height="300" width="100%"/>
<br>
<section class="container-fluid pt-3">
    <div id="pc"></div>
    <div id="mobile"></div>
    <div class="row">
        <div class="col-md-1" style="width: 2%;"></div>
            <div class="col-md-10">
                <div id="view1">
					<br><br>
                    <h1>Última Factura</h1>
                    <br>
					<?php
					echo '<div id="printable0">
					<h3>La Peluquería de Javier Borneo - C.U.I.T. Nº 20-22506157-3</h3>
					<h4>Factura Nº: ' . $id . ' a: ' . $client . '</h4>
						<div class="row">
                            <div style="width: 1px;"></div>
                            <div class="column left" style="background-color:#d0d0d0;">
                            Servicio
                            </div>
                            <div class="column middle" style="background-color:#d8d8d8; text-align:right;">
                            Precio
                            </div>
                            <div class="column right" style="background-color:#dedede; text-align:right;">
                            Cantidad
                            </div>
                            <div class="column moreright" style="background-color:#e0e0e0; text-align:right;">
                            Base Imponible
                            </div>
                            <div class="column moreright" style="background-color:#e8e8e8; text-align:right;">
                            I.V.A.
                            </div>
                            <div class="column moreright" style="background-color:#efefef; text-align:right;">
                            Total
                            </div>
						</div>
						<div class="row">
                            <div style="width: 1px;"></div>
                            <div class="column left" style="background-color:#d0d0d0;">';
                            for ($i = 0; $i < count($service); $i++)
                            {
                                echo $service[0][$i];
                            }
                            echo '</div>
                            <div class="column middle" style="background-color:#d8d8d8; text-align:right;">';
                            for ($i = 0; $i < count($service); $i++)
                            {
                                echo $price[0][$i];
                            }
                            echo '</div>
                            <div class="column right" style="background-color:#dedede; text-align:right;">';
                            echo $qtty;
                            echo '</div>
                            <div class="column moreright" style="background-color:#e0e0e0; text-align:right;">';
                            echo number_format((float)$total * 100 / 121, 2, ',', '.') . " $";
                            echo '</div>
                            <div class="column moreright" style="background-color:#e8e8e8; text-align:right;">';
                            echo number_format((float)$total * 100 / 121 * .21, 2, ',', '.') . " $";
                            echo '</div>
                            <div class="column moreright" style="background-color:#efefef; text-align:right;">';
                            echo number_format((float)$total, 2, ',', '.') . " $";
                            echo '</div>
                        </div>
                        <div class="row">
					    <div class="column total" style="background-color:#000; text-align:right; color:white; margin-left:33.4%">Total I.V.A. Incluido: ' . number_format((float)$total, 2, ",", ".") . ' $</div></div>
					</div>
                    <a id="image0" download="Factura Nº: ' . $id . ' a: ' . $client . '.png"></a>
                    <br><br><br><br><br>
                    <div class="row">
                    <div class="col-md-3">
                        <button onclick="printIt(-1)" style="width:160px; height:80px;" class="btn btn-primary">Imprimir Ticket</button>
                    </div>
                    <div class="col-md-4">
                        <button onclick="pdfDown(0)" class="btn btn-secondary btn-lg">Descarga la Factura en PDF</button>
                    </div>
                    <div class="col-md-5">
                        <button onclick="window.open(\'saveIt.php?id=' . $id . '\', \'_blank\')" style="width:160px; height:80px;" class="btn btn-info">Guardar Factura en Exel</button>
                    <script>capture(0);</script>
                    </div>
                    </div>';
					?>
                    <br><br>
                    <button class="btn btn-danger btn-lg" onclick="window.close()">Cierra Esta Ventana</button>
                    <br><br><br><br>
				</div>
            </div>
        <div class="col-md-1" style="width: 2%;"></div>
    </div>
</section>
<?php
include "includes/footer.html";
?>