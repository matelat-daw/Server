var productsPage = {
    async init() {
        this.setupFilters();
        await this.loadProducts();
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
                this.displayProducts(response.products, container);
            } else {
                this.showSampleProducts(container);
            }
        } catch (error) {

            this.showSampleProducts(container);
        }
    },

    displayProducts(products, container) {
        container.innerHTML = '';
        
        if (products.length === 0) {
            container.innerHTML = '<p>No se encontraron productos</p>';
            return;
        }
        
        products.forEach(product => {
            const card = productCardComponent.create(product);
            container.appendChild(card);
        });
    },

    showSampleProducts(container) {
        const sampleProducts = [
            { 
                id: 1, 
                name: 'Laptop HP', 
                price: 599.99, 
                description: 'Laptop HP 15.6" Intel Core i5, 8GB RAM, 256GB SSD',
                category: 'Electrónica',
                image: '/Nueva-WEB/media/productos/laptop.jpg'
            },
            { 
                id: 2, 
                name: 'Mouse Logitech', 
                price: 19.99, 
                description: 'Mouse inalámbrico con sensor óptico de alta precisión',
                category: 'Accesorios',
                image: '/Nueva-WEB/media/productos/mouse.jpg'
            },
            { 
                id: 3, 
                name: 'Teclado Mecánico', 
                price: 79.99, 
                description: 'Teclado mecánico RGB con switches azules',
                category: 'Accesorios',
                image: '/Nueva-WEB/media/productos/teclado.jpg'
            },
            { 
                id: 4, 
                name: 'Monitor Samsung', 
                price: 149.99, 
                description: 'Monitor 24" Full HD con tecnología IPS',
                category: 'Electrónica',
                image: '/Nueva-WEB/media/productos/monitor.jpg'
            },
            { 
                id: 5, 
                name: 'Auriculares Sony', 
                price: 89.99, 
                description: 'Auriculares inalámbricos con cancelación de ruido',
                category: 'Audio',
                image: '/Nueva-WEB/media/productos/auriculares.jpg'
            },
            { 
                id: 6, 
                name: 'Webcam Logitech HD', 
                price: 59.99, 
                description: 'Webcam Full HD 1080p con micrófono integrado',
                category: 'Accesorios',
                image: '/Nueva-WEB/media/productos/webcam.jpg'
            }
        ];
        
        this.displayProducts(sampleProducts, container);
    }
};
