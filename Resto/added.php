<?php
include "includes/conn.php";
include "includes/categories.php";
include "includes/products.php";

// Verificar que los datos lleguen por POST
if (!isset($_POST["product"])) {
    header("Location: add.php");
    exit();
}

$name = trim($_POST['product']);
$price = floatval($_POST['price']);
$id = intval($_POST['id']);

// Inicializar managers
$categoryManager = getCategoryManager($conn);
$productManager = new ProductManager($conn);

// Validaciones mejoradas usando la tabla categories
if (empty($name) || $price < 0 || !$categoryManager->categoryExists($id)) {
    header("Location: adding.php?id=" . $id . "&error=1");
    exit();
}

// Verificar si el producto ya existe
if ($productManager->productExistsByName($name, $id)) {
    header("Location: adding.php?id=" . $id . "&error=2");
    exit();
}

// Obtener información de la categoría desde la base de datos
$currentType = $categoryManager->getCategory($id);
if (!$currentType) {
    header("Location: add.php");
    exit();
}

$title = htmlspecialchars($currentType['name']) . " Agregado";

include "includes/header.php";

try {
    // Usar ProductManager para agregar el producto
    $productId = $productManager->addProduct($name, $price, $id);
    $success = true;
    
    // Obtener estadísticas adicionales para mostrar
    $categoryStats = $productManager->getCategoryStatistics($id);
    
} catch (Exception $e) {
    $success = false;
    $error_message = "Error al agregar el producto: " . $e->getMessage();
}
?>

<main class="d-flex flex-column min-vh-100">
    <div class="container-fluid p-3 p-md-4 p-lg-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                
                <?php if ($success): ?>
                <!-- Mensaje de Éxito -->
                <div class="card shadow-lg border-0 mb-4">
                    <div class="card-header bg-success text-white text-center py-4">
                        <div class="mb-3">
                            <i class="bi bi-check-circle display-1 mb-3"></i>
                            <h1 class="h3 mb-0">¡Producto Agregado Exitosamente!</h1>
                        </div>
                    </div>
                    
                    <div class="card-body p-4 p-md-5">
                        <!-- Información del producto agregado -->
                        <div class="text-center mb-4">
                            <div class="bg-light rounded-3 p-4 mb-4">
                                <i class="bi bi-<?php echo $currentType['icon']; ?> text-<?php echo $currentType['color']; ?> display-4 mb-3"></i>
                                <h2 class="h4 text-<?php echo $currentType['color']; ?> mb-2">
                                    <?php echo htmlspecialchars($name); ?>
                                </h2>
                                <p class="h5 text-muted mb-0">
                                    <strong>$<?php echo number_format($price, 2); ?></strong>
                                </p>
                                <small class="text-muted">
                                    ID: #<?php echo $productId; ?> | Categoría: <?php echo htmlspecialchars($currentType['name']); ?>
                                </small>
                            </div>
                        </div>
                        
                        <!-- Estadísticas de la categoría -->
                        <div class="row text-center mb-4">
                            <div class="col-4">
                                <div class="bg-success bg-opacity-10 rounded p-3">
                                    <i class="bi bi-check-circle text-success h4 mb-1"></i>
                                    <p class="small mb-0 text-success fw-bold">Guardado</p>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="bg-<?php echo $currentType['color']; ?> bg-opacity-10 rounded p-3">
                                    <div class="h4 mb-1 text-<?php echo $currentType['color']; ?> fw-bold">
                                        <?php echo $categoryStats['total_products']; ?>
                                    </div>
                                    <p class="small mb-0 text-<?php echo $currentType['color']; ?> fw-bold">
                                        Total en <?php echo $currentType['name']; ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="bg-info bg-opacity-10 rounded p-3">
                                    <div class="h4 mb-1 text-info fw-bold">
                                        $<?php echo number_format($categoryStats['avg_price'], 1); ?>
                                    </div>
                                    <p class="small mb-0 text-info fw-bold">Precio Promedio</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Acciones disponibles -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            <a href="adding.php?id=<?php echo $id; ?>" class="btn btn-outline-<?php echo $currentType['color']; ?> btn-lg">
                                <i class="bi bi-plus-circle me-2"></i>
                                Agregar Otro <?php echo htmlspecialchars($currentType['name']); ?>
                            </a>
                            <a href="add.php" class="btn btn-<?php echo $currentType['color']; ?> btn-lg">
                                <i class="bi bi-arrow-left me-2"></i>
                                Volver al Menú
                            </a>
                        </div>
                    </div>
                </div>
                
                <?php else: ?>
                <!-- Mensaje de Error -->
                <div class="card shadow-lg border-0 mb-4">
                    <div class="card-header bg-danger text-white text-center py-4">
                        <div class="mb-3">
                            <i class="bi bi-exclamation-triangle display-1 mb-3"></i>
                            <h1 class="h3 mb-0">Error al Agregar Producto</h1>
                        </div>
                    </div>
                    
                    <div class="card-body p-4 p-md-5 text-center">
                        <div class="alert alert-danger mb-4">
                            <i class="bi bi-bug me-2"></i>
                            <?php echo htmlspecialchars($error_message ?? 'Ocurrió un error inesperado.'); ?>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            <a href="adding.php?id=<?php echo $id; ?>" class="btn btn-outline-danger btn-lg">
                                <i class="bi bi-arrow-clockwise me-2"></i>
                                Intentar Nuevamente
                            </a>
                            <a href="add.php" class="btn btn-secondary btn-lg">
                                <i class="bi bi-house me-2"></i>
                                Volver al Inicio
                            </a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Información adicional y estadísticas -->
                <div class="card border-0 bg-light">
                    <div class="card-body p-4">
                        <div class="row text-center">
                            <div class="col-12 col-md-3 mb-3 mb-md-0">
                                <i class="bi bi-shield-check text-success h3"></i>
                                <h6 class="mt-2 text-success">Seguro</h6>
                                <small class="text-muted">Datos protegidos</small>
                            </div>
                            <div class="col-12 col-md-3 mb-3 mb-md-0">
                                <i class="bi bi-lightning-charge text-warning h3"></i>
                                <h6 class="mt-2 text-warning">Instantáneo</h6>
                                <small class="text-muted">Disponible ahora</small>
                            </div>
                            <div class="col-12 col-md-3 mb-3 mb-md-0">
                                <i class="bi bi-graph-up text-info h3"></i>
                                <h6 class="mt-2 text-info">Organizado</h6>
                                <small class="text-muted">Base actualizada</small>
                            </div>
                            <div class="col-12 col-md-3">
                                <i class="bi bi-<?php echo $currentType['icon']; ?> text-<?php echo $currentType['color']; ?> h3"></i>
                                <h6 class="mt-2 text-<?php echo $currentType['color']; ?>"><?php echo $currentType['name']; ?></h6>
                                <small class="text-muted">Rango: $<?php echo number_format($categoryStats['min_price'], 1); ?> - $<?php echo number_format($categoryStats['max_price'], 1); ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php if ($success): ?>
<script>
// Toast de confirmación mejorado
document.addEventListener('DOMContentLoaded', function() {
    toast(0, 'Producto Agregado', 'Artículo <?php echo addslashes($currentType['name']); ?> "<?php echo addslashes($name); ?>" ha Sido Agregado Correctamente.');
});
</script>
<?php else: ?>
<script>
// Toast de error
document.addEventListener('DOMContentLoaded', function() {
    toast(2, 'Error al Agregar', 'No se Pudo Agregar el Producto. Por favor, Intenta Nuevamente.');
});
</script>
<?php endif; ?>

<?php include "includes/footer.html"; ?>