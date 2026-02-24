// App principal - Energy
(function() {
    'use strict';
    
    var App = function() {
        this.currentPage = null;
        this.basePath = '/Energy/frontend/';
        this.initialized = false;
    };
    
    App.prototype.init = function() {
        if (this.initialized) return;
        this.initialized = true;
        
        console.log('🚀 Energy App iniciando...');
        
        var self = this;
        
        // Cargar componentes estructurales
        setTimeout(function() {
            self.loadComponent('header');
            self.loadComponent('footer');
        }, 100);
        
        // Cargar página inicial
        setTimeout(function() {
            var hashContent = window.location.hash.substring(1) || 'home';
            // Extraer solo el nombre de la página (antes del ?)
            var page = hashContent.split('?')[0] || 'home';
            self.loadPage(page);
        }, 300);
        
        this.setupRouting();
    };
    
    App.prototype.loadComponent = function(name) {
        if (window[name + 'Component'] && window[name + 'Component'].init) {
            window[name + 'Component'].init();
        }
    };
    
    App.prototype.setupRouting = function() {
        var self = this;
        window.addEventListener('hashchange', function() {
            var hashContent = window.location.hash.substring(1) || 'home';
            // Extraer solo el nombre de la página (antes del ?)
            var page = hashContent.split('?')[0] || 'home';
            self.loadPage(page);
        });
    };
    
    App.prototype.loadPage = function(pageName) {
        console.log('📄 Cargando página:', pageName);
        
        var mainContent = document.getElementById('main-content');
        if (!mainContent) {
            console.error('main-content no encontrado');
            return;
        }
        
        // No actualizar hash porque puede tener parámetros
        
        this.currentPage = pageName;
        
        // Mostrar loading
        mainContent.innerHTML = '<div class="loading"><div class="spinner"></div></div>';
        
        // Cargar HTML de la página
        var pageUrl = this.basePath + 'pages/' + pageName + '/' + pageName + '.html';
        
        fetch(pageUrl)
            .then(function(response) {
                if (!response.ok) throw new Error('Página no encontrada');
                return response.text();
            })
            .then(function(html) {
                mainContent.innerHTML = html;
                
                // Cargar CSS de la página
                var cssUrl = '/Energy/frontend/pages/' + pageName + '/' + pageName + '.css';
                var existingLink = document.querySelector('link[href="' + cssUrl + '"]');
                if (!existingLink) {
                    var link = document.createElement('link');
                    link.rel = 'stylesheet';
                    link.href = cssUrl;
                    document.head.appendChild(link);
                }
                
                // Cargar JS de la página
                var scriptUrl = '/Energy/frontend/pages/' + pageName + '/' + pageName + '.js';
                var existingScript = document.querySelector('script[src="' + scriptUrl + '"]');
                if (!existingScript) {
                    var script = document.createElement('script');
                    script.src = scriptUrl;
                    script.onload = function() {
                        // Inicializar página si tiene método init
                        if (window[pageName + 'Page'] && window[pageName + 'Page'].init) {
                            window[pageName + 'Page'].init();
                        }
                    };
                    document.body.appendChild(script);
                } else {
                    // Si el script ya existe, solo inicializar
                    if (window[pageName + 'Page'] && window[pageName + 'Page'].init) {
                        window[pageName + 'Page'].init();
                    }
                }
            })
            .catch(function(error) {
                console.error('Error cargando página:', error);
                mainContent.innerHTML = 
                    '<div class="card" style="max-width: 600px; margin: 2rem auto; text-align: center;">' +
                    '<h2>⚠️ Página no encontrada</h2>' +
                    '<p>La página "' + pageName + '" no existe.</p>' +
                    '<button class="btn btn-primary" onclick="app.loadPage(\'home\')">Ir al inicio</button>' +
                    '</div>';
            });
    };
    
    // Inicializar app cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            window.app = new App();
            window.app.init();
        });
    } else {
        window.app = new App();
        window.app.init();
    }
})();
