<?php
include "includes/conn.php";

// Función para obtener el ID de la mesa
function getTableId($conn, $table)
{
    try {
        $sql = "SELECT id FROM tables WHERE name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$table]);
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        
        if ($row && isset($row->id)) {
            return $row->id;
        } else {
            // Si no se encuentra la mesa, intentar buscar por coincidencia parcial
            $sql = "SELECT id FROM tables WHERE name LIKE ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute(['%' . $table . '%']);
            $row = $stmt->fetch(PDO::FETCH_OBJ);
            
            if ($row && isset($row->id)) {
                return $row->id;
            } else {
                // Si aún no se encuentra, registrar error y retornar null
                error_log("No se encontró mesa con nombre: " . $table);
                return null;
            }
        }
    } catch (PDOException $e) {
        error_log("Error en getTableId: " . $e->getMessage());
        return null;
    }
}

// Verificar que se recibió el POST
if (!isset($_POST["table"])) {
    header("Location: index.html");
    exit;
}

// Procesar datos del POST
$table = $_POST['table'];

// Debug: Mostrar qué mesa se está buscando
error_log("DEBUG addInvoice.php - Buscando mesa: " . $table);

if (file_exists($table . ".txt"))
{
    unlink($table . ".txt");
}

$table_id = getTableId($conn, $table);

// Debug: Mostrar qué ID se obtuvo
error_log("DEBUG addInvoice.php - ID de mesa obtenido: " . ($table_id ?? 'NULL'));

$client = $_POST["client"];
if ($client == "")
{
    $client = null;
}
$invoice = $_POST['invoice'];
$waiter = $_POST["waiter"];
if ($waiter == "")
{
    $waiter = null;
}
$date = date("Y-m-d");
$time = date("H:i:s");
$article = "";
$quantity1 = "";
$prices = "";
$part = "";
$total = 0;
$j = 0;

$record = explode (",", $invoice);
for ($i = 0; $i < count($record) - 1; $i+=4)
{
    $id[$j] = $record[$i];
    $price[$j] = $record[$i + 2];
    $quantity[$j] = $record[$i + 3];
    $total += $price[$j] * $quantity[$j];
    $j++;
}

// Validar que tengamos un table_id válido antes de insertar
if ($table_id === null) {
    include "includes/modal-invoice.html";
    $title = "Error al Guardar Factura";
    include "includes/header.php";
    ?>
    <section class="container-fluid pt-3">
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <div class="alert alert-danger mt-3">
                    <h4>Error al Procesar la Factura</h4>
                    <p>No se pudo identificar la mesa '<strong><?php echo htmlspecialchars($table); ?></strong>'.</p>
                    <p>Por favor, verifica que la mesa exista en el sistema.</p>
                    <a href='javascript:history.back()' class='btn btn-primary'>Volver</a>
                </div>
            </div>
            <div class="col-md-2"></div>
        </div>
    </section>
    <?php
    include "includes/footer.html";
    exit;
}

// Proceder con el INSERT
try {
    $stmt = $conn->prepare('INSERT INTO invoice VALUES(:id, :client_id, :waiter_id, :table_id, :total, :inv_date, :inv_time);');
    $stmt->execute(array(':id' => null, ':client_id' => $client, ':waiter_id' => $waiter, ':table_id' => $table_id, ':total' => $total, ':inv_date' => $date, ':inv_time' => $time));
    $sql = "SELECT id FROM invoice ORDER BY id DESC LIMIT 1;";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_OBJ);
    $invoice_id = $row->id;

    $sql = "INSERT INTO sold VALUES(:id, :invoice_id, :product_id, :quantity);";
    $stmt = $conn->prepare($sql);
    for ($i = 0; $i < count($id); $i++)
    {
        $stmt->execute(array(':id' => null, ':invoice_id' => $invoice_id, ':product_id' => $id[$i], ':quantity' => $quantity[$i]));
    }
    
    // Factura guardada exitosamente
    include "includes/modal-invoice.html";
    $title = "Guardando Factura";
    include "includes/header.php";
    ?>
    <section class="container-fluid pt-3">
        <div id="pc"></div>
        <div id="mobile"></div>
        <div class="row">
            <div class="col-md-1"></div>
            <div class="col-md-10">
                <div id="view1">
                    <script>toast('0', 'Facturado', 'Factura de monto: <?php echo $total; ?> Almacenada en la Base de Datos Correctamente.');</script>
                </div>
            </div>
            <div class="col-md-1"></div>
        </div>
    </section>
    <?php
} catch (PDOException $e) {
    include "includes/modal-invoice.html";
    $title = "Error al Guardar Factura";
    include "includes/header.php";
    ?>
    <section class="container-fluid pt-3">
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <div class="alert alert-danger mt-3">
                    <h4>Error al Guardar la Factura</h4>
                    <p><?php echo htmlspecialchars($e->getMessage()); ?></p>
                    <a href='javascript:history.back()' class='btn btn-primary'>Volver</a>
                </div>
            </div>
            <div class="col-md-2"></div>
        </div>
    </section>
    <?php
    error_log("Error en addInvoice.php: " . $e->getMessage());
}

include "includes/footer.html";
?>
