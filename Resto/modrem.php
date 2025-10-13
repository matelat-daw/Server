<?php
include "includes/conn.php";
include "includes/categories.php";
include "includes/products.php";

$title = "Modificar/Eliminar Productos";

// Inicializar managers
$categoryManager = getCategoryManager($conn);
$productManager = new ProductManager($conn);

$message = '';
$messageType = '';
$selectedCategory = $_GET['category'] ?? 'all';

// Procesar formularios
if (isset($_POST["action"])) {
    try {
        if ($_POST["action"] === "delete") {
            $id = intval($_POST['id']);
            $productName = $_POST['product_name'];
            
            $stmt = $conn->prepare("DELETE FROM food WHERE id = :id");
            $stmt->execute([':id' => $id]);
            
            $message = "El producto '{$productName}' ha sido eliminado correctamente.";
            $messageType = "success";
            
        } elseif ($_POST["action"] === "update") {
            $id = intval($_POST['id']);
            $product = trim($_POST['product']);
            $price = floatval($_POST['price']);
            $kind = intval($_POST['kind']);
            
            // Validaciones
            if (empty($product) || $price < 0) {
                throw new Exception("Datos inválidos. Verifica el nombre y precio.");
            }
            
            $stmt = $conn->prepare("UPDATE food SET name = :name, price = :price, kind = :kind WHERE id = :id");
            $stmt->execute([
                ':name' => $product,
                ':price' => $price,
                ':kind' => $kind,
                ':id' => $id
            ]);
            
            $message = "El producto '{$product}' ha sido modificado correctamente.";
            $messageType = "success";
        }
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
        $messageType = "error";
    }
}

// Obtener productos según la categoría seleccionada
if ($selectedCategory === 'all') {
    $products = $productManager->getAllProducts();
} else {
    $products = $productManager->getProductsByCategory(intval($selectedCategory));
}

$categories = $categoryManager->getAllCategories();

include "includes/header.php";
?>

