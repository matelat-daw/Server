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
        
        var self = this;
        
        // Cargar componentes estructurales
        setTimeout(function() {
            self.loadComponent('header');
            self.loadComponent('footer');
        }, 100);
        
        // Cargar página inicial desde la URL
        setTimeout(function() {
            var initialRoute = self.getInitialRoute();
            self.loadPageDirect(initialRoute);
        }, 300);
        
        this.setupRouting();
    };
    
    // Obtener ruta inicial desde el hash de la URL
    App.prototype.getInitialRoute = function() {
        var hash = window.location.hash;
        
        if (!hash || hash === '#' || hash === '#home') {
            // Asegurar que la URL inicial sea correcta
            window.history.replaceState({}, '', '/Energy/#home');
            return 'home';
        }
        
        // Extraer nombre de la ruta (ej: #activate?token=... -> activate)
        var route = hash.substring(1); // Quitar el #
        var questionMarkIndex = route.indexOf('?');
        if (questionMarkIndex > 0) {
            route = route.substring(0, questionMarkIndex);
        }
        
        return route || 'home';
    };
    
    App.prototype.loadComponent = function(name) {
        if (window[name + 'Component'] && window[name + 'Component'].init) {
            window[name + 'Component'].init();
        }
    };
    
    App.prototype.setupRouting = function() {
        var self = this;
        
        // Listener para hashchange (navegación manual en URL)
        window.addEventListener('hashchange', function() {
            var hashContent = window.location.hash.substring(1) || 'home';
            var page = hashContent.split('?')[0] || 'home';
            self.loadPageDirect(page);
        });
        
        // Listener para botón atrás/adelante del navegador
        window.addEventListener('popstate', function(e) {
            var hashContent = window.location.hash.substring(1) || 'home';
            var page = hashContent.split('?')[0] || 'home';
            self.loadPageDirect(page);
        });
        
        // Listener para enlaces con data-route
        document.addEventListener('click', function(e) {
            var target = e.target;
            while (target && target !== document) {
                if (target.hasAttribute && target.hasAttribute('data-route')) {
                    e.preventDefault();
                    var route = target.getAttribute('data-route');
                    self.navigate(route);
                    return;
                }
                target = target.parentNode;
            }
        });
    };
    
    // Método público para navegar programáticamente
    App.prototype.navigate = function(route) {
        if (this.currentPage === route) return;
        this.loadPageDirect(route);
        window.history.pushState({}, '', '/Energy/#' + route);
    };
    
    // Cargar página sin actualizar URL (usado internamente)
    App.prototype.loadPageDirect = function(pageName) {
        var mainContent = document.getElementById('main-content');
        if (!mainContent) return;
        
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
                
                // Actualizar breadcrumb
                if (window.headerComponent && window.headerComponent.updateBreadcrumb) {
                    window.headerComponent.updateBreadcrumb();
                }
                
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
                mainContent.innerHTML = 
                    '<div class="card" style="max-width: 600px; margin: 2rem auto; text-align: center;">' +
                    '<h2>⚠️ Página no encontrada</h2>' +
                    '<p>La página "' + pageName + '" no existe.</p>' +
                    '<button class="btn btn-primary" onclick="app.navigate(\'home\')">Ir al inicio</button>' +
                    '</div>';
            });
    };
    
    // Cargar página y actualizar URL (para compatibilidad con código existente)
    App.prototype.loadPage = function(pageName) {
        this.navigate(pageName);
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
