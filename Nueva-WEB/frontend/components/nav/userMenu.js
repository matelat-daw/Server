// User menu component for nav bar
var userMenuComponent = {
    updateUser: function(user) {
        var wrapper = document.getElementById('user-menu-wrapper');
        if (!wrapper) return;
        // Limpiar wrapper antes de inyectar
        wrapper.innerHTML = '';
        // Mostrar nombre amigable
        var displayName = user.first_name || user.username || user.email || 'Usuario';
        wrapper.innerHTML = `
            <div class="user-menu">
                <button class="user-menu-btn" id="user-menu-btn">
                    <span class="user-menu-name">${displayName}</span>
                    <span class="user-menu-caret">▼</span>
                </button>
                <div class="user-menu-dropdown" id="user-menu-dropdown" style="display:none;">
                    <a href="#profile" class="user-menu-link">Perfil</a>
                    <a href="#orders" class="user-menu-link">Mis compras</a>
                    <a href="#" class="user-menu-link" id="logout-link">Cerrar sesión</a>
                </div>
            </div>
        `;
        var btn = document.getElementById('user-menu-btn');
        var dropdown = document.getElementById('user-menu-dropdown');
        if (btn && dropdown) {
            // Eliminar listeners previos
            btn.onclick = null;
            btn.onclick = function(e) {
                e.stopPropagation();
                dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
            };
            // Solo cerrar el dropdown si está abierto
            document.addEventListener('click', function hideDropdown() {
                if (dropdown.style.display === 'block') {
                    dropdown.style.display = 'none';
                }
            }, { once: true });
        }
        var logout = document.getElementById('logout-link');
        if (logout) {
            logout.onclick = function(e) {
                e.preventDefault();
                AuthService.logout();
            };
        }
    }
};
window.userMenuComponent = userMenuComponent;
