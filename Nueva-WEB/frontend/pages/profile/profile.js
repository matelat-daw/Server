// profile.js - Lógica de perfil de usuario
(function() {
    function validateProfileForm(form) {
        let valid = true;
        let username = form.username;
        let email = form.email;
        let password = form.password;
        let confirm = form['confirm-password'];
        // Limpiar errores
        form.querySelectorAll('.input-error').forEach(el => el.classList.remove('input-error'));
        form.querySelectorAll('.error-message').forEach(el => el.remove());
        // Validaciones
        if (!username.value.trim()) {
            showError(username, 'El nombre de usuario es obligatorio');
            valid = false;
        }
        if (!email.value.trim()) {
            showError(email, 'El email es obligatorio');
            valid = false;
        } else if (!/^\S+@\S+\.\S+$/.test(email.value)) {
            showError(email, 'Introduce un email válido');
            valid = false;
        }
        if (password.value || confirm.value) {
            if (password.value.length < 8) {
                showError(password, 'La contraseña debe tener al menos 8 caracteres');
                valid = false;
            }
            if (password.value !== confirm.value) {
                showError(confirm, 'Las contraseñas no coinciden');
                valid = false;
            }
        }
        return valid;
    }

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

    window.profilePage = {
        init: function() {
            const form = document.getElementById('profile-form');
            if (!form) return;
            // Cargar datos actuales
            const user = AuthService.getCurrentUser();
            if (user) {
                form.username.value = user.username;
                form.email.value = user.email;
            }
            // Guardar cambios
            form.onsubmit = async function(e) {
                e.preventDefault();
                if (!validateProfileForm(form)) return;
                const formData = new FormData(form);
                // Si la contraseña está vacía, no la envíes
                if (!form.password.value) {
                    formData.delete('password');
                    formData.delete('confirm-password');
                }
                try {
                    const response = await fetch('/Nueva-WEB/api/users/' + user.id, {
                        method: 'PUT',
                        body: formData,
                        credentials: 'include'
                    });
                    const data = await response.json();
                    if (data.success) {
                        showModal('Perfil actualizado correctamente', 'success');
                        AuthService.setUser(data.user);
                    } else {
                        showModal(data.message || 'Error al actualizar el perfil', 'error');
                    }
                } catch (err) {
                    showModal('Error de conexión', 'error');
                }
            };
            // Eliminar cuenta
            document.getElementById('delete-profile-btn').onclick = function() {
                document.getElementById('delete-profile-modal').style.display = 'flex';
            };
            document.getElementById('cancel-delete-profile').onclick = function() {
                document.getElementById('delete-profile-modal').style.display = 'none';
            };
            document.getElementById('confirm-delete-profile').onclick = async function() {
                try {
                    const response = await fetch('/Nueva-WEB/api/users/' + user.id, {
                        method: 'DELETE',
                        credentials: 'include'
                    });
                    const data = await response.json();
                    if (data.success) {
                        showModal('Cuenta eliminada', 'success');
                        AuthService.logout();
                    } else {
                        showModal(data.message || 'No se pudo eliminar la cuenta', 'error');
                    }
                } catch (err) {
                    showModal('Error de conexión', 'error');
                }
            };
        }
    };
})();

