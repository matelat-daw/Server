// Auth Service - Maneja la autenticación
const AuthService = {
    currentUser: null,

    async login(email, password) {
        const payload = { email, password };

        try {
            const response = await ApiService.post('/login', payload);

            if (response && response.success && response.user) {
                this.currentUser = response.user;
                localStorage.setItem('currentUser', JSON.stringify(response.user));
                
                // Emitir evento
                const event = new CustomEvent('userLoggedIn', { detail: response.user });
                document.dispatchEvent(event);
                
                return { success: true, user: response.user };
            } else {
                const message = response?.message || 'Error de conexión';
                return { success: false, message };
            }
        } catch (error) {
            console.error('Login error:', error);
            return { success: false, message: 'Error durante el inicio de sesión' };
        }
    },

    async register(userData) {
        try {
            const response = await ApiService.post('/register', userData);

            if (response && response.success) {
                return { success: true, user: response.user, message: response.message };
            } else {
                return { success: false, message: response?.message || 'Error en el registro' };
            }
        } catch (error) {
            console.error('Register error:', error);
            return { success: false, message: 'Error durante el registro' };
        }
    },

    async logout() {
        try {
            await ApiService.post('/logout', {});
        } catch (error) {
            console.error('Logout error:', error);
        }

        // Limpiar datos locales
        this.currentUser = null;
        localStorage.removeItem('currentUser');
        
        const event = new CustomEvent('userLoggedOut');
        document.dispatchEvent(event);
        
        window.location.href = '/Nueva-BS/#/home';
    },

    getCurrentUser() {
        if (!this.currentUser) {
            const userData = localStorage.getItem('currentUser');
            if (userData) {
                try {
                    this.currentUser = JSON.parse(userData);
                } catch (error) {
                    console.error('Error parsing user data:', error);
                    localStorage.removeItem('currentUser');
                }
            }
        }
        return this.currentUser;
    },

    isAuthenticated() {
        return !!this.getCurrentUser();
    }
};

window.AuthService = AuthService;
