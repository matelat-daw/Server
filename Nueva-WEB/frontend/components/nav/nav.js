var navComponent = {
    init() {
        this.setupMenuToggle();
        this.setupNavLinks();
        this.setupAuthButtons();
        // Mostrar menú de usuario si ya está autenticado
        if (window.AuthService && AuthService.isAuthenticated && AuthService.isAuthenticated()) {
            this.updateForUser(AuthService.getCurrentUser());
        }
    },

    setupMenuToggle() {
        const menuToggle = document.getElementById('menu-toggle');
        const navMenu = document.getElementById('nav-menu');
        
        if (menuToggle) {
            menuToggle.addEventListener('click', () => {
                navMenu.classList.toggle('active');
                menuToggle.classList.toggle('active');
            });
        }
    },

    setupNavLinks() {
        const navLinks = document.querySelectorAll('.nav-link');
        
        navLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                navLinks.forEach(l => l.classList.remove('active'));
                e.target.classList.add('active');
                
                const navMenu = document.getElementById('nav-menu');
                const menuToggle = document.getElementById('menu-toggle');
                navMenu.classList.remove('active');
                menuToggle.classList.remove('active');
            });
        });
    },

    setupAuthButtons() {
        const btnLogin = document.getElementById('btn-login');
        const btnRegister = document.getElementById('btn-register');
        
        if (btnLogin) {
            btnLogin.addEventListener('click', () => {
                loginComponent.show();
            });
        }
        
        if (btnRegister) {
            btnRegister.addEventListener('click', () => {
                window.location.hash = '#register';
            });
        }
    },

    updateForUser(user) {
        const guestMenu = document.getElementById('guest-menu');
        const userMenuWrapper = document.getElementById('user-menu-wrapper');
        if (user) {
            guestMenu.style.display = 'none';
            userMenuWrapper.style.display = 'block';
            // Limpiar y renderizar el menú de usuario siempre
            if (window.userMenuComponent && typeof userMenuComponent.updateUser === 'function') {
                userMenuComponent.updateUser(user);
            } else {
                userMenuWrapper.innerHTML = `<div style='color:red'>Error: userMenuComponent no disponible</div>`;
            }
        } else {
            guestMenu.style.display = 'flex';
            userMenuWrapper.style.display = 'none';
            userMenuWrapper.innerHTML = '';
        }
    }
};

document.addEventListener('userLoggedIn', (e) => {
    navComponent.updateForUser(e.detail);
});

document.addEventListener('userLoggedOut', () => {
    navComponent.updateForUser(null);
});