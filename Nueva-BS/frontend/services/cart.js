// Cart Service - Maneja el carrito de compras
class CartService {
    constructor() {
        this.storageKey = 'nuevabs_cart';
        this.items = this.loadFromStorage();
    }

    loadFromStorage() {
        try {
            const data = localStorage.getItem(this.storageKey);
            return data ? JSON.parse(data) : [];
        } catch (error) {
            console.error('Error loading cart:', error);
            return [];
        }
    }

    saveToStorage() {
        try {
            localStorage.setItem(this.storageKey, JSON.stringify(this.items));
            this.dispatchCartUpdate();
        } catch (error) {
            console.error('Error saving cart:', error);
        }
    }

    dispatchCartUpdate() {
        window.dispatchEvent(new CustomEvent('cart-updated', {
            detail: { items: this.items, total: this.getTotal() }
        }));
    }

    addItem(product) {
        const existingItem = this.items.find(item => item.id === product.id);

        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            this.items.push({
                id: product.id,
                name: product.name,
                price: parseFloat(product.price),
                image: product.image || '/Nueva-BS/frontend/imgs/producto-generico.svg',
                category: product.category || 'General',
                quantity: 1
            });
        }

        this.saveToStorage();
    }

    removeItem(productId) {
        this.items = this.items.filter(item => item.id !== productId);
        this.saveToStorage();
    }

    updateQuantity(productId, quantity) {
        const item = this.items.find(item => item.id === productId);
        if (item) {
            item.quantity = parseInt(quantity);
            if (item.quantity <= 0) {
                this.removeItem(productId);
            } else {
                this.saveToStorage();
            }
        }
    }

    clear() {
        this.items = [];
        this.saveToStorage();
    }

    getItems() {
        return this.items;
    }

    getTotal() {
        return this.items.reduce((total, item) => {
            return total + (item.price * item.quantity);
        }, 0);
    }

    getItemCount() {
        return this.items.reduce((count, item) => count + item.quantity, 0);
    }
}

// Instancia global
window.cartService = new CartService();
