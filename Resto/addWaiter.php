<?php
include "includes/conn.php";

$title = "Gestión de Camareros del Restaurante";

// Obtener lista de camareros para los formularios de modificar y eliminar
try {
    $stmt = $conn->prepare("SELECT id, name FROM waiter ORDER BY name");
    $stmt->execute();
    $waiters = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $waiters = [];
    $error_message = "Error al cargar camareros: " . $e->getMessage();
}

include "includes/header.php";

// Manejo de mensajes de error y éxito
$message = '';
$messageType = '';

if (isset($_GET['error'])) {
    $message = $_GET['error'];
    $messageType = 'danger';
} elseif (isset($_GET['success'])) {
    $message = $_GET['success'];
    $messageType = 'success';
}
?>

<?php if ($message): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    toast('<?php echo $messageType === "success" ? "0" : "1"; ?>', 
          '<?php echo $messageType === "success" ? "Éxito" : "Error"; ?>', 
          '<?php echo addslashes($message); ?>');
});
</script>
<?php endif; ?>

<main class="d-flex flex-column min-vh-100">
    <div class="container-fluid p-3 p-md-4 p-lg-5">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-11">
                
                <!-- Encabezado -->
                <div class="text-center mb-5">
                    <h1 class="display-5 mb-3">
                        <i class="bi bi-person-badge me-3 text-primary"></i>
                        Gestión de Camareros
                    </h1>
                    <p class="lead text-muted">Administra el personal de servicio del restaurante</p>
                </div>

                <!-- Error message si existe -->
                <?php if (isset($error_message)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <?php echo htmlspecialchars($error_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <!-- Tarjeta de Pestañas -->
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-primary text-white">
                        <ul class="nav nav-tabs card-header-tabs nav-fill" id="waiterTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="add-tab" data-bs-toggle="tab" data-bs-target="#add-waiter" type="button" role="tab">
                                    <i class="bi bi-person-plus me-2"></i>Agregar Camarero
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="modify-tab" data-bs-toggle="tab" data-bs-target="#modify-waiter" type="button" role="tab">
                                    <i class="bi bi-pencil-square me-2"></i>Modificar Camarero
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="delete-tab" data-bs-toggle="tab" data-bs-target="#delete-waiter" type="button" role="tab">
                                    <i class="bi bi-person-x me-2"></i>Eliminar Camarero
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="list-tab" data-bs-toggle="tab" data-bs-target="#list-waiters" type="button" role="tab">
                                    <i class="bi bi-list-ul me-2"></i>Ver Camareros
                                </button>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="card-body p-4">
                        <div class="tab-content" id="waiterTabsContent">
                            
                            <!-- Tab: Agregar Camarero -->
                            <div class="tab-pane fade show active" id="add-waiter" role="tabpanel">
                                <div class="row justify-content-center">
                                    <div class="col-12 col-lg-8">
                                        <div class="card border-0 bg-light">
                                            <div class="card-body p-4 p-lg-5">
                                                <h3 class="text-center mb-4">
                                                    <i class="bi bi-person-plus text-success me-2"></i>
                                                    Nuevo Camarero
                                                </h3>
                                                <p class="text-center text-muted mb-4">
                                                    Ingresa el nombre del nuevo camarero/mesero del restaurante.
                                                </p>
                                                
                                                <form action="addingWaiter.php" method="post" class="needs-validation" novalidate>
                                                    <div class="row g-3">
                                                        <!-- Nombre -->
                                                        <div class="col-12">
                                                            <label for="waiter_name" class="form-label fw-bold">
                                                                <i class="bi bi-person me-1"></i>Nombre Completo del Camarero *
                                                            </label>
                                                            <input type="text" 
                                                                   class="form-control form-control-lg" 
                                                                   id="waiter_name" 
                                                                   name="waiter_name" 
                                                                   placeholder="Ej: Juan Pérez" 
                                                                   required 
                                                                   minlength="3"
                                                                   maxlength="100"
                                                                   autofocus>
                                                            <div class="invalid-feedback">
                                                                Por favor, ingresa el nombre del camarero (mínimo 3 caracteres).
                                                            </div>
                                                            <div class="form-text">
                                                                <i class="bi bi-info-circle me-1"></i>
                                                                Nombre que aparecerá en las facturas y pedidos.
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="d-grid gap-2 mt-4">
                                                        <button type="submit" class="btn btn-success btn-lg">
                                                            <i class="bi bi-check-circle me-2"></i>
                                                            Agregar Camarero
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab: Modificar Camarero -->
                            <div class="tab-pane fade" id="modify-waiter" role="tabpanel">
                                <div class="row justify-content-center">
                                    <div class="col-12 col-lg-8">
                                        <div class="card border-0 bg-light">
                                            <div class="card-body p-4 p-lg-5">
                                                <h3 class="text-center mb-4">
                                                    <i class="bi bi-pencil-square text-primary me-2"></i>
                                                    Modificar Datos del Camarero
                                                </h3>
                                                
                                                <?php if (empty($waiters)): ?>
                                                <div class="alert alert-warning text-center">
                                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                                    No hay camareros registrados. Agrega uno primero.
                                                </div>
                                                <?php else: ?>
                                                <form action="modifyWaiter.php" method="post" id="modifyForm" class="needs-validation" novalidate>
                                                    <div class="row g-3">
                                                        <!-- Seleccionar Camarero -->
                                                        <div class="col-12">
                                                            <label for="waiter_select" class="form-label fw-bold">
                                                                <i class="bi bi-person-check me-1"></i>Selecciona el Camarero *
                                                            </label>
                                                            <select class="form-select form-select-lg" 
                                                                    id="waiter_select" 
                                                                    name="waiter_id" 
                                                                    required>
                                                                <option value="">-- Elige un camarero --</option>
                                                                <?php foreach($waiters as $waiter): ?>
                                                                <option value="<?php echo $waiter['id']; ?>" 
                                                                        data-name="<?php echo htmlspecialchars($waiter['name']); ?>">
                                                                    <?php echo htmlspecialchars($waiter['name']); ?>
                                                                </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                            <div class="invalid-feedback">Por favor, selecciona un camarero.</div>
                                                        </div>
                                                        
                                                        <!-- Nuevo Nombre -->
                                                        <div class="col-12">
                                                            <label for="new_name" class="form-label fw-bold">
                                                                <i class="bi bi-pencil me-1"></i>Nuevo Nombre *
                                                            </label>
                                                            <input type="text" 
                                                                   class="form-control form-control-lg" 
                                                                   id="new_name" 
                                                                   name="new_name" 
                                                                   placeholder="Ingresa el nuevo nombre" 
                                                                   required 
                                                                   minlength="3"
                                                                   maxlength="100">
                                                            <div class="invalid-feedback">
                                                                Por favor, ingresa el nuevo nombre (mínimo 3 caracteres).
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="d-grid gap-2 mt-4">
                                                        <button type="submit" class="btn btn-primary btn-lg">
                                                            <i class="bi bi-pencil-square me-2"></i>
                                                            Modificar Datos del Camarero
                                                        </button>
                                                    </div>
                                                </form>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Tab: Eliminar Camarero -->
                            <div class="tab-pane fade" id="delete-waiter" role="tabpanel">
                                <div class="row justify-content-center">
                                    <div class="col-12 col-lg-8">
                                        <div class="card border-0 bg-light border-danger">
                                            <div class="card-body p-4 p-lg-5">
                                                <h3 class="text-center mb-4">
                                                    <i class="bi bi-person-x text-danger me-2"></i>
                                                    Eliminar Camarero
                                                </h3>
                                                <div class="alert alert-danger" role="alert">
                                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                                    <strong>¡Atención!</strong> Esta acción es permanente y no se puede deshacer.
                                                </div>
                                                
                                                <?php if (empty($waiters)): ?>
                                                <div class="alert alert-warning text-center">
                                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                                    No hay camareros registrados.
                                                </div>
                                                <?php else: ?>
                                                <form action="deleteWaiter.php" method="post" id="deleteForm">
                                                    <div class="row g-3">
                                                        <div class="col-12">
                                                            <label for="waiter_delete" class="form-label fw-bold">
                                                                <i class="bi bi-person-x me-1"></i>Selecciona el Camarero a Eliminar
                                                            </label>
                                                            <select class="form-select form-select-lg" 
                                                                    id="waiter_delete" 
                                                                    name="waiter_id" 
                                                                    required>
                                                                <option value="">-- Elige un camarero --</option>
                                                                <?php foreach($waiters as $waiter): ?>
                                                                <option value="<?php echo $waiter['id']; ?>" 
                                                                        data-name="<?php echo htmlspecialchars($waiter['name']); ?>">
                                                                    <?php echo htmlspecialchars($waiter['name']); ?>
                                                                </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                            <div class="invalid-feedback">Por favor, selecciona un camarero.</div>
                                                            <div class="form-text text-danger">
                                                                <i class="bi bi-exclamation-triangle me-1"></i>
                                                                La eliminación es permanente e irreversible
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="d-grid gap-2">
                                                            <button type="button" class="btn btn-danger btn-lg" id="deleteButton" disabled>
                                                                <i class="bi bi-trash me-2"></i>
                                                                Eliminar Camarero Definitivamente
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab: Ver Camareros -->
                            <div class="tab-pane fade" id="list-waiters" role="tabpanel">
                                <h3 class="text-center mb-4">
                                    <i class="bi bi-list-ul text-info me-2"></i>
                                    Lista de Camareros
                                </h3>
                                
                                <?php if (empty($waiters)): ?>
                                <div class="text-center py-5">
                                    <i class="bi bi-inbox display-1 text-muted mb-3"></i>
                                    <h4 class="text-muted">No hay camareros registrados</h4>
                                    <p class="text-muted">Agrega tu primer camarero usando la pestaña "Agregar Camarero".</p>
                                </div>
                                <?php else: ?>
                                <div class="row g-3">
                                    <?php foreach($waiters as $index => $waiter): ?>
                                    <div class="col-12 col-md-6 col-lg-4">
                                        <div class="card h-100 shadow-sm border-primary">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="bg-primary text-white rounded-circle p-3 me-3">
                                                        <i class="bi bi-person-badge fs-4"></i>
                                                    </div>
                                                    <div>
                                                        <h5 class="card-title mb-0">
                                                            <?php echo htmlspecialchars($waiter['name']); ?>
                                                        </h5>
                                                        <small class="text-muted">
                                                            ID: <?php echo $waiter['id']; ?>
                                                        </small>
                                                    </div>
                                                </div>
                                                <div class="btn-group w-100" role="group">
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-primary"
                                                            onclick="modifyWaiter(<?php echo $waiter['id']; ?>, '<?php echo htmlspecialchars($waiter['name'], ENT_QUOTES); ?>')">
                                                        <i class="bi bi-pencil"></i> Modificar
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                                            data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                            data-waiter-id="<?php echo $waiter['id']; ?>"
                                                            data-waiter-name="<?php echo htmlspecialchars($waiter['name']); ?>">
                                                        <i class="bi bi-trash"></i> Eliminar
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                
                                <div class="text-center mt-4">
                                    <div class="alert alert-info">
                                        <i class="bi bi-info-circle me-2"></i>
                                        Total de camareros registrados: <strong><?php echo count($waiters); ?></strong>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Acciones adicionales -->
                <div class="text-center mt-5">
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
                                    Volver al Inicio
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</main>

<!-- Modal de confirmación de eliminación -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Confirmar Eliminación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">¿Estás seguro de que deseas eliminar al camarero:</p>
                <div class="alert alert-warning">
                    <strong id="deleteWaiterName"></strong>
                </div>
                <p class="text-danger mb-0">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    <strong>Esta acción no se puede deshacer.</strong>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Cancelar
                </button>
                <form action="deleteWaiter.php" method="post" id="confirmDeleteForm">
                    <input type="hidden" name="waiter_id" id="deleteWaiterId">
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i>Eliminar Definitivamente
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Validación de formularios Bootstrap
(function() {
    'use strict';
    var forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
})();

// Auto-rellenar el formulario de modificar cuando se selecciona un camarero
document.getElementById('waiter_select')?.addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const name = selectedOption.getAttribute('data-name');
    document.getElementById('new_name').value = name || '';
});

