// Cart Component - Modal del carrito de compras
var cartComponent = {
    modal: null,
    
    init() {
        // Esperar a que el servicio esté disponible
        if (typeof window.cartService === 'undefined') {
            console.warn('CartService aún no está disponible, reintentando en 100ms...');
            setTimeout(() => this.init(), 100);
            return;
        }
        
        console.log('✓ CartService inicializado correctamente');
        this.createModal();
        this.attachEventListeners();
        this.updateCartBadge();
    },

    createModal() {
        // Crear estructura del modal
        const modalHTML = `
            <div id="cart-modal" class="modal-overlay" style="display: none;">
                <div class="modal-content cart-modal">
                    <div class="modal-header">
                        <h2>🛒 Carrito de Compras</h2>
                        <button class="close-btn" onclick="cartComponent.close()">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div id="cart-items-container"></div>
                        <div id="cart-empty" style="display: none; text-align: center; padding: 40px;">
                            <p style="font-size: 1.2em; color: #666;">Tu carrito está vacío</p>
                            <button onclick="cartComponent.close()" class="btn-primary">Seguir comprando</button>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="cart-total">
                            <strong>Total:</strong>
                            <span id="cart-total-amount">€0.00</span>
                        </div>
                        <div class="cart-actions">
                            <button onclick="cartComponent.clearCart()" class="btn-secondary">Vaciar carrito</button>
                            <button onclick="cartComponent.checkout()" class="btn-primary">Proceder al pago</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Agregar al body
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        this.modal = document.getElementById('cart-modal');
    },

    attachEventListeners() {
        // Escuchar eventos del carrito
        window.addEventListener('cart-updated', () => {
            this.render();
            this.updateCartBadge();
        });

        // Cerrar modal al hacer clic fuera
        this.modal.addEventListener('click', (e) => {
            if (e.target === this.modal) {
                this.close();
            }
        });
    },

    open() {
        // Verificar que el servicio está disponible
        if (typeof window.cartService === 'undefined') {
            console.error('CartService no está inicializado. Verifica que services/cart.js se cargó correctamente.');
            window.showModal('El servicio del carrito no está disponible. Por favor recarga la página.', 'error');
            return;
        }
        
        // Verificar que el modal existe
        if (!this.modal) {
            console.error('El modal del carrito no ha sido creado');
            window.showModal('El modal del carrito no está disponible. Por favor recarga la página.', 'error');
            return;
        }
        
        this.render();
        this.modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    },

    close() {
        this.modal.style.display = 'none';
        document.body.style.overflow = '';
    },

    render() {
        if (!window.cartService) {
            console.error('CartService no disponible en render()');
            return;
        }
        
        const items = window.cartService.getItems();
        const container = document.getElementById('cart-items-container');
        const emptyMessage = document.getElementById('cart-empty');
        const totalAmount = document.getElementById('cart-total-amount');

        // Validar que los elementos existen
        if (!container || !emptyMessage || !totalAmount) {
            console.error('Los elementos del modal del carrito no existen en el DOM');
            return;
        }

        if (items.length === 0) {
            container.style.display = 'none';
            emptyMessage.style.display = 'block';
            totalAmount.textContent = '€0.00';
            return;
        }

        container.style.display = 'block';
        emptyMessage.style.display = 'none';

        // Renderizar items
        container.innerHTML = items.map(item => `
            <div class="cart-item" data-id="${item.id}">
                <img src="${item.image}" alt="${item.name}" class="cart-item-image">
                <div class="cart-item-details">
                    <h4>${item.name}</h4>
                    <p class="cart-item-category">${item.category}</p>
                    <p class="cart-item-price">€${item.price.toFixed(2)}</p>
                </div>
                <div class="cart-item-quantity">
                    <button onclick="cartComponent.decreaseQuantity(${item.id})" class="qty-btn">-</button>
                    <input type="number" value="${item.quantity}" min="1" 
                           onchange="cartComponent.updateQuantity(${item.id}, this.value)"
                           class="qty-input">
                    <button onclick="cartComponent.increaseQuantity(${item.id})" class="qty-btn">+</button>
                </div>
                <div class="cart-item-subtotal">
                    €${(item.price * item.quantity).toFixed(2)}
                </div>
                <button onclick="cartComponent.removeItem(${item.id})" class="remove-btn" title="Eliminar">
                    🗑️
                </button>
            </div>
        `).join('');

        // Actualizar total
        totalAmount.textContent = `€${window.cartService.getTotal().toFixed(2)}`;
    },

    updateCartBadge() {
        const badge = document.getElementById('cart-badge');
        const itemCount = window.cartService.getItemCount();
        
        if (badge) {
            badge.textContent = itemCount;
            badge.style.display = itemCount > 0 ? 'flex' : 'none';
        }
    },

    increaseQuantity(productId) {
        const item = window.cartService.getItems().find(i => i.id == productId);
        if (item) {
            window.cartService.updateQuantity(productId, item.quantity + 1);
        }
    },

    decreaseQuantity(productId) {
        const item = window.cartService.getItems().find(i => i.id == productId);
        if (item && item.quantity > 1) {
            window.cartService.updateQuantity(productId, item.quantity - 1);
        }
    },

    updateQuantity(productId, newValue) {
        const quantity = parseInt(newValue);
        if (quantity > 0) {
            window.cartService.updateQuantity(productId, quantity);
        }
    },

    removeItem(productId) {
        const modal = document.createElement('div');
        modal.className = 'modal-overlay';
        modal.style.cssText = 'position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.5);display:flex;align-items:center;justify-content:center;z-index:10000;';
        
        modal.innerHTML = `
            <div class="modal-content" style="background:#fff;padding:2rem;border-radius:10px;box-shadow:0 4px 20px rgba(0,0,0,0.15);text-align:center;max-width:400px;">
                <div style="font-size:3rem;margin-bottom:1rem;">🗑️</div>
                <h3 style="margin-bottom:1rem;color:#e53e3e;">Eliminar producto</h3>
                <p style="margin-bottom:2rem;color:#4a5568;">¿Seguro que quieres eliminar este producto del carrito?</p>
                <div style="display:flex;gap:1rem;justify-content:center;">
                    <button id="confirm-remove" style="background:#e53e3e;color:#fff;padding:0.75rem 1.5rem;border:none;border-radius:5px;cursor:pointer;font-weight:600;">Eliminar</button>
                    <button id="cancel-remove" style="background:#718096;color:#fff;padding:0.75rem 1.5rem;border:none;border-radius:5px;cursor:pointer;font-weight:600;">Cancelar</button>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        document.getElementById('confirm-remove').onclick = () => {
            window.cartService.removeItem(productId);
            modal.remove();
        };
        
        document.getElementById('cancel-remove').onclick = () => {
            modal.remove();
        };
        
        modal.onclick = (e) => {
            if (e.target === modal) modal.remove();
        };
    },

    clearCart() {
        const modal = document.createElement('div');
        modal.className = 'modal-overlay';
        modal.style.cssText = 'position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.5);display:flex;align-items:center;justify-content:center;z-index:10000;';
        
        modal.innerHTML = `
            <div class="modal-content" style="background:#fff;padding:2rem;border-radius:10px;box-shadow:0 4px 20px rgba(0,0,0,0.15);text-align:center;max-width:400px;">
                <div style="font-size:3rem;margin-bottom:1rem;">🗑️</div>
                <h3 style="margin-bottom:1rem;color:#e53e3e;">Vaciar carrito</h3>
                <p style="margin-bottom:2rem;color:#4a5568;">¿Seguro que quieres vaciar todo el carrito?</p>
                <div style="display:flex;gap:1rem;justify-content:center;">
                    <button id="confirm-clear" style="background:#e53e3e;color:#fff;padding:0.75rem 1.5rem;border:none;border-radius:5px;cursor:pointer;font-weight:600;">Vaciar</button>
                    <button id="cancel-clear" style="background:#718096;color:#fff;padding:0.75rem 1.5rem;border:none;border-radius:5px;cursor:pointer;font-weight:600;">Cancelar</button>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        document.getElementById('confirm-clear').onclick = () => {
            window.cartService.clear();
            modal.remove();
        };
        
        document.getElementById('cancel-clear').onclick = () => {
            modal.remove();
        };
        
        modal.onclick = (e) => {
            if (e.target === modal) modal.remove();
        };
    },

    checkout() {
        const items = window.cartService.getItems();
        if (items.length === 0) {
            window.showModal('El carrito está vacío. Agrega productos antes de proceder al pago.', 'warning');
            return;
        }

        // Aquí implementarías el proceso de pago
        window.showModal('Funcionalidad de pago en desarrollo. Pronto podrás completar tu compra.', 'info');
        // window.location.href = '/Nueva-WEB/frontend/pages/checkout.html';
    }
};

// Inicializar el componente cuando el DOM esté listo
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => cartComponent.init());
} else {
    cartComponent.init();
}
