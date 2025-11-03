// Cart Service - Nueva WEB
class CartService {
    constructor() {
        this.items = [];
        this.total = 0;
        this.storageKey = 'nuevaweb_cart';
        this.loadFromStorage();
    }

    // Cargar carrito desde localStorage
    loadFromStorage() {
        try {
            const savedCart = localStorage.getItem(this.storageKey);
            if (savedCart) {
                const cartData = JSON.parse(savedCart);
                this.items = cartData.items || [];
                this.total = cartData.total || 0;
                this.calculateTotal();
            }
        } catch (error) {
            console.error('Error cargando carrito:', error);
            this.items = [];
            this.total = 0;
        }
    }

    // Guardar carrito en localStorage
    saveToStorage() {
        try {
            const cartData = {
                items: this.items,
                total: this.total,
                timestamp: new Date().toISOString()
            };
            localStorage.setItem(this.storageKey, JSON.stringify(cartData));
        } catch (error) {
            console.error('Error guardando carrito:', error);
        }
    }

    // Agregar producto al carrito
    addItem(product, quantity = 1) {
        const existingItem = this.items.find(item => item.id === product.id);
        
        if (existingItem) {
            existingItem.quantity += quantity;
        } else {
            this.items.push({
                id: product.id,
                name: product.name || product.title,
                price: parseFloat(product.price) || 0,
                image: product.image || product.img || '/Nueva-WEB/frontend/imgs/default-product.jpg',
                quantity: quantity,
                category: product.category || 'General'
            });
        }

        this.calculateTotal();
        this.saveToStorage();
        this.dispatchEvent('cart-updated');
        this.dispatchEvent('item-added', { product, quantity });
        
        return true;
    }

    // Quitar producto del carrito
    removeItem(productId) {
        const itemIndex = this.items.findIndex(item => item.id == productId);
        
        if (itemIndex > -1) {
            const removedItem = this.items.splice(itemIndex, 1)[0];
            this.calculateTotal();
            this.saveToStorage();
            this.dispatchEvent('cart-updated');
            this.dispatchEvent('item-removed', { item: removedItem });
            return true;
        }
        
        return false;
    }

    // Actualizar cantidad
    updateQuantity(productId, newQuantity) {
        const item = this.items.find(item => item.id == productId);
        
        if (item) {
            if (newQuantity <= 0) {
                return this.removeItem(productId);
            }
            
            item.quantity = parseInt(newQuantity);
            this.calculateTotal();
            this.saveToStorage();
            this.dispatchEvent('cart-updated');
            this.dispatchEvent('quantity-updated', { productId, newQuantity });
            return true;
        }
        
        return false;
    }

    // Calcular total
    calculateTotal() {
        this.total = this.items.reduce((sum, item) => {
            return sum + (item.price * item.quantity);
        }, 0);
    }

    // Vaciar carrito
    clear() {
        this.items = [];
        this.total = 0;
        this.saveToStorage();
        this.dispatchEvent('cart-cleared');
        this.dispatchEvent('cart-updated');
    }

    // Obtener todos los items
    getItems() {
        return [...this.items];
    }

    // Obtener cantidad total de productos
    getItemCount() {
        return this.items.reduce((count, item) => count + item.quantity, 0);
    }

    // Obtener total
    getTotal() {
        return this.total;
    }

    // Verificar si un producto está en el carrito
    hasItem(productId) {
        return this.items.some(item => item.id == productId);
    }

    // Despachar eventos personalizados
    dispatchEvent(eventName, detail = {}) {
        const event = new CustomEvent(eventName, {
            detail: {
                cart: this,
                items: this.getItems(),
                total: this.total,
                itemCount: this.getItemCount(),
                ...detail
            }
        });
        window.dispatchEvent(event);
    }
}

// Crear instancia global
window.cartService = new CartService();
