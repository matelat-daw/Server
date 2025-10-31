// Ensure showModal is available (import from register.js if not global)
if (typeof showModal !== 'function') {
    window.showModal = function(message, type = 'error') {
        let modal = document.getElementById('global-modal');
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'global-modal';
            modal.innerHTML = `
                <div class="modal-backdrop"></div>
                <div class="modal-content">
                    <span id="modal-message"></span>
                    <button id="modal-close">OK</button>
                </div>
            `;
            document.body.appendChild(modal);
        }
        modal.querySelector('#modal-message').textContent = message;
        modal.style.display = 'flex';
        modal.className = type === 'success' ? 'modal-success' : 'modal-error';
        modal.querySelector('#modal-close').onclick = function() {
            modal.style.display = 'none';
        };
    };
}

var loginComponent = {
    modal: null,
    
    init() {
        this.createModal();
    },

    createModal() {
        var container = document.createElement('div');
        container.id = 'login-container';
        document.body.appendChild(container);
        
        fetch('/Nueva-WEB/frontend/pages/login/login.html')
            .then(function(response) { return response.text(); })
            .then(function(html) {
                container.innerHTML = html;
                // Usar el propio contenedor como modal (no existe login-modal en la plantilla)
                loginComponent.modal = container;
                loginComponent.modal.style.display = 'none';
                loginComponent.setupForm();
                loginComponent.setupClose();
                loginComponent.setupSwitchToRegister();
            });
    },

    setupForm() {
        var form = document.getElementById('login-form');
        if (form) {
            if (form.dataset.bound === '1') return; // evitar doble binding
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                var username = document.getElementById('username').value;
                var password = document.getElementById('password').value;
                var result = await AuthService.login({ username, password });
                if (result.success) {
                    loginComponent.hide();
                    var event = new CustomEvent('userLoggedIn', { detail: result.user });
                    document.dispatchEvent(event);
                    // Navegar a inicio tras login exitoso
                    if (window.app && typeof window.app.navigate === 'function') {
                        window.app.navigate('home');
                    } else {
                        window.location.hash = '#home';
                    }
                } else {
                    // Stay on login, show modal error
                    showModal(result.message || 'Credenciales incorrectas', 'error');
                }
            });
            form.dataset.bound = '1';
        }
    },

    setupClose() {
        var closeBtn = document.getElementById('close-login');
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                loginComponent.hide();
            });
        }
        
        if (this.modal) {
            this.modal.addEventListener('click', function(e) {
                if (e.target === loginComponent.modal) {
                    loginComponent.hide();
                }
            });
        }
    },

    setupSwitchToRegister() {
        var switchBtn = document.getElementById('switch-to-register');
        if (switchBtn) {
            switchBtn.addEventListener('click', function(e) {
                e.preventDefault();
                loginComponent.hide();
                if (window.registerComponent && typeof window.registerComponent.show === 'function') {
                    window.registerComponent.show();
                } else {
                    window.location.hash = '#register';
                }
            });
        }
    },

    show() {
        if (this.modal) {
            this.modal.style.display = 'flex';
        }
    },

    hide() {
        if (this.modal) {
            this.modal.style.display = 'none';
        }
    }
};

// Controlador de página para app.js (cuando se navega a #login)
var loginPage = {
    init: function() {
        // Asegura que el formulario del DOM de la página tenga el handler
        loginComponent.setupForm();
    }
};

// Defensa adicional: si el script se carga y el formulario ya está en el DOM
if (document.readyState === 'complete' || document.readyState === 'interactive') {
    if (document.getElementById('login-form')) {
        loginComponent.setupForm();
    }
} else {
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('login-form')) {
            loginComponent.setupForm();
        }
    });
}