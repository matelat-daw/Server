<?php
include "includes/conn.php";
$title = "Turnos - La Peluquería de Javier Borneo";
include "includes/header.php";
include "includes/modal-profile.html";
include "includes/nav_profile.php";
include "includes/nav-mob-profile.php";

// Verificar que se hayan recibido todos los datos necesarios
if (!isset($_POST['id']) || !isset($_POST['date']) || !isset($_POST['time'])) {
    echo "<script>toast(1, 'Error', 'Faltan datos para procesar el turno. Por favor, selecciona una fecha y hora.');</script>";
    echo "<script>setTimeout(function(){ window.location.href='request.php'; }, 2000);</script>";
    include "includes/footer.html";
    exit;
}

$id = htmlspecialchars($_POST['id']);
$date = htmlspecialchars($_POST['date']);
$time = htmlspecialchars($_POST['time']);

// Usar prepared statements para evitar SQL injection
$stmt = $conn->prepare("SELECT name FROM client WHERE id = :id");
$stmt->execute([':id' => $id]);

if ($stmt->rowCount() > 0)
{
	$row = $stmt->fetch(PDO::FETCH_OBJ);
	$name = $row->name;
	
	// Actualizar con prepared statement
	$stmt = $conn->prepare("UPDATE client SET date = :date, time = :time WHERE id = :id");
	$stmt->execute([
		':date' => $date,
		':time' => $time,
		':id' => $id
	]);
	
	echo "<script>toast(0, 'Turno Confirmado', 'Turno del Cliente: " . htmlspecialchars($name) . " registrado exitosamente.');</script>";
	echo "<br><br>";
	echo "<div style='text-align: center; padding: 2rem;'>";
	echo "<h2>✅ Tu Turno ha sido Confirmado</h2>";
	echo "<div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 2rem; border-radius: 15px; margin: 2rem auto; max-width: 500px;'>";
	echo "<p style='font-size: 1.2rem; margin-bottom: 1rem;'><strong>Cliente:</strong> " . htmlspecialchars($name) . "</p>";
	echo "<p style='font-size: 1.5rem; margin-bottom: 0.5rem;'><strong>Fecha:</strong> " . date('d/m/Y', strtotime($date)) . "</p>";
	echo "<p style='font-size: 1.5rem;'><strong>Hora:</strong> " . $time . " Hs.</p>";
	echo "</div>";
	echo "<a href='profile.php' class='btn btn-primary btn-lg' style='margin: 1rem;'>Volver a Mi Perfil</a>";
	echo "<a href='request.php' class='btn btn-secondary btn-lg' style='margin: 1rem;'>Solicitar Otro Turno</a>";
	echo "</div>";
}
else
{
	echo "<script>toast(1, 'Error', 'No se encontró el cliente. Por favor, intenta de nuevo.');</script>";
	echo "<script>setTimeout(function(){ window.location.href='profile.php'; }, 2000);</script>";
}

$conn = null;
?>
<script>screenSize();</script>
<script>screen();</script>
<!-- Script para detectar si la pantalla modifica su tamaño horizontal -->
<script>
    window.addEventListener('resize', screen);
    screen();
</script>
</body>
</html>