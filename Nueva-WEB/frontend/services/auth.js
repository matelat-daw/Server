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
        var self = this;
        return ApiService.post('/register', userData)
            .then(function(response) {
                if (response && response.success) {
                    self.currentUser = response.user;
                    // Guardar usuario en localStorage
                    localStorage.setItem('currentUser', JSON.stringify(response.user));
                    var event = new CustomEvent('userLoggedIn', { detail: response.user });
                    document.dispatchEvent(event);
                    return { success: true, user: response.user };
                } else {
                    return { success: false, message: response ? response.message : 'Error en el registro' };
                }
            })
            .catch(function(error) {
                return { success: false, message: 'Error durante el registro' };
            });
    },

    logout: function() {
        this.currentUser = null;
        // Eliminar usuario de localStorage
        localStorage.removeItem('currentUser');
        var event = new CustomEvent('userLoggedOut');
        document.dispatchEvent(event);
        if (window.app) {
            window.app.navigate('home');
        }
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
                    // Forzar actualización del nav tras recarga
                    if (window.navComponent && typeof navComponent.updateForUser === 'function') {
                        navComponent.updateForUser(self.currentUser);
                    }
                    return true;
                } else {
                    self.logout();
                    return false;
                }
            })
            .catch(function(error) {
                self.logout();
                return false;
            });
    }
};

window.AuthService = AuthService;