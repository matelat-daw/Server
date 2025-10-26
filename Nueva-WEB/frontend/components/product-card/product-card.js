var productCardComponent = {
    create: function(product) {
        var card = document.createElement('div');
        card.className = 'product-card';
        
        var img = document.createElement('img');
        img.src = product.image || 'https://via.placeholder.com/300x200/FF6B9D/FFFFFF?text=Producto';
        img.alt = product.name || 'Producto';
        img.className = 'product-image';
        img.onerror = function() {
            this.src = 'https://via.placeholder.com/300x200/FF6B9D/FFFFFF?text=Producto';
        };
        
        var nameEl = document.createElement('h3');
        nameEl.className = 'product-name';
        nameEl.textContent = product.name || 'Producto sin nombre';
        
        var descEl = document.createElement('p');
        descEl.className = 'product-description';
        descEl.textContent = product.description || '';
        
        var priceEl = document.createElement('p');
        priceEl.className = 'product-price';
        var price = typeof product.price === 'number' ? product.price.toFixed(2) : product.price;
        priceEl.textContent = price + '€';
        
        var button = document.createElement('button');
        button.className = 'btn-primary add-to-cart';
        button.setAttribute('data-product-id', product.id);
        button.textContent = 'Añadir al Carrito';
        
        var self = this;
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            self.addToCart(product);
        });
        
        card.appendChild(img);
        card.appendChild(nameEl);
        card.appendChild(descEl);
        card.appendChild(priceEl);
        card.appendChild(button);
        
        return card;
    },

    addToCart: function(product) {
        console.log('Adding to cart:', product);
        alert(product.name + ' añadido al carrito');
    }
};

// Make it globally available
window.productCardComponent = productCardComponent;