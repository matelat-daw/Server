var homePage = {
    initialized: false,
    
    init: function() {
        if (this.initialized) return;
        this.initialized = true;
        
        var self = this;
        var attempts = 0;
        var maxAttempts = 30;
        
        var checkContainer = function() {
            var container = document.getElementById('featured-products-container');
            if (container) {
                console.log('Container found, loading products...');
                self.loadFeaturedProducts();
            } else if (attempts < maxAttempts) {
                attempts++;
                setTimeout(checkContainer, 100);
            } else {
                console.error('Featured products container not found after ' + maxAttempts + ' attempts');
            }
        };
        
        checkContainer();
    },

    loadFeaturedProducts: function() {
        var container = document.getElementById('featured-products-container');
        
        if (!container) {
            console.error('Featured products container not found');
            return;
        }
        
        var self = this;
        
        // Show loading
        container.innerHTML = '<p style="text-align: center; color: var(--text-secondary); padding: 2rem;">Cargando productos...</p>';
        
        ApiService.get('/products/featured')
            .then(function(response) {
                console.log('API Response:', response);
                if (response && response.success && response.products) {
                    self.displayProducts(container, response.products);
                } else {
                    console.log('No products from API, showing sample products');
                    self.showSampleProducts(container);
                }
            })
            .catch(function(error) {
                console.error('Error loading products:', error);
                self.showSampleProducts(container);
            });
    },

    displayProducts: function(container, products) {
        if (!container) return;
        
        container.innerHTML = '';
        
        if (!products || products.length === 0) {
            container.innerHTML = '<p style="text-align: center; color: var(--text-secondary);">No hay productos destacados</p>';
            return;
        }
        
        for (var i = 0; i < products.length; i++) {
            var card = productCardComponent.create(products[i]);
            container.appendChild(card);
        }
        
        console.log('Products displayed:', products.length);
    },

    showSampleProducts: function(container) {
        var sampleProducts = [
            { 
                id: 1, 
                name: 'Laptop HP', 
                price: 599.99, 
                description: 'Laptop HP 15.6" Intel Core i5, 8GB RAM', 
                image: 'https://via.placeholder.com/300/FF6B9D/FFFFFF?text=Laptop+HP' 
            },
            { 
                id: 2, 
                name: 'Mouse Logitech', 
                price: 19.99, 
                description: 'Mouse inalámbrico Logitech M185', 
                image: 'https://via.placeholder.com/300/C77DFF/FFFFFF?text=Mouse' 
            },
            { 
                id: 3, 
                name: 'Teclado Mecánico', 
                price: 79.99, 
                description: 'Teclado mecánico RGB gaming', 
                image: 'https://via.placeholder.com/300/9D4EDD/FFFFFF?text=Teclado' 
            },
            { 
                id: 4, 
                name: 'Monitor Samsung', 
                price: 149.99, 
                description: 'Monitor Samsung 24" Full HD', 
                image: 'https://via.placeholder.com/300/7209B7/FFFFFF?text=Monitor' 
            }
        ];
        
        console.log('Showing sample products:', sampleProducts.length);
        this.displayProducts(container, sampleProducts);
    }
};