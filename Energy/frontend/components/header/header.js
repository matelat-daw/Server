// Header Component
var headerComponent = {
    init: function() {
        this.loadHeaderHTML();
        this.initThemeToggle();
        this.updateUserMenu();
        this.setupLogoClick();
    },
    
    loadHeaderHTML: function() {
        var container = document.getElementById('header-component');
        if (!container) return;
        
        fetch('/Energy/frontend/components/header/header.html')
            .then(function(response) { return response.text(); })
            .then(function(html) {
                container.innerHTML = html;
                
                // Cargar CSS del header
                var link = document.createElement('link');
                link.rel = 'stylesheet';
                link.href = '/Energy/frontend/components/header/header.css';
                document.head.appendChild(link);
                
                // Re-inicializar después de cargar HTML
                headerComponent.initThemeToggle();
                headerComponent.updateUserMenu();
                headerComponent.setupLogoClick();
            })
            .catch(function(error) {
                console.error('Error cargando header:', error);
            });
    },
    
    initThemeToggle: function() {
        var btn = document.getElementById('theme-toggle');
        if (!btn) return;
        
        var self = this;
        
        function setTheme(theme) {
            if (theme === 'dark') {
                document.body.classList.add('dark-theme');
                btn.textContent = '☀️';
            } else {
                document.body.classList.remove('dark-theme');
                btn.textContent = '🌙';
            }
            localStorage.setItem('theme', theme);
        }
        
        // Estado inicial
        var savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark') {
            setTheme('dark');
        } else {
            setTheme('light');
        }
        
        btn.onclick = function() {
            var currentTheme = localStorage.getItem('theme') === 'dark' ? 'dark' : 'light';
            setTheme(currentTheme === 'dark' ? 'light' : 'dark');
        };
    },
    
    updateUserMenu: function() {
        var menuArea = document.getElementById('user-menu-area');
        if (!menuArea) return;
        
        var isLoggedIn = window.authService && window.authService.isLoggedIn();
        
        if (isLoggedIn) {
            var user = window.authService.getUser();
            menuArea.innerHTML = 
                '<button class="auth-btn btn-login" onclick="app.loadPage(\'profile\')">👤 ' + (user.name || 'Perfil') + '</button>' +
                '<button class="auth-btn btn-register" onclick="authService.logout(); app.loadPage(\'home\')">Salir</button>';
        } else {
            menuArea.innerHTML = 
                '<button class="auth-btn btn-login" onclick="app.loadPage(\'login\')">Iniciar Sesión</button>' +
                '<button class="auth-btn btn-register" onclick="app.loadPage(\'register\')">Registrarse</button>';
        }
    },
    
    setupLogoClick: function() {
        var logoSection = document.querySelector('.logo-section');
        if (logoSection) {
            logoSection.onclick = function() {
                if (window.app) {
                    window.app.loadPage('home');
                }
            };
        }
    }
};

window.headerComponent = headerComponent;
