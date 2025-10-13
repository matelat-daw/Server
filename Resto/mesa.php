<?php
include "includes/conn.php";
include "includes/categories.php";
include "includes/products.php";

$table = $_REQUEST["table"];

// Inicializar managers
$categoryManager = getCategoryManager($conn);
$productManager = new ProductManager($conn);

if (isset($_POST['invoice']))
{
    $waiter = $_POST["waiter"];
	$invoice = $_POST['invoice'];
	$record = explode (":", $invoice);
	for ($i = 2; $i <= count($record); $i+=2)
	{
		switch($i)
		{
			case 2:
			$mesa10 = $record[0] . ';' . $record[1];
			break;
			case 4:
			$mesa11 = $record[2] . ';' . $record[3];
			break;
			case 6:
			$mesa12 = $record[4] . ';' . $record[5];
			break;
			case 8:
			$mesa13 = $record[6] . ';' . $record[7];
			break;
			case 10:
			$mesa14 = $record[8] . ';' . $record[9];
			break;
			case 12:
			$mesa15 = $record[10] . ';' . $record[11];
			break;
			case 14:
			$mesa16 = $record[12] . ';' . $record[13];
			break;
			case 16:
			$mesa17 = $record[14] . ';' . $record[15];
			break;
		}
	}
}
if (!isset($_POST["waiter"]))
{
    $waiter = "";
}
$title = "Pedido de la Mesa: " . $table;
include "includes/header.php";
?>

