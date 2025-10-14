<?php
include "includes/conn.php";
$title = "Agregando Servicio";
include "includes/header.php";
include "includes/modal-dismiss.html";
$service = $_POST['service'];
$price = $_POST['price'];
$path = htmlspecialchars($_FILES["img"]["name"]);
$tmp = htmlspecialchars($_FILES["img"]["tmp_name"]);
$img = "img/" . basename($path);
move_uploaded_file($tmp, $img);
$stmt = $conn->prepare('INSERT INTO service VALUES(:id, :service, :price, :img)');
$stmt->execute(array(':id' => null, ':service' => $service, ':price' => $price, ':img' => $img));
echo "<script>toast('0', 'Servicio : " . $service. "', 'Se ha agregado correctamente.');</script>";
?>
</body>

</html>