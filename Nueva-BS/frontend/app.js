// App Router - Maneja la navegación SPA
const App = {
    routes: {
        '/': 'home',
        '/home': 'home',
        '/products': 'products',
        '/about': 'about',
        '/contact': 'contact',
        '/login': 'login',
        '/register': 'register',
        '/profile': 'profile',
        '/activate': 'activate'
    },

    init() {
        this.handleRouteChange();
        window.addEventListener('hashchange', () => this.handleRouteChange());
        
        // Manejar links internos
        document.addEventListener('click', (e) => {
            if (e.target.tagName === 'A' && e.target.href.includes('#/')) {
                const hash = e.target.href.split('#')[1];
                if (hash) {
                    e.preventDefault();
                    window.location.hash = hash;
                }
            }
        });
    },

    handleRouteChange() {
        const hash = window.location.hash.slice(1) || '/';
        // Extract route without query parameters
        const route = hash.split('?')[0];
        const routeName = this.routes[route] || 'home';
        
        this.loadPage(routeName);
    },

    loadPage(pageName) {
        const pageMap = {
            'home': window.homePage,
            'products': window.productsPage,
            'about': window.aboutPage,
            'contact': window.contactPage,
            'login': window.loginPage,
            'register': window.registerPage,
            'profile': window.profilePage,
            'activate': window.activatePage
        };

        const page = pageMap[pageName];

        if (page && typeof page.init === 'function') {
            // Scroll to top
            window.scrollTo(0, 0);
            
            // Inicializar página
            page.init();
        } else {
            this.show404();
        }
    },

    show404() {
        const container = document.getElementById('main-content');
        if (!container) return;

        container.innerHTML = `
            <div class="container text-center my-5 py-5">
                <i class="fas fa-exclamation-triangle fa-5x text-warning mb-4"></i>
                <h1 class="display-4 fw-bold mb-3">404</h1>
                <h3 class="mb-4">Página No Encontrada</h3>
                <p class="text-muted mb-4">
                    Lo sentimos, la página que buscas no existe.
                </p>
                <a href="/Nueva-BS/#/home" class="btn btn-primary btn-lg">
                    <i class="fas fa-home me-2"></i>Volver al Inicio
                </a>
            </div>
        `;
    }
};

// Inicializar cuando el DOM esté listo
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => App.init());
} else {
    App.init();
}
