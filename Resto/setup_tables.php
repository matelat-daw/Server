<?php
/**
 * Setup de Mesas - Configuración inicial de mesas del restaurante
 * Permite insertar, ver y gestionar todas las mesas del sistema
 */
include "includes/conn.php";

$message = '';
$messageType = '';
$action = $_GET['action'] ?? 'view';

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['populate_all'])) {
        $action = 'populate';
    } elseif (isset($_POST['add_table'])) {
        $action = 'add';
    } elseif (isset($_POST['delete_table'])) {
        $action = 'delete';
    }
}

// Definir todas las mesas del sistema
$allTables = [
    'Zona de Entrada' => ['Entrada 1', 'Entrada 2', 'Entrada 3', 'Entrada 4'],
    'Zona de Barra' => ['Barra 1', 'Barra 2', 'Barra 3'],
    'Zona de Patio' => ['Patio 1', 'Patio 2', 'Patio 3', 'Patio 4', 'Patio 5'],
    'Zona de Vereda' => ['Vereda 1', 'Vereda 2', 'Vereda 3'],
    'Mesas Principales' => ['Mesa 1', 'Mesa 2', 'Mesa 3', 'Mesa 4', 'Mesa 5', 'Mesa 6', 'Mesa 7', 'Mesa 8', 'Mesa 9', 'Mesa 10', 'Mesa 11', 'Mesa 12', 'Mesa 13'],
    'Tablones' => ['Tablón 1', 'Tablón 2']
];

// ============================================
// ACCIÓN: POBLAR TODAS LAS MESAS
// ============================================
if ($action === 'populate') {
    try {
        $conn->beginTransaction();
        $inserted = 0;
        $skipped = 0;
        
        foreach ($allTables as $zone => $tables) {
            foreach ($tables as $tableName) {
                // Verificar si ya existe
                $checkStmt = $conn->prepare("SELECT id FROM tables WHERE name = ?");
                $checkStmt->execute([$tableName]);
                
                if ($checkStmt->rowCount() == 0) {
                    // No existe, insertar
                    $insertStmt = $conn->prepare("INSERT INTO tables (name) VALUES (?)");
                    $insertStmt->execute([$tableName]);
                    $inserted++;
                } else {
                    $skipped++;
                }
            }
        }
        
        $conn->commit();
        $message = "✅ Proceso completado: $inserted mesas insertadas, $skipped ya existían.";
        $messageType = 'success';
        
    } catch (PDOException $e) {
        $conn->rollBack();
        $message = "❌ Error: " . $e->getMessage();
        $messageType = 'danger';
    }
}

// ============================================
// ACCIÓN: AGREGAR MESA INDIVIDUAL
// ============================================
if ($action === 'add' && isset($_POST['table_name'])) {
    $tableName = trim($_POST['table_name']);
    
    if (empty($tableName)) {
        $message = "⚠️ El nombre de la mesa no puede estar vacío";
        $messageType = 'warning';
    } else {
        try {
            $stmt = $conn->prepare("INSERT INTO tables (name) VALUES (?)");
            $stmt->execute([$tableName]);
            $message = "✅ Mesa '$tableName' agregada correctamente (ID: " . $conn->lastInsertId() . ")";
            $messageType = 'success';
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $message = "⚠️ La mesa '$tableName' ya existe";
                $messageType = 'warning';
            } else {
                $message = "❌ Error: " . $e->getMessage();
                $messageType = 'danger';
            }
        }
    }
}

// ============================================
// ACCIÓN: ELIMINAR MESA
// ============================================
if ($action === 'delete' && isset($_POST['table_id'])) {
    $tableId = $_POST['table_id'];
    
    try {
        // Verificar si tiene facturas asociadas
        $checkStmt = $conn->prepare("SELECT COUNT(*) as count FROM invoice WHERE table_id = ?");
        $checkStmt->execute([$tableId]);
        $count = $checkStmt->fetch(PDO::FETCH_OBJ)->count;
        
        if ($count > 0) {
            $message = "⚠️ No se puede eliminar: la mesa tiene $count factura(s) asociada(s)";
            $messageType = 'warning';
        } else {
            $deleteStmt = $conn->prepare("DELETE FROM tables WHERE id = ?");
            $deleteStmt->execute([$tableId]);
            $message = "✅ Mesa eliminada correctamente";
            $messageType = 'success';
        }
    } catch (PDOException $e) {
        $message = "❌ Error: " . $e->getMessage();
        $messageType = 'danger';
    }
}

// ============================================
// OBTENER MESAS ACTUALES
// ============================================
try {
    $stmt = $conn->prepare("SELECT * FROM tables ORDER BY name ASC");
    $stmt->execute();
    $currentTables = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $currentTables = [];
    $message = "❌ Error al obtener mesas: " . $e->getMessage();
    $messageType = 'danger';
}

