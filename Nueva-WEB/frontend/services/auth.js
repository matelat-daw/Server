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
                    var event = new CustomEvent('userLoggedIn', { detail: response.user });
                    document.dispatchEvent(event);
                    console.log('Login successful for user:', response.user.username);
                    return { success: true, user: response.user };
                } else {
                    var message = (response && response.message) ? response.message : 'Error de conexión';
                    console.log('Login failed:', message);
                    return { success: false, message: message };
                }
            })
            .catch(function(error) {
                console.error('Login error:', error);
                return { success: false, message: 'Error durante el inicio de sesión' };
            });
    },

    register: function(userData) {
        var self = this;
        console.log('Attempting registration for:', userData.email);
        return ApiService.post('/register', userData)
            .then(function(response) {
                if (response && response.success) {
                    self.currentUser = response.user;
                    var event = new CustomEvent('userLoggedIn', { detail: response.user });
                    document.dispatchEvent(event);
                    console.log('Registration successful for user:', response.user.username);
                    return { success: true, user: response.user };
                } else {
                    console.log('Registration failed:', response ? response.message : 'Unknown error');
                    return { success: false, message: response ? response.message : 'Error en el registro' };
                }
            })
            .catch(function(error) {
                console.error('Registration error:', error);
                return { success: false, message: 'Error durante el registro' };
            });
    },

    logout: function() {
        console.log('Logging out user');
        this.currentUser = null;
        var event = new CustomEvent('userLoggedOut');
        document.dispatchEvent(event);
        if (window.app) {
            window.app.navigate('home');
        }
    },

    getCurrentUser: function() {
        return this.currentUser;
    },

    isAuthenticated: function() {
        return !!this.currentUser;
    },

    validateToken: function() {
        var self = this;
        return ApiService.get('/auth/validate')
            .then(function(response) {
                if (response && response.success) {
                    self.currentUser = response.user;
                    return true;
                } else {
                    self.logout();
                    return false;
                }
            })
            .catch(function(error) {
                console.error('Token validation error:', error);
                self.logout();
                return false;
            });
    }
};

window.AuthService = AuthService;