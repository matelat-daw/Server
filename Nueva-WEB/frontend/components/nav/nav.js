
var navComponent = {
    init: function() {
        this.setupMenuToggle();
        this.setupNavLinks();
        this.setupAuthButtons();
        // NO llamar a updateForUser aquí - lo hará app.js
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
            console.log('🔹 nav.updateForUser: Mostrando menú para', user.username || user.email);
            
            if (guestMenu) guestMenu.style.display = 'none';
            if (userMenuWrapper) {
                // Obtener nombre y avatar
                var displayName = user.first_name || user.username || user.email || 'Usuario';
                var avatarUrl = user.profile_img || '/Nueva-WEB/media/default.jpg';
                
                // SIEMPRE inyectar el HTML completo con los datos del usuario
                userMenuWrapper.innerHTML = '<div class="user-menu">\
                    <div class="user-info">\
                        <img id="user-avatar" src="' + avatarUrl + '" alt="User Avatar" class="avatar">\
                        <span id="user-name" class="username">' + displayName + '</span>\
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
                
                console.log('✓ HTML del menú inyectado en wrapper');
                
                // Mostrar el wrapper
                userMenuWrapper.style.display = 'block';
                
                // Configurar el dropdown
                var userButton = document.getElementById('user-button');
                var userDropdown = document.getElementById('user-dropdown');
                if (userButton && userDropdown) {
                    userButton.onclick = function(e) {
                        e.stopPropagation();
                        userDropdown.classList.toggle('show');
                    };
                    document.addEventListener('click', function() {
                        if (userDropdown) userDropdown.classList.remove('show');
                    });
                }
                
                // Configurar logout
                if (window.userMenuComponent && typeof userMenuComponent.setupLogout === 'function') {
                    userMenuComponent.setupLogout();
                }
                
                console.log('✓ Menú de usuario completamente configurado');
            } else {
                if (attempt < 10) {
                    console.warn('⚠ user-menu-wrapper no encontrado, reintentando...');
                    setTimeout(function() { navComponent.updateForUser(user, attempt + 1); }, 100);
                }
            }
        } else {
            console.log('🔹 nav.updateForUser: Ocultando menú (no hay usuario)');
            if (guestMenu) guestMenu.style.display = 'flex';
            if (userMenuWrapper) {
                userMenuWrapper.style.display = 'none';
                userMenuWrapper.innerHTML = '';
            }
        }
    }
};

// Event listeners para actualizar nav cuando cambia el estado del usuario
document.addEventListener('userLoggedIn', function(e) {
    navComponent.updateForUser(e.detail);
});

document.addEventListener('userLoggedOut', function() {
    navComponent.updateForUser(null);
});