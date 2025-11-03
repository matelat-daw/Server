
var homePage = {
    initialized: false,
    
    init: function() {
        if (this.initialized) {

            return;
        }
        this.initialized = true;

        var self = this;
        
        // Buscar contenedor con múltiples intentos
        var attempts = 0;
        var maxAttempts = 20;
        
        var findContainer = function() {
            attempts++;
            var container = document.getElementById('featured-products-container');
            
            if (container) {

                self.loadFeaturedProducts();
            } else if (attempts < maxAttempts) {

                setTimeout(findContainer, 100);
            } else {

                console.log('Page HTML:', document.getElementById('main-content').innerHTML.substring(0, 200));
            }
        };
        
        findContainer();
    },

    loadFeaturedProducts: function() {
        var container = document.getElementById('featured-products-container');
        
        if (!container) {

            return;
        }

        var self = this;
        
        container.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:2rem;color:#718096;"><div class="loading"></div><div style="margin-top:1rem;">Cargando productos...</div></div>';
        
        // Intentar cargar de la API
        if (window.ApiService) {

            ApiService.get('/products/featured')
                .then(function(response) {

                    if (response && response.success && response.products && response.products.length > 0) {

                        self.displayProducts(container, response.products);
                    } else {

                        self.showSampleProducts(container);
                    }
                })
                .catch(function(error) {

                    self.showSampleProducts(container);
                });
        } else {

            self.showSampleProducts(container);
        }
    },

    displayProducts: function(container, products) {
        if (!container) {

            return;
        }
        
        container.innerHTML = '';
        
        if (!products || products.length === 0) {
            container.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:2rem;color:#718096;">No hay productos disponibles</div>';
            return;
        }

        for (var i = 0; i < products.length; i++) {
            try {
                if (window.productCardComponent) {
                    var card = productCardComponent.create(products[i]);
                    container.appendChild(card);
                } else {
                    this.createSimpleCard(container, products[i]);
                }
            } catch (error) {

            }
        }
    },

    createSimpleCard: function(container, product) {
        var card = document.createElement('div');
        card.className = 'product-card';
        card.style.cssText = 'background:white;padding:1.5rem;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.1);text-align:center;';
        
        var html = '<div style="background:#f0f0f0;height:150px;border-radius:8px;margin-bottom:1rem;display:flex;align-items:center;justify-content:center;color:#999;">📦 Imagen</div>';
        html += '<h3 style="margin:0.5rem 0;color:#1a202c;">' + product.name + '</h3>';
        html += '<p style="color:#718096;font-size:0.9rem;margin:0.5rem 0;">' + (product.description || '').substring(0, 60) + '...</p>';
        html += '<p style="font-size:1.5rem;font-weight:bold;color:#FF6B9D;margin:1rem 0;">' + product.price + '€</p>';
        html += '<button class="btn-primary" style="width:100%;padding:0.75rem;border:none;background:linear-gradient(135deg,#FF6B9D,#C77DFF);color:white;border-radius:8px;cursor:pointer;font-weight:600;">Añadir al Carrito</button>';
        
        card.innerHTML = html;
        container.appendChild(card);
    },

    showSampleProducts: function(container) {
        var samples = [
            { 
                id: 1, 
                name: 'Laptop HP ProBook', 
                price: 599.99, 
                description: 'Laptop HP 15.6" Intel Core i5, 8GB RAM, 256GB SSD', 
                stock: 15,
                category: 'Electrónica',
                image: '/Nueva-WEB/media/productos/laptop.jpg'
            },
            { 
                id: 2, 
                name: 'Mouse Logitech', 
                price: 89.99, 
                description: 'Mouse inalámbrico Logitech con sensor de alta precisión', 
                stock: 25,
                category: 'Accesorios',
                image: '/Nueva-WEB/media/productos/mouse.jpg'
            },
            { 
                id: 3, 
                name: 'Teclado Mecánico RGB', 
                price: 129.99, 
                description: 'Teclado mecánico RGB gaming con switches mecánicos', 
                stock: 12,
                category: 'Accesorios',
                image: '/Nueva-WEB/media/productos/teclado.jpg'
            },
            { 
                id: 4, 
                name: 'Monitor Samsung 4K', 
                price: 299.99, 
                description: 'Monitor Samsung 27" 4K UHD con panel IPS', 
                stock: 8,
                category: 'Electrónica',
                image: '/Nueva-WEB/media/productos/monitor.jpg'
            }
        ];

        this.displayProducts(container, samples);
    }
};

// Make it globally available
window.homePage = homePage;

// Reset on page change
document.addEventListener('pageChanged', function(e) {

    if (homePage) {
        homePage.initialized = false;
    }
});
