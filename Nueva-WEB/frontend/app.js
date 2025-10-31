(function() {
    'use strict';
    
    console.log('=== app.js START ===');
    console.log('Window.app before check:', !!window.app);
    
    // Si ya existe, destruir y recrear
    if (window.app) {
        console.warn('⚠️ App already exists! Destroying old instance...');
        delete window.app;
    }
    
    // Main Application Constructor
    var App = function() {
        console.log('📱 App constructor called');
        this.currentPage = null;
        this.basePath = '/Nueva-WEB/frontend/';
        this.initialized = false;
    };

    App.prototype.init = function() {
        if (this.initialized) {
            console.log('App already initialized');
            return;
        }
        this.initialized = true;
        
        console.log('🚀 App.init() called');
        console.log('Document readyState:', document.readyState);
        
        var self = this;
        
        // Cargar página después de un breve delay
        setTimeout(function() {
            console.log('⏰ Timeout fired, loading home page');
            self.loadPageDirect('home');
        }, 800);
        
        // Intentar cargar componentes (no bloqueantes)
        setTimeout(function() {
            console.log('Loading components...');
            self.tryLoadComponent('header');
            self.tryLoadComponent('nav');
            self.tryLoadComponent('footer');
        }, 200);
        
        this.setupRouting();
        
        // Validar sesión con el backend tras recarga
        if (window.AuthService && typeof AuthService.validateToken === 'function') {
            AuthService.validateToken().then((isValid) => {
                if (isValid && AuthService.getCurrentUser) {
                    // Forzar actualización del nav y menú de usuario tras recarga
                    if (window.navComponent && typeof navComponent.updateForUser === 'function') {
                        navComponent.updateForUser(AuthService.getCurrentUser());
                    }
                    this.updateUIForLoggedInUser();
                }
            });
        }
    };

    App.prototype.tryLoadComponent = function(componentName) {
        var container = document.getElementById(componentName + '-component');
        
        if (!container) {
            console.warn('❌ Container not found:', componentName + '-component');
            return;
        }
        
        var basePath = this.basePath;
        console.log('📦 Loading component:', componentName);
        
        fetch(basePath + 'components/' + componentName + '/' + componentName + '.html')
            .then(function(response) {
                if (!response.ok) throw new Error('HTTP ' + response.status);
                return response.text();
            })
            .then(function(html) {
                console.log('✅ Component HTML loaded:', componentName);
                container.innerHTML = html;
                
                setTimeout(function() {
                    var component = window[componentName + 'Component'];
                    if (component && typeof component.init === 'function') {
                        try {
                            component.init();
                            console.log('✅ Component initialized:', componentName);
                        } catch (error) {
                            console.error('❌ Error initializing:', componentName, error);
                        }
                    }
                }, 100);
            })
            .catch(function(error) { 
                console.error('❌ Error loading component:', componentName, error.message);
                container.innerHTML = '<div style="padding:0.5rem;background:#ffe;color:#990;font-size:0.8rem;">⚠️ ' + componentName + ' no disponible</div>';
            });
    };

    App.prototype.loadPageDirect = function(pageName) {
        var mainContent = document.getElementById('main-content');
        
        if (!mainContent) {
            console.error('❌ FATAL: main-content not found!');
            return;
        }
        
        console.log('📄 Loading page:', pageName);
        
        var basePath = this.basePath;
        var self = this;
        
        mainContent.innerHTML = '<div style="text-align:center;padding:3rem;"><div class="loading"></div><div style="margin-top:1rem;color:#718096;">Cargando ' + pageName + '...</div></div>';
        
        var pageUrl = basePath + 'pages/' + pageName + '/' + pageName + '.html';
        console.log('Fetching:', pageUrl);
        
        fetch(pageUrl)
            .then(function(response) {
                console.log('Page response status:', response.status);
                if (!response.ok) throw new Error('HTTP ' + response.status);
                return response.text();
            })
            .then(function(html) {
                console.log('✅ Page HTML loaded, length:', html.length);
                console.log('HTML preview:', html.substring(0, 100));
                
                mainContent.innerHTML = html;
                self.currentPage = pageName;
                
                // Dispatch event
                var event = new CustomEvent('pageChanged', { detail: { page: pageName } });
                document.dispatchEvent(event);
                console.log('📢 pageChanged event dispatched');
                
                // Initialize page
                setTimeout(function() {
                    var pageObj = window[pageName + 'Page'];
                    console.log('Looking for:', pageName + 'Page');
                    console.log('Found:', !!pageObj);
                    
                    if (pageObj) {
                        if (typeof pageObj.init === 'function') {
                            console.log('🎯 Initializing page:', pageName);
                            pageObj.initialized = false;
                            try {
                                pageObj.init();
                                console.log('✅ Page initialized:', pageName);
                            } catch (error) {
                                console.error('❌ Page init error:', error);
                                console.error('Stack:', error.stack);
                            }
                        } else {
                            console.error('❌ Page has no init function');
                        }
                    } else {
                        console.error('❌ Page object not found!');
                        console.log('Available Page objects:', Object.keys(window).filter(function(k) { 
                            return k.toLowerCase().indexOf('page') > -1; 
                        }));
                    }
                }, 400);
            })
            .catch(function(error) { 
                console.error('❌ Error loading page:', error);
                mainContent.innerHTML = '<div style="text-align:center;padding:2rem;"><h3 style="color:#e53e3e;">❌ Error</h3><p>' + error.message + '</p><p style="font-size:0.9rem;color:#666;">URL: ' + pageUrl + '</p></div>';
            });
    };

    App.prototype.loadPage = function(pageName) {
        if (this.currentPage === pageName) {
            console.log('Page already loaded:', pageName);
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
        console.log('🧭 Navigate to:', route);
        this.loadPage(route);
        window.history.pushState({}, '', '#' + route);
    };

    App.prototype.updateUIForLoggedInUser = function() {
        try {
            var event = new CustomEvent('userLoggedIn', { 
                detail: AuthService.getCurrentUser() 
            });
            document.dispatchEvent(event);
        } catch (error) {
            console.error('Error updating UI:', error);
        }
    };

    // Initialize - SIEMPRE crear nueva instancia
    console.log('🎬 Creating new App instance...');
    window.app = new App();
    console.log('App instance created:', !!window.app);
    
    // Init when ready
    if (document.readyState === 'loading') {
        console.log('📄 DOM is loading, adding DOMContentLoaded listener');
        document.addEventListener('DOMContentLoaded', function() {
            console.log('📄 DOMContentLoaded fired!');
            window.app.init();
        });
    } else {
        console.log('📄 DOM already loaded, initializing now');
        window.app.init();
    }
    
    console.log('=== app.js END ===');
})();