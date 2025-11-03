// Contact Page
const contactPage = {
    init() {
        this.render();
        this.setupForm();
    },

    render() {
        const container = document.getElementById('main-content');
        if (!container) return;

        container.innerHTML = `
            <div class="bg-primary text-white py-4">
                <div class="container">
                    <h2 class="display-6 fw-bold mb-0">
                        <i class="fas fa-envelope me-2"></i>Contacto
                    </h2>
                </div>
            </div>

            <div class="container my-5">
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <h3 class="fw-bold mb-4">Información de Contacto</h3>
                        
                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="fas fa-map-marker-alt text-primary me-2"></i>Dirección
                                </h5>
                                <p class="card-text text-muted">
                                    Calle Principal 123<br>
                                    28001 Madrid, España
                                </p>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="fas fa-phone text-primary me-2"></i>Teléfono
                                </h5>
                                <p class="card-text text-muted">
                                    +34 123 456 789
                                </p>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="fas fa-envelope text-primary me-2"></i>Email
                                </h5>
                                <p class="card-text text-muted">
                                    info@techstore.com
                                </p>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="fas fa-clock text-primary me-2"></i>Horario
                                </h5>
                                <p class="card-text text-muted mb-0">
                                    Lunes a Viernes: 9:00 - 18:00<br>
                                    Sábados: 10:00 - 14:00<br>
                                    Domingos: Cerrado
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h3 class="fw-bold mb-4">Envíanos un Mensaje</h3>
                        
                        <form id="contact-form">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nombre *</label>
                                <input type="text" class="form-control" id="name" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" required>
                            </div>

                            <div class="mb-3">
                                <label for="subject" class="form-label">Asunto *</label>
                                <input type="text" class="form-control" id="subject" required>
                            </div>

                            <div class="mb-3">
                                <label for="message" class="form-label">Mensaje *</label>
                                <textarea class="form-control" id="message" rows="5" required></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-paper-plane me-2"></i>Enviar Mensaje
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        `;
    },

    setupForm() {
        const form = document.getElementById('contact-form');
        if (form) {
            form.addEventListener('submit', (e) => this.handleSubmit(e));
        }
    },

    async handleSubmit(e) {
        e.preventDefault();

        const formData = {
            name: document.getElementById('name').value,
            email: document.getElementById('email').value,
            subject: document.getElementById('subject').value,
            message: document.getElementById('message').value
        };

        try {
            const response = await ApiService.post('/contact', formData);

            if (response.success) {
                window.showModal('¡Mensaje enviado con éxito! Te responderemos pronto.', 'success');
                e.target.reset();
            } else {
                window.showModal(response.message || 'Error al enviar el mensaje', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            window.showModal('Gracias por tu mensaje. Te contactaremos pronto.', 'success');
            e.target.reset();
        }
    }
};

window.contactPage = contactPage;
