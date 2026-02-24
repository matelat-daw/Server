// Profile Page
var profilePage = {
    currentUser: null,
    
    init: function() {
        console.log('👤 Página de perfil cargada');
        
        // Verificar si está logueado
        if (!window.authService || !window.authService.isLoggedIn()) {
            window.app.loadPage('login');
            return;
        }
        
        this.currentUser = window.authService.getUser();
        this.loadUserData();
        this.setupForms();
    },
    
    loadUserData: function() {
        if (!this.currentUser) return;
        
        // Llenar formulario con datos del usuario
        document.getElementById('profile-first-name').value = this.currentUser.first_name || '';
        document.getElementById('profile-last-name').value = this.currentUser.last_name || '';
        document.getElementById('profile-second-last-name').value = this.currentUser.second_last_name || '';
        document.getElementById('profile-username').value = this.currentUser.username || '';
        document.getElementById('profile-email').value = this.currentUser.email || '';
        document.getElementById('profile-phone').value = this.currentUser.phone || '';
        
        // Mostrar rol
        var roleBadge = document.getElementById('user-role-badge');
        var roleText = this.currentUser.roles && this.currentUser.roles[0] || 'usuario';
        var roleEmoji = roleText === 'admin' ? '👑' : roleText === 'seller' ? '💼' : '👤';
        roleBadge.textContent = roleEmoji + ' ' + roleText.charAt(0).toUpperCase() + roleText.slice(1);
    },
    
    setupForms: function() {
        this.setupProfileForm();
        this.setupPasswordForm();
    },
    
    setupProfileForm: function() {
        var form = document.getElementById('profile-form');
        var messageDiv = document.getElementById('profile-message');
        var saveBtn = document.getElementById('save-btn');
        
        if (!form) return;
        
        var self = this;
        
        form.onsubmit = function(e) {
            e.preventDefault();
            
            var formData = {
                first_name: document.getElementById('profile-first-name').value.trim(),
                last_name: document.getElementById('profile-last-name').value.trim(),
                second_last_name: document.getElementById('profile-second-last-name').value.trim() || null,
                username: document.getElementById('profile-username').value.trim(),
                email: document.getElementById('profile-email').value.trim(),
                phone: document.getElementById('profile-phone').value.trim() || null
            };
            
            messageDiv.style.display = 'none';
            saveBtn.classList.add('loading');
            saveBtn.disabled = true;
            
            // Actualizar perfil
            window.apiService.put('/auth/profile', formData)
                .then(function(response) {
                    console.log('✓ Perfil actualizado:', response);
                    
                    // Actualizar datos en authService y localStorage
                    if (response.data) {
                        window.authService.user = response.data;
                        localStorage.setItem('user', JSON.stringify(response.data));
                        self.currentUser = response.data;
                    }
                    
                    // Mostrar éxito
                    messageDiv.className = 'message success';
                    messageDiv.textContent = '✓ Perfil actualizado exitosamente';
                    messageDiv.style.display = 'block';
                    
                    // Actualizar header
                    if (window.headerComponent) {
                        window.headerComponent.updateUserMenu();
                    }
                    
                    saveBtn.classList.remove('loading');
                    saveBtn.disabled = false;
                    
                    // Ocultar mensaje después de 3 segundos
                    setTimeout(function() {
                        messageDiv.style.display = 'none';
                    }, 3000);
                })
                .catch(function(error) {
                    console.error('✗ Error actualizando perfil:', error);
                    
                    messageDiv.className = 'message error';
                    messageDiv.textContent = error.message || 'Error al actualizar el perfil';
                    messageDiv.style.display = 'block';
                    
                    saveBtn.classList.remove('loading');
                    saveBtn.disabled = false;
                });
        };
    },
    
    setupPasswordForm: function() {
        var form = document.getElementById('password-form');
        var messageDiv = document.getElementById('password-message');
        var passwordBtn = document.getElementById('password-btn');
        
        if (!form) return;
        
        form.onsubmit = function(e) {
            e.preventDefault();
            
            var currentPassword = document.getElementById('current-password').value;
            var newPassword = document.getElementById('new-password').value;
            var confirmPassword = document.getElementById('confirm-password').value;
            
            // Validar que las contraseñas coincidan
            if (newPassword !== confirmPassword) {
                messageDiv.className = 'message error';
                messageDiv.textContent = 'Las contraseñas no coinciden';
                messageDiv.style.display = 'block';
                return;
            }
            
            if (newPassword.length < 6) {
                messageDiv.className = 'message error';
                messageDiv.textContent = 'La contraseña debe tener al menos 6 caracteres';
                messageDiv.style.display = 'block';
                return;
            }
            
            messageDiv.style.display = 'none';
            passwordBtn.classList.add('loading');
            passwordBtn.disabled = true;
            
            // Cambiar contraseña
            var userId = self.currentUser.id;
            window.apiService.put('/users/' + userId + '/password', {
                current_password: currentPassword,
                new_password: newPassword
            })
                .then(function(response) {
                    console.log('✓ Contraseña actualizada');
                    
                    messageDiv.className = 'message success';
                    messageDiv.textContent = '✓ Contraseña actualizada exitosamente';
                    messageDiv.style.display = 'block';
                    
                    // Limpiar formulario
                    form.reset();
                    
                    passwordBtn.classList.remove('loading');
                    passwordBtn.disabled = false;
                    
                    setTimeout(function() {
                        messageDiv.style.display = 'none';
                    }, 3000);
                })
                .catch(function(error) {
                    console.error('✗ Error cambiando contraseña:', error);
                    
                    messageDiv.className = 'message error';
                    messageDiv.textContent = error.message || 'Error al cambiar la contraseña';
                    messageDiv.style.display = 'block';
                    
                    passwordBtn.classList.remove('loading');
                    passwordBtn.disabled = false;
                });
        };
    },
    
    switchTab: function(tabName) {
        // Desactivar todos los tabs
        var tabBtns = document.querySelectorAll('.tab-btn');
        var tabContents = document.querySelectorAll('.tab-content');
        
        tabBtns.forEach(function(btn) { btn.classList.remove('active'); });
        tabContents.forEach(function(content) { content.classList.remove('active'); });
        
        // Activar tab seleccionado
        event.target.classList.add('active');
        document.getElementById('tab-' + tabName).classList.add('active');
    },
    
    confirmDelete: function() {
        document.getElementById('delete-modal').style.display = 'flex';
    },
    
    closeDeleteModal: function() {
        document.getElementById('delete-modal').style.display = 'none';
    },
    
    deleteAccount: function() {
        var userId = this.currentUser.id;
        
        window.apiService.delete('/users/' + userId)
            .then(function(response) {
                console.log('✓ Cuenta eliminada');
                
                // Cerrar sesión
                window.authService.logout();
                
                // Redirigir a home
                window.app.loadPage('home');
                
                alert('Tu cuenta ha sido eliminada exitosamente');
            })
            .catch(function(error) {
                console.error('✗ Error eliminando cuenta:', error);
                alert('Error al eliminar la cuenta: ' + (error.message || 'Error desconocido'));
            });
    }
};

window.profilePage = profilePage;
