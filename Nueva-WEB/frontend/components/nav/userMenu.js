// User menu component for nav bar
var userMenuComponent = {
    updateUser: function(user) {
        var wrapper = document.getElementById('user-menu-wrapper');
        if (!wrapper) return;
        wrapper.innerHTML = `
            <div class="user-menu">
                <button class="user-menu-btn" id="user-menu-btn">
                    <span class="user-menu-name">${user.username}</span>
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
            btn.onclick = function(e) {
                e.stopPropagation();
                dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
            };
            document.addEventListener('click', function() {
                dropdown.style.display = 'none';
            });
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
