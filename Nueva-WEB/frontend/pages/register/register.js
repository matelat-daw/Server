
// Utility functions
function showError(input, message) {
    if (input.classList) input.classList.add('input-error');
    var msg = document.createElement('div');
    msg.className = 'error-message';
    msg.textContent = message;
    if (input.parentNode && input.parentNode.insertBefore) {
        input.parentNode.insertBefore(msg, input.nextSibling);
    } else if (input.appendChild) {
        input.appendChild(msg);
    } else {
        document.body.appendChild(msg);
    }
}

// Solo lógica de validación y submit del formulario
function initRegisterForm() {
    var form = document.getElementById('register-form');
    if (!form) return;
    if (form.dataset.bound === '1') return; // Evitar doble binding

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        // Get fields
        var firstName = document.getElementById('first-name');
        var lastName = document.getElementById('last-name');
        var username = document.getElementById('username');
        var email = document.getElementById('email');
        var password = document.getElementById('password');
        var confirm = document.getElementById('confirm-password');
        var gender = form.querySelector('input[name="gender"]:checked');

        // Validar que todos los campos existen
        if (!firstName || !lastName || !username || !email || !password || !confirm) {

            showModal('Error: El formulario no se cargó correctamente. Por favor, recarga la página.', 'error');
            return;
        }

        // Remove previous errors
        form.querySelectorAll('.input-error').forEach(el => el.classList.remove('input-error'));
        form.querySelectorAll('.error-message').forEach(el => el.remove());

        let valid = true;

        // First name required
        if (!firstName.value.trim()) {
            showError(firstName, 'El nombre es requerido');
            valid = false;
        }
        // Last name required
        if (!lastName.value.trim()) {
            showError(lastName, 'Los apellidos son requeridos');
            valid = false;
        }
        // Username required
        if (!username.value.trim()) {
            showError(username, 'Username is required');
            valid = false;
        }
        // Email required and valid
        if (!email.value.trim()) {
            showError(email, 'Email is required');
            valid = false;
        } else if (!/^\S+@\S+\.\S+$/.test(email.value)) {
            showError(email, 'Enter a valid email address');
            valid = false;
        }
        // Gender required
        if (!gender) {
            showError(form.querySelector('#gender-group'), 'Selecciona un género');
            valid = false;
        }
        // Password required
        if (!password.value) {
            showError(password, 'Password is required');
            valid = false;
        }
        // Confirm password required
        if (!confirm.value) {
            showError(confirm, 'Please confirm your password');
            valid = false;
        }
        // Passwords match
        if (password.value && confirm.value && password.value !== confirm.value) {
            showModal('Passwords do not match', 'error');
            showError(confirm, 'Passwords do not match');
            valid = false;
        }

        if (!valid) return;

        // Call backend
        var result = await AuthService.register({
            first_name: firstName.value.trim(),
            last_name: lastName.value.trim(),
            username: username.value.trim(),
            email: email.value.trim(),
            password: password.value,
            gender: gender ? gender.value : 'other'
        });
        if (result.success) {
            // Mostrar modal de confirmación de email
            if (result.user && result.user.requiresActivation) {
                showActivationModal(result.user.email, function() {
                    if (window.app && typeof window.app.navigate === 'function') {
                        window.app.navigate('home');
                    } else {
                        window.location.hash = '#home';
                    }
                });
            } else {
                // Legacy: si no requiere activación (usuarios antiguos)
                showModal('¡Registro exitoso! Ahora puedes iniciar sesión desde Inicio.', 'success', function() {
                    if (window.app && typeof window.app.navigate === 'function') {
                        window.app.navigate('home');
                    } else {
                        window.location.hash = '#home';
                    }
                });
            }
        } else {
            showModal(result.message || 'Registration failed', 'error');
        }
    });
    form.dataset.bound = '1';
}

// NO ejecutar init automáticamente al cargar el script
// Solo cuando se navega a la página de registro (app.js llama a init)

// Exponer como registerPage para compatibilidad con app.js
window.registerPage = {
    init: initRegisterForm
};

// Modal de activación por email
function showActivationModal(email, onClose) {
    // Eliminar modal existente si hay
    var existingModal = document.getElementById('activation-modal');
    if (existingModal) {
        existingModal.remove();
    }
    
    var modal = document.createElement('div');
    modal.id = 'activation-modal';
    modal.className = 'activation-modal-overlay';
    modal.innerHTML = `
        <div class="activation-modal-content">
            <div class="activation-modal-header">
                <div class="activation-icon">📧</div>
                <h2>¡Revisa tu Correo Electrónico!</h2>
            </div>
            <div class="activation-modal-body">
                <p class="activation-email">Hemos enviado un correo de activación a:</p>
                <p class="activation-email-address">${email}</p>
                <p class="activation-instructions">
                    Por favor, revisa tu bandeja de entrada y haz clic en el enlace de activación 
                    para completar tu registro y poder iniciar sesión.
                </p>
                <div class="activation-warning">
                    <span class="warning-icon">⚠️</span>
                    <p>El enlace expirará en 24 horas. Si no ves el correo, revisa tu carpeta de spam.</p>
                </div>
            </div>
            <div class="activation-modal-footer">
                <button class="activation-btn-primary" id="activation-modal-close">Entendido</button>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
    
    // Cerrar solo al pulsar el botón
    modal.querySelector('#activation-modal-close').onclick = function() {
        closeActivationModal(onClose);
    };
    
    // Cerrar al hacer clic fuera del modal
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeActivationModal(onClose);
        }
    });
}

function closeActivationModal(onClose) {
    var modal = document.getElementById('activation-modal');
    if (modal) {
        modal.style.opacity = '0';
        setTimeout(function() {
            modal.remove();
            if (typeof onClose === 'function') onClose();
        }, 300);
    }
}
