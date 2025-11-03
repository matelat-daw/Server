// Footer Component
const footerComponent = {
    init() {
        this.render();
    },

    render() {
        const container = document.getElementById('footer-component');
        if (!container) return;

        container.innerHTML = `
            <footer class="mt-5">
                <div class="container">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <h5 class="fw-bold mb-3">
                                <i class="fas fa-laptop-code me-2"></i>TechStore
                            </h5>
                            <p class="opacity-75">
                                Tu tienda de confianza para productos de tecnología.
                                Los mejores precios y la mejor calidad.
                            </p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <h5 class="fw-bold mb-3">Enlaces Rápidos</h5>
                            <ul class="list-unstyled">
                                <li class="mb-2"><a href="/Nueva-BS/#/home"><i class="fas fa-angle-right me-2"></i>Inicio</a></li>
                                <li class="mb-2"><a href="/Nueva-BS/#/products"><i class="fas fa-angle-right me-2"></i>Productos</a></li>
                                <li class="mb-2"><a href="/Nueva-BS/#/about"><i class="fas fa-angle-right me-2"></i>Acerca de</a></li>
                                <li class="mb-2"><a href="/Nueva-BS/#/contact"><i class="fas fa-angle-right me-2"></i>Contacto</a></li>
                            </ul>
                        </div>
                        <div class="col-md-4 mb-3">
                            <h5 class="fw-bold mb-3">Contacto</h5>
                            <p class="mb-2"><i class="fas fa-envelope me-2"></i>info@techstore.com</p>
                            <p class="mb-2"><i class="fas fa-phone me-2"></i>+34 123 456 789</p>
                            <div class="mt-3">
                                <a href="#" class="text-white me-3"><i class="fab fa-facebook fa-lg"></i></a>
                                <a href="#" class="text-white me-3"><i class="fab fa-twitter fa-lg"></i></a>
                                <a href="#" class="text-white me-3"><i class="fab fa-instagram fa-lg"></i></a>
                                <a href="#" class="text-white"><i class="fab fa-linkedin fa-lg"></i></a>
                            </div>
                        </div>
                    </div>
                    <hr class="mt-4 mb-3" style="opacity: 0.3;">
                    <div class="text-center pb-3">
                        <p class="mb-0 opacity-75">
                            © ${new Date().getFullYear()} TechStore. Todos los derechos reservados.
                        </p>
                    </div>
                </div>
            </footer>
        `;
    }
};

window.footerComponent = footerComponent;

// Auto-inicializar
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => footerComponent.init());
} else {
    footerComponent.init();
}
