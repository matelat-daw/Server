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
            console.error('Error loading products:', error);
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
            { id: 1, name: 'Laptop HP', price: 599.99, description: 'Laptop HP 15.6" Intel Core i5' },
            { id: 2, name: 'Mouse Logitech', price: 19.99, description: 'Mouse inalámbrico' },
            { id: 3, name: 'Teclado Mecánico', price: 79.99, description: 'Teclado mecánico RGB' },
            { id: 4, name: 'Monitor Samsung', price: 149.99, description: 'Monitor 24" Full HD' }
        ];
        
        this.displayProducts(sampleProducts, container);
    }
};