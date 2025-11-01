var userMenuComponent = {
    init() {
        this.setupDropdown();
        this.setupLogout();
    },

    setupDropdown() {
        const userButton = document.getElementById('user-button');
        const userDropdown = document.getElementById('user-dropdown');
        
        if (userButton) {
            userButton.addEventListener('click', (e) => {
                e.stopPropagation();
                userDropdown.classList.toggle('show');
            });
        }

        document.addEventListener('click', () => {
            if (userDropdown) {
                userDropdown.classList.remove('show');
            }
        });
    },

    setupLogout() {
        var logoutBtn = document.getElementById('logout-btn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', function(e) {
                e.preventDefault();
                // Evitar múltiples modales
                if (document.getElementById('logout-modal')) return;
                // Crear modal de confirmación
                var modal = document.createElement('div');
                modal.id = 'logout-modal';
                modal.style.position = 'fixed';
                modal.style.top = '0';
                modal.style.left = '0';
                modal.style.width = '100vw';
                modal.style.height = '100vh';
                modal.style.background = 'rgba(0,0,0,0.4)';
                modal.style.display = 'flex';
                modal.style.alignItems = 'center';
                modal.style.justifyContent = 'center';
                modal.style.zIndex = '9999';
                modal.innerHTML = `
                    <div style="background:#fff;padding:2rem 2.5rem;border-radius:10px;box-shadow:0 2px 16px #0002;text-align:center;max-width:90vw;">
                        <h3 style="margin-bottom:1rem;">¿Cerrar sesión?</h3>
                        <p style="margin-bottom:2rem;">¿Seguro que deseas cerrar tu sesión?</p>
                        <button data-logout-action="confirm" style="background:#e53e3e;color:#fff;padding:0.5rem 1.5rem;border:none;border-radius:5px;margin-right:1rem;cursor:pointer;">Aceptar</button>
                        <button data-logout-action="cancel" style="background:#eee;color:#333;padding:0.5rem 1.5rem;border:none;border-radius:5px;cursor:pointer;">Cancelar</button>
                    </div>
                `;
                document.body.appendChild(modal);
                // Delegación de eventos para los botones
                modal.addEventListener('click', function(ev) {
                    if (ev.target && ev.target.getAttribute('data-logout-action') === 'cancel') {
                        document.body.removeChild(modal);
                    }
                    if (ev.target && ev.target.getAttribute('data-logout-action') === 'confirm') {
                        document.body.removeChild(modal);
                        // Llamar a AuthService.logout() que maneja todo el proceso
                        if (window.AuthService && typeof AuthService.logout === 'function') {
                            AuthService.logout();
                        } else {
                            // Fallback: limpiar manualmente y redirigir
                            localStorage.removeItem('currentUser');
                            document.cookie = 'auth_token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
                            window.location.hash = '#home';
                            window.location.reload();
                        }
                    }
                });
            });
        }
    },

    updateUser(user) {
        var userName = document.getElementById('user-name');
        var userAvatar = document.getElementById('user-avatar');
        if (userName) {
            userName.textContent = user.first_name || user.username || 'Usuario';
        }
        if (userAvatar) {
            // Usar profile_img del backend o fallback a default
            var avatarUrl = user.profile_img || '/Nueva-WEB/media/default.jpg';
            userAvatar.src = avatarUrl;
        }
        // Siempre volver a enganchar el logout tras renderizar
        this.setupLogout();
    },

    updateCartCount(count) {
        const cartCount = document.getElementById('cart-count');
        if (cartCount) {
            cartCount.textContent = count;
            cartCount.style.display = count > 0 ? 'inline' : 'none';
        }
    }
};