<main class="d-flex flex-column min-vh-100">
    <div class="container-fluid p-3 p-md-4 p-lg-5">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-11 col-xl-10">
                
                <!-- Encabezado -->
                <div class="text-center mb-4">
                    <h1 class="display-5 mb-3">
                        <i class="bi bi-pencil-square me-3 text-primary"></i>
                        Modificar/Eliminar Productos
                    </h1>
                    <p class="lead text-muted">Gestiona los productos del restaurante por categorías</p>
                </div>

                <!-- Mensajes de respuesta -->
                <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $messageType === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show mb-4" role="alert">
                    <i class="bi bi-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-triangle'; ?> me-2"></i>
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <!-- Filtros por categoría -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-funnel me-2"></i>Filtrar por Categoría
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-auto">
                                <a href="?category=all" class="btn <?php echo $selectedCategory === 'all' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                                    <i class="bi bi-grid-3x3-gap me-2"></i>Todas las Categorías
                                </a>
                            </div>
                            <?php foreach($categories as $category): ?>
                            <div class="col-auto">
                                <a href="?category=<?php echo $category['id']; ?>" 
                                   class="btn <?php echo $selectedCategory == $category['id'] ? 'btn-' . $category['color'] : 'btn-outline-' . $category['color']; ?>">
                                    <i class="bi bi-<?php echo $category['icon']; ?> me-2"></i>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </a>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Lista de productos -->
                <?php if (empty($products)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-inbox display-1 text-muted mb-3"></i>
                    <h3 class="text-muted">No hay productos en esta categoría</h3>
                    <p class="text-muted">Selecciona otra categoría o <a href="add.php">agrega un nuevo producto</a>.</p>
                </div>
                <?php else: ?>
                
                <div class="row g-4">
                    <?php foreach($products as $product): 
                        // Verificación defensiva de campos
                        $productKind = $product['kind'] ?? 0;
                        $category = $categoryManager->getCategory($productKind);
                        $categoryColor = $category['color'] ?? 'secondary';
                        $categoryIcon = $category['icon'] ?? 'tag';
                        $categoryName = $category['name'] ?? 'Sin categoría';
                        
                        // Debug: verificar estructura del producto
                        if (!isset($product['kind'])) {
                            error_log("Producto sin campo 'kind': " . print_r($product, true));
                        }
                    ?>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card shadow-sm h-100 border-0">
                            <div class="card-header bg-<?php echo $categoryColor; ?> text-white">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-<?php echo $categoryIcon; ?> me-2"></i>
                                    <small class="opacity-75"><?php echo htmlspecialchars($categoryName); ?></small>
                                    <span class="badge bg-light text-dark ms-auto">#<?php echo $product['id']; ?></span>
                                </div>
                            </div>
                            
                            <div class="card-body">
                                <!-- Formulario de modificación -->
                                <form method="post" class="mb-3">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">
                                            <i class="bi bi-tag me-1"></i>Nombre del Producto
                                        </label>
                                        <input type="text" name="product" class="form-control" 
                                               value="<?php echo htmlspecialchars($product['name']); ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">
                                            <i class="bi bi-currency-dollar me-1"></i>Precio
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" step="0.01" name="price" class="form-control" 
                                                   value="<?php echo $product['price']; ?>" min="0" required>
                                            <span class="input-group-text">ARS</span>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">
                                            <i class="bi bi-collection me-1"></i>Categoría
                                        </label>
                                        <select name="kind" class="form-select">
                                            <?php foreach($categories as $cat): ?>
                                            <option value="<?php echo $cat['id']; ?>" 
                                                    <?php echo $cat['id'] == $productKind ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($cat['name']); ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-success w-100 mb-2">
                                        <i class="bi bi-check-circle me-2"></i>Modificar Producto
                                    </button>
                                </form>
                                
                                <!-- Formulario de eliminación -->
                                <form method="post" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este producto?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                    <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product['name']); ?>">
                                    
                                    <button type="submit" class="btn btn-outline-danger w-100">
                                        <i class="bi bi-trash me-2"></i>Eliminar Producto
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Acciones adicionales -->
                <div class="text-center mt-5">
                    <div class="card border-0 bg-light">
                        <div class="card-body p-4">
                            <h5 class="mb-3">Acciones Rápidas</h5>
                            <div class="d-flex flex-wrap justify-content-center gap-3">
                                <a href="add.php" class="btn btn-primary btn-lg">
                                    <i class="bi bi-plus-circle me-2"></i>
                                    Agregar Producto
                                </a>
                                <button onclick="window.close()" class="btn btn-outline-secondary btn-lg">
                                    <i class="bi bi-x-circle me-2"></i>
                                    Cerrar Ventana
                                </button>
                                <a href="index.html" class="btn btn-outline-primary btn-lg">
                                    <i class="bi bi-house me-2"></i>
                                    Ir al Inicio
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

.btn {
    transition: all 0.2s ease;
}

.form-control:focus, .form-select:focus {
    border-color: var(--bs-primary);
    box-shadow: 0 0 0 0.2rem rgba(var(--bs-primary-rgb), 0.25);
}

@media (max-width: 768px) {
    .card-body {
        padding: 1rem;
    }
}
</style>

<script>
// Confirmación mejorada para eliminación
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('form[onsubmit*="confirm"]');
    deleteButtons.forEach(form => {
        form.addEventListener('submit', function(e) {
            const productName = this.querySelector('input[name="product_name"]').value;
            if (!confirm(`¿Estás seguro de que quieres eliminar "${productName}"?\n\nEsta acción no se puede deshacer.`)) {
                e.preventDefault();
            }
        });
    });
    
    // Auto-focus en el primer campo del primer formulario visible
    const firstInput = document.querySelector('.card input[name="product"]');
    if (firstInput) {
        firstInput.focus();
    }
});

<?php if (!empty($message)): ?>
// Toast de confirmación
document.addEventListener('DOMContentLoaded', function() {
    <?php if ($messageType === 'success'): ?>
    toast(0, 'Operación Exitosa', '<?php echo addslashes($message); ?>');
    <?php else: ?>
    toast(2, 'Error', '<?php echo addslashes($message); ?>');
    <?php endif; ?>
});
<?php endif; ?>
</script>

<?php include "includes/footer.html"; ?>