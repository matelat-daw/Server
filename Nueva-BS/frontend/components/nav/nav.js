// Navigation Component (se puede usar para breadcrumbs u otra navegación secundaria)
const navComponent = {
    init() {
        // Por ahora vacío, pero disponible para futuras mejoras
        const container = document.getElementById('nav-component');
        if (container) {
            container.innerHTML = '';
        }
    }
};

window.navComponent = navComponent;
