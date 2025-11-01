// app.js principal - Optimizado
(function() {
    'use strict';

    var App = function() {
        this.currentPage = null;
        this.basePath = '/Nueva-WEB/frontend/';
        this.initialized = false;
    };

    App.prototype.init = function() {
        if (this.initialized) {
            return;
        }
        this.initialized = true;
        
        var self = this;
        
        console.log('🚀 Iniciando aplicación...');
        
        // Cargar página inicial
        setTimeout(function() { self.loadPageDirect('home'); }, 800);
        
        // Cargar componentes estructurales
        setTimeout(function() {
            self.tryLoadComponent('header');
            
            // Asegurar que nav-component existe
            var navContainer = document.getElementById('nav-component');
            if (!navContainer) {
                navContainer = document.createElement('div');
                navContainer.id = 'nav-component';
                var appElement = document.getElementById('app');
                var mainContent = document.getElementById('main-content');
                if (appElement && mainContent) {
                    appElement.insertBefore(navContainer, mainContent);
                }
            }
            
            // Cargar navegación y manejar menú de usuario
            self.tryLoadComponent('nav');
            self.initializeUserMenu();
            
            self.tryLoadComponent('footer');
        }, 200);
        
        this.setupRouting();
    };

    // Inicializar menú de usuario tras cargar nav
    App.prototype.initializeUserMenu = function() {
        var self = this;
        var ensureUserMenu = function(attempt) {
            attempt = attempt || 1;
            
            var user = self.getUserFromStorage();
            var navReady = window.navComponent && typeof navComponent.updateForUser === 'function';
            var wrapper = document.getElementById('user-menu-wrapper');
            
            if (navReady && wrapper) {
                // SIEMPRE mostrar menú si hay usuario en localStorage
                if (user) {
                    console.log('✓ Usuario en localStorage, mostrando menú inmediatamente');
                    navComponent.updateForUser(user);
                    wrapper.style.display = 'block';
                    
                    // Actualizar componente de menú si existe
                    if (window.userMenuComponent && typeof userMenuComponent.updateUser === 'function') {
                        userMenuComponent.updateUser(user);
                    }
                }
                
                // Validar token en segundo plano (pero NO ocultar si falla por red)
                if (window.AuthService && typeof AuthService.validateToken === 'function') {
                    AuthService.validateToken().then(function(isValid) {
                        if (isValid) {
                            var validUser = AuthService.getCurrentUser();
                            if (validUser) {
                                // Token válido: actualizar con datos frescos
                                console.log('✓ Token válido, actualizando datos');
                                navComponent.updateForUser(validUser);
                                if (window.userMenuComponent && typeof userMenuComponent.updateUser === 'function') {
                                    userMenuComponent.updateUser(validUser);
                                }
                                wrapper.style.display = 'block';
                                self.updateUIForLoggedInUser();
                            }
                        } else {
                            // Token realmente inválido (401 del servidor): ocultar menú
                            console.warn('✗ Token inválido, cerrando sesión');
                            localStorage.removeItem('currentUser');
                            navComponent.updateForUser(null);
                        }
                    }).catch(function(error) {
                        // Error de red u otro: MANTENER menú visible
                        console.warn('⚠ Error validando token (posible red), manteniendo sesión:', error);
                        // NO hacer nada - el menú ya está visible con datos de localStorage
                    });
                }
            } else if (attempt < 20) {
                setTimeout(function() { ensureUserMenu(attempt + 1); }, 100);
            }
        };
        
        setTimeout(function() { ensureUserMenu(1); }, 200);
    };

    // Obtener usuario de localStorage de forma segura
    App.prototype.getUserFromStorage = function() {
        try {
            var userStr = localStorage.getItem('currentUser');
            if (userStr) {
                return JSON.parse(userStr);
            }
        } catch (e) {
            console.error('Error al leer usuario de localStorage:', e);
        }
        return null;
    };

    App.prototype.tryLoadComponent = function(componentName) {
        var container = document.getElementById(componentName + '-component');
        
        if (!container) {
            console.warn('Container no encontrado:', componentName + '-component');
            return;
        }
        
        var basePath = this.basePath;
        var componentUrl = basePath + 'components/' + componentName + '/' + componentName + '.html';
        
        fetch(componentUrl)
            .then(function(response) {
                if (!response.ok) throw new Error('HTTP ' + response.status);
                return response.text();
            })
            .then(function(html) {
                container.innerHTML = html;
                
                // Inicializar componente tras cargar HTML
                setTimeout(function() {
                    var component = window[componentName + 'Component'];
                    if (component && typeof component.init === 'function') {
                        try {
                            component.init();
                            console.log('✓ Componente inicializado:', componentName);
                        } catch (error) {
                            console.error('Error al inicializar componente:', componentName, error);
                        }
                    }
                }, 100);
            })
            .catch(function(error) {
                console.error('Error al cargar componente:', componentName, error);
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
                // Cargar estilos específicos de la página
                self.setPageStyles(pageName);
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
                            // Asegurar que el menú se muestra
                            navComponent.updateForUser(user);
                            wrapper.style.display = 'block';
                            console.log('✓ Menú de usuario actualizado tras cambio de página');
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

    // Inyecta el CSS de la página actual con ruta absoluta correcta
    App.prototype.setPageStyles = function(pageName) {
        var href = this.basePath + 'pages/' + pageName + '/' + pageName + '.css';
        var linkId = 'page-style';
        var existing = document.getElementById(linkId);
        if (!existing) {
            existing = document.createElement('link');
            existing.rel = 'stylesheet';
            existing.id = linkId;
            document.head.appendChild(existing);
        }
        existing.href = href;
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

    // Initialize app
    if (window.app) {
        delete window.app;
    }
    
    window.app = new App();

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            window.app.init();
        });
    } else {
        window.app.init();
    }
})();