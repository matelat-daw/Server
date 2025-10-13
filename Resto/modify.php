<?php
include "includes/conn.php";

// Verificar que se recibió el ID del cliente
if (!isset($_POST["client"]) || !filter_var($_POST["client"], FILTER_VALIDATE_INT)) {
    header('Location: addUser.php?error=Cliente no válido');
    exit;
}

$id = $_POST["client"];

try {
    // Obtener los datos del cliente
    $sql = "SELECT * FROM client WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_OBJ);
    
    if (!$row) {
        header('Location: addUser.php?error=Cliente no encontrado');
        exit;
    }

} catch (PDOException $e) {
    error_log("Error al obtener cliente: " . $e->getMessage());
    header('Location: addUser.php?error=Error en la base de datos');
    exit;
}

$title = "Modificando los Datos de un Cliente";
include "includes/header.php";
?>

<div class="container-fluid pt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-user-edit me-2"></i>
                        Modificando Cliente: <?php echo htmlspecialchars($row->name); ?>
                    </h4>
                </div>
                <div class="card-body">
                    <form action="modifyuser.php" method="post" id="modifyForm" class="needs-validation" novalidate>
                        <input type="hidden" name="id" value="<?php echo $row->id; ?>">
                        
                        <div class="row">
                            <!-- Información Personal -->
                            <div class="col-md-6">
                                <h5 class="text-secondary mb-3">
                                    <i class="fas fa-user me-2"></i>Información Personal
                                </h5>
                                
                                <div class="mb-3">
                                    <label for="client" class="form-label">
                                        <i class="fas fa-signature me-1"></i>Nombre *
                                    </label>
                                    <input type="text" class="form-control" id="client" name="client" 
                                           value="<?php echo htmlspecialchars($row->name); ?>" 
                                           required maxlength="50">
                                    <div class="invalid-feedback">
                                        Por favor ingresa el nombre del cliente.
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="surname1" class="form-label">
                                        <i class="fas fa-user me-1"></i>Primer Apellido *
                                    </label>
                                    <input type="text" class="form-control" id="surname1" name="surname1" 
                                           value="<?php echo htmlspecialchars($row->surname1); ?>" 
                                           required maxlength="50">
                                    <div class="invalid-feedback">
                                        Por favor ingresa el primer apellido.
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="surname2" class="form-label">
                                        <i class="fas fa-user me-1"></i>Segundo Apellido
                                    </label>
                                    <input type="text" class="form-control" id="surname2" name="surname2" 
                                           value="<?php echo htmlspecialchars($row->surname2 ?? ''); ?>" 
                                           maxlength="50">
                                </div>
                            </div>

                            <!-- Información de Contacto -->
                            <div class="col-md-6">
                                <h5 class="text-secondary mb-3">
                                    <i class="fas fa-address-book me-2"></i>Información de Contacto
                                </h5>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope me-1"></i>Email *
                                    </label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($row->email); ?>" 
                                           required maxlength="100">
                                    <div class="invalid-feedback">
                                        Por favor ingresa un email válido.
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="phone" class="form-label">
                                        <i class="fas fa-phone me-1"></i>Teléfono *
                                    </label>
                                    <input type="tel" class="form-control" id="phone" name="phone" 
                                           value="<?php echo htmlspecialchars($row->phone); ?>" 
                                           required maxlength="20">
                                    <div class="invalid-feedback">
                                        Por favor ingresa el teléfono.
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="address" class="form-label">
                                        <i class="fas fa-map-marker-alt me-1"></i>Dirección *
                                    </label>
                                    <textarea class="form-control" id="address" name="address" 
                                              required maxlength="200" rows="3"><?php echo htmlspecialchars($row->address); ?></textarea>
                                    <div class="invalid-feedback">
                                        Por favor ingresa la dirección.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Seguridad -->
                        <div class="row">
                            <div class="col-12">
                                <h5 class="text-secondary mb-3">
                                    <i class="fas fa-lock me-2"></i>Contraseña (dejar en blanco para no cambiar)
                                </h5>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="pass" class="form-label">
                                        <i class="fas fa-key me-1"></i>Nueva Contraseña
                                    </label>
                                    <input type="password" class="form-control" id="pass" name="pass" 
                                           minlength="6" maxlength="50">
                                    <div class="form-text">Mínimo 6 caracteres</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="pass2" class="form-label">
                                        <i class="fas fa-key me-1"></i>Confirmar Contraseña
                                    </label>
                                    <input type="password" class="form-control" id="pass2" name="pass2" 
                                           minlength="6" maxlength="50">
                                    <div class="invalid-feedback" id="passError">
                                        Las contraseñas no coinciden.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <a href="addUser.php" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Volver
                                    </a>
                                    <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                        <i class="fas fa-save me-2"></i>Modificar Cliente
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('modifyForm');
    const pass = document.getElementById('pass');
    const pass2 = document.getElementById('pass2');
    const submitBtn = document.getElementById('submitBtn');
    
    // Validación de contraseñas
    function validatePasswords() {
        const password1 = pass.value;
        const password2 = pass2.value;
        
        if (password1 || password2) {
            if (password1 !== password2) {
                pass2.setCustomValidity('Las contraseñas no coinciden');
                pass2.classList.add('is-invalid');
                return false;
            } else if (password1.length < 6) {
                pass.setCustomValidity('La contraseña debe tener al menos 6 caracteres');
                pass.classList.add('is-invalid');
                return false;
            } else {
                pass.setCustomValidity('');
                pass2.setCustomValidity('');
                pass.classList.remove('is-invalid');
                pass2.classList.remove('is-invalid');
                pass.classList.add('is-valid');
                pass2.classList.add('is-valid');
                return true;
            }
        } else {
            pass.setCustomValidity('');
            pass2.setCustomValidity('');
            pass.classList.remove('is-invalid', 'is-valid');
            pass2.classList.remove('is-invalid', 'is-valid');
            return true;
        }
    }
    
    pass.addEventListener('input', validatePasswords);
    pass2.addEventListener('input', validatePasswords);
    
    // Validación del formulario
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!validatePasswords()) {
            return false;
        }
        
        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            return false;
        }
        
        // Deshabilitar botón durante el envío
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Modificando...';
        
        form.submit();
    });
    
    // Validación en tiempo real
    const inputs = form.querySelectorAll('input[required], textarea[required]');
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            if (this.checkValidity()) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else {
                this.classList.remove('is-valid');
                this.classList.add('is-invalid');
            }
        });
    });
});
</script>

<?php include "includes/footer.html"; ?>