
var navComponent = {
    init: function() {
        this.setupMenuToggle();
        this.setupNavLinks();
        this.setupAuthButtons();
        var self = this;
        setTimeout(function() {
            if (window.AuthService && AuthService.isAuthenticated && AuthService.isAuthenticated()) {
                self.updateForUser(AuthService.getCurrentUser());
            }
        }, 200);
    },
    setupMenuToggle: function() {
        var menuToggle = document.getElementById('menu-toggle');
        var navMenu = document.getElementById('nav-menu');
        if (menuToggle) {
            menuToggle.addEventListener('click', function() {
                navMenu.classList.toggle('active');
                menuToggle.classList.toggle('active');
            });
        }
    },
    setupNavLinks: function() {
        var navLinks = document.querySelectorAll('.nav-link');
        navLinks.forEach(function(link) {
            link.addEventListener('click', function(e) {
                navLinks.forEach(function(l) { l.classList.remove('active'); });
                e.target.classList.add('active');
                var navMenu = document.getElementById('nav-menu');
                var menuToggle = document.getElementById('menu-toggle');
                navMenu.classList.remove('active');
                menuToggle.classList.remove('active');
            });
        });
    },
    setupAuthButtons: function() {
        var btnLogin = document.getElementById('btn-login');
        var btnRegister = document.getElementById('btn-register');
        if (btnLogin) {
            btnLogin.addEventListener('click', function() {
                loginComponent.show();
            });
        }
        if (btnRegister) {
            btnRegister.addEventListener('click', function() {
                window.location.hash = '#register';
            });
        }
    },
    updateForUser: function(user, attempt) {
        attempt = attempt || 1;
        var guestMenu = document.getElementById('guest-menu');
        var userMenuWrapper = document.getElementById('user-menu-wrapper');
        if (user) {
            if (guestMenu) guestMenu.style.display = 'none';
            if (userMenuWrapper) {
                userMenuWrapper.style.display = 'block';
                if (!userMenuWrapper.innerHTML.trim()) {
                    userMenuWrapper.innerHTML = '<div class="user-menu">\
                        <div class="user-info">\
                            <img id="user-avatar" src="/Nueva-WEB/frontend/assets/default-avatar.png" alt="User Avatar" class="avatar">\
                            <span id="user-name" class="username">Usuario</span>\
                        </div>\
                        <div class="menu-options">\
                            <button id="user-button" class="dropdown-toggle">Opciones <span class="dropdown-caret" style="margin-left:0.4em;font-size:1.1em;">&#9662;</span></button>\
                            <div id="user-dropdown" class="dropdown-menu">\
                                <a href="#profile" class="dropdown-item">Perfil</a>\
                                <a href="#cart" class="dropdown-item">Compras</a>\
                                <a href="#" id="logout-btn" class="dropdown-item">Cerrar Sesión</a>\
                            </div>\
                        </div>\
                    </div>';
                }
                // Activar el desplegable correctamente
                var userButton = document.getElementById('user-button');
                var userDropdown = document.getElementById('user-dropdown');
                if (userButton && userDropdown) {
                    userButton.onclick = function(e) {
                        e.stopPropagation();
                        userDropdown.classList.toggle('show');
                    };
                    document.addEventListener('click', function hideDropdown() {
                        userDropdown.classList.remove('show');
                    }, { once: true });
                }
                if (window.userMenuComponent && typeof userMenuComponent.updateUser === 'function') {
                    userMenuComponent.updateUser(user);
                } else {
                    userMenuWrapper.innerHTML = '<div style="color:red">Error: userMenuComponent no disponible</div>';
                }
            } else {
                if (attempt < 10) {
                    setTimeout(function() { navComponent.updateForUser(user, attempt + 1); }, 100);
                }
            }
        } else {
            if (guestMenu) guestMenu.style.display = 'flex';
            if (userMenuWrapper) {
                userMenuWrapper.style.display = 'none';
                userMenuWrapper.innerHTML = '';
            }
        }
    }
};

// Event listeners fuera del objeto navComponent
document.addEventListener('userLoggedIn', function(e) {
    navComponent.updateForUser(e.detail);
});
document.addEventListener('userLoggedOut', function() {
    navComponent.updateForUser(null);
});

// Event listeners fuera del objeto navComponent
document.addEventListener('userLoggedIn', function(e) {
    navComponent.updateForUser(e.detail);
});
document.addEventListener('userLoggedOut', function() {
    navComponent.updateForUser(null);
});