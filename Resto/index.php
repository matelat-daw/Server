<?php
if (json_decode(file_get_contents('php://input'), true))
{
	$_POST = json_decode(file_get_contents('php://input'), true);
}
if (isset($_POST["id"]))
{
    if (isset($_POST["waiter"]))
    {
        $waiter = $_POST["waiter"];
    }
    else
    {
        $waiter = "";
    }
	$id = $_POST["id"];
	$product = $_POST["product"];
	$invoice = $_POST["invoice"];
	$name = $id . ".txt";
	if (!file_exists($name))
	{
		$file = fopen($name, "w") or die("Unable to open file!");
		fwrite($file, $id);
        fwrite($file, ",");
        fwrite($file, $waiter);
		fwrite($file, ";");
		fwrite($file, $product);
		fwrite($file, ":");
		fwrite($file, $invoice);
	}
	else
	{
		$file = fopen($name, "a") or die("Unable to open file!");
		fwrite($file, ":");
		fwrite($file, $product);
		fwrite($file, ":");
		fwrite($file, $invoice);
	}
	fclose($file);
	$response["error"] = false;
	echo json_encode($response);
	exit();
}
$n = 0;
$files = glob('*.txt');
while ($n < count($files))
{
	$name = $files[$n];
	$file = fopen($name, "r") or die("Unable to open file!");
	$invoice = fread($file, filesize($name));
	$array[$n] = explode(";", $invoice);
    $table = explode(",", $array[$n][0]);
	fclose($file);
	echo '<form name="data' . $n . '" method="post" action="mesa.php?table=' . $table[0] . '" target="' . $table[0] . '">
    <input type="hidden" name="waiter" value="' . $waiter . '">
	<input type="hidden" name="invoice" value="' . $array[$n][1] . '">
	</form>
	<script type="text/javascript">document.forms["data' . $n . '"].submit();</script>';
	$n++;
}
$title = "Esperando Datos";
include "includes/reload.php";
?>
<section class="container-fluid pt-3">
    <div class="row">
        <div class="col-md-1"></div>
            <div class="col-md-10">
                <div id="view1">
                    <br><br>
					<?php
					if ($n == 0)
					{
						echo '<h1 class="color">Esperando Datos</h1>';
					}
					else
					{
						echo '<h1 class="badColor">Facturando...</h1>';
					}
					?>
					<br>
					<button onclick="window.open('index.html')" style="width:250px; height:128px">Abrir Sistema de Facturación</button>
				</div>
            </div>
        <div class="col-md-1"></div>
    </div>
</section>
<?php
include "includes/footer.html";
?>