// API Service
var apiService = {
    baseURL: '/Energy/api',
    
    request: function(endpoint, options) {
        options = options || {};
        var url = this.baseURL + endpoint;
        
        var config = {
            method: options.method || 'GET',
            headers: {
                'Content-Type': 'application/json'
            },
            credentials: 'include' // Importante: envía cookies automáticamente
        };
        
        // Agregar body si existe
        if (options.body) {
            config.body = JSON.stringify(options.body);
        }
        
        return fetch(url, config)
            .then(function(response) {
                return response.json().then(function(data) {
                    if (!response.ok) {
                        throw data;
                    }
                    return data;
                });
            });
    },

    /**
     * Descargar archivo (Excel, PDF, etc.)
     */
    downloadFile: function(endpoint, filename) {
        var url = this.baseURL + endpoint;
        
        return fetch(url, {
            method: 'GET',
            credentials: 'include'
        })
        .then(function(response) {
            if (!response.ok) {
                throw new Error('Error en la descarga: ' + response.status);
            }
            return response.blob();
        })
        .then(function(blob) {
            // Crear un link temporal para descargar
            var link = document.createElement('a');
            var blobUrl = window.URL.createObjectURL(blob);
            link.href = blobUrl;
            link.download = filename || 'archivo';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            window.URL.revokeObjectURL(blobUrl);
        });
    },
    
    get: function(endpoint, data, isDownload) {
        if (isDownload) {
            return this.downloadFile(endpoint, 'archivo');
        }
        return this.request(endpoint, { method: 'GET' });
    },
    
    post: function(endpoint, data) {
        return this.request(endpoint, { method: 'POST', body: data });
    },
    
    put: function(endpoint, data) {
        return this.request(endpoint, { method: 'PUT', body: data });
    },
    
    delete: function(endpoint) {
        return this.request(endpoint, { method: 'DELETE' });
    }
};

window.apiService = apiService;
