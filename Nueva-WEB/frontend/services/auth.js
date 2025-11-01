var AuthService = {
    currentUser: null,

    login: function(credentials) {
        var self = this;
        var payload = {};
        if (credentials.email) {
            payload.email = credentials.email;
        } else if (credentials.username) {
            payload.email = credentials.username;
        }
        payload.password = credentials.password;

        return ApiService.post('/login', payload)
            .then(function(response) {
                if (response && response.success && response.user) {
                    self.currentUser = response.user;
                    // Guardar usuario en localStorage
                    localStorage.setItem('currentUser', JSON.stringify(response.user));
                    var event = new CustomEvent('userLoggedIn', { detail: response.user });
                    document.dispatchEvent(event);
                    return { success: true, user: response.user };
                } else {
                    var message = (response && response.message) ? response.message : 'Error de conexión';
                    return { success: false, message: message };
                }
            })
            .catch(function(error) {
                return { success: false, message: 'Error durante el inicio de sesión' };
            });
    },

    register: function(userData) {
        // No iniciar sesión automáticamente tras el registro
        return ApiService.post('/register', userData)
            .then(function(response) {
                if (response && response.success) {
                    // No guardar usuario ni emitir userLoggedIn
                    return { success: true, user: response.user, message: response.message };
                } else {
                    return { success: false, message: response ? response.message : 'Error en el registro' };
                }
            })
            .catch(function(error) {
                return { success: false, message: 'Error durante el registro' };
            });
    },

    logout: function() {
        var self = this;
        // Llamar al backend para eliminar la cookie del servidor
        return ApiService.post('/logout', {})
            .then(function(response) {
                // Limpiar datos locales independientemente de la respuesta del servidor
                self.currentUser = null;
                localStorage.removeItem('currentUser');
                var event = new CustomEvent('userLoggedOut');
                document.dispatchEvent(event);
                if (window.app) {
                    window.app.navigate('home');
                }
                return { success: true };
            })
            .catch(function(error) {
                // Aunque falle la petición al servidor, limpiar datos locales
                self.currentUser = null;
                localStorage.removeItem('currentUser');
                var event = new CustomEvent('userLoggedOut');
                document.dispatchEvent(event);
                if (window.app) {
                    window.app.navigate('home');
                }
                return { success: false, message: 'Error al cerrar sesión en el servidor' };
            });
    },

    getCurrentUser: function() {
        if (this.currentUser) return this.currentUser;
        // Intentar restaurar de localStorage
        try {
            var userStr = localStorage.getItem('currentUser');
            if (userStr) {
                this.currentUser = JSON.parse(userStr);
                return this.currentUser;
            }
        } catch (e) {}
        return null;
    },

    isAuthenticated: function() {
        return !!this.getCurrentUser();
    },

    validateToken: function() {
        var self = this;
        return ApiService.get('/auth/validate')
            .then(function(response) {
                if (response && response.success && response.user) {
                    self.currentUser = response.user;
                    // Guardar usuario en localStorage
                    localStorage.setItem('currentUser', JSON.stringify(response.user));
                    console.log('✓ Token validado correctamente');
                    // NO forzar actualización del nav aquí - lo hace app.js
                    return true;
                } else {
                    // Token inválido CONFIRMADO por el servidor (401)
                    console.warn('✗ Token inválido según servidor, cerrando sesión');
                    self.currentUser = null;
                    localStorage.removeItem('currentUser');
                    var event = new CustomEvent('userLoggedOut');
                    document.dispatchEvent(event);
                    return false;
                }
            })
            .catch(function(error) {
                // Error de RED o servidor caído: NO cerrar sesión
                console.warn('⚠ Error al validar token (posible problema de red):', error);
                // Mantener usuario actual de localStorage
                return false; // False indica error, pero NO limpia la sesión
            });
    }
};

window.AuthService = AuthService;