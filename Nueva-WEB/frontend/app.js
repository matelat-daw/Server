// app.js principal
(function() {
    'use strict';

    // Si ya existe, destruir y recrear
    if (window.app) {
        delete window.app;
    }

    var App = function() {
        this.currentPage = null;
        this.basePath = '/Nueva-WEB/frontend/';
        this.initialized = false;
    };

    App.prototype.init = function() {
        if (this.initialized) return;
        this.initialized = true;
        var self = this;
        // 1. Cargar header y nav primero, luego footer
        setTimeout(function() { self.loadPageDirect('home'); }, 800);
        setTimeout(function() {
            self.tryLoadComponent('header');
            // Asegurar nav-container existe
            var navContainer = document.getElementById('nav-component');
            if (!navContainer) {
                navContainer = document.createElement('div');
                navContainer.id = 'nav-component';
                document.getElementById('app').insertBefore(navContainer, document.getElementById('main-content'));
            }
            // Cargar nav y, tras inicializar, actualizar menú usuario inmediatamente si hay usuario en localStorage
            self.tryLoadComponent('nav');
            // Refuerzo: esperar a que nav esté en el DOM y navComponent inicializado
            var ensureUserMenu = function(attempt) {
                attempt = attempt || 1;
                var user = null;
                try {
                    var userStr = localStorage.getItem('currentUser');
                    if (userStr) user = JSON.parse(userStr);
                } catch (e) {}
                var navReady = window.navComponent && typeof navComponent.updateForUser === 'function';
                var wrapper = document.getElementById('user-menu-wrapper');
                if (navReady && wrapper) {
                    if (user) {
                        navComponent.updateForUser(user);
                    } else {
                        navComponent.updateForUser(null);
                    }
                    // Validar token después de mostrar menú provisional
                    if (window.AuthService && typeof AuthService.validateToken === 'function') {
                        AuthService.validateToken().then((isValid) => {
                            var validUser = null;
                            if (isValid && AuthService.getCurrentUser) {
                                validUser = AuthService.getCurrentUser();
                            }
                            if (validUser) {
                                navComponent.updateForUser(validUser);
                                if (window.userMenuComponent && typeof userMenuComponent.updateUser === 'function') {
                                    userMenuComponent.updateUser(validUser);
                                    wrapper.style.display = 'block';
                                }
                                self.updateUIForLoggedInUser();
                            } else {
                                // Si el token NO es válido, ocultar el menú y limpiar usuario
                                localStorage.removeItem('currentUser');
                                navComponent.updateForUser(null);
                            }
                        });
                    }
                } else if (attempt < 20) {
                    setTimeout(function() { ensureUserMenu(attempt + 1); }, 100);
                }
            };
            setTimeout(function() { ensureUserMenu(1); }, 200);
            self.tryLoadComponent('footer');
        }, 200);
        this.setupRouting();
    };

    App.prototype.tryLoadComponent = function(componentName) {
        var container = document.getElementById(componentName + '-component');
        
        if (!container) {
            console.warn('❌ Container not found:', componentName + '-component');
            return;
        }
        
        var basePath = this.basePath;
    // ...existing code...
        
        fetch(basePath + 'components/' + componentName + '/' + componentName + '.html')
            .then(function(response) {
                if (!response.ok) throw new Error('HTTP ' + response.status);
                return response.text();
            })
            .then(function(html) {
                // ...existing code...
                container.innerHTML = html;
                
                setTimeout(function() {
                    var component = window[componentName + 'Component'];
                    if (component && typeof component.init === 'function') {
                        try {
                            component.init();
                            // ...existing code...
                        } catch (error) {
                            console.error('❌ Error initializing:', componentName, error);
                        }
                    }
                }, 100);
            })
            .catch(function(error) { 
                // ...existing code...
                container.innerHTML = '<div style="padding:0.5rem;background:#ffe;color:#990;font-size:0.8rem;">⚠️ ' + componentName + ' no disponible</div>';
            });
    };

    App.prototype.loadPageDirect = function(pageName) {
        var mainContent = document.getElementById('main-content');
        
        if (!mainContent) {
            console.error('❌ FATAL: main-content not found!');
            return;
        }
        
    // ...existing code...
        
        var basePath = this.basePath;
        var self = this;
        
        mainContent.innerHTML = '<div style="text-align:center;padding:3rem;"><div class="loading"></div><div style="margin-top:1rem;color:#718096;">Cargando ' + pageName + '...</div></div>';
        var pageUrl = basePath + 'pages/' + pageName + '/' + pageName + '.html';
        fetch(pageUrl)
            .then(function(response) {
                if (!response.ok) throw new Error('HTTP ' + response.status);
                return response.text();
            })
            .then(function(html) {
                mainContent.innerHTML = html;
                self.currentPage = pageName;
                // Dispatch event
                var event = new CustomEvent('pageChanged', { detail: { page: pageName } });
                document.dispatchEvent(event);
                // Refuerzo: actualizar menú usuario tras cada cambio de página
                var updateMenuAfterPage = function(attempt) {
                    attempt = attempt || 1;
                    var user = null;
                    if (window.AuthService && AuthService.getCurrentUser) {
                        user = AuthService.getCurrentUser();
                    }
                    if (!user) {
                        try {
                            var userStr = localStorage.getItem('currentUser');
                            if (userStr) user = JSON.parse(userStr);
                        } catch (e) {}
                    }
                    var navReady = window.navComponent && typeof navComponent.updateForUser === 'function';
                    var wrapper = document.getElementById('user-menu-wrapper');
                    if (navReady && wrapper) {
                        if (user) {
                            navComponent.updateForUser(user);
                        } else {
                            navComponent.updateForUser(null);
                        }
                    } else if (attempt < 10) {
                        setTimeout(function() { updateMenuAfterPage(attempt + 1); }, 100);
                    }
                };
                setTimeout(function() { updateMenuAfterPage(1); }, 200);
                // Initialize page
                setTimeout(function() {
                    var pageObj = window[pageName + 'Page'];
                    if (pageObj) {
                        if (typeof pageObj.init === 'function') {
                            pageObj.initialized = false;
                            try {
                                pageObj.init();
                            } catch (error) {}
                        }
                    }
                }, 400);
            })
            .catch(function(error) { 
                mainContent.innerHTML = '<div style="text-align:center;padding:2rem;"><h3 style="color:#e53e3e;">❌ Error</h3><p>' + error.message + '</p><p style="font-size:0.9rem;color:#666;">URL: ' + pageUrl + '</p></div>';
            });
    };

    App.prototype.loadPage = function(pageName) {
        if (this.currentPage === pageName) {
            // ...existing code...
            return;
        }
        this.loadPageDirect(pageName);
    };

    App.prototype.setupRouting = function() {
        var self = this;
        
        document.addEventListener('click', function(e) {
            var target = e.target;
            while (target && target !== document) {
                if (target.hasAttribute && target.hasAttribute('data-route')) {
                    e.preventDefault();
                    var route = target.getAttribute('data-route');
                    console.log('🔗 Route clicked:', route);
                    self.navigate(route);
                    return;
                }
                target = target.parentNode;
            }
        });
    };

    App.prototype.navigate = function(route) {
    // ...existing code...
        this.loadPage(route);
        window.history.pushState({}, '', '#' + route);
    };

    App.prototype.updateUIForLoggedInUser = function() {
        // Log solo para login o validación de token
        try {
            var user = AuthService.getCurrentUser();
            if (user) {
                console.log('[Auth] Usuario logueado:', user.username || user.email);
            }
            var event = new CustomEvent('userLoggedIn', { detail: user });
            document.dispatchEvent(event);
        } catch (error) {}
    };

    // Initialize - SIEMPRE crear nueva instancia
    window.app = new App();

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            window.app.init();
        });
    } else {
        window.app.init();
    }
})();