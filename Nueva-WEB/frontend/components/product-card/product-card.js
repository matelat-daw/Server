var productCardComponent = {
    create: function(product) {
        var card = document.createElement('div');
        card.className = 'product-card';
        
        var img = document.createElement('img');
        img.src = product.image || '/Nueva-WEB/frontend/imgs/producto-generico.svg';
        img.alt = product.name || 'Producto';
        img.className = 'product-image';
        img.onerror = function handler() {
            // Si ya intentó cargar el placeholder, no volver a intentar
            if (this.src.includes('producto-generico.svg')) {
                this.onerror = null; // No volver a intentar, dejar enlace roto
            } else {
                this.src = '/Nueva-WEB/frontend/imgs/producto-generico.svg';
            }
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

        alert(product.name + ' añadido al carrito');
    }
};

// Make it globally available
window.productCardComponent = productCardComponent;
