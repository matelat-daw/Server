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
                
                var email = document.getElementById('login-email').value;
                var password = document.getElementById('login-password').value;
                
                var result = await AuthService.login(email, password);
                
                if (result.success) {
                    loginComponent.hide();
                    var event = new CustomEvent('userLoggedIn', { detail: result.user });
                    document.dispatchEvent(event);
                } else {
                    alert(result.message || 'Error al iniciar sesión');
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