// API Service - Maneja las peticiones al backend
const ApiService = {
    baseURL: '/Nueva-BS/api',

    async request(endpoint, options = {}) {
        const url = `${this.baseURL}${endpoint}`;
        
        const config = {
            ...options,
            headers: {
                'Content-Type': 'application/json',
                ...options.headers
            },
            credentials: 'include' // Importante para cookies
        };

        if (options.body) {
            config.body = JSON.stringify(options.body);
        }

        try {
            const response = await fetch(url, config);
            const contentType = response.headers.get('content-type') || '';

            let payload;
            if (contentType.includes('application/json')) {
                payload = await response.json();
            } else {
                const text = await response.text();
                payload = { success: false, message: text };
            }

            if (!response.ok || !contentType.includes('application/json')) {
                const dbError = response.headers.get('X-DB-Error');
                const statusMessage = payload && payload.message ? payload.message : (
                    response.status === 401 ? 'Credenciales incorrectas' : 'Error de la API'
                );
                const fullMessage = dbError ? `${statusMessage}. Detalle: ${dbError}` : statusMessage;
                return { success: false, status: response.status, message: fullMessage };
            }

            return payload;
        } catch (error) {
            console.error('API Error:', error);
            return { success: false, message: 'Error de conexión con la API' };
        }
    },

    get(endpoint) {
        return this.request(endpoint, { method: 'GET' });
    },

    post(endpoint, body) {
        return this.request(endpoint, { method: 'POST', body });
    },

    put(endpoint, body) {
        return this.request(endpoint, { method: 'PUT', body });
    },

    delete(endpoint) {
        return this.request(endpoint, { method: 'DELETE' });
    }
};

window.ApiService = ApiService;