$title = "Configuración de Mesas";
include "includes/header.php";
?>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            
            <!-- Header -->
            <div class="text-center mb-4">
                <h1 class="display-5 fw-bold text-primary">
                    <i class="bi bi-table me-3"></i>
                    Gestión de Mesas del Restaurante
                </h1>
                <p class="lead text-muted">Configura todas las mesas disponibles en el sistema</p>
            </div>

            <!-- Mensaje de feedback -->
            <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <!-- Estadísticas -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card border-primary">
                        <div class="card-body text-center">
                            <h3 class="text-primary mb-0"><?php echo count($currentTables); ?></h3>
                            <p class="text-muted mb-0">Mesas Registradas</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-success">
                        <div class="card-body text-center">
                            <h3 class="text-success mb-0">33</h3>
                            <p class="text-muted mb-0">Mesas Totales del Sistema</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-warning">
                        <div class="card-body text-center">
                            <h3 class="text-warning mb-0"><?php echo 33 - count($currentTables); ?></h3>
                            <p class="text-muted mb-0">Mesas Faltantes</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <ul class="nav nav-tabs mb-4" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#populate" type="button">
                        <i class="bi bi-cloud-upload me-2"></i>Poblar Mesas
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#view" type="button">
                        <i class="bi bi-list-ul me-2"></i>Ver Mesas (<?php echo count($currentTables); ?>)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#add" type="button">
                        <i class="bi bi-plus-circle me-2"></i>Agregar Mesa
                    </button>
                </li>
            </ul>

            <div class="tab-content">
                
                <!-- TAB: Poblar Mesas -->
                <div class="tab-pane fade show active" id="populate">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="bi bi-magic me-2"></i>Poblar Todas las Mesas Automáticamente</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <h6 class="alert-heading"><i class="bi bi-info-circle me-2"></i>Información</h6>
                                <p class="mb-0">Este proceso insertará automáticamente las 33 mesas del sistema organizadas en 6 zonas. Las mesas que ya existan serán omitidas.</p>
                            </div>

                            <h6 class="fw-bold mb-3">Mesas que se insertarán:</h6>
                            <div class="row g-3">
                                <?php foreach ($allTables as $zone => $tables): ?>
                                <div class="col-md-6">
                                    <div class="card border-secondary">
                                        <div class="card-header bg-light">
                                            <strong><?php echo $zone; ?></strong> (<?php echo count($tables); ?> mesas)
                                        </div>
                                        <div class="card-body">
                                            <ul class="mb-0">
                                                <?php foreach ($tables as $table): ?>
                                                <li><?php echo $table; ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>

                            <form method="POST" class="mt-4" onsubmit="return confirm('¿Estás seguro de que quieres poblar todas las mesas?');">
                                <button type="submit" name="populate_all" class="btn btn-primary btn-lg">
                                    <i class="bi bi-cloud-upload me-2"></i>Poblar Todas las Mesas (33 mesas)
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- TAB: Ver Mesas -->
                <div class="tab-pane fade" id="view">
                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Mesas Registradas</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($currentTables)): ?>
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                No hay mesas registradas. Usa la pestaña "Poblar Mesas" para insertar todas las mesas automáticamente.
                            </div>
                            <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre de la Mesa</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($currentTables as $table): ?>
                                        <tr>
                                            <td><?php echo $table['id']; ?></td>
                                            <td><strong><?php echo htmlspecialchars($table['name']); ?></strong></td>
                                            <td>
                                                <form method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar la mesa <?php echo htmlspecialchars($table['name']); ?>?');">
                                                    <input type="hidden" name="table_id" value="<?php echo $table['id']; ?>">
                                                    <button type="submit" name="delete_table" class="btn btn-sm btn-danger">
                                                        <i class="bi bi-trash3"></i> Eliminar
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- TAB: Agregar Mesa -->
                <div class="tab-pane fade" id="add">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Agregar Mesa Individual</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="mb-3">
                                    <label for="table_name" class="form-label">Nombre de la Mesa</label>
                                    <input type="text" 
                                           class="form-control form-control-lg" 
                                           id="table_name" 
                                           name="table_name" 
                                           placeholder="Ej: Mesa 14, Patio 6, VIP 1" 
                                           required>
                                    <div class="form-text">Ingresa un nombre único para la nueva mesa</div>
                                </div>
                                <button type="submit" name="add_table" class="btn btn-success btn-lg">
                                    <i class="bi bi-plus-circle me-2"></i>Agregar Mesa
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Botones de navegación -->
            <div class="mt-4 text-center">
                <a href="index.html" class="btn btn-outline-secondary">
                    <i class="bi bi-house me-2"></i>Volver al Inicio
                </a>
                <a href="debug_tables.php" class="btn btn-outline-info">
                    <i class="bi bi-bug me-2"></i>Debug de Mesas
                </a>
            </div>

        </div>
    </div>
</div>

<?php include "includes/footer.html"; ?>
