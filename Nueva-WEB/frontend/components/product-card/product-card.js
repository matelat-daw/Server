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
        // Verificar que el cartService esté disponible
        if (typeof window.cartService === 'undefined') {
            window.showModal('El servicio del carrito no está disponible. Por favor recarga la página.', 'error');
            return;
        }

        // Preparar el producto en el formato que espera el carrito
        const cartItem = {
            id: product.id,
            name: product.name,
            price: parseFloat(product.price),
            image: product.image || '/Nueva-WEB/frontend/imgs/producto-generico.svg',
            category: product.category || 'Sin categoría'
        };

        // Agregar al carrito
        window.cartService.addItem(cartItem);

        // Mostrar notificación de éxito
        this.showAddedNotification(product);
    },

    showAddedNotification: function(product) {
        // Crear notificación temporal
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 80px;
            right: 20px;
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(72, 187, 120, 0.4);
            z-index: 10000;
            display: flex;
            align-items: center;
            gap: 1rem;
            animation: slideIn 0.3s ease;
            max-width: 350px;
        `;

        notification.innerHTML = `
            <div style="font-size: 2rem;">✅</div>
            <div style="flex: 1;">
                <div style="font-weight: 600; margin-bottom: 0.25rem;">Producto agregado</div>
                <div style="font-size: 0.9rem; opacity: 0.95;">${product.name}</div>
            </div>
            <button onclick="this.parentElement.remove()" style="background: rgba(255,255,255,0.2); border: none; color: white; width: 30px; height: 30px; border-radius: 50%; cursor: pointer; font-size: 1.2rem; display: flex; align-items: center; justify-content: center;">×</button>
        `;

        // Agregar animación CSS si no existe
        if (!document.getElementById('notification-styles')) {
            const style = document.createElement('style');
            style.id = 'notification-styles';
            style.textContent = `
                @keyframes slideIn {
                    from {
                        transform: translateX(400px);
                        opacity: 0;
                    }
                    to {
                        transform: translateX(0);
                        opacity: 1;
                    }
                }
                @keyframes slideOut {
                    from {
                        transform: translateX(0);
                        opacity: 1;
                    }
                    to {
                        transform: translateX(400px);
                        opacity: 0;
                    }
                }
            `;
            document.head.appendChild(style);
        }

        document.body.appendChild(notification);

        // Auto-eliminar después de 3 segundos
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
};

// Make it globally available
window.productCardComponent = productCardComponent;
