// Product Card Component
const productCardComponent = {
    create(product) {
        const col = document.createElement('div');
        col.className = 'col-lg-3 col-md-4 col-sm-6 mb-4';

        col.innerHTML = `
            <div class="card product-card h-100">
                <img src="${product.image || '/Nueva-BS/frontend/imgs/producto-generico.svg'}" 
                     class="card-img-top" 
                     alt="${product.name}"
                     onerror="this.src='/Nueva-BS/frontend/imgs/producto-generico.svg'">
                <div class="card-body d-flex flex-column">
                    <span class="badge bg-primary mb-2 align-self-start">${product.category || 'General'}</span>
                    <h5 class="card-title">${product.name}</h5>
                    <p class="card-text text-muted">${product.description || ''}</p>
                    <div class="price mt-auto">€${parseFloat(product.price).toFixed(2)}</div>
                    <button class="btn btn-primary btn-add-cart mt-2" data-product-id="${product.id}">
                        <i class="fas fa-cart-plus me-2"></i>Añadir al Carrito
                    </button>
                </div>
            </div>
        `;

        const button = col.querySelector('.btn-add-cart');
        button.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            this.addToCart(product);
        });

        return col;
    },

    addToCart(product) {
        if (typeof window.cartService === 'undefined') {
            window.showModal('El servicio del carrito no está disponible. Por favor recarga la página.', 'error');
            return;
        }

        const cartItem = {
            id: product.id,
            name: product.name,
            price: parseFloat(product.price),
            image: product.image || '/Nueva-BS/frontend/imgs/producto-generico.svg',
            category: product.category || 'General'
        };

        window.cartService.addItem(cartItem);
        this.showNotification(product);
    },

    showNotification(product) {
        // Crear toast de Bootstrap
        const toastHTML = `
            <div class="toast toast-notification show" role="alert">
                <div class="toast-header bg-success text-white">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong class="me-auto">Producto Agregado</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">
                    <strong>${product.name}</strong> se ha agregado al carrito correctamente.
                </div>
            </div>
        `;

        const toastContainer = document.createElement('div');
        toastContainer.innerHTML = toastHTML;
        document.body.appendChild(toastContainer);

        const toastElement = toastContainer.querySelector('.toast');
        const toast = new bootstrap.Toast(toastElement, { delay: 3000 });
        
        toastElement.addEventListener('hidden.bs.toast', () => {
            toastContainer.remove();
        });

        toast.show();
    }
};

window.productCardComponent = productCardComponent;
