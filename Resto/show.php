<?php
include "includes/conn.php";
$title = "Ver Todas las Facturas";
include "includes/header.php";
?>
<section class="container-fluid pt-3">
    <div class="row">
        <div class="col-md-1"></div>
            <div class="col-md-10">
                <div id="view1">
					<?php
					$stmt = $conn->prepare('SELECT * FROM invoice ORDER BY inv_date DESC, inv_time DESC');
					$stmt->execute();
					while($row = $stmt->fetch(PDO::FETCH_OBJ))
					{
						if ($row->tables != "0")
						{
							echo '<div><h1>Factura de : ' .  $row->tables . '</h1></div>';
							echo '<p>Fecha : ' . $row->inv_date . ' - ' . $row->inv_time . '</p>';
							echo '<div class="row">';
							echo '<div class="column left" style="background-color:#aaa;">';
							echo "<h4>Artículo</h4>";
							echo '</div>';
							echo '<div class="column middle" style="background-color:#bbb; text-align:right;">';
							echo "<h4>Precio</h4>";
							echo '</div>';
							echo '<div class="column right" style="background-color:#ccc; text-align:right;">';
							echo "<h4>Cantidad</h4>";
							echo '</div>';
							echo '<div class="column moreright" style="background-color:#cac; text-align:right;">';
							echo "<h4>Parcial</h4>";
							echo '</div>';
							echo '</div>';
							$record = explode (",", $row->invoice);
							$total = 0;
							for ($i = 0; $i < count($record) - 2; $i+=3)
							{
								echo '<div class="row">';
								echo '<div class="column left" style="background-color:#aaa;">';
								echo '<h3>' . $record[$i] . '</h3>';
								echo '</div>';
								echo '<div class="column middle" style="background-color:#bbb; text-align:right;">';
								echo '<h3>' . $record[$i + 1] . '</h3>';
								echo '</div>';
								echo '<div class="column right" style="background-color:#ccc; text-align:right;">';
								echo '<h3>' . $record[$i + 2] . '</h3>';
								echo '</div>';
								echo '<div class="column moreright" style="background-color:#cac; text-align:right;">';
								$total += $record[$i + 2] * $record[$i + 1];
								echo '<h3>' . $record[$i + 2] * $record[$i + 1] . '</h3>';
								echo '</div>';
								echo '</div>';
							}
							echo '<div class="column right" style="background-color:#000; text-align:right; color:white; margin-left:33.8%">Total : ' . $total . '</div>';
							echo '</div>';
							echo '<br>';
							echo '<br>';
						}
					}
					?>
				</div>
            </div>
        <div class="col-md-1"></div>
    </div>
</section>
<?php
include "includes/footer.html";
?>