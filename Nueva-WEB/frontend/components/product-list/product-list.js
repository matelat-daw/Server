// frontend/components/product-list/product-list.js
document.addEventListener("DOMContentLoaded", function() {
    const productListContainer = document.getElementById("product-list");

    function fetchProducts() {
        fetch('/api/products') // Adjust the API endpoint as necessary
            .then(response => response.json())
            .then(data => {
                renderProducts(data);
            })
            .catch(error => {
                console.error('Error fetching products:', error);
            });
    }

    function renderProducts(products) {
        productListContainer.innerHTML = ''; // Clear existing products
        products.forEach(product => {
            const productCard = document.createElement('div');
            productCard.className = 'product-card';
            productCard.innerHTML = `
                <h3>${product.name}</h3>
                <p>${product.description}</p>
                <span>$${product.price}</span>
                <button onclick="addToCart(${product.id})">Add to Cart</button>
            `;
            productListContainer.appendChild(productCard);
        });
    }

    function addToCart(productId) {
        // Logic to add the product to the cart
        console.log(`Product ${productId} added to cart`);
    }

    fetchProducts();
});