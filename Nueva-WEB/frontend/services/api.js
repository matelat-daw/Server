const ApiService = {
    baseUrl: '/Nueva-WEB/api',

    async request(endpoint, options = {}) {
        const url = `${this.baseUrl}${endpoint}`;
        const token = AuthService.getToken();
        
        const config = {
            ...options,
            headers: {
                'Content-Type': 'application/json',
                ...options.headers,
                ...(token && { 'Authorization': `Bearer ${token}` })
            },
            credentials: 'include'
        };

        if (options.body) {
            config.body = JSON.stringify(options.body);
        }

        try {
            const response = await fetch(url, config);
            // Intentar parsear JSON si es posible; sino, devolver texto
            const contentType = response.headers.get('content-type') || '';

            let payload;
            if (contentType.includes('application/json')) {
                payload = await response.json();
            } else {
                const text = await response.text();
                // En nuestra API todas las respuestas válidas son JSON;
                // si llega texto/HTML, trátalo como error para no confundir al caller.
                payload = { success: false, message: text };
            }

            // Normalizar respuestas no exitosas o no-JSON para dar mensajes útiles
            if (!response.ok || !contentType.includes('application/json')) {
                // Leer cabeceras de depuración si existen
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