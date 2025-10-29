var loginComponent = {
    modal: null,
    
    init() {
        this.createModal();
    },

    createModal() {
        var container = document.createElement('div');
        container.id = 'login-container';
        document.body.appendChild(container);
        
        fetch('/Nueva-WEB/frontend/components/login/login.html')
            .then(function(response) { return response.text(); })
            .then(function(html) {
                container.innerHTML = html;
                loginComponent.modal = document.getElementById('login-modal');
                loginComponent.setupForm();
                loginComponent.setupClose();
                loginComponent.setupSwitchToRegister();
            });
    },

    setupForm() {
        var form = document.getElementById('login-form');
        if (form) {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                // Recoge los datos del formulario
                var username = document.getElementById('username').value;
                var password = document.getElementById('password').value;
                // Llama a AuthService.login pasando un objeto
                var result = await AuthService.login({ username, password });
                if (result.success) {
                    loginComponent.hide();
                    var event = new CustomEvent('userLoggedIn', { detail: result.user });
                    document.dispatchEvent(event);
                } else {
                    if (typeof showModal === 'function') {
                        showModal(result.message || 'Credenciales incorrectas', 'error');
                    } else {
                        alert(result.message || 'Credenciales incorrectas');
                    }
                    // El formulario permanece visible para reintentar
                }
            });
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
                registerComponent.show();
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