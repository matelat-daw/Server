const AuthService = {
    currentUser: null,
    tokenKey: 'auth_token',
    userKey: 'user_data',

    async login(email, password) {
        try {
            const response = await ApiService.post('/auth/login', { email, password });
            if (response.success) {
                this.setToken(response.token);
                this.setUser(response.user);
                return { success: true, user: response.user };
            }
            return { success: false, message: response.message };
        } catch (error) {
            return { success: false, message: 'Error en el login' };
        }
    },

    async register(userData) {
        try {
            const response = await ApiService.post('/auth/register', userData);
            if (response.success) {
                this.setToken(response.token);
                this.setUser(response.user);
                return { success: true, user: response.user };
            }
            return { success: false, message: response.message };
        } catch (error) {
            return { success: false, message: 'Error en el registro' };
        }
    },

    logout() {
        localStorage.removeItem(this.tokenKey);
        localStorage.removeItem(this.userKey);
        this.currentUser = null;
        const event = new CustomEvent('userLoggedOut');
        document.dispatchEvent(event);
        window.app.navigate('home');
    },

    setToken(token) {
        localStorage.setItem(this.tokenKey, token);
    },

    getToken() {
        return localStorage.getItem(this.tokenKey);
    },

    setUser(user) {
        this.currentUser = user;
        localStorage.setItem(this.userKey, JSON.stringify(user));
    },

    getCurrentUser() {
        if (!this.currentUser) {
            const userData = localStorage.getItem(this.userKey);
            this.currentUser = userData ? JSON.parse(userData) : null;
        }
        return this.currentUser;
    },

    isAuthenticated() {
        return !!this.getToken();
    }
};