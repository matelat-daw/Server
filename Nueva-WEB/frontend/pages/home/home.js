// frontend/pages/home/home.js
document.addEventListener("DOMContentLoaded", function() {
    const welcomeMessage = document.createElement("h1");
    welcomeMessage.textContent = "Bienvenido a nuestra tienda!";
    document.body.appendChild(welcomeMessage);

    // Function to load featured products
    function loadFeaturedProducts() {
        // Placeholder for fetching products from the API
        fetch('/api/products/featured')
            .then(response => response.json())
            .then(products => {
                const productList = document.createElement("div");
                productList.className = "product-list";

                products.forEach(product => {
                    const productCard = document.createElement("div");
                    productCard.className = "product-card";
                    productCard.innerHTML = `
                        <h2>${product.name}</h2>
                        <p>${product.description}</p>
                        <span>${product.price} €</span>
                        <button>Add to Cart</button>
                    `;
                    productList.appendChild(productCard);
                });

                document.body.appendChild(productList);
            })
            .catch(error => console.error('Error fetching featured products:', error));
    }

    loadFeaturedProducts();
});