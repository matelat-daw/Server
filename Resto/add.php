<?php
include "includes/conn.php";
include "includes/categories.php";

$title = "Gestión de Productos del Restaurante";
$debug = isset($_GET['debug']) ? true : false;
$categories = [];
$error_message = '';

try {
    // Inicializar el manager de categorías
    $categoryManager = getCategoryManager($conn);
    $categories = $categoryManager->getAllCategories();
    
    if ($debug) {
        echo "<!-- DEBUG: Categorías cargadas: " . print_r($categories, true) . " -->";
    }
    
    // Verificar que tenemos categorías
    if (empty($categories)) {
        throw new Exception("No se pudieron cargar las categorías desde la base de datos.");
    }
    
} catch (Exception $e) {
    $error_message = $e->getMessage();
    
    // Fallback en caso de error
    $categories = [
        ['id' => 0, 'name' => 'Plato Principal', 'icon' => 'egg-fried', 'color' => 'primary'],
        ['id' => 1, 'name' => 'Bebida', 'icon' => 'cup-straw', 'color' => 'info'],
        ['id' => 2, 'name' => 'Postre', 'icon' => 'cake2', 'color' => 'warning'],
        ['id' => 3, 'name' => 'Café', 'icon' => 'cup-hot', 'color' => 'dark'],
        ['id' => 4, 'name' => 'Vino', 'icon' => 'wine', 'color' => 'danger']
    ];
    
    if ($debug) {
        echo "<!-- DEBUG ERROR: " . $error_message . " -->";
        echo "<!-- DEBUG: Usando fallback categories -->";
    }
}

include "includes/header.php";
?>

<main class="d-flex flex-column min-vh-100">
    <div class="container-fluid p-3 p-md-4 p-lg-5">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10">
                <!-- Encabezado -->
                <div class="text-center mb-5">
                    <h1 class="display-4 mb-3">
                        <i class="bi bi-plus-circle me-3 text-primary"></i>
                        Gestión de Productos
                    </h1>
                    <p class="lead text-muted">Selecciona una categoría para agregar un nuevo producto</p>
                </div>

                <!-- Mensaje de error si hay problemas -->
                <?php if (!empty($error_message)): ?>
                <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Advertencia:</strong> <?php echo htmlspecialchars($error_message); ?>
                    <br><small>Se están mostrando las categorías por defecto.</small>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <!-- Grid de categorías -->
                <div class="row g-4 mb-5">
                    <?php foreach($categories as $category): ?>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card shadow-lg border-0 h-100 category-card" data-category="<?php echo htmlspecialchars($category['id']); ?>">
                            <div class="card-body text-center p-4 p-lg-5">
                                <div class="bg-<?php echo htmlspecialchars($category['color']); ?> bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                                    <i class="bi bi-<?php echo htmlspecialchars($category['icon']); ?> text-<?php echo htmlspecialchars($category['color']); ?> h1 mb-0"></i>
                                </div>
                                <h3 class="h4 mb-3 text-<?php echo htmlspecialchars($category['color']); ?>">
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </h3>
                                <a href="adding.php?id=<?php echo htmlspecialchars($category['id']); ?>" class="btn btn-<?php echo htmlspecialchars($category['color']); ?> btn-lg w-100">
                                    <i class="bi bi-plus-circle me-2"></i>
                                    Agregar <?php echo htmlspecialchars($category['name']); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Acciones adicionales -->
                <div class="text-center">
                    <div class="card border-0 bg-light">
                        <div class="card-body p-4">
                            <h5 class="mb-3">Acciones Rápidas</h5>
                            <div class="d-flex flex-wrap justify-content-center gap-3">
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
.category-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    cursor: pointer;
}

.category-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
}
</style>

<?php include "includes/footer.html"; ?>