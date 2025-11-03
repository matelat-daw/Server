// Profile Page - Perfil de usuario
const profilePage = {
    async init() {
        // Verificar autenticación
        if (!AuthService.isAuthenticated()) {
            window.location.href = '/Nueva-BS/#/login';
            return;
        }

        this.render();
        this.loadUserData();
        this.setupForm();
        this.setupImagePreview();
    },

    render() {
        const container = document.getElementById('main-content');
        if (!container) return;

        container.innerHTML = `
            <div class="bg-primary text-white py-4">
                <div class="container">
                    <h2 class="display-6 fw-bold mb-0">
                        <i class="fas fa-user-circle me-2"></i>Mi Perfil
                    </h2>
                </div>
            </div>

            <div class="container my-5">
                <div class="row">
                    <div class="col-lg-4 mb-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center">
                                <div class="mb-3 position-relative d-inline-block">
                                    <img id="profile-image-preview" 
                                         src="https://ui-avatars.com/api/?name=User&size=200&background=0d6efd&color=fff" 
                                         class="rounded-circle" 
                                         style="width: 150px; height: 150px; object-fit: cover;"
                                         alt="Foto de perfil">
                                    <label for="profile-image-input" 
                                           class="position-absolute bottom-0 end-0 btn btn-primary btn-sm rounded-circle"
                                           style="width: 40px; height: 40px; cursor: pointer;"
                                           title="Cambiar foto">
                                        <i class="fas fa-camera"></i>
                                    </label>
                                    <input type="file" 
                                           id="profile-image-input" 
                                           accept="image/*" 
                                           style="display: none;">
                                </div>
                                <h5 class="card-title mb-1" id="profile-name">Usuario</h5>
                                <p class="text-muted small mb-3" id="profile-email-display">email@example.com</p>
                                <span class="badge bg-primary">
                                    <i class="fas fa-user me-1"></i>Usuario
                                </span>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm mt-3">
                            <div class="card-body">
                                <h6 class="card-title mb-3">
                                    <i class="fas fa-info-circle me-2 text-primary"></i>Información de Cuenta
                                </h6>
                                <p class="small mb-2">
                                    <strong>Usuario desde:</strong><br>
                                    <span id="member-since">-</span>
                                </p>
                                <p class="small mb-0">
                                    <strong>Estado:</strong><br>
                                    <span class="badge bg-success">Activo</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <h5 class="card-title mb-4">
                                    <i class="fas fa-edit me-2 text-primary"></i>Editar Información
                                </h5>

                                <form id="profile-form">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="first_name" class="form-label">Nombre *</label>
                                            <input type="text" class="form-control" id="first_name" name="first_name" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="last_name" class="form-label">Apellidos *</label>
                                            <input type="text" class="form-control" id="last_name" name="last_name" required>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="username" class="form-label">Nombre de Usuario *</label>
                                        <input type="text" class="form-control" id="username" name="username" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email *</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Teléfono</label>
                                        <input type="tel" class="form-control" id="phone" name="phone">
                                    </div>

                                    <hr class="my-4">

                                    <h6 class="mb-3">
                                        <i class="fas fa-lock me-2 text-primary"></i>Cambiar Contraseña
                                    </h6>
                                    <p class="text-muted small">Deja estos campos en blanco si no deseas cambiar tu contraseña</p>

                                    <div class="mb-3">
                                        <label for="password" class="form-label">Nueva Contraseña</label>
                                        <input type="password" class="form-control" id="password" name="password" minlength="6">
                                        <div class="form-text">Mínimo 6 caracteres</div>
                                    </div>

                                    <div class="mb-4">
                                        <label for="confirm_password" class="form-label">Confirmar Nueva Contraseña</label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                                    </div>

                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="fas fa-save me-2"></i>Guardar Cambios
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" onclick="profilePage.confirmDeleteAccount()">
                                            <i class="fas fa-trash me-2"></i>Eliminar Cuenta
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    },

    loadUserData() {
        const user = AuthService.getCurrentUser();
        if (!user) return;

        // Actualizar preview de imagen
        if (user.profile_img) {
            document.getElementById('profile-image-preview').src = user.profile_img;
        } else {
            const displayName = user.first_name && user.last_name 
                ? `${user.first_name}+${user.last_name}` 
                : user.username || 'User';
            document.getElementById('profile-image-preview').src = 
                `https://ui-avatars.com/api/?name=${encodeURIComponent(displayName)}&size=200&background=0d6efd&color=fff`;
        }

        // Actualizar nombre y email en el card
        const displayName = user.first_name && user.last_name 
            ? `${user.first_name} ${user.last_name}` 
            : user.username;
        document.getElementById('profile-name').textContent = displayName;
        document.getElementById('profile-email-display').textContent = user.email;

        // Llenar formulario
        document.getElementById('first_name').value = user.first_name || '';
        document.getElementById('last_name').value = user.last_name || '';
        document.getElementById('username').value = user.username || '';
        document.getElementById('email').value = user.email || '';
        document.getElementById('phone').value = user.phone || '';

        // Fecha de registro (si existe)
        if (user.created_at) {
            const date = new Date(user.created_at);
            document.getElementById('member-since').textContent = date.toLocaleDateString('es-ES', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }
    },

    setupImagePreview() {
        const input = document.getElementById('profile-image-input');
        const preview = document.getElementById('profile-image-preview');

        input.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (event) => {
                    preview.src = event.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    },

    setupForm() {
        const form = document.getElementById('profile-form');
        if (!form) return;

        form.addEventListener('submit', (e) => this.handleSubmit(e));
    },

    async handleSubmit(e) {
        e.preventDefault();

        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;

        // Validar contraseñas si se proporcionan
        if (password || confirmPassword) {
            if (password !== confirmPassword) {
                window.showModal('Las contraseñas no coinciden', 'error');
                return;
            }
            if (password.length < 6) {
                window.showModal('La contraseña debe tener al menos 6 caracteres', 'error');
                return;
            }
        }

        const formData = new FormData();
        formData.append('first_name', document.getElementById('first_name').value);
        formData.append('last_name', document.getElementById('last_name').value);
        formData.append('username', document.getElementById('username').value);
        formData.append('email', document.getElementById('email').value);
        formData.append('phone', document.getElementById('phone').value);

        if (password) {
            formData.append('password', password);
        }

        // Agregar imagen si se seleccionó
        const imageInput = document.getElementById('profile-image-input');
        if (imageInput.files.length > 0) {
            formData.append('profile_image', imageInput.files[0]);
        }

        try {
            const user = AuthService.getCurrentUser();
            const response = await fetch(`/Nueva-BS/api/users/${user.id}`, {
                method: 'PUT',
                body: formData,
                credentials: 'include'
            });

            const data = await response.json();

            if (data.success) {
                // Actualizar usuario en localStorage
                AuthService.currentUser = data.user;
                localStorage.setItem('currentUser', JSON.stringify(data.user));
                
                // Emitir evento para actualizar header
                const event = new CustomEvent('userLoggedIn', { detail: data.user });
                document.dispatchEvent(event);

                window.showModal('Perfil actualizado correctamente', 'success', () => {
                    this.loadUserData();
                    // Limpiar campos de contraseña
                    document.getElementById('password').value = '';
                    document.getElementById('confirm_password').value = '';
                });
            } else {
                window.showModal(data.message || 'Error al actualizar el perfil', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            window.showModal('Error de conexión. Inténtalo de nuevo.', 'error');
        }
    },

    confirmDeleteAccount() {
        window.showConfirm(
            '¿Estás seguro de que deseas eliminar tu cuenta? Esta acción no se puede deshacer y perderás todos tus datos.',
            'Eliminar Cuenta',
            () => this.deleteAccount()
        );
    },

    async deleteAccount() {
        try {
            const user = AuthService.getCurrentUser();
            const response = await fetch(`/Nueva-BS/api/users/${user.id}`, {
                method: 'DELETE',
                credentials: 'include'
            });

            const data = await response.json();

            if (data.success) {
                window.showModal('Cuenta eliminada correctamente. Serás redirigido al inicio.', 'success', () => {
                    AuthService.logout();
                });
            } else {
                window.showModal(data.message || 'No se pudo eliminar la cuenta', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            window.showModal('Error de conexión. Inténtalo de nuevo.', 'error');
        }
    }
};

window.profilePage = profilePage;
