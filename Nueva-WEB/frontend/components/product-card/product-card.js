var productCardComponent = {
    create: function(product) {
        var card = document.createElement('div');
        card.className = 'product-card';
        
        var imageUrl = product.image || 'https://via.placeholder.com/300/FF6B9D/FFFFFF?text=' + encodeURIComponent(product.name);
        var description = product.description || '';
        var price = typeof product.price === 'number' ? product.price.toFixed(2) : product.price;
        
        var img = document.createElement('img');
        img.src = imageUrl;
        img.alt = product.name;
        img.className = 'product-image';
        img.onerror = function() {
            this.src = 'https://via.placeholder.com/300/FF6B9D/FFFFFF?text=Producto';
        };
        
        var nameEl = document.createElement('h3');
        nameEl.className = 'product-name';
        nameEl.textContent = product.name;
        
        var descEl = document.createElement('p');
        descEl.className = 'product-description';
        descEl.textContent = description;
        
        var priceEl = document.createElement('p');
        priceEl.className = 'product-price';
        priceEl.textContent = price + '€';
        
        var button = document.createElement('button');
        button.className = 'btn-primary add-to-cart';
        button.setAttribute('data-product-id', product.id);
        button.textContent = 'Añadir al Carrito';
        
        var self = this;
        button.addEventListener('click', function() {
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