// Home Page
const homePage = {
    async init() {
        this.render();
        await this.loadFeaturedProducts();
    },

    render() {
        const container = document.getElementById('main-content');
        if (!container) return;

        container.innerHTML = `
            <!-- Hero Section -->
            <section class="hero-section">
                <div class="container">
                    <h1 class="display-4 fw-bold mb-3">
                        <i class="fas fa-laptop-code me-3"></i>Bienvenido a TechStore
                    </h1>
                    <p class="lead mb-4">
                        Los mejores productos de tecnología al mejor precio
                    </p>
                    <a href="/Nueva-BS/#/products" class="btn btn-light btn-lg px-5">
                        <i class="fas fa-shopping-bag me-2"></i>Ver Productos
                    </a>
                </div>
            </section>

            <!-- Featured Products -->
            <section class="container my-5">
                <div class="text-center mb-5">
                    <h2 class="display-6 fw-bold mb-2">Productos Destacados</h2>
                    <p class="text-muted">Descubre nuestras mejores ofertas</p>
                </div>
                <div id="featured-products-container" class="row">
                    <div class="col-12 text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="text-muted mt-3">Cargando productos...</p>
                    </div>
                </div>
            </section>

            <!-- Features Section -->
            <section class="container my-5">
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="card h-100 text-center border-0 shadow-sm">
                            <div class="card-body">
                                <i class="fas fa-truck fa-3x text-primary mb-3"></i>
                                <h5 class="card-title">Envío Gratis</h5>
                                <p class="card-text text-muted">En pedidos superiores a 50€</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100 text-center border-0 shadow-sm">
                            <div class="card-body">
                                <i class="fas fa-shield-alt fa-3x text-primary mb-3"></i>
                                <h5 class="card-title">Compra Segura</h5>
                                <p class="card-text text-muted">Protección total en tus pagos</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100 text-center border-0 shadow-sm">
                            <div class="card-body">
                                <i class="fas fa-headset fa-3x text-primary mb-3"></i>
                                <h5 class="card-title">Soporte 24/7</h5>
                                <p class="card-text text-muted">Estamos aquí para ayudarte</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        `;
    },

    async loadFeaturedProducts() {
        const container = document.getElementById('featured-products-container');
        if (!container) return;

        try {
            const response = await ApiService.get('/products/featured');
            
            if (response && response.success && response.products && response.products.length > 0) {
                this.displayProducts(response.products);
            } else {
                this.showSampleProducts();
            }
        } catch (error) {
            console.error('Error loading products:', error);
            this.showSampleProducts();
        }
    },

    displayProducts(products) {
        const container = document.getElementById('featured-products-container');
        if (!container) return;

        container.innerHTML = '';

        if (!products || products.length === 0) {
            container.innerHTML = `
                <div class="col-12 text-center py-5">
                    <p class="text-muted">No hay productos disponibles</p>
                </div>
            `;
            return;
        }

        products.forEach(product => {
            const card = productCardComponent.create(product);
            container.appendChild(card);
        });
    },

    showSampleProducts() {
        const samples = [
            {
                id: 1,
                name: 'Laptop HP ProBook',
                price: 599.99,
                description: 'Laptop HP 15.6" Intel Core i5, 8GB RAM, 256GB SSD',
                category: 'Portátiles',
                image: '/Nueva-BS/frontend/imgs/laptop.jpg'
            },
            {
                id: 2,
                name: 'Mouse Logitech MX Master',
                price: 89.99,
                description: 'Mouse inalámbrico profesional con sensor de alta precisión',
                category: 'Accesorios',
                image: '/Nueva-BS/frontend/imgs/mouse.jpg'
            },
            {
                id: 3,
                name: 'Teclado Mecánico RGB',
                price: 129.99,
                description: 'Teclado mecánico gaming con switches mecánicos y RGB',
                category: 'Periféricos',
                image: '/Nueva-BS/frontend/imgs/keyboard.jpg'
            },
            {
                id: 4,
                name: 'Monitor Samsung 4K',
                price: 299.99,
                description: 'Monitor Samsung 27" 4K UHD con panel IPS',
                category: 'Monitores',
                image: '/Nueva-BS/frontend/imgs/monitor.jpg'
            }
        ];

        this.displayProducts(samples);
    }
};

window.homePage = homePage;
