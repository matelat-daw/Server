// Login Page
const loginPage = {
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
                    <div class="col-md-5">
                        <div class="card border-0 shadow-lg">
                            <div class="card-body p-5">
                                <div class="text-center mb-4">
                                    <i class="fas fa-user-circle fa-4x text-primary mb-3"></i>
                                    <h2 class="fw-bold">Iniciar Sesión</h2>
                                    <p class="text-muted">Accede a tu cuenta de TechStore</p>
                                </div>

                                <form id="login-form">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">
                                            <i class="fas fa-envelope me-2"></i>Email
                                        </label>
                                        <input type="email" 
                                               class="form-control" 
                                               id="email" 
                                               placeholder="tu@email.com"
                                               required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="password" class="form-label">
                                            <i class="fas fa-lock me-2"></i>Contraseña
                                        </label>
                                        <input type="password" 
                                               class="form-control" 
                                               id="password" 
                                               placeholder="••••••••"
                                               required>
                                    </div>

                                    <div class="mb-3 form-check">
                                        <input type="checkbox" class="form-check-input" id="remember">
                                        <label class="form-check-label" for="remember">
                                            Recordarme
                                        </label>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100 mb-3">
                                        <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                                    </button>

                                    <div class="text-center">
                                        <a href="/Nueva-BS/#/forgot-password" class="text-muted small">
                                            ¿Olvidaste tu contraseña?
                                        </a>
                                    </div>

                                    <hr class="my-4">

                                    <div class="text-center">
                                        <p class="text-muted mb-2">¿No tienes cuenta?</p>
                                        <a href="/Nueva-BS/#/register" class="btn btn-outline-primary w-100">
                                            <i class="fas fa-user-plus me-2"></i>Crear Cuenta
                                        </a>
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
        const form = document.getElementById('login-form');
        if (form) {
            form.addEventListener('submit', (e) => this.handleSubmit(e));
        }
    },

    async handleSubmit(e) {
        e.preventDefault();

        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        try {
            const response = await AuthService.login(email, password);

            if (response.success) {
                window.showModal('¡Bienvenido de vuelta!', 'success', () => {
                    window.location.href = '/Nueva-BS/#/home';
                });
            } else {
                window.showModal(response.message || 'Credenciales incorrectas', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            window.showModal('Error al iniciar sesión. Inténtalo de nuevo.', 'error');
        }
    }
};

window.loginPage = loginPage;