<main class="d-flex flex-column min-vh-100">
    <div class="container-fluid p-3 p-md-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h2 text-primary mb-0">
                        <i class="bi bi-clipboard-check me-2"></i>Facturando: <?php echo htmlspecialchars($table); ?>
                    </h1>
                    <button onclick='deleting("<?php echo htmlspecialchars($table); ?>")' class="btn btn-outline-danger">
                        <i class="bi bi-trash3 me-1"></i>Anular Factura
                    </button>
                </div>
            </div>
        </div>
        <!-- Sección de Productos -->
        <div class="row g-4">
            <!-- Card Platos -->
            <div class="col-12 col-lg-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-egg-fried me-2"></i>Platos Principales
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="meal" class="form-label">Selecciona un Plato</label>
                            <select class="form-select form-select-lg" name="plate" id="meal">
                                <option value="">Elige un plato...</option>
                                <?php
                                $products = $productManager->getProductsByCategory(0);
                                foreach($products as $row) {
                                    echo '<option value="' . $row['id'] . ',' . htmlspecialchars($row['name']) . ',' . $row['price'] . '">' 
                                         . htmlspecialchars($row['name']) . ' - $' . number_format($row['price'], 2) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="row align-items-end">
                            <div class="col-8">
                                <label for="qtty" class="form-label">Cantidad</label>
                                <input id="qtty" type="number" name="qtty" value="1" min="1" class="form-control form-control-lg">
                            </div>
                            <div class="col-4">
                                <button onclick="add_plate()" class="btn btn-primary btn-lg w-100">
                                    <i class="bi bi-plus-circle me-1"></i>Agregar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Bebidas -->
            <div class="col-12 col-lg-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-cup-straw me-2"></i>Bebidas
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="bev" class="form-label">Selecciona una Bebida</label>
                            <select class="form-select form-select-lg" name="bever" id="bev">
                                <option value="">Elige una bebida...</option>
                                <?php
                                $products = $productManager->getProductsByCategory(1);
                                foreach($products as $row) {
                                    echo '<option value="' . $row['id'] . ',' . htmlspecialchars($row['name']) . ',' . $row['price'] . '">' 
                                         . htmlspecialchars($row['name']) . ' - $' . number_format($row['price'], 2) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="row align-items-end">
                            <div class="col-8">
                                <label for="qtty2" class="form-label">Cantidad</label>
                                <input id="qtty2" type="number" name="qtty2" value="1" min="1" class="form-control form-control-lg">
                            </div>
                            <div class="col-4">
                                <button onclick="add_bebida()" class="btn btn-info btn-lg w-100">
                                    <i class="bi bi-plus-circle me-1"></i>Agregar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Postres -->
            <div class="col-12 col-lg-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-cake2 me-2"></i>Postres
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="dess" class="form-label">Selecciona un Postre</label>
                            <select class="form-select form-select-lg" name="dessert" id="dess">
                                <option value="">Elige un postre...</option>
                                <?php
                                $products = $productManager->getProductsByCategory(2);
                                foreach($products as $row) {
                                    echo '<option value="' . $row['id'] . ',' . htmlspecialchars($row['name']) . ',' . $row['price'] . '">' 
                                         . htmlspecialchars($row['name']) . ' - $' . number_format($row['price'], 2) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="row align-items-end">
                            <div class="col-8">
                                <label for="qtty3" class="form-label">Cantidad</label>
                                <input id="qtty3" type="number" name="qtty3" value="1" min="1" class="form-control form-control-lg">
                            </div>
                            <div class="col-4">
                                <button onclick="add_postre()" class="btn btn-warning btn-lg w-100">
                                    <i class="bi bi-plus-circle me-1"></i>Agregar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Cafés -->
            <div class="col-12 col-lg-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-dark text-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-cup-hot me-2"></i>Cafés
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="coffe" class="form-label">Selecciona un Café</label>
                            <select class="form-select form-select-lg" name="coffe" id="coffe">
                                <option value="">Elige un café...</option>
                                <?php
                                $products = $productManager->getProductsByCategory(3);
                                foreach($products as $row) {
                                    echo '<option value="' . $row['id'] . ',' . htmlspecialchars($row['name']) . ',' . $row['price'] . '">' 
                                         . htmlspecialchars($row['name']) . ' - $' . number_format($row['price'], 2) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="row align-items-end">
                            <div class="col-8">
                                <label for="qtty4" class="form-label">Cantidad</label>
                                <input id="qtty4" type="number" name="qtty4" value="1" min="1" class="form-control form-control-lg">
                            </div>
                            <div class="col-4">
                                <button onclick="add_coffe()" class="btn btn-dark btn-lg w-100">
                                    <i class="bi bi-plus-circle me-1"></i>Agregar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Vinos -->
            <div class="col-12 col-lg-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-danger text-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-wine me-2"></i>Vinos
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="wine" class="form-label">Selecciona un Vino</label>
                            <select class="form-select form-select-lg" name="wine" id="wine">
                                <option value="">Elige un vino...</option>
                                <?php
                                $products = $productManager->getProductsByCategory(4);
                                foreach($products as $row) {
                                    echo '<option value="' . $row['id'] . ',' . htmlspecialchars($row['name']) . ',' . $row['price'] . '">' 
                                         . htmlspecialchars($row['name']) . ' - $' . number_format($row['price'], 2) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="row align-items-end">
                            <div class="col-8">
                                <label for="qtty5" class="form-label">Cantidad</label>
                                <input id="qtty5" type="number" name="qtty5" value="1" min="1" class="form-control form-control-lg">
                            </div>
                            <div class="col-4">
                                <button onclick="add_wine()" class="btn btn-danger btn-lg w-100">
                                    <i class="bi bi-plus-circle me-1"></i>Agregar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<        <!-- Sección de Facturación -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-receipt me-2"></i>Finalizar Pedido
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action='addInvoice.php' method="post">
                            <div class="row align-items-end">
                                <div class="col-12 col-md-8">
                                    <label for="client" class="form-label">Cliente</label>
                                    <select name="client" id="client" class="form-select form-select-lg">
                                        <option value="">Consumidor Final</option>
                                        <?php
                                        $sql = "SELECT id, name from client";
                                        $stmt = $conn->prepare($sql);
                                        $stmt->execute();
                                        if ($stmt->rowCount() > 0) {
                                            while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                                                echo '<option value="' . $row->id . '">' . htmlspecialchars($row->name) . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-12 col-md-4 mt-3 mt-md-0">
                                    <button type="submit" class="btn btn-success btn-lg w-100">
                                        <i class="bi bi-receipt-cutoff me-2"></i>Generar Factura
                                    </button>
                                </div>
                            </div>
                            
                            <input type="hidden" name="table" value="<?php echo htmlspecialchars($table); ?>">
                            <input type="hidden" name="invoice" id="invoice">
                            <input type="hidden" name="waiter" value="<?php echo htmlspecialchars($waiter); ?>">
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resumen del Pedido -->
        <div class="row mt-4">
            <div class="col-12 col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="card-title mb-0">
                            <i class="bi bi-list-task me-2"></i>Pedido para Cocina
                        </h6>
                    </div>
                    <div class="card-body">
                        <div id="platos" class="text-muted" style="min-height: 50px;">
                            <em>Los elementos que requieren preparación aparecerán aquí...</em>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="card-title mb-0">
                            <i class="bi bi-cart3 me-2"></i>Detalles de Facturación
                        </h6>
                    </div>
                    <div class="card-body">
                        <div id="plate" class="text-muted" style="min-height: 50px;">
                            <em>Los elementos del pedido aparecerán aquí...</em>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php
// Procesar datos del pedido si existen
if (isset($_POST['invoice'])) {
    for ($i = 2; $i <= count($record); $i+=2) {
        switch($i) {
            case 2:
                echo '<script>addData("' . $mesa10 . '")</script>';
                break;
            case 4:
                echo '<script>addData("' . $mesa11 . '")</script>';
                break;
            case 6:
                echo '<script>addData("' . $mesa12 . '")</script>';
                break;
            case 8:
                echo '<script>addData("' . $mesa13 . '")</script>';
                break;
            case 10:
                echo '<script>addData("' . $mesa14 . '")</script>';
                break;
            case 12:
                echo '<script>addData("' . $mesa15 . '")</script>';
                break;
            case 14:
                echo '<script>addData("' . $mesa16 . '")</script>';
                break;
            case 16:
                echo '<script>addData("' . $mesa17 . '")</script>';
                break;
            case 18:
                echo '<script>addData("' . $mesa18 . '")</script>';
                break;    
        }
    }
}

include "includes/footer.html";
?>