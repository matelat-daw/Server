<?php
include "includes/conn.php";
$timestart = $_POST['start'];
setlocale(LC_TIME, 'es_ES.UTF-8');
date_default_timezone_set("America/Argentina/Buenos_Aires");
$day = date("Y-m-d");
$day_after = date(("Y-m-d"), strtotime($timestart . '+1 day'));
$final = 0;
$title = "Mostrando Arqueo de Caja por Fecha";
include "includes/header.php";

$stmt = $conn->prepare('SELECT total, inv_date, inv_time FROM invoice WHERE inv_date >="' . $timestart . '"');
$stmt->execute();
while($row = $stmt->fetch(PDO::FETCH_OBJ))
{
	if ($timestart != "")
	{
		if ($row->inv_time >= "19:00:00" && $row->inv_date == $timestart)
		{
			$final += $row->total;
		}
		if ($row->inv_date == $day_after && $row->inv_time < "19:00:00")
		{
			$final += $row->total;
		}
	}
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
					<?php
					echo "<p style='font-size:xx-large'>La Facturación del día : " . $timestart . " fue : " . $final . " Pesos.</p>";
					?>
				</div>
            </div>
        <div class="col-md-1"></div>
    </div>
</section>
<?php
include "includes/footer.html";
?>