<?php
include "includes/conn.php";
include "includes/categories.php";

$id = $_GET['id'] ?? 0;
$error = $_GET['error'] ?? 0;

// Inicializar el manager de categorías
$categoryManager = getCategoryManager($conn);

// Verificar que la categoría existe
if (!$categoryManager->categoryExists($id)) {
    header("Location: add.php");
    exit();
}

// Obtener información de la categoría desde la base de datos
$currentType = $categoryManager->getCategory($id);
$title = "Agregando " . $currentType['name'];

// Mensajes de error
$errorMessages = [
    1 => "Los datos ingresados no son válidos. Por favor, verifica la información.",
    2 => "Ya existe un producto con ese nombre en esta categoría. Por favor, usa un nombre diferente.",
    3 => "Error de conexión con la base de datos. Intenta nuevamente."
];

include "includes/header.php";
?>

<main class="d-flex flex-column min-vh-100">
    <div class="container-fluid p-3 p-md-4 p-lg-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <!-- Encabezado con breadcrumb -->
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="index.html" class="text-decoration-none">
                                <i class="bi bi-house-door me-1"></i>Inicio
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="add.php" class="text-decoration-none">Gestión de Productos</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Agregar <?php echo htmlspecialchars($currentType['name']); ?>
                        </li>
                    </ol>
                </nav>

                <?php if ($error > 0 && isset($errorMessages[$error])): ?>
                <!-- Alerta de Error -->
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill me-3 h4 mb-0"></i>
                        <div>
                            <strong>Error:</strong> <?php echo htmlspecialchars($errorMessages[$error]); ?>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>

                <!-- Card principal del formulario -->
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-<?php echo $currentType['color']; ?> text-white py-4">
                        <div class="text-center">
                            <i class="bi bi-<?php echo $currentType['icon']; ?> display-4 mb-3"></i>
                            <h1 class="h3 mb-0">Agregar Nuevo <?php echo htmlspecialchars($currentType['name']); ?></h1>
                            <p class="mb-0 opacity-75">Complete la información del producto</p>
                        </div>
                    </div>
                    
                    <div class="card-body p-4 p-md-5">
                        <form action="added.php" method="post" class="needs-validation" novalidate>
                            <div class="row g-4">
                                <!-- Campo Nombre -->
                                <div class="col-12">
                                    <label for="product" class="form-label h5">
                                        <i class="bi bi-tag me-2 text-<?php echo $currentType['color']; ?>"></i>
                                        Nombre del <?php echo htmlspecialchars($currentType['name']); ?>
                                    </label>
                                    <input 
                                        type="text" 
                                        name="product" 
                                        id="product"
                                        class="form-control form-control-lg" 
                                        placeholder="Ej: <?php echo $categoryManager->getPlaceholder($id); ?>"
                                        required
                                        autocomplete="off"
                                    >
                                    <div class="invalid-feedback">
                                        Por favor, ingrese el nombre del producto.
                                    </div>
                                    <div class="form-text">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Ingrese un nombre descriptivo para el producto
                                    </div>
                                </div>

                                <!-- Campo Precio -->
                                <div class="col-12">
                                    <label for="price" class="form-label h5">
                                        <i class="bi bi-currency-dollar me-2 text-<?php echo $currentType['color']; ?>"></i>
                                        Precio
                                    </label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text">$</span>
                                        <input 
                                            type="number" 
                                            step="0.01" 
                                            name="price" 
                                            id="price"
                                            class="form-control" 
                                            placeholder="0.00"
                                            min="0"
                                            required
                                            autocomplete="off"
                                        >
                                        <span class="input-group-text">ARS</span>
                                    </div>
                                    <div class="invalid-feedback">
                                        Por favor, ingrese un precio válido.
                                    </div>
                                    <div class="form-text">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Precio en pesos argentinos (ARS)
                                    </div>
                                </div>

                                <!-- Vista previa del producto -->
                                <div class="col-12">
                                    <div class="card bg-light border-2 border-dashed">
                                        <div class="card-body text-center">
                                            <h6 class="text-muted mb-2">Vista Previa</h6>
                                            <div id="preview" class="h5 text-<?php echo $currentType['color']; ?>">
                                                <i class="bi bi-<?php echo $currentType['icon']; ?> me-2"></i>
                                                <span id="preview-name">Nombre del producto</span> - 
                                                $<span id="preview-price">0.00</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Botones de acción -->
                            <div class="row mt-5">
                                <div class="col-12">
                                    <div class="d-flex gap-3 justify-content-between flex-column flex-sm-row">
                                        <a href="add.php" class="btn btn-outline-secondary btn-lg">
                                            <i class="bi bi-arrow-left me-2"></i>Cancelar
                                        </a>
                                        <button type="submit" class="btn btn-<?php echo $currentType['color']; ?> btn-lg">
                                            <i class="bi bi-check-circle me-2"></i>
                                            Agregar <?php echo htmlspecialchars($currentType['name']); ?>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
// Vista previa en tiempo real
document.addEventListener('DOMContentLoaded', function() {
    const productInput = document.getElementById('product');
    const priceInput = document.getElementById('price');
    const previewName = document.getElementById('preview-name');
    const previewPrice = document.getElementById('preview-price');

    function updatePreview() {
        const name = productInput.value.trim() || 'Nombre del producto';
        const price = parseFloat(priceInput.value) || 0;
        
        previewName.textContent = name;
        previewPrice.textContent = price.toFixed(2);
    }

    productInput.addEventListener('input', updatePreview);
    priceInput.addEventListener('input', updatePreview);

    // Validación de Bootstrap
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    // Auto-focus en el primer campo
    productInput.focus();
});
</script>
<?php
include "includes/footer.html";
?>