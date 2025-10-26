var registerComponent = {
    modal: null,
    
    init() {
        this.createModal();
    },

    createModal() {
        var container = document.createElement('div');
        container.id = 'register-container';
        document.body.appendChild(container);
        
        fetch('/Nueva-WEB/frontend/components/register/register.html')
            .then(function(response) { return response.text(); })
            .then(function(html) {
                container.innerHTML = html;
                registerComponent.modal = document.getElementById('register-modal');
                registerComponent.setupForm();
                registerComponent.setupClose();
                registerComponent.setupSwitchToLogin();
            });
    },

    setupForm() {
        var form = document.getElementById('register-form');
        if (form) {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                var username = document.getElementById('register-username').value;
                var email = document.getElementById('register-email').value;
                var password = document.getElementById('register-password').value;
                var confirm = document.getElementById('register-confirm').value;
                
                if (password !== confirm) {
                    alert('Las contraseñas no coinciden');
                    return;
                }
                
                var result = await AuthService.register({ username: username, email: email, password: password });
                
                if (result.success) {
                    registerComponent.hide();
                    var event = new CustomEvent('userLoggedIn', { detail: result.user });
                    document.dispatchEvent(event);
                } else {
                    alert(result.message || 'Error al registrarse');
                }
            });
        }
    },

    setupClose() {
        var closeBtn = document.getElementById('close-register');
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                registerComponent.hide();
            });
        }
        
        if (this.modal) {
            this.modal.addEventListener('click', function(e) {
                if (e.target === registerComponent.modal) {
                    registerComponent.hide();
                }
            });
        }
    },

    setupSwitchToLogin() {
        var switchBtn = document.getElementById('switch-to-login');
        if (switchBtn) {
            switchBtn.addEventListener('click', function(e) {
                e.preventDefault();
                registerComponent.hide();
                loginComponent.show();
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