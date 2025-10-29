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
        const logoutBtn = document.getElementById('logout-btn');
        
        if (logoutBtn) {
            logoutBtn.addEventListener('click', (e) => {
                e.preventDefault();
                AuthService.logout();
            });
        }
    },

    updateUser(user) {
        const userName = document.getElementById('user-name');
        const userAvatar = document.getElementById('user-avatar');
        
        if (userName) {
            userName.textContent = user.first_name || user.username || 'Usuario';
        }
        
        if (userAvatar) {
            const avatarUrl = user.avatar || `https://ui-avatars.com/api/?name=${user.username}&background=FF6B9D&color=fff`;
            userAvatar.src = avatarUrl;
        }
    },

    updateCartCount(count) {
        const cartCount = document.getElementById('cart-count');
        if (cartCount) {
            cartCount.textContent = count;
            cartCount.style.display = count > 0 ? 'inline' : 'none';
        }
    }
};