// Habilitar botón de eliminar solo cuando se selecciona un camarero
document.getElementById('waiter_delete')?.addEventListener('change', function() {
    document.getElementById('deleteButton').disabled = this.value === '';
});

// Confirmar eliminación antes de enviar
document.getElementById('deleteButton')?.addEventListener('click', function() {
    const select = document.getElementById('waiter_delete');
    const selectedOption = select.options[select.selectedIndex];
    const name = selectedOption.getAttribute('data-name');
    
    if (confirm('¿Estás seguro de que deseas eliminar al camarero "' + name + '"?\n\nEsta acción no se puede deshacer.')) {
        document.getElementById('deleteForm').submit();
    }
});

// Función para modificar desde la lista
function modifyWaiter(id, name) {
    // Cambiar a la pestaña de modificar
    const modifyTab = new bootstrap.Tab(document.getElementById('modify-tab'));
    modifyTab.show();
    
    // Seleccionar el camarero
    document.getElementById('waiter_select').value = id;
    document.getElementById('new_name').value = name;
    
    // Hacer scroll al formulario
    document.getElementById('waiter_select').scrollIntoView({ behavior: 'smooth' });
}

// Modal de eliminación
const deleteModal = document.getElementById('deleteModal');
if (deleteModal) {
    deleteModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const waiterId = button.getAttribute('data-waiter-id');
        const waiterName = button.getAttribute('data-waiter-name');
        
        document.getElementById('deleteWaiterName').textContent = waiterName;
        document.getElementById('deleteWaiterId').value = waiterId;
    });
}
</script>

<?php
include "includes/footer.html";
?>
