// Header Component
const headerComponent = {
    init() {
        this.render();
        this.updateCartBadge();
        this.initDropdowns();
        
        window.addEventListener('cart-updated', () => this.updateCartBadge());
        
        // Escuchar eventos de login/logout
        document.addEventListener('userLoggedIn', () => this.updateUserMenu());
        document.addEventListener('userLoggedOut', () => this.updateUserMenu());
    },

    initDropdowns() {
        // Inicializar todos los dropdowns de Bootstrap
        const dropdownElements = document.querySelectorAll('[data-bs-toggle="dropdown"]');
        dropdownElements.forEach(el => {
            new bootstrap.Dropdown(el);
        });
    },

    render() {
        const container = document.getElementById('header-component');
        if (!container) return;

        container.innerHTML = `
            <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #0a58ca 0%, #0d6efd 100%);">
                <div class="container">
                    <a class="navbar-brand fw-bold" href="/Nueva-BS/#/home">
                        <i class="fas fa-laptop-code me-2"></i>TechStore
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav ms-auto align-items-center">
                            <li class="nav-item">
                                <a class="nav-link" href="/Nueva-BS/#/home">
                                    <i class="fas fa-home me-1"></i>Inicio
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/Nueva-BS/#/products">
                                    <i class="fas fa-shopping-bag me-1"></i>Productos
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/Nueva-BS/#/about">
                                    <i class="fas fa-info-circle me-1"></i>Acerca de
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/Nueva-BS/#/contact">
                                    <i class="fas fa-envelope me-1"></i>Contacto
                                </a>
                            </li>
                            <li class="nav-item ms-lg-3">
                                <button class="btn btn-light position-relative" onclick="cartComponent.open()">
                                    <i class="fas fa-shopping-cart"></i>
                                    <span id="cart-badge" class="cart-badge" style="display: none;">0</span>
                                </button>
                            </li>
                            <li class="nav-item ms-2" id="user-menu-container">
                                ${this.renderUserMenu()}
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        `;
    },

    updateUserMenu() {
        const container = document.getElementById('user-menu-container');
        if (container) {
            container.innerHTML = this.renderUserMenu();
            
            // Reinicializar dropdowns de Bootstrap después de actualizar el HTML
            const dropdownElements = container.querySelectorAll('[data-bs-toggle="dropdown"]');
            dropdownElements.forEach(el => {
                new bootstrap.Dropdown(el);
            });
        }
    },

    confirmLogout() {
        window.showConfirm(
            '¿Estás seguro de que quieres cerrar sesión?',
            'Cerrar Sesión',
            () => {
                AuthService.logout();
            }
        );
    },

    renderUserMenu() {
        if (AuthService.isAuthenticated()) {
            const user = AuthService.getCurrentUser();
            console.log('Usuario actual:', user); // Debug
            
            // Construir nombre completo o usar username
            let displayName = 'Usuario';
            if (user) {
                if (user.first_name && user.last_name) {
                    displayName = `${user.first_name} ${user.last_name}`;
                } else if (user.first_name) {
                    displayName = user.first_name;
                } else if (user.username) {
                    displayName = user.username;
                }
            }
            
            return `
                <div class="dropdown">
                    <button class="btn btn-outline-light dropdown-toggle" type="button" id="userMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle me-1"></i>${displayName}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenuButton">
                        <li>
                            <h6 class="dropdown-header">
                                <i class="fas fa-envelope me-2"></i>${user?.email || ''}
                            </h6>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="/Nueva-BS/#/profile"><i class="fas fa-id-card me-2"></i>Mi Perfil</a></li>
                        <li><a class="dropdown-item" href="/Nueva-BS/#/orders"><i class="fas fa-box me-2"></i>Mis Pedidos</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); headerComponent.confirmLogout();"><i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión</a></li>
                    </ul>
                </div>
            `;
        } else {
            return `
                <a href="/Nueva-BS/#/login" class="btn btn-outline-light">
                    <i class="fas fa-sign-in-alt me-1"></i>Iniciar Sesión
                </a>
            `;
        }
    },

    updateCartBadge() {
        const badge = document.getElementById('cart-badge');
        if (badge) {
            const count = window.cartService.getItemCount();
            if (count > 0) {
                badge.textContent = count;
                badge.style.display = 'flex';
            } else {
                badge.style.display = 'none';
            }
        }
    }
};

window.headerComponent = headerComponent;

// Auto-inicializar
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => headerComponent.init());
} else {
    headerComponent.init();
}
