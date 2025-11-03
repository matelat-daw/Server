// About Page
const aboutPage = {
    init() {
        this.render();
    },

    render() {
        const container = document.getElementById('main-content');
        if (!container) return;

        container.innerHTML = `
            <div class="bg-primary text-white py-4">
                <div class="container">
                    <h2 class="display-6 fw-bold mb-0">
                        <i class="fas fa-info-circle me-2"></i>Acerca de Nosotros
                    </h2>
                </div>
            </div>

            <div class="container my-5">
                <div class="row align-items-center mb-5">
                    <div class="col-md-6">
                        <h3 class="fw-bold mb-3">¿Quiénes Somos?</h3>
                        <p class="text-muted mb-3">
                            TechStore es tu tienda de confianza para productos de tecnología. 
                            Con más de 10 años de experiencia en el mercado, nos dedicamos a 
                            ofrecer los mejores productos tecnológicos al mejor precio.
                        </p>
                        <p class="text-muted">
                            Nuestro compromiso es brindarte la mejor experiencia de compra, 
                            con productos de calidad, envíos rápidos y un servicio al cliente excepcional.
                        </p>
                    </div>
                    <div class="col-md-6 text-center">
                        <i class="fas fa-laptop-code" style="font-size: 150px; color: var(--primary-blue); opacity: 0.3;"></i>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="card h-100 text-center border-primary">
                            <div class="card-body">
                                <i class="fas fa-award fa-3x text-primary mb-3"></i>
                                <h5 class="card-title">Calidad Garantizada</h5>
                                <p class="card-text text-muted">
                                    Todos nuestros productos cuentan con garantía oficial
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100 text-center border-primary">
                            <div class="card-body">
                                <i class="fas fa-users fa-3x text-primary mb-3"></i>
                                <h5 class="card-title">Atención Personalizada</h5>
                                <p class="card-text text-muted">
                                    Nuestro equipo está listo para ayudarte
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100 text-center border-primary">
                            <div class="card-body">
                                <i class="fas fa-shipping-fast fa-3x text-primary mb-3"></i>
                                <h5 class="card-title">Envío Rápido</h5>
                                <p class="card-text text-muted">
                                    Recibe tus productos en tiempo récord
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
};

window.aboutPage = aboutPage;
