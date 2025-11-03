// Cart Component - Modal del carrito con Bootstrap
const cartComponent = {
    modalInstance: null,

    init() {
        if (typeof window.cartService === 'undefined') {
            setTimeout(() => this.init(), 100);
            return;
        }

        this.createModal();
        this.attachEventListeners();
    },

    createModal() {
        const container = document.getElementById('cart-modal-container');
        if (!container) return;

        container.innerHTML = `
            <div class="modal fade" id="cartModal" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-shopping-cart me-2"></i>Carrito de Compras
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div id="cart-items-container"></div>
                            <div id="cart-empty" class="text-center py-5" style="display: none;">
                                <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">Tu carrito está vacío</h5>
                                <p class="text-muted">Agrega productos para comenzar tu compra</p>
                                <button class="btn btn-primary mt-3" data-bs-dismiss="modal">
                                    Ir a comprar
                                </button>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="w-100">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Total:</h5>
                                    <h4 class="mb-0 text-primary" id="cart-total-amount">€0.00</h4>
                                </div>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-outline-danger flex-fill" onclick="cartComponent.clearCart()">
                                        <i class="fas fa-trash me-2"></i>Vaciar Carrito
                                    </button>
                                    <button class="btn btn-primary flex-fill" onclick="cartComponent.checkout()">
                                        <i class="fas fa-credit-card me-2"></i>Proceder al Pago
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        const modalElement = document.getElementById('cartModal');
        this.modalInstance = new bootstrap.Modal(modalElement);
    },

    attachEventListeners() {
        window.addEventListener('cart-updated', () => this.render());
    },

    open() {
        if (!this.modalInstance) {
            window.showModal('El modal del carrito no está disponible.', 'error');
            return;
        }

        this.render();
        this.modalInstance.show();
    },

    close() {
        if (this.modalInstance) {
            this.modalInstance.hide();
        }
    },

    render() {
        if (!window.cartService) return;

        const items = window.cartService.getItems();
        const container = document.getElementById('cart-items-container');
        const emptyMessage = document.getElementById('cart-empty');
        const totalAmount = document.getElementById('cart-total-amount');

        if (!container || !emptyMessage || !totalAmount) return;

        if (items.length === 0) {
            container.style.display = 'none';
            emptyMessage.style.display = 'block';
            totalAmount.textContent = '€0.00';
            return;
        }

        container.style.display = 'block';
        emptyMessage.style.display = 'none';

        container.innerHTML = items.map(item => `
            <div class="card mb-3">
                <div class="row g-0 align-items-center">
                    <div class="col-md-3">
                        <img src="${item.image}" class="img-fluid rounded-start p-2" alt="${item.name}">
                    </div>
                    <div class="col-md-9">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="card-title mb-1">${item.name}</h5>
                                    <span class="badge bg-primary mb-2">${item.category}</span>
                                    <p class="card-text fw-bold text-primary mb-2">€${item.price.toFixed(2)}</p>
                                </div>
                                <button class="btn btn-sm btn-outline-danger" onclick="cartComponent.removeItem(${item.id})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <div class="input-group input-group-sm" style="max-width: 150px;">
                                <button class="btn btn-outline-secondary" onclick="cartComponent.decreaseQuantity(${item.id})">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" class="form-control text-center" value="${item.quantity}" 
                                       min="1" onchange="cartComponent.updateQuantity(${item.id}, this.value)">
                                <button class="btn btn-outline-secondary" onclick="cartComponent.increaseQuantity(${item.id})">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            <div class="mt-2">
                                <strong>Subtotal:</strong> €${(item.price * item.quantity).toFixed(2)}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');

        totalAmount.textContent = '€' + window.cartService.getTotal().toFixed(2);
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
        window.showConfirm(
            '¿Estás seguro de que quieres eliminar este producto del carrito?',
            'Eliminar Producto',
            () => window.cartService.removeItem(productId)
        );
    },

    clearCart() {
        window.showConfirm(
            '¿Estás seguro de que quieres vaciar todo el carrito?',
            'Vaciar Carrito',
            () => window.cartService.clear()
        );
    },

    checkout() {
        const items = window.cartService.getItems();
        if (items.length === 0) {
            window.showModal('El carrito está vacío. Agrega productos antes de proceder al pago.', 'warning');
            return;
        }

        window.showModal('Funcionalidad de pago en desarrollo. Pronto podrás completar tu compra.', 'info');
    }
};

window.cartComponent = cartComponent;

// Auto-inicializar
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => cartComponent.init());
} else {
    cartComponent.init();
}
