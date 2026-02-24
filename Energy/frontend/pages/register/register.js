// Register Page
var registerPage = {
    init: function() {
        console.log('📝 Página de registro cargada');
        this.setupForm();
        this.setupPasswordToggles();
        
        // Si ya está logueado, redirigir a perfil
        if (window.authService && window.authService.isLoggedIn()) {
            window.app.loadPage('profile');
        }
    },
    
    setupPasswordToggles: function() {
        var self = this;
        var toggleButtons = [
            { btn: 'toggle-password-1', input: 'reg-password' },
            { btn: 'toggle-password-2', input: 'reg-password-confirm' }
        ];
        
        toggleButtons.forEach(function(item) {
            var toggleBtn = document.getElementById(item.btn);
            var passwordInput = document.getElementById(item.input);
            
            if (!toggleBtn || !passwordInput) return;
            
            toggleBtn.onclick = function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                var type = passwordInput.type === 'password' ? 'text' : 'password';
                passwordInput.type = type;
                
                var eyeIcon = toggleBtn.querySelector('.eye-icon');
                if (type === 'text') {
                    eyeIcon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
                } else {
                    eyeIcon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
                }
            };
        });
    },
    
    setupForm: function() {
        var form = document.getElementById('register-form');
        var errorDiv = document.getElementById('register-error');
        var registerBtn = document.getElementById('register-btn');
        
        if (!form) return;
        
        form.onsubmit = function(e) {
            e.preventDefault();
            
            var firstName = document.getElementById('reg-first-name').value.trim();
            var lastName = document.getElementById('reg-last-name').value.trim();
            var secondLastName = document.getElementById('reg-second-last-name').value.trim();
            var password = document.getElementById('reg-password').value;
            var passwordConfirm = document.getElementById('reg-password-confirm').value;
            
            var formData = {
                first_name: firstName,
                last_name: lastName,
                second_last_name: secondLastName || null,
                email: document.getElementById('reg-email').value.trim(),
                username: document.getElementById('reg-username').value.trim(),
                password: password,
                phone: document.getElementById('reg-phone').value.trim() || null,
                role: document.getElementById('reg-role').value
            };
            
            // Validación de contraseña
            if (password.length < 6) {
                errorDiv.className = 'error-message';
                errorDiv.textContent = 'La contraseña debe tener al menos 6 caracteres';
                errorDiv.style.display = 'block';
                return;
            }
            
            // Validación de confirmación de contraseña
            if (password !== passwordConfirm) {
                errorDiv.className = 'error-message';
                errorDiv.textContent = 'Las contraseñas no coinciden';
                errorDiv.style.display = 'block';
                return;
            }
            
            // Ocultar error previo
            errorDiv.style.display = 'none';
            
            // Loading state
            registerBtn.classList.add('loading');
            registerBtn.disabled = true;
            
            // Llamar al servicio de autenticación
            window.authService.register(formData)
                .then(function(response) {
                    console.log('✓ Registro exitoso:', response);
                    
                    // Verificar si requiere activación por email
                    if (response.requiresActivation) {
                        errorDiv.className = 'success-message';
                        errorDiv.innerHTML = 
                            '✓ ¡Cuenta creada exitosamente!<br>' +
                            '<strong>Por favor revisa tu email para activar tu cuenta.</strong><br>' +
                            'Enviamos un enlace de activación a tu correo electrónico.';
                        errorDiv.style.display = 'block';
                        
                        // Limpiar formulario
                        form.reset();
                        
                        // Redirigir a home después de 3 segundos
                        setTimeout(function() {
                            window.app.loadPage('home');
                        }, 3000);
                    } else {
                        // Si no requiere activación, redirigir al perfil
                        errorDiv.className = 'success-message';
                        errorDiv.textContent = '✓ ¡Cuenta creada exitosamente! Redirigiendo a tu perfil...';
                        errorDiv.style.display = 'block';
                        
                        setTimeout(function() {
                            window.app.loadPage('profile');
                        }, 1500);
                    }
                })
                .catch(function(error) {
                    console.error('✗ Error en registro:', error);
                    
                    // Mostrar error
                    errorDiv.className = 'error-message';
                    errorDiv.textContent = error.message || 'Error al crear la cuenta. Por favor intenta de nuevo.';
                    errorDiv.style.display = 'block';
                    
                    // Remover loading state
                    registerBtn.classList.remove('loading');
                    registerBtn.disabled = false;
                });
        };
    }
};

window.registerPage = registerPage;
