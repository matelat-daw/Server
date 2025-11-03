# 🛒 Carrito de Compras - Nueva WEB

## ✅ Implementación Completada

Se ha implementado un carrito de compras completo basado en el de la aplicación Canarias.

### Archivos Creados/Modificados:

1. **`frontend/services/cart.js`** - Servicio del carrito
   - Gestión de items en localStorage
   - Agregar/eliminar productos
   - Actualizar cantidades
   - Calcular totales

2. **`frontend/components/cart/cart.js`** - Componente visual del modal
   - Modal con lista de productos
   - Controles de cantidad (+/-)
   - Botones de acción (vaciar, pagar)
   - Badge con contador de items

3. **`frontend/components/cart/cart.css`** - Estilos del modal
   - Diseño responsive
   - Animaciones
   - Tema claro/oscuro compatible

4. **`frontend/components/header/header.html`** - Botón del carrito
   - Icono 🛒 en el header
   - Badge con contador
   - Click para abrir modal

5. **`frontend/index.html`** - Scripts incluidos
   - cart.js service cargado
   - cart.js component cargado
   - Inicialización automática

## 📖 Cómo Usar

### Para agregar un producto al carrito:

```javascript
// Desde cualquier página o componente
window.cartService.addItem({
    id: 1,
    name: 'Producto de ejemplo',
    price: 29.99,
    image: '/ruta/imagen.jpg',
    category: 'Electrónica'
}, 1); // cantidad
```

### Ejemplo en una página de productos:

```javascript
// En tu página de productos (products.js):
function renderProduct(product) {
    return `
        <div class="product-card">
            <img src="${product.image}" alt="${product.name}">
            <h3>${product.name}</h3>
            <p class="price">€${product.price}</p>
            <button onclick="addToCart(${product.id}, '${product.name}', ${product.price}, '${product.image}')">
                Agregar al Carrito 🛒
            </button>
        </div>
    `;
}

function addToCart(id, name, price, image) {
    window.cartService.addItem({
        id: id,
        name: name,
        price: price,
        image: image,
        category: 'General'
    });
    
    // Mostrar notificación
    alert('Producto agregado al carrito');
}
```

### Escuchar eventos del carrito:

```javascript
// Escuchar cuando se actualiza el carrito
window.addEventListener('cart-updated', (event) => {
    console.log('Carrito actualizado:', event.detail);
    console.log('Total items:', event.detail.itemCount);
    console.log('Total precio:', event.detail.total);
});

// Escuchar cuando se agrega un item
window.addEventListener('item-added', (event) => {
    console.log('Producto agregado:', event.detail.product);
});

// Escuchar cuando se elimina un item
window.addEventListener('item-removed', (event) => {
    console.log('Producto eliminado:', event.detail.item);
});
```

## 🎨 Personalización

### Cambiar colores del carrito:

Edita `frontend/components/cart/cart.css`:

```css
/* Cambiar color primario */
.btn-primary {
    background: #TU_COLOR; /* Cambia #4CAF50 */
}

/* Cambiar color del badge */
.cart-badge {
    background: #TU_COLOR; /* Cambia #f44336 */
}
```

### Modificar el proceso de pago:

Edita `frontend/components/cart/cart.js`, función `checkout()`:

```javascript
checkout() {
    const items = window.cartService.getItems();
    if (items.length === 0) {
        alert('El carrito está vacío');
        return;
    }

    // Aquí implementa tu lógica de pago
    // Por ejemplo: redirigir a página de checkout
    window.location.href = '/Nueva-WEB/frontend/pages/checkout.html';
    
    // O enviar a un API de pago
    // fetch('/Nueva-WEB/api/orders/create', { ... })
}
```

## 🚀 Funcionalidades

- ✅ Agregar productos al carrito
- ✅ Eliminar productos del carrito
- ✅ Actualizar cantidades (+/-)
- ✅ Calcular total automáticamente
- ✅ Persistencia en localStorage
- ✅ Badge con contador de items
- ✅ Modal responsive
- ✅ Vaciar carrito completo
- ✅ Eventos personalizados para integración

## 📱 Responsive

El carrito está optimizado para:
- ✅ Desktop (>768px)
- ✅ Tablet (768px)
- ✅ Mobile (<768px)

## 🔧 API del CartService

### Métodos disponibles:

```javascript
// Agregar item
window.cartService.addItem(product, quantity);

// Eliminar item
window.cartService.removeItem(productId);

// Actualizar cantidad
window.cartService.updateQuantity(productId, newQuantity);

// Obtener todos los items
window.cartService.getItems();

// Obtener cantidad total de productos
window.cartService.getItemCount();

// Obtener total en precio
window.cartService.getTotal();

// Vaciar carrito
window.cartService.clear();

// Verificar si un producto está en el carrito
window.cartService.hasItem(productId);
```

## 🎉 ¡Listo para usar!

El carrito ya está completamente funcional. Solo necesitas:
1. Tener productos en tu aplicación
2. Llamar a `window.cartService.addItem()` cuando el usuario haga clic en "Agregar al carrito"
3. El resto se maneja automáticamente

---

**Nota**: El carrito guarda los datos en `localStorage` con la clave `nuevaweb_cart`, por lo que persiste entre sesiones del navegador.
