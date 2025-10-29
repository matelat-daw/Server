var AuthService = {
    currentUser: null,
    tokenKey: 'nueva_web_token',
    userKey: 'nueva_web_user',

    login: function(email, password) {
        var self = this;
        console.log('Attempting login for:', email);
        
    return ApiService.post('/login', { 
            email: email, 
            password: password 
        })
        .then(function(response) {
            if (response && response.success) {
                self.setToken(response.token);
                self.setUser(response.user);
                
                var event = new CustomEvent('userLoggedIn', { detail: response.user });
                document.dispatchEvent(event);
                
                console.log('Login successful for user:', response.user.username);
                return { success: true, user: response.user };
            } else {
                console.log('Login failed:', response ? response.message : 'Unknown error');
                return { success: false, message: response ? response.message : 'Error de conexión' };
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
                self.setToken(response.token);
                self.setUser(response.user);
                
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
        localStorage.removeItem(this.tokenKey);
        localStorage.removeItem(this.userKey);
        this.currentUser = null;
        
        var event = new CustomEvent('userLoggedOut');
        document.dispatchEvent(event);
        
        if (window.app) {
            window.app.navigate('home');
        }
    },

    setToken: function(token) {
        if (token) {
            localStorage.setItem(this.tokenKey, token);
        }
    },

    getToken: function() {
        return localStorage.getItem(this.tokenKey);
    },

    setUser: function(user) {
        if (user) {
            this.currentUser = user;
            localStorage.setItem(this.userKey, JSON.stringify(user));
        }
    },

    getCurrentUser: function() {
        if (!this.currentUser) {
            var userData = localStorage.getItem(this.userKey);
            this.currentUser = userData ? JSON.parse(userData) : null;
        }
        return this.currentUser;
    },

    isAuthenticated: function() {
        return !!this.getToken() && !!this.getCurrentUser();
    },

    validateToken: function() {
        var self = this;
        if (!this.getToken()) {
            return Promise.resolve(false);
        }

        return ApiService.get('/auth/validate')
        .then(function(response) {
            if (response && response.success) {
                self.setUser(response.user);
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

// Make it globally available
window.AuthService = AuthService;