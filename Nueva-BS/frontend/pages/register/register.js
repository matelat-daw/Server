// Register Page
const registerPage = {
    init() {
        // Redirigir si ya está autenticado
        if (AuthService.isAuthenticated()) {
            window.location.href = '/Nueva-BS/#/home';
            return;
        }

        this.render();
        this.setupForm();
    },

    render() {
        const container = document.getElementById('main-content');
        if (!container) return;

        container.innerHTML = `
            <div class="container my-5">
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-lg">
                            <div class="card-body p-5">
                                <div class="text-center mb-4">
                                    <i class="fas fa-user-plus fa-4x text-primary mb-3"></i>
                                    <h2 class="fw-bold">Crear Cuenta</h2>
                                    <p class="text-muted">Únete a TechStore hoy mismo</p>
                                </div>

                                <form id="register-form">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="first_name" class="form-label">
                                                <i class="fas fa-user me-2"></i>Nombre
                                            </label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="first_name"
                                                   name="first_name"
                                                   placeholder="Juan"
                                                   required>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="last_name" class="form-label">
                                                <i class="fas fa-user me-2"></i>Apellidos
                                            </label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="last_name"
                                                   name="last_name"
                                                   placeholder="Pérez"
                                                   required>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="username" class="form-label">
                                            <i class="fas fa-user-circle me-2"></i>Nombre de Usuario <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="username"
                                               name="username"
                                               placeholder="juanperez"
                                               minlength="3"
                                               pattern="[a-zA-Z0-9_]+"
                                               title="Solo letras, números y guión bajo. Mínimo 3 caracteres"
                                               required>
                                        <div class="invalid-feedback">
                                            El nombre de usuario debe tener al menos 3 caracteres (solo letras, números y guión bajo)
                                        </div>
                                        <div class="valid-feedback">
                                            ¡Nombre de usuario válido!
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="email" class="form-label">
                                            <i class="fas fa-envelope me-2"></i>Email <span class="text-danger">*</span>
                                        </label>
                                        <input type="email" 
                                               class="form-control" 
                                               id="email"
                                               name="email"
                                               placeholder="tu@email.com"
                                               required>
                                        <div class="invalid-feedback">
                                            Por favor ingresa un email válido
                                        </div>
                                        <div class="valid-feedback">
                                            ¡Email válido!
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="gender" class="form-label">
                                            <i class="fas fa-venus-mars me-2"></i>Género
                                        </label>
                                        <select class="form-select" id="gender" name="gender">
                                            <option value="other">Prefiero no decirlo</option>
                                            <option value="male">Masculino</option>
                                            <option value="female">Femenino</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="password" class="form-label">
                                            <i class="fas fa-lock me-2"></i>Contraseña
                                        </label>
                                        <input type="password" 
                                               class="form-control" 
                                               id="password"
                                               name="password"
                                               placeholder="••••••••"
                                               required>
                                        <div class="form-text">Mínimo 8 caracteres</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="confirm-password" class="form-label">
                                            <i class="fas fa-lock me-2"></i>Confirmar Contraseña
                                        </label>
                                        <input type="password" 
                                               class="form-control" 
                                               id="confirm-password"
                                               name="confirm-password"
                                               placeholder="••••••••"
                                               required>
                                    </div>

                                    <div class="mb-3 form-check">
                                        <input type="checkbox" class="form-check-input" id="terms" required>
                                        <label class="form-check-label" for="terms">
                                            Acepto los <a href="#" class="text-primary">términos y condiciones</a>
                                        </label>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100 mb-3">
                                        <i class="fas fa-user-plus me-2"></i>Crear Cuenta
                                    </button>

                                    <div class="text-center">
                                        <p class="text-muted mb-0">
                                            ¿Ya tienes cuenta? 
                                            <a href="/Nueva-BS/#/login" class="text-primary fw-bold">
                                                Inicia Sesión
                                            </a>
                                        </p>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    },

    setupForm() {
        const form = document.getElementById('register-form');
        if (!form) return;

        // Validación en tiempo real de contraseñas
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm-password');

        password.addEventListener('input', () => {
            this.validatePasswordStrength(password);
            if (confirmPassword.value) {
                this.validatePasswordMatch(password, confirmPassword);
            }
        });

        confirmPassword.addEventListener('input', () => {
            this.validatePasswordMatch(password, confirmPassword);
        });

        // Validación de username
        const username = document.getElementById('username');
        username.addEventListener('input', () => {
            this.validateUsername(username);
        });

        // Validación de email
        const email = document.getElementById('email');
        email.addEventListener('blur', () => {
            this.validateEmail(email);
        });

        form.addEventListener('submit', (e) => this.handleSubmit(e));
    },

    validateUsername(input) {
        const value = input.value.trim();
        const feedback = input.parentElement.querySelector('.invalid-feedback') || document.createElement('div');
        
        if (value.length < 3) {
            input.classList.add('is-invalid');
            input.classList.remove('is-valid');
            feedback.className = 'invalid-feedback d-block';
            feedback.textContent = 'El nombre de usuario debe tener al menos 3 caracteres';
            if (!input.parentElement.querySelector('.invalid-feedback')) {
                input.parentElement.appendChild(feedback);
            }
            return false;
        } else if (!/^[a-zA-Z0-9_]+$/.test(value)) {
            input.classList.add('is-invalid');
            input.classList.remove('is-valid');
            feedback.className = 'invalid-feedback d-block';
            feedback.textContent = 'Solo letras, números y guión bajo';
            if (!input.parentElement.querySelector('.invalid-feedback')) {
                input.parentElement.appendChild(feedback);
            }
            return false;
        } else {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
            if (input.parentElement.querySelector('.invalid-feedback')) {
                input.parentElement.querySelector('.invalid-feedback').remove();
            }
            return true;
        }
    },

    validateEmail(input) {
        const value = input.value.trim();
        const feedback = input.parentElement.querySelector('.invalid-feedback') || document.createElement('div');
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (!emailRegex.test(value)) {
            input.classList.add('is-invalid');
            input.classList.remove('is-valid');
            feedback.className = 'invalid-feedback d-block';
            feedback.textContent = 'Introduce un email válido';
            if (!input.parentElement.querySelector('.invalid-feedback')) {
                input.parentElement.appendChild(feedback);
            }
            return false;
        } else {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
            if (input.parentElement.querySelector('.invalid-feedback')) {
                input.parentElement.querySelector('.invalid-feedback').remove();
            }
            return true;
        }
    },

    validatePasswordStrength(input) {
        const value = input.value;
        const feedback = input.parentElement.querySelector('.form-text');
        
        if (value.length === 0) {
            input.classList.remove('is-invalid', 'is-valid');
            feedback.textContent = 'Mínimo 8 caracteres';
            feedback.className = 'form-text';
            return false;
        } else if (value.length < 8) {
            input.classList.add('is-invalid');
            input.classList.remove('is-valid');
            feedback.textContent = '⚠️ Demasiado corta (mínimo 8 caracteres)';
            feedback.className = 'form-text text-danger';
            return false;
        } else if (value.length < 12) {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
            feedback.textContent = '✓ Contraseña aceptable';
            feedback.className = 'form-text text-warning';
            return true;
        } else {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
            feedback.textContent = '✓ Contraseña fuerte';
            feedback.className = 'form-text text-success';
            return true;
        }
    },

    validatePasswordMatch(password, confirmPassword) {
        const feedback = confirmPassword.parentElement.querySelector('.invalid-feedback') || document.createElement('div');
        
        if (confirmPassword.value.length === 0) {
            confirmPassword.classList.remove('is-invalid', 'is-valid');
            if (confirmPassword.parentElement.querySelector('.invalid-feedback')) {
                confirmPassword.parentElement.querySelector('.invalid-feedback').remove();
            }
            return false;
        } else if (password.value !== confirmPassword.value) {
            confirmPassword.classList.add('is-invalid');
            confirmPassword.classList.remove('is-valid');
            feedback.className = 'invalid-feedback d-block';
            feedback.textContent = '❌ Las contraseñas no coinciden';
            if (!confirmPassword.parentElement.querySelector('.invalid-feedback')) {
                confirmPassword.parentElement.appendChild(feedback);
            }
            return false;
        } else {
            confirmPassword.classList.remove('is-invalid');
            confirmPassword.classList.add('is-valid');
            feedback.className = 'valid-feedback d-block';
            feedback.textContent = '✓ Las contraseñas coinciden';
            if (!confirmPassword.parentElement.querySelector('.invalid-feedback')) {
                confirmPassword.parentElement.appendChild(feedback);
            } else {
                confirmPassword.parentElement.querySelector('.invalid-feedback').className = 'valid-feedback d-block';
                confirmPassword.parentElement.querySelector('.valid-feedback').textContent = '✓ Las contraseñas coinciden';
            }
            return true;
        }
    },

    async handleSubmit(e) {
        e.preventDefault();

        // Obtener valores
        const username = document.getElementById('username').value.trim();
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm-password').value;
        const firstName = document.getElementById('first_name').value.trim();
        const lastName = document.getElementById('last_name').value.trim();

        // Debug: verificar que todos los campos existen y tienen valores
        console.log('Valores del formulario:', {
            username,
            email,
            password: password ? '***' : '',
            firstName,
            lastName
        });

        // Validar todos los campos
        let isValid = true;
        const errors = [];

        if (!firstName) {
            errors.push('El nombre es requerido');
            isValid = false;
        }

        if (!lastName) {
            errors.push('Los apellidos son requeridos');
            isValid = false;
        }

        if (!this.validateUsername(document.getElementById('username'))) {
            errors.push('Nombre de usuario inválido');
            isValid = false;
        }

        if (!this.validateEmail(document.getElementById('email'))) {
            errors.push('Email inválido');
            isValid = false;
        }

        if (password.length < 8) {
            errors.push('La contraseña debe tener al menos 8 caracteres');
            isValid = false;
        }

        if (password !== confirmPassword) {
            errors.push('Las contraseñas no coinciden');
            isValid = false;
        }

        if (!document.getElementById('terms').checked) {
            errors.push('Debes aceptar los términos y condiciones');
            isValid = false;
        }

        if (!isValid) {
            window.showModal(
                '<strong>Por favor corrige los siguientes errores:</strong><br><br>' + 
                errors.map(err => '• ' + err).join('<br>'),
                'error'
            );
            return;
        }

        const userData = {
            username: username,
            email: email,
            password: password,
            first_name: firstName,
            last_name: lastName,
            gender: document.getElementById('gender').value
        };

        try {
            const response = await AuthService.register(userData);

            if (response.success) {
                window.showModal(
                    '¡Cuenta creada con éxito! Revisa tu correo para activar tu cuenta.', 
                    'success',
                    () => {
                        window.location.href = '/Nueva-BS/#/login';
                    }
                );
            } else {
                window.showModal(response.message || 'Error al crear la cuenta', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            window.showModal('Error al crear la cuenta. Inténtalo de nuevo.', 'error');
        }
    }
};

window.registerPage = registerPage;
