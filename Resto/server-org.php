<?php
if (json_decode(file_get_contents('php://input'), true))
{
	$_POST = json_decode(file_get_contents('php://input'), true);
}
if (isset($_REQUEST['id']))
{
	include "includes/conn.php";

	$id = $_REQUEST["id"];
    $id = (int)$id;
	$product = array();
    $stmt = $conn->prepare("SELECT * FROM food WHERE kind=$id");
	$stmt->execute();
	while($row = $stmt->fetch(PDO::FETCH_OBJ))
	{
		$temp = array();
        $temp['id'] = $row->id;
        $temp['food'] = $row->name;
        $temp['price'] = $row->price;
		array_push($product, $temp);
	}
	echo json_encode($product);
	exit();
}
$title = "Enviando Datos a Android";
include "includes/header.php";
?>
<section class="container-fluid pt-3">
<div id="pc"></div>
    <div id="mobile"></div>
    <div class="row">
        <div class="col-md-1"></div>
            <div class="col-md-10">
                <div id="view1">
                </div>
            </div>
        <div class="col-md-1"></div>
    </div>
</section>
<?php
include "includes/footer.html";
?>