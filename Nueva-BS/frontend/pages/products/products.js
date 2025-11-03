// Products Page
const productsPage = {
    async init() {
        this.render();
        await this.loadProducts();
        this.setupFilters();
    },

    render() {
        const container = document.getElementById('main-content');
        if (!container) return;

        container.innerHTML = `
            <div class="bg-primary text-white py-4">
                <div class="container">
                    <h2 class="display-6 fw-bold mb-0">
                        <i class="fas fa-shopping-bag me-2"></i>Nuestros Productos
                    </h2>
                </div>
            </div>

            <div class="container my-4">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" 
                                   class="form-control" 
                                   id="search-products" 
                                   placeholder="Buscar productos...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <select class="form-select" id="category-filter">
                            <option value="">Todas las categorías</option>
                            <option value="Portátiles">Portátiles</option>
                            <option value="Accesorios">Accesorios</option>
                            <option value="Periféricos">Periféricos</option>
                            <option value="Monitores">Monitores</option>
                            <option value="Audio">Audio</option>
                        </select>
                    </div>
                </div>

                <div id="products-container" class="row">
                    <div class="col-12 text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="text-muted mt-3">Cargando productos...</p>
                    </div>
                </div>
            </div>
        `;
    },

    setupFilters() {
        const categoryFilter = document.getElementById('category-filter');
        const searchInput = document.getElementById('search-products');

        if (categoryFilter) {
            categoryFilter.addEventListener('change', () => this.loadProducts());
        }

        if (searchInput) {
            searchInput.addEventListener('input', () => this.loadProducts());
        }
    },

    async loadProducts() {
        const container = document.getElementById('products-container');
        if (!container) return;

        try {
            const response = await ApiService.get('/products');

            if (response && response.success && response.products) {
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
        const container = document.getElementById('products-container');
        if (!container) return;

        // Aplicar filtros
        const categoryFilter = document.getElementById('category-filter')?.value || '';
        const searchQuery = document.getElementById('search-products')?.value.toLowerCase() || '';

        let filteredProducts = products;

        if (categoryFilter) {
            filteredProducts = filteredProducts.filter(p => p.category === categoryFilter);
        }

        if (searchQuery) {
            filteredProducts = filteredProducts.filter(p =>
                p.name.toLowerCase().includes(searchQuery) ||
                p.description?.toLowerCase().includes(searchQuery)
            );
        }

        container.innerHTML = '';

        if (filteredProducts.length === 0) {
            container.innerHTML = `
                <div class="col-12 text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No se encontraron productos</h5>
                    <p class="text-muted">Intenta con otros criterios de búsqueda</p>
                </div>
            `;
            return;
        }

        filteredProducts.forEach(product => {
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
            },
            {
                id: 5,
                name: 'Auriculares Sony WH-1000XM5',
                price: 349.99,
                description: 'Auriculares inalámbricos con cancelación de ruido líder',
                category: 'Audio',
                image: '/Nueva-BS/frontend/imgs/headphones.jpg'
            },
            {
                id: 6,
                name: 'Webcam Logitech HD Pro',
                price: 79.99,
                description: 'Webcam Full HD 1080p con micrófono integrado',
                category: 'Accesorios',
                image: '/Nueva-BS/frontend/imgs/webcam.jpg'
            }
        ];

        this.displayProducts(samples);
    }
};

window.productsPage = productsPage;
