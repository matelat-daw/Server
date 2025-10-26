// Main Application
var App = function() {
    this.currentPage = 'home';
    this.basePath = '/Nueva-WEB/frontend/';
    this.componentsLoaded = 0;
    this.totalComponents = 3; // header, nav, footer
    this.init();
};

App.prototype.init = function() {
    var self = this;
    
    // Wait for all components to load before loading page
    this.loadComponent('header', function() {
        self.componentsLoaded++;
        self.checkAllComponentsLoaded();
    });
    
    this.loadComponent('nav', function() {
        self.componentsLoaded++;
        self.checkAllComponentsLoaded();
    });
    
    this.loadComponent('footer', function() {
        self.componentsLoaded++;
        self.checkAllComponentsLoaded();
    });
    
    // Setup routing
    this.setupRouting();
    
    // Check auth status
    if (AuthService.isAuthenticated()) {
        this.updateUIForLoggedInUser();
    }
};

App.prototype.checkAllComponentsLoaded = function() {
    if (this.componentsLoaded === this.totalComponents) {
        // All components loaded, now load initial page
        this.loadPage('home');
    }
};

App.prototype.loadComponent = function(componentName, callback) {
    var container = document.getElementById(componentName + '-component');
    var basePath = this.basePath;
    
    if (!container) {
        console.error('Container not found for component: ' + componentName);
        if (callback) callback();
        return;
    }
    
    fetch(basePath + 'components/' + componentName + '/' + componentName + '.html')
        .then(function(response) {
            if (!response.ok) {
                throw new Error('HTTP error! status: ' + response.status);
            }
            return response.text();
        })
        .then(function(html) {
            container.innerHTML = html;
            
            // Initialize component if it has an init function
            var component = window[componentName + 'Component'];
            if (component && typeof component.init === 'function') {
                component.init();
            }
            
            if (callback) callback();
        })
        .catch(function(error) { 
            console.error('Error loading component ' + componentName + ':', error);
            if (callback) callback();
        });
};

App.prototype.loadPage = function(pageName) {
    var self = this;
    var mainContent = document.getElementById('main-content');
    var basePath = this.basePath;
    
    if (!mainContent) {
        console.error('Main content container not found');
        return;
    }
    
    // Show loading state
    mainContent.innerHTML = '<div style="text-align: center; padding: 2rem;">Cargando...</div>';
    
    fetch(basePath + 'pages/' + pageName + '/' + pageName + '.html')
        .then(function(response) {
            if (!response.ok) {
                throw new Error('HTTP error! status: ' + response.status);
            }
            return response.text();
        })
        .then(function(html) {
            mainContent.innerHTML = html;
            
            // Wait a bit for DOM to be ready
            setTimeout(function() {
                // Initialize page if it has an init function
                var page = window[pageName + 'Page'];
                if (page && typeof page.init === 'function') {
                    page.init();
                }
            }, 50);
            
            self.currentPage = pageName;
        })
        .catch(function(error) { 
            console.error('Error loading page ' + pageName + ':', error);
            mainContent.innerHTML = '<div style="text-align: center; padding: 2rem; color: red;">Error al cargar la página</div>';
        });
};

App.prototype.setupRouting = function() {
    var self = this;
    
    // Handle clicks on elements with data-route attribute
    document.addEventListener('click', function(e) {
        var target = e.target;
        
        // Check if clicked element or its parent has data-route
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
    
    // Handle browser back/forward buttons
    window.addEventListener('popstate', function() {
        var hash = window.location.hash.replace('#', '') || 'home';
        self.loadPage(hash);
    });
    
    // Load page based on initial hash
    var initialHash = window.location.hash.replace('#', '');
    if (initialHash && initialHash !== 'home') {
        setTimeout(function() {
            self.loadPage(initialHash);
        }, 500);
    }
};

App.prototype.navigate = function(route) {
    this.loadPage(route);
    window.history.pushState({}, '', '#' + route);
};

App.prototype.updateUIForLoggedInUser = function() {
    var event = new CustomEvent('userLoggedIn', { 
        detail: AuthService.getCurrentUser() 
    });
    document.dispatchEvent(event);
};

// Initialize app when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        window.app = new App();
    });
} else {
    // DOM is already ready
    window.app = new App();
}