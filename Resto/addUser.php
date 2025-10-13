<?php
include "includes/conn.php";

$title = "Gestión de Clientes del Restaurante";

// Obtener lista de clientes para los formularios de modificar y eliminar
try {
    $stmt = $conn->prepare("SELECT id, name, surname1, surname2, email, phone, address FROM client ORDER BY name");
    $stmt->execute();
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $clients = [];
    $error_message = "Error al cargar clientes: " . $e->getMessage();
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
                        <i class="bi bi-people me-3 text-primary"></i>
                        Gestión de Clientes
                    </h1>
                    <p class="lead text-muted">Administra la información de los clientes del restaurante</p>
                </div>

                <!-- Error message si existe -->
                <?php if (isset($error_message)): ?>
                <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <?php echo htmlspecialchars($error_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <!-- Navegación por tabs -->
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-light">
                        <ul class="nav nav-tabs card-header-tabs" id="clientTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="add-tab" data-bs-toggle="tab" data-bs-target="#add-client" type="button" role="tab">
                                    <i class="bi bi-person-plus me-2"></i>Agregar Cliente
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="modify-tab" data-bs-toggle="tab" data-bs-target="#modify-client" type="button" role="tab">
                                    <i class="bi bi-pencil-square me-2"></i>Modificar Cliente
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="delete-tab" data-bs-toggle="tab" data-bs-target="#delete-client" type="button" role="tab">
                                    <i class="bi bi-person-x me-2"></i>Eliminar Cliente
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="list-tab" data-bs-toggle="tab" data-bs-target="#list-clients" type="button" role="tab">
                                    <i class="bi bi-list-ul me-2"></i>Ver Clientes
                                </button>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="card-body p-4">
                        <div class="tab-content" id="clientTabsContent">
                            
                            <!-- Tab: Agregar Cliente -->
                            <div class="tab-pane fade show active" id="add-client" role="tabpanel">
                                <div class="row justify-content-center">
                                    <div class="col-12 col-lg-8">
                                        <div class="card border-0 bg-light">
                                            <div class="card-body p-4 p-lg-5">
                                                <h3 class="text-center mb-4">
                                                    <i class="bi bi-person-plus text-success me-2"></i>
                                                    Nuevo Cliente
                                                </h3>
                                                <p class="text-center text-muted mb-4">
                                                    Complete la información del cliente. Los campos marcados con * son obligatorios.
                                                </p>
                                                
                                                <form action="addingUser.php" method="post" class="needs-validation" novalidate onsubmit="return verify()">
                                                    <div class="row g-3">
                                                        <!-- Nombre -->
                                                        <div class="col-12 col-md-6">
                                                            <label for="client" class="form-label fw-bold">
                                                                <i class="bi bi-person me-1"></i>Nombre *
                                                            </label>
                                                            <input type="text" id="client" name="client" class="form-control form-control-lg" 
                                                                   placeholder="Ej: Juan" required>
                                                            <div class="invalid-feedback">Por favor, ingrese el nombre.</div>
                                                        </div>
                                                        
                                                        <!-- Apellido Principal -->
                                                        <div class="col-12 col-md-6">
                                                            <label for="surname1" class="form-label fw-bold">
                                                                <i class="bi bi-person me-1"></i>Apellido Principal *
                                                            </label>
                                                            <input type="text" id="surname1" name="surname1" class="form-control form-control-lg" 
                                                                   placeholder="Ej: Pérez" required>
                                                            <div class="invalid-feedback">Por favor, ingrese el apellido.</div>
                                                        </div>
                                                        
                                                        <!-- Apellido Secundario -->
                                                        <div class="col-12 col-md-6">
                                                            <label for="surname2" class="form-label fw-bold">
                                                                <i class="bi bi-person me-1"></i>Apellido Secundario
                                                            </label>
                                                            <input type="text" id="surname2" name="surname2" class="form-control form-control-lg" 
                                                                   placeholder="Ej: García (opcional)">
                                                        </div>
                                                        
                                                        <!-- Email -->
                                                        <div class="col-12">
                                                            <label for="email" class="form-label fw-bold">
                                                                <i class="bi bi-envelope me-1"></i>Email *
                                                            </label>
                                                            <input type="email" id="email" name="email" class="form-control form-control-lg" 
                                                                   placeholder="ejemplo@correo.com" required>
                                                            <div class="invalid-feedback">Por favor, ingrese un email válido.</div>
                                                        </div>
                                                        
                                                        <!-- Teléfono -->
                                                        <div class="col-12 col-md-6">
                                                            <label for="phone" class="form-label fw-bold">
                                                                <i class="bi bi-telephone me-1"></i>Teléfono *
                                                            </label>
                                                            <input type="tel" id="phone" name="phone" class="form-control form-control-lg" 
                                                                   placeholder="11-1234-5678" required>
                                                            <div class="invalid-feedback">Por favor, ingrese el teléfono.</div>
                                                        </div>
                                                        
                                                        <!-- Dirección -->
                                                        <div class="col-12 col-md-6">
                                                            <label for="address" class="form-label fw-bold">
                                                                <i class="bi bi-geo-alt me-1"></i>Dirección *
                                                            </label>
                                                            <input type="text" id="address" name="address" class="form-control form-control-lg" 
                                                                   placeholder="Calle Falsa 123" required>
                                                            <div class="invalid-feedback">Por favor, ingrese la dirección.</div>
                                                        </div>
                                                        
                                                        <!-- Contraseñas -->
                                                        <div class="col-12">
                                                            <div class="border-top pt-3 mt-3">
                                                                <h5 class="text-muted mb-3">
                                                                    <i class="bi bi-shield-lock me-2"></i>
                                                                    Contraseña (Opcional)
                                                                </h5>
                                                                <div class="row g-3">
                                                                    <div class="col-12 col-md-6">
                                                                        <label for="pass" class="form-label">Contraseña</label>
                                                                        <input type="password" id="pass" name="pass" class="form-control" 
                                                                               placeholder="Dejar en blanco si no requiere">
                                                                    </div>
                                                                    <div class="col-12 col-md-6">
                                                                        <label for="pass2" class="form-label">Repetir Contraseña</label>
                                                                        <input type="password" id="pass2" name="pass2" class="form-control" 
                                                                               placeholder="Repetir contraseña">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="d-grid gap-2 mt-4">
                                                        <button type="submit" class="btn btn-success btn-lg">
                                                            <i class="bi bi-person-plus me-2"></i>
                                                            Agregar Cliente
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Tab: Modificar Cliente -->
                            <div class="tab-pane fade" id="modify-client" role="tabpanel">
                                <div class="row justify-content-center">
                                    <div class="col-12 col-lg-8">
                                        <div class="card border-0 bg-light">
                                            <div class="card-body p-4 p-lg-5">
                                                <h3 class="text-center mb-4">
                                                    <i class="bi bi-pencil-square text-primary me-2"></i>
                                                    Modificar Cliente
                                                </h3>
                                                <p class="text-center text-muted mb-4">
                                                    Selecciona un cliente para modificar su información.
                                                </p>
                                                
                                                <?php if (empty($clients)): ?>
                                                <div class="text-center py-4">
                                                    <i class="bi bi-inbox display-4 text-muted mb-3"></i>
                                                    <h5 class="text-muted">No hay clientes registrados</h5>
                                                    <p class="text-muted">Agrega un cliente primero para poder modificarlo.</p>
                                                </div>
                                                <?php else: ?>
                                                <form action="modify.php" method="post" class="needs-validation" novalidate>
                                                    <div class="mb-4">
                                                        <label for="modify-client-select" class="form-label fw-bold h5">
                                                            <i class="bi bi-person-gear me-2"></i>Seleccionar Cliente
                                                        </label>
                                                        <select name="client" id="modify-client-select" class="form-select form-select-lg" required>
                                                            <option value="">Selecciona un cliente...</option>
                                                            <?php foreach($clients as $client): ?>
                                                            <option value="<?php echo $client['id']; ?>">
                                                                <?php echo htmlspecialchars($client['name'] . ' ' . $client['surname1']); ?>
                                                                <small class="text-muted"> - <?php echo htmlspecialchars($client['email']); ?></small>
                                                            </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                        <div class="invalid-feedback">Por favor, selecciona un cliente.</div>
                                                        <div class="form-text">
                                                            <i class="bi bi-info-circle me-1"></i>
                                                            Elige el cliente cuyos datos deseas modificar
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="d-grid gap-2">
                                                        <button type="submit" class="btn btn-primary btn-lg">
                                                            <i class="bi bi-pencil-square me-2"></i>
                                                            Modificar Datos del Cliente
                                                        </button>
                                                    </div>
                                                </form>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Tab: Eliminar Cliente -->
                            <div class="tab-pane fade" id="delete-client" role="tabpanel">
                                <div class="row justify-content-center">
                                    <div class="col-12 col-lg-8">
                                        <div class="card border-0 bg-light border-danger">
                                            <div class="card-body p-4 p-lg-5">
                                                <h3 class="text-center mb-4">
                                                    <i class="bi bi-person-x text-danger me-2"></i>
                                                    Eliminar Cliente
                                                </h3>
                                                <div class="alert alert-danger" role="alert">
                                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                                    <strong>¡Atención!</strong> Esta acción eliminará permanentemente todos los datos del cliente y no se puede deshacer.
                                                </div>
                                                
                                                <?php if (empty($clients)): ?>
                                                <div class="text-center py-4">
                                                    <i class="bi bi-inbox display-4 text-muted mb-3"></i>
                                                    <h5 class="text-muted">No hay clientes registrados</h5>
                                                    <p class="text-muted">No hay clientes para eliminar.</p>
                                                </div>
                                                <?php else: ?>
                                                <form action="deluser.php" method="post" class="needs-validation" novalidate id="deleteForm">
                                                    <div class="mb-4">
                                                        <label for="delete-client-select" class="form-label fw-bold h5">
                                                            <i class="bi bi-person-x me-2"></i>Seleccionar Cliente a Eliminar
                                                        </label>
                                                        <select name="client" id="delete-client-select" class="form-select form-select-lg" required>
                                                            <option value="">Selecciona un cliente...</option>
                                                            <?php foreach($clients as $client): ?>
                                                            <option value="<?php echo $client['id']; ?>" 
                                                                    data-name="<?php echo htmlspecialchars($client['name'] . ' ' . $client['surname1']); ?>"
                                                                    data-phone="<?php echo htmlspecialchars($client['phone']); ?>"
                                                                    data-email="<?php echo htmlspecialchars($client['email']); ?>">
                                                                <?php echo htmlspecialchars($client['name'] . ' ' . $client['surname1']); ?>
                                                                - <?php echo htmlspecialchars($client['phone']); ?>
                                                            </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                        <div class="invalid-feedback">Por favor, selecciona un cliente.</div>
                                                        <div class="form-text text-danger">
                                                            <i class="bi bi-exclamation-triangle me-1"></i>
                                                            La eliminación es permanente e irreversible
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="d-grid gap-2">
                                                        <button type="button" class="btn btn-danger btn-lg" id="deleteButton" disabled>
                                                            <i class="bi bi-trash me-2"></i>
                                                            Eliminar Cliente Definitivamente
                                                        </button>
                                                    </div>
                                                </form>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab: Ver Clientes -->
                            <div class="tab-pane fade" id="list-clients" role="tabpanel">
                                <h3 class="text-center mb-4">
                                    <i class="bi bi-list-ul text-info me-2"></i>
                                    Lista de Clientes
                                </h3>
                                
                                <?php if (empty($clients)): ?>
                                <div class="text-center py-5">
                                    <i class="bi bi-inbox display-1 text-muted mb-3"></i>
                                    <h4 class="text-muted">No hay clientes registrados</h4>
                                    <p class="text-muted">Agrega tu primer cliente usando la pestaña "Agregar Cliente".</p>
                                </div>
                                <?php else: ?>
                                <div class="row g-3">
                                    <?php foreach($clients as $index => $client): ?>
                                    <div class="col-12 col-md-6 col-lg-4">
                                        <div class="card h-100 shadow-sm">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                                        <i class="bi bi-person text-primary h4 mb-0"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="card-title mb-0">
                                                            <?php echo htmlspecialchars($client['name'] . ' ' . $client['surname1']); ?>
                                                        </h6>
                                                        <small class="text-muted">Cliente #<?php echo $client['id']; ?></small>
                                                    </div>
                                                </div>
                                                <div class="mb-2">
                                                    <small class="text-muted">
                                                        <i class="bi bi-envelope me-1"></i>
                                                        <?php echo htmlspecialchars($client['email']); ?>
                                                    </small>
                                                </div>
                                                <div class="mb-3">
                                                    <small class="text-muted">
                                                        <i class="bi bi-telephone me-1"></i>
                                                        <?php echo htmlspecialchars($client['phone']); ?>
                                                    </small>
                                                </div>
                                                <div class="btn-group w-100" role="group">
                                                    <form action="modify.php" method="post" class="flex-fill">
                                                        <input type="hidden" name="client" value="<?php echo $client['id']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-primary w-100">
                                                            <i class="bi bi-pencil"></i> Modificar
                                                        </button>
                                                    </form>
                                                    <button type="button" class="btn btn-sm btn-outline-danger flex-fill ms-1"
                                                            data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                            data-client-id="<?php echo $client['id']; ?>"
                                                            data-client-name="<?php echo htmlspecialchars($client['name'] . ' ' . $client['surname1']); ?>"
                                                            data-client-phone="<?php echo htmlspecialchars($client['phone']); ?>"
                                                            data-client-email="<?php echo htmlspecialchars($client['email']); ?>">
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
                                        Total de clientes registrados: <strong><?php echo count($clients); ?></strong>
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
                                    Ir al Inicio
                                </a>
                                <a href="add.php" class="btn btn-primary btn-lg">
                                    <i class="bi bi-plus-circle me-2"></i>
                                    Gestionar Productos
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Modal de Confirmación de Eliminación -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-danger">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Confirmar Eliminación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <i class="bi bi-person-x display-1 text-danger mb-3"></i>
                    <h4 class="text-danger">¡ATENCIÓN!</h4>
                </div>
                
                <div class="alert alert-danger">
                    <strong>Esta acción NO se puede deshacer.</strong>
                    <br>Se eliminará permanentemente toda la información del cliente.
                </div>
                
                <div id="clientInfo" class="card bg-light">
                    <div class="card-body">
                        <h6 class="card-title text-primary">Cliente a eliminar:</h6>
                        <div class="row">
                            <div class="col-sm-4"><strong>Nombre:</strong></div>
                            <div class="col-sm-8" id="modalClientName">-</div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4"><strong>Teléfono:</strong></div>
                            <div class="col-sm-8" id="modalClientPhone">-</div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4"><strong>Email:</strong></div>
                            <div class="col-sm-8" id="modalClientEmail">-</div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="confirmDelete" required>
                        <label class="form-check-label text-danger fw-bold" for="confirmDelete">
                            Confirmo que entiendo que esta acción es irreversible
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>Cancelar
                </button>
                <form id="confirmDeleteForm" action="deluser.php" method="post" class="d-inline">
                    <input type="hidden" name="client" id="modalClientId">
                    <button type="submit" class="btn btn-danger" id="confirmDeleteBtn" disabled>
                        <span class="btn-content">
                            <i class="bi bi-trash me-2"></i>
                            Eliminar Definitivamente
                        </span>
                        <span class="btn-loading d-none">
                            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                            Eliminando...
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.nav-tabs .nav-link {
    border: none;
    color: #6c757d;
    font-weight: 500;
}

.nav-tabs .nav-link.active {
    background-color: #fff;
    color: #0d6efd;
    border-bottom: 3px solid #0d6efd;
}

.nav-tabs .nav-link:hover {
    color: #0d6efd;
    border-bottom: 2px solid #dee2e6;
}

.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

.form-control:focus, .form-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

@media (max-width: 768px) {
    .card-body {
        padding: 1.5rem !important;
    }
}
</style>

<script>
// Validación de Bootstrap
document.addEventListener('DOMContentLoaded', function() {
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
    
    // Auto-focus en el primer campo del tab activo
    const firstInput = document.querySelector('#add-client input[name="client"]');
    if (firstInput) {
        firstInput.focus();
    }
    
    // Cambiar focus cuando se cambia de tab
    const tabButtons = document.querySelectorAll('#clientTabs button[data-bs-toggle="tab"]');
    tabButtons.forEach(button => {
        button.addEventListener('shown.bs.tab', function(e) {
            const targetPane = document.querySelector(e.target.getAttribute('data-bs-target'));
            const firstInput = targetPane.querySelector('input, select');
            if (firstInput) {
                setTimeout(() => firstInput.focus(), 100);
            }
        });
    });

    // Gestión del modal de eliminación
    const deleteModal = document.getElementById('deleteModal');
    const deleteForm = document.getElementById('deleteForm');
    const deleteButton = document.getElementById('deleteButton');
    const deleteSelect = document.getElementById('delete-client-select');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    const confirmDeleteCheckbox = document.getElementById('confirmDelete');
    const modalClientId = document.getElementById('modalClientId');
    const modalClientName = document.getElementById('modalClientName');
    const modalClientPhone = document.getElementById('modalClientPhone');
    const modalClientEmail = document.getElementById('modalClientEmail');

    // Habilitar/deshabilitar botón de eliminar según selección
    if (deleteSelect && deleteButton) {
        deleteSelect.addEventListener('change', function() {
            deleteButton.disabled = !this.value;
        });

        // Manejar click del botón eliminar (mostrar modal)
        deleteButton.addEventListener('click', function() {
            const selectedOption = deleteSelect.options[deleteSelect.selectedIndex];
            if (selectedOption && selectedOption.value) {
                modalClientId.value = selectedOption.value;
                modalClientName.textContent = selectedOption.dataset.name;
                modalClientPhone.textContent = selectedOption.dataset.phone;
                modalClientEmail.textContent = selectedOption.dataset.email;
                
                // Mostrar modal
                const modal = new bootstrap.Modal(deleteModal);
                modal.show();
            }
        });
    }

    // Manejar botones de eliminación en las cards
    document.querySelectorAll('[data-bs-target="#deleteModal"]').forEach(button => {
        button.addEventListener('click', function() {
            const clientId = this.dataset.clientId;
            const clientName = this.dataset.clientName;
            const clientPhone = this.dataset.clientPhone;
            const clientEmail = this.dataset.clientEmail;
            
            modalClientId.value = clientId;
            modalClientName.textContent = clientName;
            modalClientPhone.textContent = clientPhone;
            modalClientEmail.textContent = clientEmail;
            
            // Resetear checkbox
            confirmDeleteCheckbox.checked = false;
            confirmDeleteBtn.disabled = true;
        });
    });

    // Habilitar/deshabilitar botón de confirmación según checkbox
    if (confirmDeleteCheckbox && confirmDeleteBtn) {
        confirmDeleteCheckbox.addEventListener('change', function() {
            confirmDeleteBtn.disabled = !this.checked;
        });
    }

    // Manejar envío del formulario de eliminación con loading state
    const confirmDeleteForm = document.getElementById('confirmDeleteForm');
    if (confirmDeleteForm) {
        confirmDeleteForm.addEventListener('submit', function() {
            const btnContent = confirmDeleteBtn.querySelector('.btn-content');
            const btnLoading = confirmDeleteBtn.querySelector('.btn-loading');
            
            // Mostrar estado de loading
            btnContent.classList.add('d-none');
            btnLoading.classList.remove('d-none');
            confirmDeleteBtn.disabled = true;
            
            // Deshabilitar botón de cancelar para evitar interrupciones
            const cancelBtn = document.querySelector('[data-bs-dismiss="modal"]');
            if (cancelBtn) {
                cancelBtn.disabled = true;
            }
        });
    }

    // Resetear modal al cerrarse
    if (deleteModal) {
        deleteModal.addEventListener('hidden.bs.modal', function() {
            confirmDeleteCheckbox.checked = false;
            confirmDeleteBtn.disabled = true;
            modalClientId.value = '';
            
            // Resetear estado de loading del botón
            const btnContent = confirmDeleteBtn.querySelector('.btn-content');
            const btnLoading = confirmDeleteBtn.querySelector('.btn-loading');
            if (btnContent && btnLoading) {
                btnContent.classList.remove('d-none');
                btnLoading.classList.add('d-none');
            }
            
            // Rehabilitar botón de cancelar
            const cancelBtn = document.querySelector('[data-bs-dismiss="modal"]');
            if (cancelBtn) {
                cancelBtn.disabled = false;
            }
        });
    }
});

// Función de verificación mejorada
function verify() {
    const pass = document.getElementById("pass");
    const pass2 = document.getElementById("pass2");
    
    // Si ambas contraseñas están vacías, está bien
    if (pass.value === "" && pass2.value === "") {
        return true;
    }
    
    // Si solo una está vacía, error
    if (pass.value === "" || pass2.value === "") {
        showWarning('Contraseñas Incompletas', 'Si vas a usar contraseña, debes completar ambos campos.');
        return false;
    }
    
    // Si no coinciden, error
    if (pass.value !== pass2.value) {
        showError('Contraseñas No Coinciden', 'Las contraseñas ingresadas no son iguales. Por favor, verifícalas.');
        return false;
    }
    
    return true;
}
</script>

<?php
include "includes/footer.html";
?>